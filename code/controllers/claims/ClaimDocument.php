<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimDocumentClass.php") ;

class ClaimDocument extends ControllerBase {
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
		$cls = new ClaimDocumentClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;

		$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_ID,$this->getParam($params,'claim_id',"")) ;
		$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_REF,$this->getParam($params,'ref',"")) ;
		$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_PATH,$this->getParam($params,'path',"")) ;
		$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_COY_ID,0) ;
		$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_ORG_ID,$orgid) ;
		// $datas[] = $this->db->fieldValue(ClaimDocumentTable::C_WS_ID,$ws) ;
		// $datas[] = $this->db->fieldValue(ClaimDocumentTable::C_MODIFY_BY,$modifyby) ;
		// $datas[] = $this->db->fieldValue(ClaimDocumentTable::C_MODIFY_DATE,$modifydate) ;
		// $datas[] = $this->db->fieldValue(ClaimDocumentTable::C_CREATE_BY,$modifyby) ;
		// $datas[] = $this->db->fieldValue(ClaimDocumentTable::C_CREATE_DATE,$modifydate) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			
			$old_path = $this->getParam($params,'path',"");
			$path_segment = explode(".", $old_path);
			$ext = $path_segment[1];
			$new_path = "claims/" .$this->getParam($params,'claim_id',""). "/claim_doc_" . $id . "_" . $this->getParam($params,'claim_id',"") . "." . $ext;
			rename($old_path, $new_path);
			
			$datas = array();
			$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_PATH,$new_path) ;
			$cls->updateRecord($id,$datas) ;
			
			if ($id > 0) { 
				$this->sendJsonResponse(Status::Ok,"Claim Document successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Claim Document to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[Claim Document]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimDocumentClass($this->db) ;
			
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_REF,$this->getParam($params,'ref',"")) ;
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_PATH,$this->getParam($params,'path',"")) ;
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Claim Document successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Claim Document]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Claim Document to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim Document id if you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimDocumentClass($this->db) ;
			try {
				$docPath = $this->getDocPath($params);
				unlink(PATH_CLAIM . $docPath);
				
				$cls->deleteRecord($id) ; 
				
				$this->sendJsonResponse(Status::Ok,"Claim Document successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Document from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim Document id if you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($params) {
		$cls = new ClaimDocumentClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimDocumentTable::C_ID) . " and " . $this->db->fieldParam(ClaimDocumentTable::C_ORG_ID) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimDocumentTable::C_ID,$params['claim_id']) ;
		$datas[] = $this->db->valueParam(ClaimDocumentTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ClaimDocumentTable::C_DOC,$datas) ;
		$list = array() ;
		foreach ($rows as $row) {
			$data = array() ;
			$data['id'] = $row[ClaimDocumentTable::C_DOC] ;
			$data['claim_id'] = $row[ClaimDocumentTable::C_ID] ;
			$data['ref'] = $row[ClaimDocumentTable::C_REF] ;
			$data['desc'] = $row[ClaimDocumentTable::C_DESC] ;
			$data['path'] = $row[ClaimDocumentTable::C_PATH] ;
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
			$cls = new ClaimDocumentClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Claim Document id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $row[ClaimDocumentTable::C_DOC] ;
				$datas['claim_id'] = $row[ClaimDocumentTable::C_ID] ;
				$datas['ref'] = $row[ClaimDocumentTable::C_REF] ;
				$datas['desc'] = $row[ClaimDocumentTable::C_DESC] ;
				$datas['path'] = $row[ClaimDocumentTable::C_PATH] ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Claim Document id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "claims/ClaimDocumentView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getDocPath($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimDocumentClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				return "No document record found";
			} else {
				return (String) $row[ClaimDocumentTable::C_PATH] ;
			}
			unset($cls) ;
		} else {
			return "No ID provided" ;
		}
	}
}
?>