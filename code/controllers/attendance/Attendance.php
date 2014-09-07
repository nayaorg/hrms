<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance\AttendanceClass.php") ;
require_once (PATH_MODELS . "attendance\AttendanceProjectClass.php") ;
require_once (PATH_MODELS . "attendance\OvertimeClass.php") ;
require_once (PATH_MODELS . "attendance\ShiftDetailClass.php") ;
require_once (PATH_MODELS . "attendance\TimeCardClass.php");
require_once (PATH_MODELS . "hr\EmployeeClass.php");
require_once (PATH_MODELS . "hr\DepartmentClass.php");
require_once (PATH_MODELS . "attendance\TimeCardLimitClass.php");
require_once (PATH_MODELS . "attendance\ProjectClass.php");

class Attendance extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = ShiftUpdateTable::C_ORG_ID ;
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
				case REQ_ADD:
					$this->addRecord($params) ;
					break ;
				case REQ_UPDATE:
					$this->updateRecord($params) ;
					break ;
				case REQ_DELETE:
					$this->deleteRecord($params) ;
					break ;
				case REQ_GET:
					$this->getRecord($params) ;
					break ;
				case REQ_GET . "_EMP":
					$this->getEmployeeList($params) ;
					break ;
				case REQ_LIST:
					$this->getList($params) ;
					break ;
				case "LIST":
					$this->getList($params) ;
					break ;
				case REQ_REPORT:
					$this->getReport($params) ;
					break ;
				case REQ_VIEW:
					$this->getView() ;
					break ;
				case "emp":
					$this->getEmp($params);
					break;
				case "shifthour":
					$this->getShiftHour($params);
					break;
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
	private function getAttendanceRecord($id, $date){
		$sql = "SELECT * FROM " . AttendanceTable::C_TABLE;
		$sql .= " WHERE " . $this->db->fieldParam(AttendanceTable::C_EMP_ID) ;
		$sql .= " AND " . $this->db->fieldParam(AttendanceTable::C_DATE) ;
		$params = array() ;
		$params[] = $this->db->valueParam(AttendanceTable::C_EMP_ID,$id) ;
		$params[] = $this->db->valueParam(AttendanceTable::C_DATE,$date) ;
		
		$rows = $this->db->getTable($sql,$params);
		
		return $rows;
	}
	private function addRecord($params) {
		$dte = date_create('now')->format('Y-m-d') ;
		$cls = new AttendanceClass($this->db);
		$cls_pro = new AttendanceProjectClass($this->db);
		
		if(! is_null($cls->getAttendanceRecord($this->getParam($params,'emp_id',""), $this->getParamDate($params,'date',$dte). " 00:00:00"))){
			if($cls->checkOverlap($this->getParam($params,'emp_id',""), $this->getParamDate($params,'date',$dte). " 00:00:00", 
						$this->getParamDate($params,'date',$dte) . " " . $this->getParam($params,'time_start',""),
						$this->getParamDate($params,'date',$dte) . " " . $this->getParam($params,'time_end',""))){
				$datas_pro = array() ;
				$orgid = $_SESSION[SE_ORGID] ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
						
				$new_seq_number = $cls_pro->getLastSeqNumber($this->getParam($params,'emp_id',""), $this->getParamDate($params,'date',$dte). " 00:00:00");
				
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_PROJECT_ID,$this->getParam($params,'project_id',"")) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_SEQ_NUMBER,$new_seq_number) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_EMP_ID,$this->getParam($params,'emp_id',"")) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_DATE,$this->getParamDate($params,'date',$dte). " 00:00:00", "") ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_WS_ID,$ws) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_MODIFY_BY,$modifyby) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_CREATE_BY,$modifyby) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_MODIFY_DATE,$modifydate) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_CREATE_DATE,$modifydate) ;
				$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_ORG_ID,$orgid) ;
				
				$pro_id = $cls_pro->addRecord($datas_pro);
					
				$cls->updateDetailAttendance($this->getParam($params,'emp_id',""), 
					$this->getParamDate($params,'date',$dte) . " 00:00:00", 
					$this->getParamDate($params,'date',$dte) . " " . $this->getParam($params,'shift_start',""), 
					$this->getParamDate($params,'date',$dte) . " " . $this->getParam($params,'shift_end',""), 
					intval($this->getParam($params,'tolerance',"")), 
					intval($this->getParam($params,'limit_before',"")), 
					intval($this->getParam($params,'limit_after',"")));
						
				$this->sendJsonResponse(Status::Ok,"attendance successfully added to the system.",$new_seq_number,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"The time is overlap with existing attendance record.","",$this->type) ;
			}
		} else {
			
			$cls_emp = new EmployeeClass($this->db);
			$row_emp = $cls_emp->getRecord($this->getParam($params,'emp_id',""));
			
			if((is_null($row_emp) || count($row_emp) == 0)){
				$this->sendJsonResponse(Status::Error,"Invalid employee id (" . $this->getParam($params,'emp_id',"") . ")","",$this->type) ;
			} else {
				$cls_ot = new OvertimeClass($this->db) ;
				$datas = array() ;
				$datas_pro = array() ;
				$orgid = $_SESSION[SE_ORGID] ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(AttendanceTable::C_EMP_ID,$this->getParam($params,'emp_id',"")) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_DATE,$this->getParamDate($params,'date',$dte). " 00:00:00", "") ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_BREAK_START,$this->getParam($params,'break_start',"")) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_BREAK_END,$this->getParam($params,'break_end',"")) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_LATE_IN,$this->getParam($params,'late_in',"")) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_EARLY_OUT,$this->getParam($params,'early_out',"")) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_CREATE_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_MODIFY_DATE,$modifydate) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_CREATE_DATE,$modifydate) ;
				$datas[] = $this->db->fieldValue(AttendanceTable::C_ORG_ID,$orgid) ;
				
				try {
					$id = $cls->addRecord($datas) ;
					
					$new_seq_number = $cls_pro->getLastSeqNumber($this->getParam($params,'emp_id',""), $this->getParamDate($params,'date',$dte). " 00:00:00");
					
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_PROJECT_ID,$this->getParam($params,'project_id',"")) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_SEQ_NUMBER,$new_seq_number) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_EMP_ID,$this->getParam($params,'emp_id',"")) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_DATE,$this->getParamDate($params,'date',$dte). " 00:00:00", "") ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_WS_ID,$ws) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_MODIFY_BY,$modifyby) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_CREATE_BY,$modifyby) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_MODIFY_DATE,$modifydate) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_CREATE_DATE,$modifydate) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_ORG_ID,$orgid) ;
					
					$pro_id = $cls_pro->addRecord($datas_pro);
				
					$cls->updateDetailAttendance($this->getParam($params,'emp_id',""), 
						$this->getParamDate($params,'date',$dte) . " 00:00:00", 
						$this->getParamDate($params,'date',$dte) . " " . $this->getParam($params,'shift_start',""), 
						$this->getParamDate($params,'date',$dte) . " " . $this->getParam($params,'shift_end',""), 
						intval($this->getParam($params,'tolerance',"")), 
						intval($this->getParam($params,'limit_before',"")), 
						intval($this->getParam($params,'limit_after',"")));
					
					$this->sendJsonResponse(Status::Ok,"attendance successfully added to the system.",$new_seq_number,$this->type);
				} catch (Exception $e) {
					Log::write('[Attendance]' . $e->getMessage());
					$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation. " . $e->getMessage(),"",$this->type) ;
				}
				unset($cls_ot) ;
			}
			unset($cls_emp) ;
		}
		unset($cls) ;
		unset($cls_pro) ;
	}
	private function updateRecord($params) {
		if (isset($params['emp_id']) && isset($params['date']) && isset($params['seq_number'])) {
			$id = $params['emp_id'] ;
			$seq_number = $params['seq_number'] ;
			$cls = new AttendanceClass($this->db) ;
			$cls_ot = new OvertimeClass($this->db) ;
			$cls_pro = new AttendanceProjectClass($this->db);
			$dte = date_create('now')->format('Y-m-d') ;
			if($cls->checkOverlap($this->getParam($params,'emp_id',""), $this->getParamDate($params,'date',$dte). " 00:00:00", 
						$this->getParamDate($params,'date',$dte) . " " . $this->getParam($params,'time_start',""),
						$this->getParamDate($params,'date',$dte) . " " . $this->getParam($params,'time_end',""),
						$seq_number)){
				try {
					$datas = array() ;
					$datas_ot = array() ;
					$datas_pro = array() ;
					$modifyby = $_SESSION[SE_USERID] ;
					$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
					$ws = $_SESSION[SE_REMOTE_IP] ;
					
					$datas[] = $this->db->fieldValue(AttendanceTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
					$datas[] = $this->db->fieldValue(AttendanceTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
					$datas[] = $this->db->fieldValue(AttendanceTable::C_BREAK_START,$this->getParam($params,'break_start',"")) ;
					$datas[] = $this->db->fieldValue(AttendanceTable::C_BREAK_END,$this->getParam($params,'break_end',"")) ;
					$datas[] = $this->db->fieldValue(AttendanceTable::C_LATE_IN,$this->getParam($params,'late_in',"")) ;
					$datas[] = $this->db->fieldValue(AttendanceTable::C_EARLY_OUT,$this->getParam($params,'early_out',"")) ;
					$datas[] = $this->db->fieldValue(AttendanceTable::C_WS_ID,$ws) ;
					$datas[] = $this->db->fieldValue(AttendanceTable::C_MODIFY_BY,$modifyby) ;
					$datas[] = $this->db->fieldValue(AttendanceTable::C_MODIFY_DATE,$modifydate) ;
					
					$cls->updateAttendanceRecord($id, $this->getParamDate($params,'date',$dte) . " 00:00:00", $datas);
					
					$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_WS_ID,$ws) ;
					$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_MODIFY_BY,$modifyby) ;
					$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_MODIFY_DATE,$modifydate) ;
					
					$cls_ot->updateOvertimeRecord($id, $this->getParamDate($params,'date',$dte) . " 00:00:00", $datas_ot, -1);
					$cls_ot->updateOvertimeRecord($id, $this->getParamDate($params,'date',$dte) . " 00:00:00", $datas_ot, -2);
					
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_PROJECT_ID,$this->getParam($params,'project_id',"")) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_WS_ID,$ws) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_MODIFY_BY,$modifyby) ;
					$datas_pro[] = $this->db->fieldValue(AttendanceProjectTable::C_MODIFY_DATE,$modifydate) ;
					
					$cls_pro->updateAttendanceProjectRecord($id, $this->getParamDate($params,'date',$dte) . " 00:00:00", $seq_number, $datas_pro);
					
					$day = $this->getParamDate($params,'date',$dte);
			
					$month = date('m', strtotime($day));
					$year = date('y', strtotime($day));
					
					$month = $month . $year;
					
					$hour = $cls->getHour($day, $month, $id) ;
					
					$sql_limit = "select * from " . TimeCardLimitTable::C_TABLE  ;
					$rows_limit = $this->db->getRow($sql_limit) ;
				
					$cls->updateDetailAttendance($id, 
						$day . " 00:00:00", 
						$day . " " . $hour[TimeCardTable::C_TIME_START], 
						$day . " " . $hour[TimeCardTable::C_TIME_END], 
						$hour[TimeCardTable::C_TOLERANCE], 
						$rows_limit[0][TimeCardLimitTable::C_BEFORE], 
						$rows_limit[0][TimeCardLimitTable::C_AFTER]);
					
					$this->sendJsonResponse(Status::Ok,"attendance detail successfully updated to the system.",$id,$this->type) ;
				} catch (Exception $e) {
					Log::write('[Attendance]' . $e->getMessage());
					$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating attendance detail to the system.","",$this->type) ;
				}
			} else {
				$this->sendJsonResponse(Status::Error,"The time is overlap with existing attendance record.","",$this->type) ;
			}
			unset($cls) ;
			unset($cls_ot) ;
			unset($cls_pro) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the attendance id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id']) && isset($params['date_attendance']) && isset($params['seq_number'])) {
			$dte = date_create('now')->format('Y-m-d') ;
			$id = $params['id'] ;
			$seq_number = $params['seq_number'] ;
			$date_attendance = substr($params['date_attendance'], 0, 4) . '-' . substr($params['date_attendance'], 4, 2) . '-' . substr($params['date_attendance'], 6, 2) . " 00:00:00" ;
			$cls = new AttendanceClass($this->db) ;
			$cls_ot = new OvertimeClass($this->db) ;
			$cls_pro = new AttendanceProjectClass($this->db);
			try {
				$cls_pro->deleteAttendanceProjectRecord($id, $date_attendance, $seq_number);
				
				if(is_null($cls_pro->getAttendanceProjectRecords($id, $date_attendance))){
					$cls->deleteAttendanceRecord($id, $date_attendance);
					$cls_ot->deleteOvertimeRecord($id, $date_attendance, -1);
					$cls_ot->deleteOvertimeRecord($id, $date_attendance, -2);
				} else {
					$day = substr($params['date_attendance'], 0, 4) . '-' . substr($params['date_attendance'], 4, 2) . '-' . substr($params['date_attendance'], 6, 2);
			
					$month = date('m', strtotime($day));
					$year = date('y', strtotime($day));
					
					$month = $month . $year;
					
					$hour = $cls->getHour($day, $month, $id) ;
					
					$sql_limit = "select * from " . TimeCardLimitTable::C_TABLE  ;
					$rows_limit = $this->db->getRow($sql_limit) ;
				
					$cls->updateDetailAttendance($id, 
						$day . " 00:00:00", 
						$day . " " . $hour[TimeCardTable::C_TIME_START], 
						$day . " " . $hour[TimeCardTable::C_TIME_END], 
						$hour[TimeCardTable::C_TOLERANCE], 
						$rows_limit[0][TimeCardLimitTable::C_TIMECARDLIMIT_BEFORE], 
						$rows_limit[0][TimeCardLimitTable::C_TIMECARDLIMIT_AFTER]);
				}
				
				$this->sendJsonResponse(Status::Ok,"attendance successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[Attendance]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting attendance record from the system.","",$this->type) ;
			}
			unset($cls) ;
			unset($cls_ot);
			unset($cls_pro);
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the attendance id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getEmp($params){
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeClass($this->db) ;
			$cls_es = new EmployeeShiftClass($this->db) ;
			$cls_tc = new TimeCardClass($this->db) ;
			$row = $cls->getRecord($id) ;
			$row_es = $cls_es->getRecord($id) ;
			
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid employee id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['name'] = $row[EmployeeTable::C_NAME];
				$datas['shiftType'] = $row_es[EmployeeShiftTable::C_SHIFT_TYPE];
				$datas['shiftGroup'] = $row_es[EmployeeShiftTable::C_SHIFT_GROUP_ID];
				
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
			unset($cls_es) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id. Please try again.","",$this->type);
		}
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getEmployeeList($params) {
		$id = $params['id'] ;
		
		$cls = new EmployeeClass($this->db) ;
		if($id == 0 || $id == ''){
			$rows = $cls->getTable() ;
		}else {
			$filter = $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$datas = array() ;
			$datas[] = $this->db->valueParam(EmployeeTable::C_DEPT,$id) ;
			$rows = $cls->getTable($filter,EmployeeTable::C_NAME,$datas) ;		
		}
		$lines = "" ;
		if (!is_null($rows) || count($rows) > 0) {
			foreach ($rows as $row) {
				
				if (strlen($lines) > 0)
					$lines .= "|" ;
				$lines .= $row[EmployeeTable::C_ID] . ":" . $row[EmployeeTable::C_NAME] ;
			}							
		}
		
		$datas = array() ;
		$datas['empList'] =  $lines ;
		
		$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
	}
	private function getShiftHour($params){
		if (isset($params['date'])) {
			
			$dte = date_create('now')->format('Y-m-d') ;
			
			$day = $this->getParamDate($params,'date',$dte);
			
			$month = date('m', strtotime($this->getParamDate($params,'date',$dte)));
			$year = date('Y', strtotime($this->getParamDate($params,'date',$dte)));
			
			$month = $month . $year;
			$empId = $params['empId'] ;
			
			$cls = new AttendanceClass($this->db) ;
			$hour = $cls->getHour($day, $month, $empId) ;
			
			
			$datas = array() ;
			
			$datas['start'] = $hour[TimeCardTable::C_TIME_START];
			$datas['end'] = $hour[TimeCardTable::C_TIME_END];
			$datas['break_start'] = $hour[TimeCardTable::C_BREAK_START];
			$datas['break_end'] = $hour[TimeCardTable::C_BREAK_END];
			$datas['tolerance'] = $hour[TimeCardTable::C_TOLERANCE];
			
	
			$cls_limit = new TimeCardLimitClass($this->db) ;
			
			$sql_limit = "select * from " . TimeCardLimitTable::C_TABLE  ;
			$rows_limit = $this->db->getRow($sql_limit) ;
			
			foreach($rows_limit as $row_limit){
				$datas['limit_before'] = $row_limit[TimeCardLimitTable::C_BEFORE];
				$datas['limit_after'] = $row_limit[TimeCardLimitTable::C_AFTER];
			}
			
			$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			
			unset($cls_limit);
			unset($cls) ;

		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id. Please try again.","",$this->type);
		}

	}
	
	private function getList($conditions=null) {
		$cls = new AttendanceClass($this->db) ;
		$cls_emp = new EmployeeClass($this->db);
		$cls_pro = new AttendanceProjectClass($this->db);
		$cls_project = new ProjectClass($this->db);
		
		$dte = date_create('now')->format('Y-m-d') ;
		$sql = "SELECT * FROM " . AttendanceTable::C_TABLE;
		$sql .= " WHERE " . AttendanceTable::C_DATE . " >= '" . $this->getParamDate($conditions,'date_start',$dte) . " 00:00:00'";
		$sql .= " AND " . AttendanceTable::C_DATE . " <= '" . $this->getParamDate($conditions,'date_end',$dte) . " 23:59:59'";
		$sql .= " AND " . AttendanceTable::C_ORG_ID . " = " . $_SESSION[SE_ORGID];
		
		$rows = $this->db->getTable($sql);
		
		$list = "" ;
		foreach ($rows as $row) {
			$dte = date_create($row[AttendanceTable::C_DATE]);
			
			$row_emp = $cls_emp->getRecord($row[AttendanceTable::C_EMP_ID]);
			
			$rows_pro = $cls_pro->getAttendanceProjectRecords($row[AttendanceTable::C_EMP_ID], $row[AttendanceTable::C_DATE]);
			
			if(is_null($rows_pro) || count($rows_pro) == 0){
			} else {
				foreach ($rows_pro as $row_pro){
					$timestart = new DateTime($row_pro[AttendanceProjectTable::C_TIME_START]);
					$timeend = new DateTime($row_pro[AttendanceProjectTable::C_TIME_END]);
					$breakstart = new DateTime($row[AttendanceTable::C_BREAK_START]);
					$breakend = new DateTime($row[AttendanceTable::C_BREAK_END]);
					
					$row_project = $cls_project->getRecord($row_pro[AttendanceProjectTable::C_PROJECT_ID]);
					
					$list .= "<tr>" ;
					$list .= "<td>" . $row[AttendanceTable::C_EMP_ID] . "</td>" ;
					$list .= '<td>' . $row_emp[EmployeeTable::C_NAME] . "</td>" ;
					$list .= "<td>" . date_format($dte, 'd/m/Y') . "</td>" ;
					$list .= "<td>" . $row_project[ProjectTable::C_REF] . "</td>" ;
					$list .= '<td style="display: none">' . $row_project[ProjectTable::C_ID] . "</td>" ;
					$list .= '<td style="display: none">' . $row_pro[AttendanceProjectTable::C_SEQ_NUMBER] . "</td>" ;
					$list .= "<td>" . $timestart->format('H:i') . "</td>" ;
					$list .= "<td>" . $timeend->format('H:i') . "</td>" ;
					$list .= "<td>" . $breakstart->format('H:i') . "</td>" ;
					$list .= "<td>" . $breakend->format('H:i') . "</td>" ;
					$list .= "<td style='text-align:center'><a href='javascript:' onclick='editAttendance(" . $row[AttendanceTable::C_EMP_ID] . ", " . date_format($dte, 'Ymd') . "," . $row_pro[AttendanceProjectTable::C_SEQ_NUMBER] . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
					$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteAttendance(" . $row[AttendanceTable::C_EMP_ID] . ", " . date_format($dte, 'Ymd') . "," . $row_pro[AttendanceProjectTable::C_SEQ_NUMBER] . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
					$list .= "</tr>" ;
				}
			}
		}
		
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type) ;
		
		unset($rows) ;
		unset($cls) ;
		unset($cls_emp) ;
		unset($cls_pro) ;
		unset($cls_project) ;
		
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id']) && isset($params['date_attendance']) && isset($params['seq_number'])) {
			$id = $params['id'] ;
			$seq_number = $params['seq_number'] ;
			$dte = date_create('now')->format('Y-m-d') ;
			$date = substr($params['date_attendance'], 0, 4) . '-' . substr($params['date_attendance'], 4, 2) . '-' . substr($params['date_attendance'], 6, 2) . " 00:00:00";
			$row = $this->getAttendanceRecord($id, $date) ;
			
			$cls_pro = new AttendanceProjectClass($this->db);
			
			$row_pro = $cls_pro->getAttendanceProjectRecord($id, $date, $seq_number);
			if (is_null($row) || count($row) == 0) {
				$this->sendJsonResponse(Status::Error,"Invalid attendance id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$dte = date_create($row[0][AttendanceTable::C_DATE]);
				$timestart = new DateTime($row_pro[AttendanceTable::C_TIME_START]);
				$timeend = new DateTime($row_pro[AttendanceTable::C_TIME_END]);
				$breakstart = new DateTime($row[0][AttendanceTable::C_BREAK_START]);
				$breakend = new DateTime($row[0][AttendanceTable::C_BREAK_END]);
				
				
				$datas['date'] = date_format($dte, 'd/m/Y');
				$datas['time_start'] = $timestart->format('H:i');
				$datas['time_end'] = $timeend->format('H:i');
				$datas['break_start'] = $breakstart->format('H:i');
				$datas['break_end'] = $breakend->format('H:i');
				$datas['emp_id'] = $row[0][AttendanceTable::C_EMP_ID];
				
				$datas['seq_number'] = $row_pro[AttendanceProjectTable::C_SEQ_NUMBER];
				$datas['project_id'] = $row_pro[AttendanceProjectTable::C_PROJECT_ID];
				
				$cls_limit = new TimeCardLimitClass($this->db) ;
				
				$sql_limit = "select * from " . TimeCardLimitTable::C_TABLE  ;
				$rows_limit = $this->db->getRow($sql_limit) ;
				
				foreach($rows_limit as $row_limit){
					$datas['limit_before'] = $row_limit[TimeCardLimitTable::C_BEFORE];
					$datas['limit_after'] = $row_limit[TimeCardLimitTable::C_AFTER];
				}
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls_pro);
		} else {
			$this->sendJsonResponse(Status::Error,"Missing employee id or attendance date. Please try again.","",$this->type);
		}
	}
	
	private function getProjectList() {
		$filter = array();
		$filter[] = array('field'=>ProjectTable::C_ORG_ID,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ProjectTable::C_TABLE, ProjectTable::C_ID, ProjectTable::C_REF,array('code'=>'','desc'=>'--- Select a Project ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/AttendanceView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$dte = date_create('now')->format('Y-m-d') ;
		$sql = "SELECT * FROM " . AttendanceTable::C_TABLE;
		$sql .= " WHERE " . AttendanceTable::C_DATE . " >= '" . $this->getParamDate($params,'date',$dte) . " 00:00:00'";
		$sql .= " AND " . AttendanceTable::C_DATE . " <= '" . $this->getParamDate($params,'dateend',$dte) . " 23:59:59'";
		$sql .= " AND " . AttendanceTable::C_ORG_ID . " = " . $_SESSION[SE_ORGID];
		
		$cls = new AttendanceClass($this->db) ;
		$clsEmp = new EmployeeClass($this->db);
		
		$rows = $this->db->getTable($sql);

		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[AttendanceTable::C_EMP_ID],40) ;
			
			$idEmp = $row[AttendanceTable::C_EMP_ID];
			$rowEmp = $clsEmp->getRecord($idEmp) ;
			
			if (is_null($rowEmp)) {
				$items[$i][] = $this->createPdfItem("",100) ;
			} else {
				$items[$i][] = $this->createPdfItem($rowEmp[EmployeeTable::C_NAME],100) ;
			}
			
			$dte = date_create($row[AttendanceTable::C_DATE]);
			
			$items[$i][] = $this->createPdfItem(date_format($dte, 'd/m/Y'),60, 0, "C") ;
			
			$timestart = new DateTime($row[AttendanceTable::C_TIME_START]);
			$timeend = new DateTime($row[AttendanceTable::C_TIME_END]);
			
			$items[$i][] = $this->createPdfItem($timestart->format('H:i'),40, 0, "C") ;
			$items[$i][] = $this->createPdfItem($timeend->format('H:i'),40, 0, "C") ;
			
			
			$timestart = new DateTime($row[AttendanceTable::C_BREAK_START]);
			$timeend = new DateTime($row[AttendanceTable::C_BREAK_END]);
			
			$items[$i][] = $this->createPdfItem($timestart->format('H:i'),40, 0, "C") ;
			$items[$i][] = $this->createPdfItem($timeend->format('H:i'),40, 0, "C") ;
			
			$items[$i][] = $this->createPdfItem($row[AttendanceTable::C_LATE_IN],40, 0, "C") ;
			$items[$i][] = $this->createPdfItem($row[AttendanceTable::C_EARLY_OUT],40, 0, "C") ;
			
			
			$items[$nr] = "1";
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("Emp. ID",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Employee Name",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("Date",60,0,"C","B") ;
		$cols[] = $this->createPdfItem("From",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("To",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Brk Fr",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Brk To",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Late In",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Early Out",40,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Attendance Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('Attendance.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>