<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimGroupHeadClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;

class ClaimGroupHead extends ControllerBase {
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
			Log::write('b ' . $this->type);
			switch ($this->type) {
				case REQ_ADD:
					$this->addRecord($params) ;
					break ;
				case REQ_DELETE:
					$this->deleteRecord($params) ;
					break ;
				case REQ_GET:
					//$this->getRecord($params) ;
					$this->getEmployeeHead($params);
					break ;
				case REQ_LIST:
					$this->getList($params) ;
					break ;
				default:
					$this->sendJsonResponse(Status::Error,"b invalid request.","",$this->type) ;
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
		$cls = new ClaimGroupHeadClass($this->db) ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		$id = $this->getParam($params,'id',"");
		$head_ids = explode("|",$this->getParam($params,'head_ids',""));
		
		for ($i=0;$i<sizeof($head_ids);$i++) {
			$datas = array() ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_ID,$id) ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_EMP,$head_ids[$i]) ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_COY_ID,0) ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_ORG_ID,$orgid) ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_WS_ID,$ws) ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_MODIFY_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_MODIFY_DATE,$modifydate) ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_CREATE_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(ClaimGroupHeadTable::C_CREATE_DATE,$modifydate) ;
			
			try {
				$cls->addRecord($datas) ;
			} catch (Exception $e) {
				Log::write('[Claim Group Head] ' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
			}
		}
			
		$this->sendJsonResponse(Status::Ok,"Claim Group Head successfully added to the system.",$this->getParam($params,'head_ids',""),$this->type);
		unset($cls) ;
	}
	private function deleteRecord($params) {
		$cls = new ClaimGroupHeadClass($this->db) ;
		if (isset($params['id']) && isset($params['head_id'])) {
			$id = $params['id'] ;
			$head_id = $params['head_id'] ;
			try {
				$cls->deleteRecord($id,$head_id) ; 
				$this->sendJsonResponse(Status::Ok,"Claim Group Head successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Group Head from the system.","",$this->type) ;
			}
		} else if (isset($params['id'])) {
			$id = $params['id'] ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"All Claim Group Head successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Group Head from the system.","",$this->type) ;
			}
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim Group id and head id if you wish to delete. Please try again.","",$this->type);
		}
		unset($cls) ;
	}
	private function getList($params) {
		$cls = new ClaimGroupHeadClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimGroupHeadTable::C_ID) . " and " . $this->db->fieldParam(ClaimGroupHeadTable::C_ORG_ID) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimGroupHeadTable::C_ID,$params['id']) ;
		$datas[] = $this->db->valueParam(ClaimGroupHeadTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ClaimGroupHeadTable::C_EMP,$datas) ;
		$list = array() ;
		foreach ($rows as $row) {
			$data = array() ;
			$data['id'] = $row[ClaimGroupHeadTable::C_ID] ;
			$data['head_id'] = $row[ClaimGroupHeadTable::C_EMP] ;
			$list[] = $data;
			unset($data);
		}
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type);
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
	}
	private function getRecord($params=null) {
		if (isset($params['id']) && isset($params['head_id'])) {
			$id = $params['id'] ;
			$head_id = $params['head_id'] ;
			$cls = new ClaimGroupHeadClass($this->db) ;
			$row = $cls->getRecord($id,$head_id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Claim Group id and head id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $row[ClaimGroupHeadTable::C_ID] ;
				$datas['head_id'] = $row[ClaimGroupHeadTable::C_EMP] ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Claim Group id and head id. Please try again.","",$this->type);
		}
	}
	public function getAllClaimGroupIdOfHead($head_id) {
		$cls = new ClaimGroupHeadClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimGroupHeadTable::C_EMP) . " and " . $this->db->fieldParam(ClaimGroupHeadTable::C_ORG_ID) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimGroupHeadTable::C_EMP,$head_id) ;
		$datas[] = $this->db->valueParam(ClaimGroupHeadTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ClaimGroupHeadTable::C_ID,$datas) ;
		$list = array() ;
		foreach ($rows as $row) {
			$data = array() ;
			$data['id'] = $row[ClaimGroupHeadTable::C_ID] ;
			$data['head_id'] = $row[ClaimGroupHeadTable::C_EMP] ;
			$list[] = $data;
			unset($data);
		}
		unset($rows) ;
		unset($cls) ;
		
		return $list ;
	}
	
	private function getEmployeeHead($params) {
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