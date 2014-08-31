<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php");
require_once (PATH_MODELS . "hr/DepartmentClass.php");
require_once (PATH_MODELS . "attendance/AttendanceClass.php") ;
require_once (PATH_MODELS . "attendance/TimeOffClass.php") ;
require_once (PATH_MODELS . "attendance/OvertimeClass.php") ;
require_once (PATH_MODELS . "attendance/ShiftUpdateClass.php") ;
require_once (PATH_MODELS . "attendance/ShiftDetailClass.php") ;
require_once (PATH_MODELS . "attendance/EmployeeShiftClass.php") ;

class DailyAbsentee extends ControllerBase {
	
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = EmployeeTable::C_ORG_ID ;
	}
	function __destruct() {
		unset($this->db) ;
	}
	public function processRequest($params) {
		$this->type = REQ_VIEW ;
		
		try {
			$this->db->open() ;
			if (isset($params) && count($params) > 0) {
				if (isset($params['type']))
					$this->type = $params['type'] ;
			}

			switch ($this->type) {
				case REQ_EXPORT:
					$this->getExport($params) ;
					break ;
				case REQ_REPORT:
					$this->getReport($params) ;
					break ;
				case REQ_LIST:
					echo $this->getList($params) ;
					break ;
				case REQ_VIEW:
					$this->getView() ;
					break ;
				default:
					$this->sendJsonResponse(Status::Error,"invalid request.","",$this->type) ;
					break ;
			}
			$this->db->close() ;
			return true ;
		} catch (Exception $e) {
			$this->db->close() ;
			die ($e->getMessage()) ;
		}
	}
	private function getOvertimeRecord($id, $date){
		$sql = "SELECT * FROM OVERTIME ";
		$sql .= " WHERE " . $this->db->fieldParam(OvertimeTable::C_EMP_ID) ;
		$sql .= " AND " . $this->db->fieldParam(OvertimeTable::C_DATE) ;
		$params = array() ;
		$params[] = $this->db->valueParam(OvertimeTable::C_EMP_ID,$id) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_DATE,$date) ;
		
		$rows = $this->db->getTable($sql,$params);
		
		return $rows;
	}
	private function getRemarks($id, $date, $date_shift, $day){
		$week = "0" . date('w', strtotime($date)) + 1;
		$type = "";
		$temp = "";
		
		//shift_update individual
		$sql = " SELECT * FROM " . ShiftUpdateTable::C_TABLE;
		$sql .= " WHERE " . ShiftUpdateTable::C_MONTH . " = '" . $date_shift . "' ";
		$sql .= " AND " . ShiftUpdateTable::C_EMP_ID . " = " . $id . " ";
		
		$row = $this->db->getTable($sql);
		
		if(is_null($row) || count($row) == 0){
			
			//shift_update collective
		
			$sql = " SELECT * FROM " . EmployeeShiftTable::C_TABLE;
			$sql .= " WHERE " . EmployeeShiftTable::C_ID . " = " . $id . " ";
			
			$row = $this->db->getTable($sql);
			
			$shift_group = $row[0][EmployeeShiftTable::C_SHIFT_GROUP_ID];
			$shift_type = $row[0][EmployeeShiftTable::C_SHIFT_TYPE];
		
			$sql = " SELECT * FROM " . ShiftUpdateTable::C_TABLE;
			$sql .= " WHERE " . ShiftUpdateTable::C_MONTH . " = '" . $date_shift . "' ";
			$sql .= " AND " . ShiftUpdateTable::C_SHIFT_GROUP_ID . " = " . $shift_group . " ";
			
			$row = $this->db->getTable($sql);
			
			if(is_null($row) || count($row) == 0){
				$sql = " SELECT * FROM " . ShiftDetailTable::C_TABLE;
				$sql .= " WHERE " . ShiftDetailTable::C_SHIFT_TYPE . " = '" . $shift_type . "' ";
				$sql .= " AND " . ShiftDetailTable::C_SHIFT_GROUP_ID . " = " . $shift_group . " ";
				
				$row = $this->db->getTable($sql);
				
				if(is_null($row) || count($row) == 0){
					return ATTENDANCE_STATUS_O;
				} else {
					$type = $row[0][ShiftUpdateTable::C_SHIFT_TYPE];
					$temp = $row[0]["SHIFT_" . ($type == 'w' ? $week : $day)];
				}
			} else {
				$type = $row[0][ShiftUpdateTable::C_SHIFT_TYPE];
				$temp = $row[0]["SHIFT_" . ($type == 'w' ? $week : $day)];
			}
		} else {
			$type = $row[0][ShiftUpdateTable::C_SHIFT_TYPE];
			$temp = $row[0]["SHIFT_" . ($type == 'w' ? $week : $day)];
		}
		
		if(is_null($temp) || $temp == "" || $temp == 0)
			return ATTENDANCE_STATUS_O;
		
		$sql = " SELECT * FROM " . TimeOffTable::C_TABLE;
		$sql .= " WHERE " . TimeOffTable::C_DATE_OFF . " = '" . $date . "' ";
		$sql .= " AND " . TimeOffTable::C_EMP_ID . " = " . $id . " ";
		
		$row = $this->db->getTable($sql);
		
		if(!(is_null($row) || count($row) == 0))
			return ATTENDANCE_STATUS_T;
		
		$sql = "SELECT * FROM " . AttendanceTable::C_TABLE;
		$sql .= " WHERE " . AttendanceTable::C_DATE . " = '" . $date . "' ";
		$sql .= " AND " . AttendanceTable::C_EMP_ID . " = " . $id . " ";
		
		$row = $this->db->getTable($sql);
		
		return (is_null($row) || count($row) == 0) ? ATTENDANCE_STATUS_N : ATTENDANCE_STATUS_P ;
	}
	
	private function getList($datas=null) {
			
		$dte = date_create('now')->format('Y-m-d') ;
		
		$diff1Day = new DateInterval('P1D');
		
		$date_report_start = $this->getParamDate($datas,'dateReportStart',$dte). " 00:00:00";
		$date_report_end = $this->getParamDate($datas,'dateReportEnd',$dte). " 00:00:00";
		
		$date_report_start = new DateTime($date_report_start);
		$date_report_end = new DateTime($date_report_end);
		$dept_id = $this->getParam($datas,'dept',"") ;
		
		$list = "" ;
			
		$date_report = $date_report_start;
		while($date_report <= $date_report_end){
			
			$month = $date_report->format('m');
			$year =  $date_report->format('y');
			$day =  $date_report->format('d');
			
			$date_report_2 = $date_report->format('Y-m-d H:i:s');
			
			$date_shift = $month . $year;
			
			$rows = $this->getData($date_report_2, $dept_id, $date_shift, $day) ;
			if (count($rows) > 0) {
				foreach ($rows as $row) {
					$remarks = $this->getRemarks($row[EmployeeTable::C_ID], $date_report_2, $date_shift, $day);
					
					if($remarks != ATTENDANCE_STATUS_P && $remarks != ATTENDANCE_STATUS_T && $remarks != ATTENDANCE_STATUS_O ){
						$list .= "<tr>" ;
						$list .= "<td>" . $date_report->format('Y-m-d') . "</td>" ;
						$list .= "<td>" . $row[EmployeeTable::C_ID] . "</td>" ;
						$list .= "<td>" . $row[EmployeeTable::C_NAME] . "</td>" ;
					
						$list .= "<td>" . $remarks . "</td>" ;
						$list .= "</tr>" ;
					}
				}
			}
			unset($rows) ;
			
			$date_report->add($diff1Day);
		}
		return $list != 0 ? "<tr><td colspan='4'>No Employee Found.</td></tr>" : $list;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/DailyAbsenteeView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getData($dateReport, $dept_id, $date_shift, $day) {
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.");
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ; ;
		if ($dept_id != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$dept_id) ;
		}
		
		$sql = " select e.* "
				. " from " . EmployeeTable::C_TABLE . "  e "
				. " left outer join " . AttendanceTable::C_TABLE . " a "
				. " on (a." . AttendanceTable::C_EMP_ID . " = e." . EmployeeTable::C_ID . " and a." . AttendanceTable::C_DATE . " = '" . $dateReport . "') "
				. " where " . $filter 
				;

		$sql .= " order by e." . EmployeeTable::C_COY_ID . ", e." . EmployeeTable::C_DEPT . ", e." . EmployeeTable::C_NAME;
		return $this->db->getTable($sql,$params) ;
	}
	private function getExport($params=null) {
		$dte = date_create('now')->format('Y-m-d') ;
		
		$diff1Day = new DateInterval('P1D');
		
		$date_report_start = $this->getParamDate($params,'date',$dte). " 00:00:00";
		$date_report_end = $this->getParamDate($params,'dateend',$dte). " 00:00:00";
		
		$date_report_start = new DateTime($date_report_start);
		$date_report_end = new DateTime($date_report_end);
		$dept_id = $this->getParam($params,'dept',"") ;
		
		$list = "" ;
		$expfile = "";
			
		$date_report = $date_report_start;
		while($date_report <= $date_report_end){
			
			$month = $date_report->format('m');
			$year =  $date_report->format('y');
			$day =  $date_report->format('d');
			
			$date_report_2 = $date_report->format('Y-m-d H:i:s');
			
			$date_shift = $month . $year;
			
			$rows = $this->getData($date_report_2, $dept_id, $date_shift, $day) ;
			if (count($rows) > 0) {
				foreach ($rows as $row) {
					$remarks = $this->getRemarks($row[EmployeeTable::C_ID], $date_report_2, $date_shift, $day);
					
					if($remarks != "Present" && $remarks != "Time Off" && $remarks != "Off Duty" ){
						$list .= "" . $date_report->format('Y-m-d');
						$list .= "," . $row[EmployeeTable::C_ID];
						$list .= "," . $row[EmployeeTable::C_NAME];
						$list .= "," . $remarks;
						$list .= "\r\n" ;
					}
				}
			}
			unset($rows) ;
			
			$date_report->add($diff1Day);
		}
		
		if($list != ''){
			if ($expfile == "")
				$expfile = "DailyAbsentee.txt" ;
			header('Content-disposition: attachment; filename='. $expfile);
			header('Content-type: text/plain');
			header("Content-Length: ".strlen($list));
			echo $list;
		} else {
			echo "No Record Found." ;
			return;
		}
		return $list != '' ? "<tr><td colspan='4'>No Employee Found.</td></tr>" : $list;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		$dte = date_create('now')->format('Y-m-d') ;
		
		$diff1Day = new DateInterval('P1D');
		
		$date_report_start = $this->getParamDate($params,'date',$dte). " 00:00:00";
		$date_report_end = $this->getParamDate($params,'dateend',$dte). " 00:00:00";
		
		
		$date_report_start = new DateTime($date_report_start);
		$date_report_end = new DateTime($date_report_end);
		$dept_id = $this->getParam($params,'dept',"") ;
		
		$datas = array() ;
		$isNotEmpty = 0;
			
		$date_report = $date_report_start;
		while($date_report <= $date_report_end){
			
			$month = $date_report->format('m');
			$year =  $date_report->format('y');
			$day =  $date_report->format('d');
			
			$date_report_2 = $date_report->format('Y-m-d H:i:s');
			
			$date_shift = $month . $year;
			
			$rows = $this->getData($date_report_2, $dept_id, $date_shift, $day) ;
			if (count($rows) > 0) {
				$nr = 'newrow';
				$np = 'newpage';
				$ph = "pageheader";
				$i = 'items';
				foreach ($rows as $row) {
					$remarks = $this->getRemarks($row[EmployeeTable::C_ID], $date_report_2, $date_shift, $day);
					
					if($remarks != "Present" && $remarks != "Time Off" && $remarks != "Off Duty" ){
						$isNotEmpty = 1;
						$items = array() ;
						$items[$i][] = $this->createPdfItem($date_report->format('Y-m-d'),100,0,"C") ;
						$items[$i][] = $this->createPdfItem($row[EmployeeTable::C_ID],50,0,"C") ;
						$items[$i][] = $this->createPdfItem($row[EmployeeTable::C_NAME],150) ;
						$items[$i][] = $this->createPdfItem($remarks,200) ;
				
						$datas[] = $items ;
					}
				}
			}
			unset($rows) ;
			
			$date_report->add($diff1Day);
		}
		if($isNotEmpty == 1){
			$cols = array() ;
			$cols[] = $this->createPdfItem("Date",100,0,"C","B");
			$cols[] = $this->createPdfItem("ID",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Name",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Remarks",200,0,"C","B") ;
			$headers = array() ;
			$headers[] = "Company : %=COMPANY=%" ;
			$pdf = new ListPdf('L');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Daily Absentee Report - " . $this->getParamDate($params,'date',$dte) . " - " . $this->getParamDate($params,'empIdBegin',$dte)) ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->setHeaders($headers) ;
			$pdf->setHeaderHeight(135) ;
			$pdf->render($datas) ;
			$pdf->Output('DailyAbsentee.pdf', 'I');
			unset($datas) ;
			unset($params) ;
			unset($items) ;
			unset($cols) ;
			unset($headers);
			return;
		} else {
			echo "<tr><td colspan='4'>No Record Found.</td></tr>" ;
			return;
		}
	}
}
?>