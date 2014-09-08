<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimHeaderClass.php");

class PortalClaim extends ControllerBase {

	function __construct() {
		$this->db 		= $_SESSION[SE_DB] ;
		$this->fldorg 	= ClaimHeaderTable::C_ORG_ID ;
		$this->orgid 	= $_SESSION[SE_ORGID] ;
	}
	function __destruct() {
		unset($this->db) ;
	}
	public function processRequest($params) {
		$this->type = REQ_VIEW ;
		try {
			$this->db->open() ;
			$this->loadSetting() ;
			if (isset($params) && count($params) > 0) {
				if (isset($params['type']))
					$this->type = $params['type'] ;
			}
			switch ($this->type) {
				case REQ_ADD:
					$this->addClaimHeader($params) ;
					break ;
				case REQ_CLAIM_FILTER:
					$this->processClaimFilter($params);
					break;	
				case PORTAL_CLAIM_ADD_VIEW:
					$this->processViewClaimAdd();
					break;
				case PORTAL_CLAIM_UPDATE_VIEW:
					$this->processViewClaimUpdate($params['id']);
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
	private function addClaimHeader($params) {
		$cls = new ClaimHeaderClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		$claimdte = date_create('now')->format('Y-m-d') ;
		
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_TYPE,$this->getParamInt($params,'claim_type',0)) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_DATE,$this->getParamDate($params,'date',$claimdte)) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_EMP,$this->getParamInt($params,'claim_by',0)) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_STATUS, ClaimStatus::Pending) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_TRAVEL,$this->getParamInt($params,'travel_plan',0)) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_COY_ID,0) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_ORG_ID,$orgid) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_CREATE_DATE,$modifydate) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Claim Document successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Claim Document to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[Claim]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	
	private function processClaimFilter($params) {
		$home = new Home();
		$list = $home->getClaimList($params['fromDate'], $params['toDate']);
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type);
		unset($list);
	}
	private function processViewClaimAdd() {
		ob_start() ;
		include (PATH_VIEWS . "portal/ClaimAddView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function processViewClaimUpdate($id) {
		ob_start() ;
		
		$cls 	= new ClaimHeaderClass($this->db) ;
		$row    = $cls->getRecord($id);
		
		include (PATH_VIEWS . "portal/ClaimAddView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function createMenuFunc($href,$desc,$type) {
		return "javascript:showPage('" . Util::convertLink($href) . "','". $desc . "'". ",'" . "$type" . "')" ;
	}
	private function getOrganizationName() {
		return $_SESSION[SE_ORGNAME] ;
	}
	private function getFullName() {
		return $_SESSION[SE_FULLNAME] ;
	}
	private function loadSetting() {
		$_SESSION[SE_ORGNAME] = "default company" ;
		$_SESSION[SE_ORGCODE] = "" ;
	}
	private function createJsFunc($func) {
		return "javascript:" . $func ;
	}
	
/** CLAIM  **/	
	private function getDepartment($dedault="") {
		$claim = new Claim();
		return $claim->getDeptGroup($dedault);
	}
	
	private function getTravelPlan($dedault="") {
		$claim = new Claim();
		return $claim->getTravelPlan($dedault);
	}

}
?>