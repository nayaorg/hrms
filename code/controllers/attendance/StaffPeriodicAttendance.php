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

class StaffPeriodicAttendance extends ControllerBase {
	
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
	
		$cls_ot = new OvertimeClass($this->db);
		
		$diff1Day = new DateInterval('P1D');
		
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($datas,'dateReportBegin',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($datas,'dateReportEnd',$dte). " 23:59:59";
		
		$filter = "" ;
		$emp_id_begin = $this->getParam($datas,'empIdBegin',"") ;
		$emp_id_end = $this->getParam($datas,'empIdEnd',"") ;
		$dept_id = $this->getParam($datas,'dept',"") ;
		$reporttype = $this->getParam($datas,'reporttype',"") ;
		
		$date_report_start = new DateTime($dateBegin);
		$date_report_end = new DateTime($dateEnd);
		
	
		$list = "" ;
		
		$sql_emp = "SELECT * FROM " . EmployeeTable::C_TABLE;
		if ($dept_id > 0) {
			$sql_emp .= " WHERE " . EmployeeTable::C_DEPT . " = " . $dept_id;
		}
		
		$rows_emp = $this->db->getTable($sql_emp);
		
		$last_id = 0;
		
		foreach($rows_emp as $row_emp){
			
			$date_report = new DateTime($date_report_start->format('Y-m-d H:i:s'));
			
			while($date_report <= $date_report_end){
			
				$emp_id_begin = $row_emp[EmployeeTable::C_ID];
				$emp_id_end = $row_emp[EmployeeTable::C_ID];
				
				$month = $date_report->format('m');
				$year =  $date_report->format('y');
				$day =  $date_report->format('d');
				
				$date_report_2 = $date_report->format('Y-m-d H:i:s');
				
				//Log::write($emp_id_begin . ' ' . $date_report_2);
				$date_shift = $month . $year;
				
				$rows = $this->getData($date_report_2, $emp_id_begin, $emp_id_end, $dept_id, $date_shift, $day) ;
				if (count($rows) > 0) {
					foreach ($rows as $row) {
				
						//Log::write('ROWS ' . $emp_id_begin . ' ' . $date_report_2);
						
						$list .= "<tr>" ;
						
						//$list .= "<td>" . ($last_id != $emp_id_begin ? $row[EmployeeTable::C_ID] : "" ) . "</td>" ;
						//$list .= '<td style="width: 10px;">' . ($last_id != $emp_id_begin ? $row[EmployeeTable::C_NAME] : "" ) . "</td>" ;
						
						$list .= "<td>" . $row[EmployeeTable::C_ID] . "</td>" ;
						$list .= '<td>' . $row[EmployeeTable::C_NAME] . "</td>" ;
						
						
						if($last_id != $emp_id_begin){
							$last_id = $emp_id_begin;
						}
						
						$list .= "<td>" . $date_report->format('d/m/Y') . "</td>" ;
				
						$list .= "<td style='text-align:center'>" . (is_null($row[AttendanceTable::C_TIME_START]) ? "--" : $row[AttendanceTable::C_TIME_START])  . "</td>" ;
						$list .= "<td style='text-align:center'>" . ($row[AttendanceTable::C_LATE_IN] != 0 ? $row[AttendanceTable::C_LATE_IN] : "--") . "</td>" ;
						$list .= "<td style='text-align:center'>" . (is_null($row[AttendanceTable::C_TIME_END]) ? "--" : $row[AttendanceTable::C_TIME_END])  . "</td>";
						$list .= "<td style='text-align:center'>" . ($row[AttendanceTable::C_EARLY_OUT] != 0 ? $row[AttendanceTable::C_EARLY_OUT] : "--") . "</td>" ;
						
						$temp_str = "";
						if(is_null($row[AttendanceTable::C_BREAK_START])){
							$temp_str = "--";
						} else {
							$timetemp = new DateTime($row[AttendanceTable::C_BREAK_START]);
							$temp_str = $timetemp->format('H:i') ;
						}
						$list .= "<td style='text-align:center'>" . $temp_str . "</td>" ;
						
						
						$temp_str = "";
						if(is_null($row[AttendanceTable::C_BREAK_END])){
							$temp_str = "--";
						} else {
							$timetemp = new DateTime($row[AttendanceTable::C_BREAK_END]);
							$temp_str = $timetemp->format('H:i') ;
						}
						$list .= "<td style='text-align:center'>" . $temp_str . "</td>" ;
				
						$ot_hour = $cls_ot->getOvertimeHour($row[EmployeeTable::C_ID], $date_report_2, -1);
						$ot_hour = $ot_hour + $cls_ot->getOvertimeHour($row[EmployeeTable::C_ID], $date_report_2, -2);
						
						$list .= "<td style='text-align:center'>" . $ot_hour . "</td>" ;
						
						$remarks = $this->getRemarks($row[EmployeeTable::C_ID], $date_report_2, $date_shift, $day);
						
						$list .= "<td>" . $remarks . "</td>" ;
						
						$list .= "</tr>" ;
					}
				}
				unset($rows) ;
				
				$date_report->add($diff1Day);
			}
			
		}
		unset($cls_ot);
		//Log::write($list);
		return $list == '' ? "<tr><td colspan='11'>No Employee Found.</td></tr>" : $list;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/StaffPeriodicAttendanceView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getData($dateReport, $emp_id_begin, $emp_id_end, $dept_id, $date_shift, $day) {
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.");
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ; ;
		if ($emp_id_begin != "" && $emp_id_end != "") {
			$filter .= " and e.EMP_ID >= " . $emp_id_begin . " and e.EMP_ID <= " . $emp_id_end;
		}
		
		$sql = " select e.*, "
				. " a." . AttendanceTable::C_TIME_START . ", a." . AttendanceTable::C_TIME_END . ", "
				. " a." . AttendanceTable::C_BREAK_START . ", a." . AttendanceTable::C_BREAK_END . ", "
				. " a." . AttendanceTable::C_LATE_IN . ", a." . AttendanceTable::C_EARLY_OUT . " "
				. " from " . EmployeeTable::C_TABLE . "  e "
				. " left outer join " . AttendanceTable::C_TABLE . " a "
				. " on (a." . AttendanceTable::C_EMP_ID . " = e." . EmployeeTable::C_ID . " and a." . AttendanceTable::C_DATE . " = '" . $dateReport . "') "
				. " where " . $filter 
				;
			
		return $this->db->getTable($sql,$params) ;
	}
	private function getExport($params=null) {
	
		$cls_ot = new OvertimeClass($this->db);
		
		$diff1Day = new DateInterval('P1D');
			
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($params,'date',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($params,'dateend',$dte). " 23:59:59";
		
		$filter = "" ;
		$emp_id_begin = $this->getParam($params,'empIdBegin',"") ;
		$emp_id_end = $this->getParam($params,'empIdEnd',"") ;
		$dept_id = $this->getParam($params,'dept',"") ;
		$reporttype = $this->getParam($params,'reporttype',"") ;
		
		
		$date_report_start = new DateTime($dateBegin);
		$date_report_end = new DateTime($dateEnd);
		
	
		$list = "" ;
		$datas = "" ;
		$expfile = "" ;
		
		$sql_emp = "SELECT * FROM " . EmployeeTable::C_TABLE;
		if ($dept_id > 0) {
			$sql_emp .= " WHERE " . EmployeeTable::C_DEPT . " = " . $dept_id;
		}
		
		$rows_emp = $this->db->getTable($sql_emp);
		
		foreach($rows_emp as $row_emp){
			
			$date_report = new DateTime($date_report_start->format('Y-m-d H:i:s'));
			
			while($date_report <= $date_report_end){
			
				$emp_id_begin = $row_emp[EmployeeTable::C_ID];
				$emp_id_end = $row_emp[EmployeeTable::C_ID];
				
				$month = $date_report->format('m');
				$year =  $date_report->format('y');
				$day =  $date_report->format('d');
				
				$date_report_2 = $date_report->format('Y-m-d H:i:s');
				
				$date_shift = $month . $year;
				
				$rows = $this->getData($date_report_2, $emp_id_begin, $emp_id_end, $dept_id, $date_shift, $day) ;
				if (count($rows) > 0) {
					foreach ($rows as $row) {
						$datas .= $row[EmployeeTable::C_ID] . "," . $row[EmployeeTable::C_NAME] . "," . $date_report->format('Y-m-d');
				
						$datas .= "," . (is_null($row[AttendanceTable::C_TIME_START]) ? "--" : $row[AttendanceTable::C_TIME_START]);
						$datas .= "," . ($row[AttendanceTable::C_LATE_IN] != 0 ? $row[AttendanceTable::C_LATE_IN] : "--");
						$datas .= "," . (is_null($row[AttendanceTable::C_TIME_END]) ? "--" : $row[AttendanceTable::C_TIME_END]);
						$datas .= "," . ($row[AttendanceTable::C_EARLY_OUT] != 0 ? $row[AttendanceTable::C_EARLY_OUT] : "--");
				
						
						$temp_str = "";
						if(is_null($row[AttendanceTable::C_BREAK_START])){
							$temp_str = "--";
						} else {
							$timetemp = new DateTime($row[AttendanceTable::C_BREAK_START]);
							$temp_str = $timetemp->format('H:i') ;
						}
						$datas .= "," . $temp_str;
						
						
						$temp_str = "";
						if(is_null($row[AttendanceTable::C_BREAK_END])){
							$temp_str = "--";
						} else {
							$timetemp = new DateTime($row[AttendanceTable::C_BREAK_END]);
							$temp_str = $timetemp->format('H:i') ;
						}
						$datas .= "," . $temp_str;
				
						$ot_hour = $cls_ot->getOvertimeHour($row[EmployeeTable::C_ID], $date_report_2, -1);
						$ot_hour = $ot_hour + $cls_ot->getOvertimeHour($row[EmployeeTable::C_ID], $date_report_2, -2);
						
						$datas .= "," . $ot_hour ;
						
						$remarks = $this->getRemarks($row[EmployeeTable::C_ID], $date_report_2, $date_shift, $day);
						
						$datas .= "," . $remarks ;
						$datas .= "\r\n" ;
					}
				}
				unset($rows) ;
				
				$date_report->add($diff1Day);
			}
			
		}
		unset($cls_ot);
		//Log::write($list);
		if($datas != ''){
			if ($expfile == "")
				$expfile = "StaffPeriodicAttendance.txt" ;
			header('Content-disposition: attachment; filename='. $expfile);
			header('Content-type: text/plain');
			header("Content-Length: ".strlen($datas));
			echo $datas;
		} else {
			echo "No Record Found." ;
			return;
		}
		return $datas == '' ? "<tr><td colspan='11'>No Employee Found.</td></tr>" : $datas;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
	
		$cls_ot = new OvertimeClass($this->db);
		
		$diff1Day = new DateInterval('P1D');
		
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($params,'date',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($params,'dateend',$dte). " 23:59:59";
		
		
		$date_report_start = new DateTime($dateBegin);
		$date_report_end = new DateTime($dateEnd);
		
		$filter = "" ;
		$emp_id_begin = $this->getParam($params,'empIdBegin',"") ;
		$emp_id_end = $this->getParam($params,'empIdEnd',"") ;
		$dept_id = $this->getParam($params,'dept',"") ;
		$reporttype = $this->getParam($params,'reporttype',"") ;
		
		$list = "" ;
		
		$sql_emp = "SELECT * FROM " . EmployeeTable::C_TABLE;
		if($emp_id_begin != -1){
			$sql_emp .= " WHERE " . EmployeeTable::C_ID . " >= " . $emp_id_begin . " and " . EmployeeTable::C_ID . " <= " . $emp_id_end;
		}
		if ($dept_id != -1) {
			$sql_emp .= " WHERE " . EmployeeTable::C_DEPT . " = " . $dept_id;
		}
		
		$rows_emp = $this->db->getTable($sql_emp);
		$datas = array() ;
		$nr = 'newrow';
		$np = 'newpage';
		$ph = "pageheader";
		$i = 'items';
		$last_id = 0;
		
		$isNotEmpty = 0;
		
		foreach($rows_emp as $row_emp){
			
			$date_report = new DateTime($date_report_start->format('Y-m-d H:i:s'));
			
			/*$items = array() ;
			$items[$i][] = $this->createPdfItem($row_emp[EmployeeTable::C_ID],50,0,"C") ;
			$items[$nr] = "1" ;
			
			$datas[] = $items ;
			
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row_emp[EmployeeTable::C_NAME],150) ;
			$items[$nr] = "1" ;
			
			$datas[] = $items ;*/
			
			while($date_report <= $date_report_end){
			
				$emp_id_begin = $row_emp[EmployeeTable::C_ID];
				$emp_id_end = $row_emp[EmployeeTable::C_ID];
				
				$month = $date_report->format('m');
				$year =  $date_report->format('y');
				$day =  $date_report->format('d');
				
				$date_report_2 = $date_report->format('Y-m-d H:i:s');
				
				//Log::write($emp_id_begin . ' ' . $date_report_2);
				$date_shift = $month . $year;
				
				$rows = $this->getData($date_report_2, $emp_id_begin, $emp_id_end, $dept_id, $date_shift, $day) ;
				if (count($rows) > 0) {
					$isNotEmpty = 1;
					foreach ($rows as $row) {
						$items = array() ;
						$items[$i][] = $this->createPdfItem(($last_id != $emp_id_begin ? $row[EmployeeTable::C_ID] : ""),50,0,"C") ;
						$items[$i][] = $this->createPdfItem(($last_id != $emp_id_begin ? $row[EmployeeTable::C_NAME] : ""),150) ;
						
						if($last_id != $emp_id_begin){
							$items[$np] = "1" ;
							$last_id = $emp_id_begin;
						}
						
						$items[$i][] = $this->createPdfItem($date_report->format('Y-m-d'),100,0,"C") ;
						
						$items[$i][] = $this->createPdfItem((is_null($row[AttendanceTable::C_TIME_START]) ? "--" : $row[AttendanceTable::C_TIME_START]),50,0,"C") ;
						$items[$i][] = $this->createPdfItem(($row[AttendanceTable::C_LATE_IN] != 0 ? $row[AttendanceTable::C_LATE_IN] : "--"),50,0,"C") ;
						$items[$i][] = $this->createPdfItem((is_null($row[AttendanceTable::C_TIME_END]) ? "--" : $row[AttendanceTable::C_TIME_END]),50,0,"C") ;
						$items[$i][] = $this->createPdfItem(($row[AttendanceTable::C_EARLY_OUT] != 0 ? $row[AttendanceTable::C_EARLY_OUT] : "--"),50,0,"C") ;
						
						$temp_str = "";
						if(is_null($row[AttendanceTable::C_BREAK_START])){
							$temp_str = "--";
						} else {
							$timetemp = new DateTime($row[AttendanceTable::C_BREAK_START]);
							$temp_str = $timetemp->format('H:i') ;
						}
						$items[$i][] = $this->createPdfItem($temp_str,50,0,"C");
						
						
						$temp_str = "";
						if(is_null($row[AttendanceTable::C_BREAK_END])){
							$temp_str = "--";
						} else {
							$timetemp = new DateTime($row[AttendanceTable::C_BREAK_END]);
							$temp_str = $timetemp->format('H:i') ;
						}
						$items[$i][] = $this->createPdfItem($temp_str,50,0,"C");
				
						$ot_hour = $cls_ot->getOvertimeHour($row[EmployeeTable::C_ID], $date_report_2, -1);
						$ot_hour = $ot_hour + $cls_ot->getOvertimeHour($row[EmployeeTable::C_ID], $date_report_2, -2);
						$items[$i][] = $this->createPdfItem($ot_hour,50,0,"C") ;
						
						
						$remarks = $this->getRemarks($row[EmployeeTable::C_ID], $date_report_2, $date_shift, $day);
						
						$items[$i][] = $this->createPdfItem($remarks,50,0,"C") ;
						$items[$nr] = "1" ;
						
						$datas[] = $items ;

					}
				}
				unset($rows) ;
				
				$date_report->add($diff1Day);
			}
		}
		if($isNotEmpty == 1){
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",50,0,"C","B");
			$cols[] = $this->createPdfItem("Name",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Date",100,0,"C","B");
			$cols[] = $this->createPdfItem("In",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Late In",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Out",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Early Out",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Break From",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Break To",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Overtime",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Remarks",50,0,"C","B") ;
			$headers = array() ;
			$headers[] = "Company : %=COMPANY=%" ;
			$pdf = new ListPdf('L');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Staff Periodic Attendance Report - " . $this->getParamDate($params,'date',$dte) . " - " . $this->getParamDate($params,'dateend',$dte)) ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->setHeaders($headers) ;
			$pdf->setHeaderHeight(135) ;
			$pdf->render($datas) ;
			$pdf->Output('StaffPeriodicAttendance.pdf', 'I');
			unset($cols) ;
			unset($headers);
			unset($items) ;
		}
		unset($cls_ot);
		unset($datas) ;
		unset($params) ;
		//Log::write($list);
		return $list == '' ? "<tr><td colspan='11'>No Employee Found.</td></tr>" : $list;
	}
}
?>