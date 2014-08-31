<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/TimeOffClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php");
require_once (PATH_MODELS . "hr\DepartmentClass.php");

class TimeOff extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = TimeOffTable::C_ORG_ID ;
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
	private function getTimeOffRecord($id, $date){
		$sql = "SELECT * FROM " . TimeOffTable::C_TABLE;
		$sql .= " WHERE " . $this->db->fieldParam(TimeOffTable::C_EMP_ID) ;
		$sql .= " AND " . $this->db->fieldParam(TimeOffTable::C_DATE_OFF) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TimeOffTable::C_EMP_ID,$id) ;
		$params[] = $this->db->valueParam(TimeOffTable::C_DATE_OFF,$date) ;
		
		$rows = $this->db->getTable($sql,$params);
		
		return $rows;
	}
	private function addRecord($params) {
	
		$dte = date_create('now')->format('Y-m-d') ;
		$rows = $this->getTimeOffRecord($this->getParam($params,'emp_id',""), $this->getParamDate($params,'date_off',$dte). " 00:00:00");
		if(is_null($rows) || count($rows) == 0){
			
			$cls_emp = new EmployeeClass($this->db);
			
			$row_emp = $cls_emp->getRecord($this->getParam($params,'emp_id',""));
			
			if((is_null($row_emp) || count($row_emp) == 0)){
				$this->sendJsonResponse(Status::Error,"Invalid employee id (" . $this->getParam($params,'emp_id',"") . ")","",$this->type) ;
			} else {
				$cls = new TimeOffClass($this->db) ;
				$datas = array() ;
				$orgid = $_SESSION[SE_ORGID] ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(TimeOffTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_EMP_ID,$this->getParam($params,'emp_id',"")) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_DATE_OFF,$this->getParamDate($params,'date_off',$dte). " 00:00:00", "") ;
				
				$datas[] = $this->db->fieldValue(TimeOffTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_CREATE_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_MODIFY_DATE,$modifydate) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_CREATE_DATE,$modifydate) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_ORG_ID,$orgid) ;
				
				try {
					$id = $cls->addRecord($datas) ;
					
					$this->sendJsonResponse(Status::Ok,"Time off successfully added to the system.",$id,$this->type);
				} catch (Exception $e) {
					Log::write('[TimeOff]' . $e->getMessage());
					$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
				}
				unset($cls) ;
			}
			unset($cls_emp) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Duplicate employee id on the same day. (" . $this->getParam($params,'emp_id',"") . ", " . $this->getParamDate($params,'date',$dte) . ")","",$this->type) ;
		}
	}
	private function updateRecord($params) {
		if (isset($params['emp_id']) && isset($params['date_off'])) {
			$id = $params['emp_id'] ;
			$cls = new TimeOffClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				$dte = date_create('now')->format('Y-m-d') ;
				
				$datas[] = $this->db->fieldValue(TimeOffTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(TimeOffTable::C_MODIFY_DATE,$modifydate) ;
				
				$cls->updateTimeOffRecord($id, $this->getParamDate($params,'date',$dte) . " 00:00:00", $datas);
				
				
				$this->sendJsonResponse(Status::Ok,"Time off detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[TimeOff]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating time off detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the time off id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id']) && isset($params['date_off'])) {
			$dte = date_create('now')->format('Y-m-d') ;
			$id = $params['id'] ;
			$date_off = substr($params['date_off'], 0, 4) . '-' . substr($params['date_off'], 4, 2) . '-' . substr($params['date_off'], 6, 2) . " 00:00:00" ;
			$cls = new TimeOffClass($this->db) ;
			try {
				
				$cls->deleteTimeOffRecord($id, $date_off);
				
				//$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Time off successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[TimeOff]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting time off record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the time off id you wish to delete. Please try again.","",$this->type);
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
		$cls = new TimeOffClass($this->db) ;
		$cls_emp = new EmployeeClass($this->db);
		
		$dte = date_create('now')->format('Y-m-d') ;
		$sql = "SELECT * FROM " . TimeOffTable::C_TABLE;
		$sql .= " WHERE " . TimeOffTable::C_DATE_OFF . " >= '" . $this->getParamDate($conditions,'date_start',$dte) . " 00:00:00'";
		$sql .= " AND " . TimeOffTable::C_DATE_OFF . " <= '" . $this->getParamDate($conditions,'date_end',$dte) . " 23:59:59'";
		$sql .= " AND " . TimeOffTable::C_ORG_ID . " = " . $_SESSION[SE_ORGID];
		
		$rows = $this->db->getTable($sql);
		/*$filter = $this->db->fieldParam(TimeOffTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TimeOffTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,TimeOffTable::C_DESC,$params) ;*/
		$list = "" ;
		foreach ($rows as $row) {
			$emp_id = $row[TimeOffTable::C_EMP_ID] ;
			$dte = date_create($row[TimeOffTable::C_DATE_OFF]);
			$timestart = new DateTime($row[TimeOffTable::C_TIME_START]);
			$timeend = new DateTime($row[TimeOffTable::C_TIME_END]);
			
			$row_emp = $cls_emp->getRecord($row[TimeOffTable::C_EMP_ID]);
			
			$list .= "<tr>" ;
			$list .= "<td>" . $row[TimeOffTable::C_EMP_ID] . "</td>" ;
			$list .= "<td>" . $row_emp[EmployeeTable::C_NAME] . "</td>" ;
			$list .= "<td>" . date_format($dte, 'd/m/Y') . "</td>" ;
			$list .= "<td>" . $timestart->format('H:i') . "</td>" ;
			$list .= "<td>" . $timeend->format('H:i') . "</td>" ;
			$list .= "<td>" . $row[TimeOffTable::C_DESC] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editTimeOff(" . $emp_id . ", " . date_format($dte, 'Ymd') . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteTimeOff(" . $emp_id . ", " . date_format($dte, 'Ymd') . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type) ;
		
		unset($rows) ;
		unset($cls_emp);
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id']) && isset($params['date_off'])) {
			$dte = date_create('now')->format('Y-m-d') ;
			$id = $params['id'] ;
			$date_off = substr($params['date_off'], 0, 4) . '-' . substr($params['date_off'], 4, 2) . '-' . substr($params['date_off'], 6, 2) . " 00:00:00" ;
			
			$cls = new TimeOffClass($this->db) ;
			$row = $this->getTimeOffRecord($id, $date_off) ;
			if (is_null($row) && count($row) == 0) {
				$this->sendJsonResponse(Status::Error,"Invalid time off id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$dte = date_create($row[0][TimeOffTable::C_DATE_OFF]);
				$timestart = new DateTime($row[0][TimeOffTable::C_TIME_START]);
				$timeend = new DateTime($row[0][TimeOffTable::C_TIME_END]);
				
				$datas['desc'] = $row[0][TimeOffTable::C_DESC];
				$datas['date_off'] = date_format($dte, 'd/m/Y');
				$datas['time_start'] = $timestart->format('H:i');
				$datas['time_end'] = $timeend->format('H:i');
				$datas['emp_id'] = $id;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing time off id. Please try again.","",$this->type);
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
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/TimeOffView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$dte = date_create('now')->format('Y-m-d') ;
		$sql = "SELECT * FROM " . TimeOffTable::C_TABLE;
		$sql .= " WHERE " . TimeOffTable::C_DATE_OFF . " >= '" . $this->getParamDate($params,'date',$dte) . " 00:00:00'";
		$sql .= " AND " . TimeOffTable::C_DATE_OFF . " <= '" . $this->getParamDate($params,'dateend',$dte) . " 23:59:59'";
		$sql .= " AND " . TimeOffTable::C_ORG_ID . " = " . $_SESSION[SE_ORGID];
		
		$cls = new TimeOffClass($this->db) ;
		$clsEmp = new EmployeeClass($this->db);
		
		$rows = $this->db->getTable($sql);

		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			//$items[$i][] = $this->createPdfItem($row[TimeOffTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[TimeOffTable::C_EMP_ID],40) ;
			
			$idEmp = $row[TimeOffTable::C_EMP_ID];
			$rowEmp = $clsEmp->getRecord($idEmp) ;
			
			if (is_null($rowEmp)) {
				$items[$i][] = $this->createPdfItem("",100) ;
			} else {
				$items[$i][] = $this->createPdfItem($rowEmp[EmployeeTable::C_NAME],100) ;
			}
			
			$dte = date_create($row[TimeOffTable::C_DATE_OFF]);
			
			$items[$i][] = $this->createPdfItem(date_format($dte, 'd/m/Y'),60, 0, "C") ;
			
			$timestart = new DateTime($row[TimeOffTable::C_TIME_START]);
			$timeend = new DateTime($row[TimeOffTable::C_TIME_END]);
			
			$items[$i][] = $this->createPdfItem($timestart->format('H:i'),40, 0, "C") ;
			$items[$i][] = $this->createPdfItem($timeend->format('H:i'),40, 0, "C") ;
			
			$items[$i][] = $this->createPdfItem($row[TimeOffTable::C_DESC],150) ;
			
			
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
		$cols[] = $this->createPdfItem("Description",150,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("TimeOff Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('timeoff.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>