<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "attendance/ProjectClass.php") ;
require_once (PATH_MODELS . "attendance/ActivityClass.php") ;
require_once (PATH_MODELS . "attendance/TimesheetClass.php") ;

class Timesheet extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
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
				case REQ_CHANGE:
					$this->changePassword($params) ;
					break ;
				case REQ_NEW:
					$this->resetPassword($params) ;
					break ;
				case REQ_LIST:
					$this->getList($params) ;
					break ;
				case REQ_REPORT:
					$this->getReport($params) ;
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
	private function addRecord($params) {
		$cls = new TimesheetClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$start = date_create('now')->format('Y-m-d') ;
		$ws = $_SESSION[SE_REMOTE_IP];
		//$name = $this->getParam($params,'name',"") ;
		//if ($cls->isNameFound($orgid,$name)) {
		//	$this->sendJsonResponse(Status::Error,"User name already exist. Please choose other name.",$name,$this->type) ;
		//	return ;
		//}
		$datas[] = $this->db->fieldValue(TimesheetTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_REF,$this->getParam($params,'refno',"")) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_EMP_ID,$this->getParamInt($params,'empid',0)) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_PROJECT,$this->getParamInt($params,'project',0)) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_ACTIVITY,$this->getParamInt($params,'activity',0));
		$datas[] = $this->db->fieldValue(TimesheetTable::C_BILLABLE,$this->getParamInt($params,'billable',0));
		$datas[] = $this->db->fieldValue(TimesheetTable::C_START_DATE,$this->getParamDate($params,'start',$start) . " 00:00:00") ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_EXPIRY_DATE,$this->getParamDate($params,'expiry',MAX_DATE)) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(TimesheetTable::C_ORG_ID,$orgid) ;
	
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Timesheet successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new timesheet to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[Users]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TimesheetClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP];
				$orgid = $_SESSION[SE_ORGID] ;
				$start = date_create('now')->format('Y-m-d') ;
				
				$datas[] = $this->db->fieldValue(TimesheetTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(TimesheetTable::C_REF,$this->getParam($params,'refno',"")) ;
				$datas[] = $this->db->fieldValue(TimesheetTable::C_EMP_ID,$this->getParamInt($params,'empid',0)) ;
				$datas[] = $this->db->fieldValue(TimesheetTable::C_PROJECT,$this->getParamInt($params,'project',0)) ;
				$datas[] = $this->db->fieldValue(TimesheetTable::C_ACTIVITY,$this->getParamInt($params,'activity',0));
				$datas[] = $this->db->fieldValue(TimesheetTable::C_BILLABLE,$this->getParamInt($params,'billable',0));
				$datas[] = $this->db->fieldValue(TimesheetTable::C_START_DATE,$this->getParamDate($params,'start',$start) . " 00:00:00") ;
				$datas[] = $this->db->fieldValue(TimesheetTable::C_EXPIRY_DATE,$this->getParamDate($params,'expiry',MAX_DATE)) ;
				$datas[] = $this->db->fieldValue(TimesheetTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(TimesheetTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(TimesheetTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Timesheet detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Users]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating timesheet detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the timesheet id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TimesheetClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Timesheet successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[Users]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting timesheet record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the timesheet id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new TimesheetClass($this->db) ;
		$proj = new ProjectClass($this->db) ;
		$act = new ActivityClass($this->db) ;
		$filter = $this->db->fieldParam(TimesheetTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TimesheetTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,TimesheetTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[TimesheetTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[TimesheetTable::C_DESC] . "</td>" ;
			$list .= "<td>" . $proj->getDescription($row[ProjectTable::C_ID]) . "</td>" ;
			$list .= "<td>" . $act->getDescription($row[ActivityTable::C_ID]) . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editTS(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteTS(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TimesheetClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid timesheet id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[TimesheetTable::C_DESC];
				$datas['refno'] = $row[TimesheetTable::C_REF] ;
				$datas['empid'] = $row[TimesheetTable::C_EMP_ID] ;
				$datas['project'] = $row[TimesheetTable::C_PROJECT] ;
				$datas['activity'] = $row[TimesheetTable::C_ACTIVITY] ;
				$datas['billable'] = $row[TimesheetTable::C_BILLABLE] ;
				$dte = date_create($row[TimesheetTable::C_START_DATE]);
				$datas['start'] = date_format($dte, 'd/m/Y'); 
				$dte = date_create($row[TimesheetTable::C_EXPIRY_DATE]) ;
				if ($dte == date_create(MAX_DATE))
					$datas['expiry'] = "" ;
				else
					$datas['expiry'] = date_format($dte,'d/m/Y') ;				
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing timesheet id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/TimesheetView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getEmpList() {
		$cls = new EmployeeClass($this->db) ;
		$vls = array() ;
		$vls[] = array ('code'=>'','desc'=>'--- Select an Employee ---' ) ;
		$rows = $cls->getValueList($_SESSION[SE_ORGID]) ;
		if (!is_null($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$vls[] = array ('code'=>$row['code'],'desc'=>$row['desc']) ;
			}
		}
		return Util::createOptionValue($vls) ;
	}
	private function getActivityList() {
		$cls = new ActivityClass($this->db) ;
		$vls = array() ;
		$vls[] = array ('code'=>'','desc'=>'--- Select a Activity ---' ) ;
		$rows = $cls->getValueList($_SESSION[SE_ORGID]) ;
		if (!is_null($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$vls[] = array ('code'=>$row['code'],'desc'=>$row['desc']) ;
			}
		}
		return Util::createOptionValue($vls) ;
	}	
	private function getProjectList() {
		$cls = new ProjectClass($this->db) ;
		$vls = array() ;
		$vls[] = array ('code'=>'','desc'=>'--- Select a Project ---' ) ;
		$rows = $cls->getValueList($_SESSION[SE_ORGID]) ;
		if (!is_null($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$vls[] = array ('code'=>$row['code'],'desc'=>$row['desc']) ;
			}
		}
		return Util::createOptionValue($vls) ;
	}
	
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new TimesheetClass($this->db) ;
		$proj = new ProjectClass($this->db) ;
		$act = new ActivityClass($this->db) ;
		$filter = $this->db->fieldParam(TimesheetTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TimesheetTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,TimesheetTable::C_ID,$params) ;
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[TimesheetTable::C_ID],50) ;
			$items[$i][] = $this->createPdfItem($row[TimesheetTable::C_DESC],200) ;
			$items[$i][] = $this->createPdfItem($row[TimesheetTable::C_REF],100) ;
			$items[$i][] = $this->createPdfItem($proj->getDescription($row[ProjectTable::C_ID]),100) ;
			$items[$i][] = $this->createPdfItem($act->getDescription($row[ActivityTable::C_ID]),100) ;
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("Timesheet ID",50,0,"C","B");
		$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;
		$cols[] = $this->createPdfItem("Ref",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("Project",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("Activity",100,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Timesheet Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('timesheet.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>