<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/OverTimeClass.php") ;
require_once (PATH_MODELS . "attendance/AttendanceClass.php") ;
require_once (PATH_MODELS . "attendance/TimeCardClass.php");
require_once (PATH_MODELS . "attendance/TimeCardLimitClass.php");
require_once (PATH_MODELS . "attendance/ProjectClass.php");
require_once (PATH_MODELS . "hr/EmployeeClass.php");
require_once (PATH_MODELS . "hr/DepartmentClass.php");

class OverTime extends ControllerBase {
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
		$cls = new OverTimeClass($this->db) ;
		$datas = array() ;
		$dte = date_create('now')->format('Y-m-d') ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		$datas[] = $this->db->fieldValue(OverTimeTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_EMP_ID,$this->getParam($params,'emp_id',"")) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_DATE,$this->getParamDate($params,'date_over',$dte). " 00:00:00", "") ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_HOUR,$this->getParam($params,'ot_hour',"")) ;
		
		$overtimeId = $cls->getMaxOvertimeId($this->getParam($params,'emp_id',""), $this->getParamDate($params,'date_over',$dte). " 00:00:00");
		$datas[] = $this->db->fieldValue(OverTimeTable::C_OVERTIME_ID,$overtimeId) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_PROJECT_ID,$this->getParam($params,'project_id',"")) ;
		
		$datas[] = $this->db->fieldValue(OverTimeTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(OverTimeTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			$this->sendJsonResponse(Status::Ok,"Over time successfully added to the system.",$overtimeId,$this->type);
		} catch (Exception $e) {
			Log::write('[OverTime]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['emp_id']) && isset($params['date_over']) && isset($params['overtime_id'])) {
			$id = $params['emp_id'] ;
			$cls_ot = new OverTimeClass($this->db) ;
			
			try {
				$datas_ot = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				$dte = date_create('now')->format('Y-m-d') ;
				
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_HOUR,$this->getParam($params,'ot_hour',"")) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas_ot[] = $this->db->fieldValue(OverTimeTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
				$datas_ot[] = $this->db->fieldValue(OverTimeTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
				$datas_ot[] = $this->db->fieldValue(OverTimeTable::C_PROJECT_ID,$this->getParam($params,'project_id',"")) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_WS_ID,$ws) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_MODIFY_BY,$modifyby) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_MODIFY_DATE,$modifydate) ;
				
				$cls_ot->updateOvertimeRecord($id, $this->getParamDate($params,'date_over',$dte) . " 00:00:00", $datas_ot, $params['overtime_id']);
				
				$this->sendJsonResponse(Status::Ok,"Over time detail successfully updated to the system.",$params['overtime_id'],$this->type) ;
			} catch (Exception $e) {
				Log::write('[OverTime]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating over time detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the over time id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id']) && isset($params['date'])&& isset($params['overtimeId'])) {
			$id = $params['id'] ;
			$date_attendance = substr($params['date'], 0, 4) . '-' . substr($params['date'], 4, 2) . '-' . substr($params['date'], 6, 2) . " 00:00:00" ;
			$cls = new OverTimeClass($this->db) ;
			try {
				$cls->deleteOvertimeRecord($id, $date_attendance, $params['overtimeId']);
				$this->sendJsonResponse(Status::Ok,"Over time successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[OverTime]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting over time record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the over time id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getEmp($params){
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeClass($this->db) ;
			$row = $cls->getRecord($id) ;
			
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid employee id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['name'] = $row[EmployeeTable::C_NAME];
				
				$this->sendJsonResponse(Status::Ok,"",$row[EmployeeTable::C_NAME],$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new AttendanceClass($this->db) ;
		$cls_emp = new EmployeeClass($this->db);
		$cls_pro = new ProjectClass($this->db);
		$dte = date_create('now')->format('Y-m-d') ;
		
		$sql = "SELECT * ";
		$sql .= " FROM " . OvertimeTable::C_TABLE . " O ";
		$sql .= " WHERE O." . OvertimeTable::C_DATE . " >= '" . $this->getParamDate($conditions,'date_start',$dte) . " 00:00:00'";
		$sql .= " AND O." . OvertimeTable::C_DATE . " <= '" . $this->getParamDate($conditions,'date_end',$dte) . " 23:59:59'";
		$sql .= " AND O." . OvertimeTable::C_ORG_ID . " = " . $_SESSION[SE_ORGID];
		
		$rows = $this->db->getTable($sql);
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[OvertimeTable::C_EMP_ID] ;
			$dte = date_create($row[OvertimeTable::C_DATE]);
			//Log::write(date('w', strtotime($dte->format('Y-m-d H:i:s'))));
			$timestart = new DateTime($row[OvertimeTable::C_TIME_START]);
			$timeend = new DateTime($row[OvertimeTable::C_TIME_END]);
			
			$row_emp = $cls_emp->getRecord($row[OvertimeTable::C_EMP_ID]);
			$row_pro = $cls_pro->getRecord($row[OvertimeTable::C_PROJECT_ID]);
			
			$list .= "<tr>" ;
			$list .= "<td>" . $row[OvertimeTable::C_EMP_ID] . "</td>" ;
			$list .= "<td>" . $row_emp[EmployeeTable::C_NAME] . "</td>" ;
			$list .= "<td>" . date_format($dte, 'd/m/Y') . "</td>" ;
			$list .= '<td style="display:none">' . $row[OvertimeTable::C_PROJECT_ID] . "</td>" ;
			$list .= "<td>" . $row_pro[ProjectTable::C_REF] . "</td>" ;
			$list .= '<td style="display: none">' . $row[OvertimeTable::C_OVERTIME_ID] . "</td>" ;
			$list .= "<td>" . $timestart->format('H:i') . "</td>" ;
			$list .= "<td>" . $timeend->format('H:i') . "</td>" ;
			$list .= "<td>" . $row[OvertimeTable::C_HOUR] . "</td>" ;
			$list .= "<td>" . $row[OvertimeTable::C_DESC] . "</td>" ;
			if($row[OvertimeTable::C_OVERTIME_ID] < 0){	
				$list .= "<td></td><td></td>" ;
			} else {
				$list .= "<td style='text-align:center'><a href='javascript:' onclick='editOverTime(" . $row[OvertimeTable::C_EMP_ID] . ", " . date_format($dte, 'Ymd') . ", " . $row[OvertimeTable::C_OVERTIME_ID] . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
				$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteOverTime(" . $row[OvertimeTable::C_EMP_ID] . ", " . date_format($dte, 'Ymd') . ", " . $row[OvertimeTable::C_OVERTIME_ID] . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			}
			$list .= "</tr>" ;
		}
		
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type) ;
		
		unset($rows) ;
		unset($cls_emp) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id']) && isset($params['date']) && isset($params['overtimeId'])) {
			$id = $params['id'] ;
			$dte = date_create('now')->format('Y-m-d') ;
			$date = substr($params['date'], 0, 4) . '-' . substr($params['date'], 4, 2) . '-' . substr($params['date'], 6, 2) . " 00:00:00";
			
			
			$cls = new AttendanceClass($this->db) ;
			$cls_ot = new OvertimeClass($this->db) ;
			$cls_pro = new ProjectClass($this->db);
			
			$row = $cls_ot->getOvertimeRecord($id, $date, $params['overtimeId']) ;
			$row_at = $cls->getAttendanceRecord($id, $date) ;
			
			if (is_null($row) || count($row) == 0) {
				$this->sendJsonResponse(Status::Error,"Invalid over time id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$dte = date_create($row[OverTimeTable::C_DATE]);
				$timestart = new DateTime($row[OverTimeTable::C_TIME_START]);
				$timeend = new DateTime($row[OverTimeTable::C_TIME_END]);
				
				$datas['desc'] = $row[OverTimeTable::C_DESC];
				
				$datas['date_over'] = date_format($dte, 'd/m/Y');
				$datas['time_start'] = $timestart->format('H:i');
				$datas['time_end'] = $timeend->format('H:i');
				$datas['emp_id'] = $row[OverTimeTable::C_EMP_ID];
				$datas['overtime_id'] = $row[OverTimeTable::C_OVERTIME_ID];
				$datas['ot_hour'] = $row[OverTimeTable::C_HOUR];
				
				$datas['project_id'] = $row[OverTimeTable::C_PROJECT_ID];
				
				$row_pro = $cls_pro->getRecord($row[OverTimeTable::C_PROJECT_ID]);
				
				$datas['project_name'] = $row_pro[ProjectTable::C_REF];
				
				$day = $dte->format('Y-m-d H:i:s');
				
				$month = $dte->format('m');
				$year = $dte->format('y');
				
				$month = $month . $year;
				$empId = $row[OverTimeTable::C_EMP_ID];
				
				$cls = new AttendanceClass($this->db) ;
				$hour = $cls->getHour($day, $month, $empId) ;
				
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
			}
			unset($cls) ;
			unset($cls_ot) ;
			unset($cls_pro) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing over time id. Please try again.","",$this->type);
		}
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
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/OverTimeView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new OverTimeClass($this->db) ;
		$clsEmp = new EmployeeClass($this->db);
		
		$dte = date_create('now')->format('Y-m-d') ;
		$sql = "SELECT O.*, A." . AttendanceTable::C_TIME_START . ", A." . AttendanceTable::C_TIME_END . " FROM " . OverTimeTable::C_TABLE . " O ";
		$sql .= " LEFT OUTER JOIN " . AttendanceTable::C_TABLE . " A on ( O." . OvertimeTable::C_EMP_ID . " = A." . AttendanceTable::C_EMP_ID . " and O." . OvertimeTable::C_DATE . " = A." . AttendanceTable::C_DATE . " )";
		$sql .= " WHERE O." . OverTimeTable::C_DATE . " >= '" . $this->getParamDate($params,'date',$dte) . " 00:00:00'";
		$sql .= " AND O." . OverTimeTable::C_DATE . " <= '" . $this->getParamDate($params,'dateend',$dte) . " 23:59:59'";
		$sql .= " AND O." . OverTimeTable::C_ORG_ID . " = " . $_SESSION[SE_ORGID];
		
		$rows = $this->db->getTable($sql);

		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[OverTimeTable::C_EMP_ID],40) ;
			
			$idEmp = $row[OverTimeTable::C_EMP_ID];
			$rowEmp = $clsEmp->getRecord($idEmp) ;
			
			if (is_null($rowEmp)) {
				$items[$i][] = $this->createPdfItem("",100) ;
			} else {
				$items[$i][] = $this->createPdfItem($rowEmp[EmployeeTable::C_NAME],100) ;
			}
			
			$dte = date_create($row[OverTimeTable::C_DATE]);
			
			$items[$i][] = $this->createPdfItem(date_format($dte, 'd/m/Y'),60, 0, "C") ;
			
			$timestart = new DateTime($row[AttendanceTable::C_TIME_START]);
			$timeend = new DateTime($row[AttendanceTable::C_TIME_END]);
			
			$items[$i][] = $this->createPdfItem($timestart->format('H:i'),40, 0, "C") ;
			$items[$i][] = $this->createPdfItem($timeend->format('H:i'),40, 0, "C") ;
			
			$items[$i][] = $this->createPdfItem($row[OverTimeTable::C_HOUR],40, 0, "C") ;
			
			$items[$i][] = $this->createPdfItem($row[OverTimeTable::C_DESC],150) ;
			$items[$nr] = "1";
			$datas[] = $items ;
		}
		$cols = array() ;
		//$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Emp. ID",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Employee Name",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("Date",60,0,"C","B") ;
		$cols[] = $this->createPdfItem("From",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("To",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("O/T",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Description",150,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("OverTime Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('OverTime.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>