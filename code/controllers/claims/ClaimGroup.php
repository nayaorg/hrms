<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimGroupClass.php") ;
require_once (PATH_MODELS . "claims/TravelPlanClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "claims/ClaimGroupHeadClass.php") ;
require_once (PATH_MODELS . "claims/ClaimGroupEmpClass.php") ;

class ClaimGroup extends ControllerBase {
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = ClaimGroupTable::C_ORG_ID ;
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
				case REQ_GET . '_HEAD':
					$this->getEmployee($params) ;
					break ;
				case REQ_GET . '_EMP':
					$this->getEmployee($params) ;
					break ;
				case REQ_LIST:
					$this->getList($params) ;
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
		$cls = new ClaimGroupClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;

		$datas[] = $this->db->fieldValue(ClaimGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(ClaimGroupTable::C_COY_ID,0) ;
		$datas[] = $this->db->fieldValue(ClaimGroupTable::C_ORG_ID,$orgid) ;
		$datas[] = $this->db->fieldValue(ClaimGroupTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(ClaimGroupTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ClaimGroupTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ClaimGroupTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ClaimGroupTable::C_CREATE_DATE,$modifydate) ;
		
		$headLimit = $this->getParam($params,'headLimit',""); 
		$empLimit = $this->getParam($params,'empLimit',""); 
		try {
			$this->db->beginTran() ;
			$id = $cls->addRecord($datas) ;
			if($id > 0)	{
				$this->addHeadLimits($id,$headLimit) ;
				$this->addEmpLimits($id,$empLimit);
				
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Claim Group Head successfully added to the system.",$id,$this->type);
			} else {
				$this->db->rollbackTran() ;
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Claim Group Head to the system.",$id, $this->type) ;
			}
			//$this->sendJsonResponse(Status::Ok,"Claim Group successfully added to the system.",$id,$this->type);
		} catch (Exception $e) {
			$this->db->rollbackTran() ;
			Log::write('[Claim Head Group] ' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$headLimit = $this->getParam($params,'headLimit',""); 
			$empLimit = $this->getParam($params,'empLimit',""); 
			
			$cls = new ClaimGroupClass($this->db) ;			
			try {
				$this->db->beginTran() ;
				
				$this->deleteHeadLimits($id) ;
				$this->addHeadLimits($id,$headLimit) ;
				
				$this->deleteEmpLimits($id) ;
				$this->addEmpLimits($id,$empLimit) ;
				
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(ClaimGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(ClaimGroupTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(ClaimGroupTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(ClaimGroupTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Claim Group successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				$this->db->rollbackTran() ;	
				Log::write('[Claim Group] ' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Claim Group to the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim Group id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimGroupClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				
				$this->deleteHeadLimits($id) ;
				$this->deleteEmpLimits($id) ;
				
				$this->sendJsonResponse(Status::Ok,"Claim Group successfully deleted from the system.",$id,$this->type);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Group from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim Group id if you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($params) {
		$cls = new ClaimGroupClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimGroupTable::C_ORG_ID) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ClaimGroupTable::C_ID,$datas) ;
		$list = array() ;
		foreach ($rows as $row) {
			$data = array() ;
			$data['id'] = $row[ClaimGroupTable::C_ID] ;
			$data['desc'] = $row[ClaimGroupTable::C_DESC] ;
			$list[] = $data;
			unset($data);
		}
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type);
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimGroupClass($this->db) ;
			
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Claim Group id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $row[ClaimGroupTable::C_ID] ;
				$datas['desc'] = $row[ClaimGroupTable::C_DESC] ;
				$datas['head'] = $this->getHeadLimit($id);
				$datas['emp'] = $this->getEmpLimit($id);
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Claim Group id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "claims/ClaimGroupView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getTravelPlan() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ExpenseGroupTable::C_TABLE, ExpenseGroupTable::C_ID, ExpenseGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Expense Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	public function getMemberListByHead($head_id) {
		$ctrl = new ClaimGroupHead();
		$claim_group_id_list = $ctrl->getAllClaimGroupIdOfHead($head_id);
		unset($ctrl);
		
		$ctrl = new ClaimGroupEmployee();
		$member_list = $ctrl->getAllMembersOfClaimGroups($claim_group_id_list);
		unset($ctrl) ;
		
		return $member_list ;
	}
	public function getMenuItems() {
		$cls = new ClaimGroupClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ClaimGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ClaimGroupTable::C_CLAIM_GROUP_ID,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[ClaimGroupTable::C_CLAIM_GROUP_ID];
			$desc = $row[ClaimGroupTable::C_CLAIM_GROUP_DESC];
			$list .= "<option value=\"" . $id . "\">" . $desc . "</option>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	public function getDesc($group_id) {
		$cls = new ClaimGroupClass($this->db) ;
		$row = $cls->getRecord($group_id) ;
		$desc = $row[ClaimGroupTable::C_CLAIM_GROUP_DESC];
		unset($cls) ;
		return $desc ;
	}

	private function getDeptGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'--- Select Dept. ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	
	private function getEmpGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(EmployeeTable::C_TABLE, EmployeeTable::C_ID, EmployeeTable::C_NAME ,array('code'=>'','desc'=>'--- Select Emp. ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	
	private function addHeadLimits($id,$limits) {
		$cls = new ClaimGroupHeadClass($this->db);
		if ($limits != "") {
			$lines = explode("|",$limits) ;
			
			for ($i= 0;$i < count($lines) ;$i++) {
				$datas = array() ;
				$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_ID ,$id);
				$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_EMP ,$lines[$i]) ;
				
				$cls->addRecord($datas) ;
			}
		}
	}
	
	private function addEmpLimits($id,$limits) {
		$cls = new ClaimGroupEmpClass($this->db);
		if ($limits != "") {
			$lines = explode("|",$limits) ;
			
			for ($i= 0;$i < count($lines) ;$i++) {
				$datas = array() ;
					$datas[] = $this->db->fieldValue(ClaimGroupEmpTable::C_CLAIM_GROUP_ID ,$id);
					$datas[] = $this->db->fieldValue(ClaimGroupEmpTable::C_CLAIM_GROUP_EMP_ID ,$lines[$i]) ;
					
					$cls->addRecord($datas) ;
			}
		}
	}
	
	private function deleteHeadLimits($id) {
		$cls = new ClaimGroupHeadClass($this->db) ;
		try {
			$cls->deleteRecord($id) ; 
			//$this->sendJsonResponse(Status::Ok,"All Claim Group Head successfully deleted from the system.","",$this->type);
		} catch (Exception $e) {
			//$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Group Head from the system.","",$this->type) ;
		}
	}
	
	
	private function deleteEmpLimits($id) {
		$cls = new ClaimGroupEmpClass($this->db) ;
		try {
			$cls->deleteRecord($id) ; 
			//$this->sendJsonResponse(Status::Ok,"All Claim Group Head successfully deleted from the system.","",$this->type);
		} catch (Exception $e) {
			//$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Group Head from the system.","",$this->type) ;
		}
	}
	
	
	private function getHeadLimit($id) {
		$cls = new ClaimGroupHeadClass($this->db) ;
		$rows = $cls->getGroupHeadTable($id) ;
		$lines = "" ;
		if (!is_null($rows) || count($rows) > 0) {
			foreach ($rows as $row) {
				
				if (strlen($lines) > 0)
					$lines .= "|" ;
				$lines .= $row[EmployeeTable::C_ID] . ":" . $row[EmployeeTable::C_NAME] ;
			}
		}
		
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
		return $lines;
	}
	
	private function getEmpLimit($id) {
		$cls = new ClaimGroupEmpClass($this->db) ;
		$rows = $cls->getGroupEmpTable($id) ;
		$lines = "" ;
		if (!is_null($rows) || count($rows) > 0) {
			foreach ($rows as $row) {
				
				if (strlen($lines) > 0)
					$lines .= "|" ;
				$lines .= $row[EmployeeTable::C_ID] . ":" . $row[EmployeeTable::C_NAME] ;
			}
		}
		
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
		return $lines;
	}
	

	private function getEmployee($params) {
		$id = $params['id'] ;
		
		$cls = new EmployeeClass($this->db) ;
		$filter = $this->db->fieldParam(EmployeeTable::C_DEPT) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(EmployeeTable::C_DEPT,$id) ;
		$rows = $cls->getTable($filter,EmployeeTable::C_NAME,$datas) ;
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
	
	
	}
?>