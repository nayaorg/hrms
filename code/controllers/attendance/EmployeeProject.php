<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_MODELS . "admin/CompanyOptions.php") ;
require_once (PATH_MODELS . "attendance/AttendanceClass.php") ;
require_once (PATH_MODELS . "attendance/TimeOffClass.php") ;
require_once (PATH_MODELS . "attendance/OvertimeClass.php") ;
require_once (PATH_MODELS . "attendance/ProjectClass.php") ;
require_once (PATH_MODELS . "attendance/HolidayClass.php") ;
require_once (PATH_MODELS . "attendance/AttendanceProjectClass.php") ;
require_once (PATH_MODELS . "attendance/EmployeeShiftClass.php") ;
require_once (PATH_MODELS . "attendance/RateGroupClass.php") ;

class EmployeeProject extends ControllerBase {
	
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
	
	private function getEmployee($dept_id){
		$params = array() ;
		$sql = "SELECT * FROM " . EmployeeTable::C_TABLE;
		if($dept_id <> 0){
			$sql .= " WHERE " . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$dept_id) ;
		}
		
		$rows = $this->db->getTable($sql,$params);
		
		return $rows;
	}
	
	private function getList($datas=null) {
	
		$cls_at = new AttendanceClass($this->db);
		$cls_ot = new OvertimeClass($this->db);
		$cls_ap = new AttendanceProjectClass($this->db);
		$cls_h = new HolidayClass($this->db);
		$cls_emp_s = new EmployeeShiftClass($this->db);
		$cls_rg = new RateGroupClass($this->db);
		$cls_emp = new EmployeeClass($this->db);
			
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($datas,'dateReportBegin',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($datas,'dateReportEnd',$dte). " 23:59:59";
		
		$date_report_start = new DateTime($dateBegin);
		$date_report_end = new DateTime($dateEnd);
		
		$filter = "" ;
		$emp_id = $this->getParam($datas,'empIdBegin',"") ;
		$project_id = $this->getParam($datas,'dept',"") ;
		
		$dept_id = $this->getParam($datas, 'empIdEnd', "");
		
		$rows_emp = $this->getEmployee($dept_id);
		
		$list = "" ;
			
		$diff1Day = new DateInterval('P1D');
		
		foreach ($rows_emp as $row_emp){
			$emp_id = $row_emp[EmployeeTable::C_ID];
			
			$list .= "<tr class='empName'>";
			$list .= "<td colspan='4'><strong>" . $row_emp[EmployeeTable::C_NAME] . "</strong></td>";
			$list .= "</tr>";
		
			$nn = 0;
			$no = 0;
			$wn = 0;
			$wo = 0;
			$hn = 0;
			$ho = 0;
				
			$date_report = new DateTime($date_report_start->format('Y-m-d H:i:s'));
			
			while($date_report <= $date_report_end){
				
				$date_report_2 = $date_report->format('Y-m-d H:i:s');
				
				$row_at = $cls_at->getAttendanceRecord($emp_id, $date_report_2);
				$rows_ap = $cls_ap->getAttendanceProjectRecords($emp_id, $date_report_2);
				$row_ot_1 = $cls_ot->getOvertimeRecord($emp_id, $date_report_2,-1);
				$row_ot_2 = $cls_ot->getOvertimeRecord($emp_id, $date_report_2,-2);
				
				$counter = 0;
				$minute = 0;
				$minute_ot = 0;
				if(is_null($rows_ap) || count($rows_ap) == 0){
				} else {
					foreach($rows_ap as $row_ap){
						$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ap[AttendanceProjectTable::C_TIME_START]);
						$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ap[AttendanceProjectTable::C_TIME_END]);
						
						$minute += ($time_end - $time_start) / 60;
						if($counter == 0 || (! is_null($row_ot_1))){
							if($row_ap[AttendanceTable::C_TIME_START] == $row_ot_1[OvertimeTable::C_TIME_START]){
								$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot_1[OvertimeTable::C_TIME_START]);
								$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot_1[OvertimeTable::C_TIME_END]);
								$minute -= ($time_end - $time_start) / 60;
								$minute_ot += ($time_end - $time_start) / 60;
							}
						}
						if(($counter == count($rows_ap) - 1) || (! is_null($row_ot_2))){
							if($row_ap[AttendanceTable::C_TIME_END] == $row_ot_2[OvertimeTable::C_TIME_END]){
								$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot_2[OvertimeTable::C_TIME_START]);
								$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot_2[OvertimeTable::C_TIME_END]);
								$minute -= ($time_end - $time_start) / 60;
								$minute_ot += ($time_end - $time_start) / 60;
							}
						}
						$counter += 1;
					}
				}
				
				if($cls_h->isHoliday($date_report_2)){
					$hn += $minute;
					$ho += $minute_ot;
				} else if (date('w', strtotime($date_report_2)) == 0 || date('w', strtotime($date_report_2)) == 6) {
					$wn += $minute;
					$wo += $minute_ot;
				} else {
					$nn += $minute;
					$no += $minute_ot;
				}
				
				$rows_ot = $cls_ot->getOvertimeRecords($emp_id, $date_report_2);
				
				$minute_ot = 0;
				if(is_null($rows_ot) || count($rows_ot) == 0){
				} else {
					foreach($rows_ot as $row_ot){
						$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot[OvertimeTable::C_TIME_START]);
						$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot[OvertimeTable::C_TIME_END]);
						
						$minute_ot += ($time_end - $time_start) / 60;
					}
				}
				
				if($cls_h->isHoliday($date_report_2)){
					$ho += $minute_ot;
				} else if (date('w', strtotime($date_report_2)) == 0 || date('w', strtotime($date_report_2)) == 6) {
					$wo += $minute_ot;
				} else {
					$no += $minute_ot;
				}
				
				$date_report->add($diff1Day);
			}
			
			$rate_group_id = $cls_emp_s->getRateGroupId($emp_id);
			
			$row_rg = $cls_rg->getRecord($rate_group_id);
			
			$total = 0;
			
			$list .= "<tr>";
			$list .= "<td>Weekday</td>";
			$list .= '<td style="text-align:right">' . $nn . '</td>';
			$list .= '<td style="text-align:right">' . $row_rg[RateGroupTable::C_RATE_NORMAL] . '</td>';
			$list .= '<td style="text-align:right">' . ($row_rg[RateGroupTable::C_RATE_NORMAL] * $nn) . '</td>';
			$total += ($row_rg[RateGroupTable::C_RATE_NORMAL] * $nn);
			$list .= "</tr>";
			
			$list .= "<tr>";
			$list .= "<td>O/T Weekday</td>";
			$list .= '<td style="text-align:right">' . $no . '</td>';
			$list .= '<td style="text-align:right">' . $row_rg[RateGroupTable::C_RATE_NORMAL_OT] . '</td>';
			$list .= '<td style="text-align:right">' . ($row_rg[RateGroupTable::C_RATE_NORMAL_OT] * $no) . '</td>';
			$total += ($row_rg[RateGroupTable::C_RATE_NORMAL_OT] * $no);
			$list .= "</tr>";
			
			$list .= "<tr>";
			$list .= "<td>Weekend</td>";
			$list .= '<td style="text-align:right">' . $wn . '</td>';
			$list .= '<td style="text-align:right">' . $row_rg[RateGroupTable::C_RATE_WEEKEND] . '</td>';
			$list .= '<td style="text-align:right">' . ($row_rg[RateGroupTable::C_RATE_WEEKEND] * $wn) . '</td>';
			$total += ($row_rg[RateGroupTable::C_RATE_WEEKEND] * $wn);
			$list .= "</tr>";
			
			$list .= "<tr>";
			$list .= "<td>O/T Weekend</td>";
			$list .= '<td style="text-align:right">' . $wo . '</td>';
			$list .= '<td style="text-align:right">' . $row_rg[RateGroupTable::C_RATE_WEEKEND_OT] . '</td>';
			$list .= '<td style="text-align:right">' . ($row_rg[RateGroupTable::C_RATE_WEEKEND_OT] * $wo) . '</td>';
			$total += ($row_rg[RateGroupTable::C_RATE_WEEKEND_OT] * $wo);
			$list .= "</tr>";
			
			$list .= "<tr>";
			$list .= "<td>Holiday</td>";
			$list .= '<td style="text-align:right">' . $hn . '</td>';
			$list .= '<td style="text-align:right">' . $row_rg[RateGroupTable::C_RATE_HOLIDAY] . '</td>';
			$list .= '<td style="text-align:right">' . ($row_rg[RateGroupTable::C_RATE_HOLIDAY] * $hn) . '</td>';
			$total += ($row_rg[RateGroupTable::C_RATE_HOLIDAY] * $hn);
			$list .= "</tr>";
			
			$list .= "<tr>";
			$list .= "<td>O/T Holiday</td>";
			$list .= '<td style="text-align:right">' . $ho . '</td>';
			$list .= '<td style="text-align:right">' . $row_rg[RateGroupTable::C_RATE_HOLIDAY_OT] . '</td>';
			$list .= '<td style="text-align:right">' . ($row_rg[RateGroupTable::C_RATE_HOLIDAY_OT] * $ho) . '</td>';
			$total += ($row_rg[RateGroupTable::C_RATE_HOLIDAY_OT] * $ho);
			$list .= "</tr>";
		}
		
		unset($cls_at);
		unset($cls_ot);
		unset($cls_ap);
		unset($cls_h);
		unset($cls_emp_s);
		unset($cls_rg);
		
		return $list ;
	}
	
	private function getProjectList() {
		$filter = array();
		$filter[] = array('field'=>ProjectTable::C_ORG_ID,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ProjectTable::C_TABLE, ProjectTable::C_ID, ProjectTable::C_REF,array('code'=>'','desc'=>'--- Select a Project ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/EmployeeProjectView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getData($dateBegin, $dateEnd, $empIdBegin, $empIdEnd, $reporttype, $dept) {
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.") . ' and '
			. ' a.' . AttendanceTable::C_DATE . " >= '" . $dateBegin . "' and a." . AttendanceTable::C_DATE . " <= '" . $dateEnd . "' ";
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		
		if ($empIdBegin != "-1") {
			$filter .= " and e." . EmployeeTable::C_ID . " >= " . $empIdBegin . " and e." . EmployeeTable::C_ID . " <= " . $empIdEnd;
		}
		if ($dept != "-1" && $dept != "") {
			$filter .= " and e." . EmployeeTable::C_DEPT . " = " . $dept ;
		}
		
		if ($reporttype == "1"){
			$filter .= " and a." . AttendanceTable::C_LATE_IN . " != 0 ";
		} else if ($reporttype == "2"){
			$filter .= " and a." . AttendanceTable::C_EARLY_OUT . " != 0 ";
		} else if ($reporttype == "3"){
			$filter .= " and (a." . AttendanceTable::C_EARLY_OUT . " != 0 or a." . AttendanceTable::C_LATE_IN . " != 0)";
		} else if ($reporttype == "4"){
			$filter .= " and a." . AttendanceTable::C_EARLY_OUT . " != 0 and a." . AttendanceTable::C_LATE_IN . " != 0 ";
		}
		
		$sql = " select e." . EmployeeTable::C_ID . ", e." . EmployeeTable::C_NAME . ", a." . AttendanceTable::C_DATE . ", "
				. " a." . AttendanceTable::C_TIME_START . ", a." . AttendanceTable::C_TIME_END . ", "
				. " a." . AttendanceTable::C_LATE_IN . ", a." . AttendanceTable::C_EARLY_OUT . " "
				. " from " . AttendanceTable::C_TABLE . "  a "
				. " left outer join " . EmployeeTable::C_TABLE . " e "
				. " on (a." . AttendanceTable::C_EMP_ID . " = e." . EmployeeTable::C_ID . ")"
				. " where " . $filter 
				. " order by e." . EmployeeTable::C_COY_ID . ",e." . EmployeeTable::C_DEPT . ",e." . EmployeeTable::C_NAME ;

		return $this->db->getTable($sql,$params) ;
	}
	private function getEmployeeProjectType(){
		$sql = '<option value="1">Late In</option>' 
				. '<option value="2">Early Out</option>'
				. '<option value="3">Late In or Early Out</option>'
				. '<option value="4">Late In and Early Out</option>';
		return $sql;
	}
	private function getExport($params=null) {
		
	
		$cls_at = new AttendanceClass($this->db);
		$cls_ot = new OvertimeClass($this->db);
		$cls_ap = new AttendanceProjectClass($this->db);
		$cls_h = new HolidayClass($this->db);
		$cls_emp_s = new EmployeeShiftClass($this->db);
		$cls_rg = new RateGroupClass($this->db);
			
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($params,'date',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($params,'dateend',$dte). " 23:59:59";
		
		$date_report_start = new DateTime($dateBegin);
		$date_report_end = new DateTime($dateEnd);
		
		$filter = "" ;
		$emp_id = $this->getParam($params,'empIdBegin',"") ;
		$project_id = $this->getParam($params,'dept',"") ;
		
		$dept_id = $this->getParam($params, 'empIdEnd', "");
		
		$rows_emp = $this->getEmployee($dept_id);
		
		$diff1Day = new DateInterval('P1D');
		
		
		$list = "" ;
		foreach($rows_emp as $row_emp){
			$emp_id = $row_emp[EmployeeTable::C_ID];
			
			$list .= "" . $row_emp[EmployeeTable::C_NAME] . "\r\n";
		
			$nn = 0;
			$no = 0;
			$wn = 0;
			$wo = 0;
			$hn = 0;
			$ho = 0;
				
			$date_report = new DateTime($date_report_start->format('Y-m-d H:i:s'));
			
			while($date_report <= $date_report_end){
				
				$date_report_2 = $date_report->format('Y-m-d H:i:s');
				
				$row_at = $cls_at->getAttendanceRecord($emp_id, $date_report_2);
				$rows_ap = $cls_ap->getAttendanceProjectRecords($emp_id, $date_report_2);
				$row_ot_1 = $cls_ot->getOvertimeRecord($emp_id, $date_report_2,-1);
				$row_ot_2 = $cls_ot->getOvertimeRecord($emp_id, $date_report_2,-2);
				
				$counter = 0;
				$minute = 0;
				$minute_ot = 0;
				if(is_null($rows_ap) || count($rows_ap) == 0){
				} else {
					foreach($rows_ap as $row_ap){
						$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ap[AttendanceProjectTable::C_TIME_START]);
						$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ap[AttendanceProjectTable::C_TIME_END]);
						
						$minute += ($time_end - $time_start) / 60;
						if($counter == 0 || (! is_null($row_ot_1))){
							if($row_ap[AttendanceTable::C_TIME_START] == $row_ot_1[OvertimeTable::C_TIME_START]){
								$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot_1[OvertimeTable::C_TIME_START]);
								$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot_1[OvertimeTable::C_TIME_END]);
								$minute -= ($time_end - $time_start) / 60;
								$minute_ot += ($time_end - $time_start) / 60;
							}
						}
						if(($counter == count($rows_ap) - 1) || (! is_null($row_ot_2))){
							if($row_ap[AttendanceTable::C_TIME_END] == $row_ot_2[OvertimeTable::C_TIME_END]){
								$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot_2[OvertimeTable::C_TIME_START]);
								$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot_2[OvertimeTable::C_TIME_END]);
								$minute -= ($time_end - $time_start) / 60;
								$minute_ot += ($time_end - $time_start) / 60;
							}
						}
						$counter += 1;
					}
				}
				
				if($cls_h->isHoliday($date_report_2)){
					$hn += $minute;
					$ho += $minute_ot;
				} else if (date('w', strtotime($date_report_2)) == 0 || date('w', strtotime($date_report_2)) == 6) {
					$wn += $minute;
					$wo += $minute_ot;
				} else {
					$nn += $minute;
					$no += $minute_ot;
				}
				
				$rows_ot = $cls_ot->getOvertimeRecords($emp_id, $date_report_2);
				
				$minute_ot = 0;
				if(is_null($rows_ot) || count($rows_ot) == 0){
				} else {
					foreach($rows_ot as $row_ot){
						$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot[OvertimeTable::C_TIME_START]);
						$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot[OvertimeTable::C_TIME_END]);
						
						$minute_ot += ($time_end - $time_start) / 60;
					}
				}
				
				if($cls_h->isHoliday($date_report_2)){
					$ho += $minute_ot;
				} else if (date('w', strtotime($date_report_2)) == 0 || date('w', strtotime($date_report_2)) == 6) {
					$wo += $minute_ot;
				} else {
					$no += $minute_ot;
				}
				
				$date_report->add($diff1Day);
			}
			
			$rate_group_id = $cls_emp_s->getRateGroupId($emp_id);
			
			$row_rg = $cls_rg->getRecord($rate_group_id);
			
			$total = 0;
			
			$list .= "Weekday,";
			$list .= $nn . ",";
			$list .= $row_rg[RateGroupTable::C_RATE_NORMAL] . ',';
			$list .= ($row_rg[RateGroupTable::C_RATE_NORMAL] * $nn) . '\r\n ';
			$total += ($row_rg[RateGroupTable::C_RATE_NORMAL] * $nn);
			
			$list .= "O/T Weekday,";
			$list .= $no . ',';
			$list .= $row_rg[RateGroupTable::C_RATE_NORMAL_OT] . ',';
			$list .= ($row_rg[RateGroupTable::C_RATE_NORMAL_OT] * $no) . '\r\n ';
			$total += ($row_rg[RateGroupTable::C_RATE_NORMAL_OT] * $no);
			
			$list .= "Weekend,";
			$list .= $wn . ',';
			$list .= $row_rg[RateGroupTable::C_RATE_WEEKEND] . ',';
			$list .= ($row_rg[RateGroupTable::C_RATE_WEEKEND] * $wn) . '\r\n ';
			$total += ($row_rg[RateGroupTable::C_RATE_WEEKEND] * $wn);
			
			$list .= "O/T Weekend,";
			$list .= $wo . ',';
			$list .= $row_rg[RateGroupTable::C_RATE_WEEKEND_OT] . ',';
			$list .= ($row_rg[RateGroupTable::C_RATE_WEEKEND_OT] * $wo) . '\r\n ';
			$total += ($row_rg[RateGroupTable::C_RATE_WEEKEND_OT] * $wo);
			
			$list .= "Holiday,";
			$list .= $hn . ',';
			$list .= $row_rg[RateGroupTable::C_RATE_HOLIDAY] . ',';
			$list .= ($row_rg[RateGroupTable::C_RATE_HOLIDAY] * $hn) . '\r\n ';
			$total += ($row_rg[RateGroupTable::C_RATE_HOLIDAY] * $hn);
			
			$list .= "O/T Holiday,";
			$list .= $ho . ',';
			$list .= $row_rg[RateGroupTable::C_RATE_HOLIDAY_OT] . ',';
			$list .= ($row_rg[RateGroupTable::C_RATE_HOLIDAY_OT] * $ho) . '\r\n ';
			$total += ($row_rg[RateGroupTable::C_RATE_HOLIDAY_OT] * $ho);
		
		}
		
		header('Content-disposition: attachment; filename=EmployeeProject.txt');
		header('Content-type: text/plain');
		header("Content-Length: ".strlen($list));
		
		unset($cls_at);
		unset($cls_ot);
		unset($cls_ap);
		unset($cls_h);
		unset($cls_emp_s);
		unset($cls_rg);
		
		echo $list ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls_at = new AttendanceClass($this->db);
		$cls_ot = new OvertimeClass($this->db);
		$cls_ap = new AttendanceProjectClass($this->db);
		$cls_h = new HolidayClass($this->db);
		$cls_emp_s = new EmployeeShiftClass($this->db);
		$cls_rg = new RateGroupClass($this->db);
			
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($params,'date',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($params,'dateend',$dte). " 23:59:59";
		
		$date_report_start = new DateTime($dateBegin);
		$date_report_end = new DateTime($dateEnd);
		
		$filter = "" ;
		$emp_id = $this->getParam($params,'empIdBegin',"") ;
		$project_id = $this->getParam($params,'dept',"") ;
		
		$dept_id = $this->getParam($params, 'empIdEnd', "");
		
		$rows_emp = $this->getEmployee($dept_id);
		
		$nr = 'newrow';
		$np = 'newpage';
		$ph = "pageheader";
		$i = 'items';
			
		$datas = array() ;
		
		foreach($rows_emp as $row_emp){
		
			$emp_id = $row_emp[EmployeeTable::C_ID];
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row_emp[EmployeeTable::C_NAME],150,0,"L") ;	
			$items[$nr] = "1" ;
			$datas[] = $items ;
		
			$nn = 0;
			$no = 0;
			$wn = 0;
			$wo = 0;
			$hn = 0;
			$ho = 0;
			
			$diff1Day = new DateInterval('P1D');
				
			$date_report = new DateTime($date_report_start->format('Y-m-d H:i:s'));
			
			while($date_report <= $date_report_end){
				
				$date_report_2 = $date_report->format('Y-m-d H:i:s');
				
				$row_at = $cls_at->getAttendanceRecord($emp_id, $date_report_2);
				$rows_ap = $cls_ap->getAttendanceProjectRecords($emp_id, $date_report_2);
				$row_ot_1 = $cls_ot->getOvertimeRecord($emp_id, $date_report_2,-1);
				$row_ot_2 = $cls_ot->getOvertimeRecord($emp_id, $date_report_2,-2);
				
				$counter = 0;
				$minute = 0;
				$minute_ot = 0;
				if(is_null($rows_ap) || count($rows_ap) == 0){
				} else {
					foreach($rows_ap as $row_ap){
						$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ap[AttendanceProjectTable::C_TIME_START]);
						$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ap[AttendanceProjectTable::C_TIME_END]);
						
						$minute += ($time_end - $time_start) / 60;
						if($counter == 0 || (! is_null($row_ot_1))){
							if($row_ap[AttendanceTable::C_TIME_START] == $row_ot_1[OvertimeTable::C_TIME_START]){
								$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot_1[OvertimeTable::C_TIME_START]);
								$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot_1[OvertimeTable::C_TIME_END]);
								$minute -= ($time_end - $time_start) / 60;
								$minute_ot += ($time_end - $time_start) / 60;
							}
						}
						if(($counter == count($rows_ap) - 1) || (! is_null($row_ot_2))){
							if($row_ap[AttendanceTable::C_TIME_END] == $row_ot_2[OvertimeTable::C_TIME_END]){
								$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot_2[OvertimeTable::C_TIME_START]);
								$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot_2[OvertimeTable::C_TIME_END]);
								$minute -= ($time_end - $time_start) / 60;
								$minute_ot += ($time_end - $time_start) / 60;
							}
						}
						$counter += 1;
					}
				}
				
				if($cls_h->isHoliday($date_report_2)){
					$hn += $minute;
					$ho += $minute_ot;
				} else if (date('w', strtotime($date_report_2)) == 0 || date('w', strtotime($date_report_2)) == 6) {
					$wn += $minute;
					$wo += $minute_ot;
				} else {
					$nn += $minute;
					$no += $minute_ot;
				}
				
				$rows_ot = $cls_ot->getOvertimeRecords($emp_id, $date_report_2);
				
				$minute_ot = 0;
				if(is_null($rows_ot) || count($rows_ot) == 0){
				} else {
					foreach($rows_ot as $row_ot){
						$time_start = strtotime(substr($date_report_2, 0, 11) . $row_ot[OvertimeTable::C_TIME_START]);
						$time_end = strtotime(substr($date_report_2, 0, 11) . $row_ot[OvertimeTable::C_TIME_END]);
						
						$minute_ot += ($time_end - $time_start) / 60;
					}
				}
				
				if($cls_h->isHoliday($date_report_2)){
					$ho += $minute_ot;
				} else if (date('w', strtotime($date_report_2)) == 0 || date('w', strtotime($date_report_2)) == 6) {
					$wo += $minute_ot;
				} else {
					$no += $minute_ot;
				}
				
				$date_report->add($diff1Day);
			}
			
			$rate_group_id = $cls_emp_s->getRateGroupId($emp_id);
			
			$row_rg = $cls_rg->getRecord($rate_group_id);
			
			$list = "" ;
			
			$total = 0;
			
			$items = array() ;
			$items[$i][] = $this->createPdfItem("    Weekday",150,0,"L") ;
			$items[$i][] = $this->createPdfItem($nn,100,0,"R") ;
			$items[$i][] = $this->createPdfItem($row_rg[RateGroupTable::C_RATE_NORMAL],100,0,"R") ;
			$items[$i][] = $this->createPdfItem(($row_rg[RateGroupTable::C_RATE_NORMAL] * $nn),100,0,"R") ;
			$total += ($row_rg[RateGroupTable::C_RATE_NORMAL] * $nn);		
			$items[$nr] = "1" ;
			$datas[] = $items ;
			
			$items = array() ;
			$items[$i][] = $this->createPdfItem("    O/T Weekday",150,0,"L") ;
			$items[$i][] = $this->createPdfItem($no,100,0,"R") ;
			$items[$i][] = $this->createPdfItem($row_rg[RateGroupTable::C_RATE_NORMAL_OT],100,0,"R") ;
			$items[$i][] = $this->createPdfItem(($row_rg[RateGroupTable::C_RATE_NORMAL_OT] * $no),100,0,"R") ;
			$total += ($row_rg[RateGroupTable::C_RATE_NORMAL_OT] * $no);		
			$items[$nr] = "1" ;
			$datas[] = $items ;
			
			$items = array() ;
			$items[$i][] = $this->createPdfItem("    Weekend",150,0,"L") ;
			$items[$i][] = $this->createPdfItem($wn,100,0,"R") ;
			$items[$i][] = $this->createPdfItem($row_rg[RateGroupTable::C_RATE_WEEKEND],100,0,"R") ;
			$items[$i][] = $this->createPdfItem(($row_rg[RateGroupTable::C_RATE_WEEKEND] * $wn),100,0,"R") ;
			$total += ($row_rg[RateGroupTable::C_RATE_WEEKEND] * $wn);		
			$items[$nr] = "1" ;
			$datas[] = $items ;
			
			$items = array() ;
			$items[$i][] = $this->createPdfItem("    O/T Weekend",150,0,"L") ;
			$items[$i][] = $this->createPdfItem($wo,100,0,"R") ;
			$items[$i][] = $this->createPdfItem($row_rg[RateGroupTable::C_RATE_WEEKEND_OT],100,0,"R") ;
			$items[$i][] = $this->createPdfItem(($row_rg[RateGroupTable::C_RATE_WEEKEND_OT] * $wo),100,0,"R") ;
			$total += ($row_rg[RateGroupTable::C_RATE_WEEKEND_OT] * $wo);		
			$items[$nr] = "1" ;
			$datas[] = $items ;
			
			$items = array() ;
			$items[$i][] = $this->createPdfItem("    Holiday",150,0,"L") ;
			$items[$i][] = $this->createPdfItem($hn,100,0,"R") ;
			$items[$i][] = $this->createPdfItem($row_rg[RateGroupTable::C_RATE_HOLIDAY],100,0,"R") ;
			$items[$i][] = $this->createPdfItem(($row_rg[RateGroupTable::C_RATE_HOLIDAY] * $hn),100,0,"R") ;
			$total += ($row_rg[RateGroupTable::C_RATE_HOLIDAY] * $hn);		
			$items[$nr] = "1" ;
			$datas[] = $items ;
			
			$items = array() ;
			$items[$i][] = $this->createPdfItem("    O/T Holiday",150,0,"L") ;
			$items[$i][] = $this->createPdfItem($ho,100,0,"R") ;
			$items[$i][] = $this->createPdfItem($row_rg[RateGroupTable::C_RATE_HOLIDAY_OT],100,0,"R") ;
			$items[$i][] = $this->createPdfItem(($row_rg[RateGroupTable::C_RATE_HOLIDAY_OT] * $ho),100,0,"R") ;
			$total += ($row_rg[RateGroupTable::C_RATE_HOLIDAY_OT] * $ho);		
			$items[$nr] = "1" ;
			$datas[] = $items ;
		
		}
		
		$cols = array() ;
		$cols[] = $this->createPdfItem("Rate Type",150,0,"C","B");
		$cols[] = $this->createPdfItem("Work Time",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("Rate",100,0,"C","B");
		$cols[] = $this->createPdfItem("Cost",100,0,"C","B") ;
		$headers = array() ;
		$headers[] = "Company : %=COMPANY=%" ;
		$pdf = new ListPdf('L');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Employee Project Report - " . $this->getParamDate($params,'date',$dte) . " - " . $this->getParamDate($params,'dateend',$dte)) ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->setHeaders($headers) ;
		$pdf->setHeaderHeight(135) ;
		$pdf->render($datas) ;
		$pdf->Output('EmployeeProjectReport.pdf', 'I');
		unset($cols) ;
		unset($headers);
		unset($items) ;
	}
}
?>