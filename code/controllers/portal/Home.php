<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimHeaderClass.php");



class Home extends ControllerBase {

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
				case PORTAL_HOME:
 					$this->processHomeInfo() ;
					break ;
				case PORTAL_CLAIM:
					$this->processClaimInfo() ;
					break ;
				case PORTAL_LEAVES:
					$this->processLeaveInfo() ;
					break ;
				case PORTAL_CALENDAR:
					$this->processCalendarInfo() ;
					break ;
				case REQ_VIEW:
					$this->processView() ;
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
	private function processView() {
		ob_start() ;
		include (PATH_VIEWS . "portal/HomeView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function processHomeInfo() {
		ob_start() ;
		include (PATH_VIEWS . "portal/HomeInfoView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function processClaimInfo() {
		ob_start() ;
		include (PATH_VIEWS . "portal/ExpenseInfoView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function processLeaveInfo() {
		ob_start() ;
		include (PATH_VIEWS . "portal/LeaveInfoView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function processCalendarInfo() {
		ob_start() ;
		include (PATH_VIEWS . "portal/CalendarInfoView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
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
	private function createMenuFunc($href,$desc,$type) {
		return "javascript:showPage('" . Util::convertLink($href) . "','". $desc . "'". ",'" . "$type" . "')" ;
	}
	
	private function createJsFunc($func) {
		return "javascript:" . $func ;
	}
	
/** CLAIM  **/	
	/**
	 * @param unknown $fromDate format: 'd/m/Y'
	 * @param unknown $toDate	format: 'd/m/Y'
	 * @return list of claim/expense data
	 */
	public function getClaimList($fromDate, $toDate, $top = 0) {
		$cls 	= new ClaimHeaderClass($this->db) ;
		$datas 	= array() ;
	
		$filter = "";
		$filter = $this->db->fieldParam(ClaimHeaderTable::C_ORG_ID);
		$datas[]= $this->db->valueParam(ClaimHeaderTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
// 		if ($_SESSION[SE_USERID] != 1) {
// 			$filter .= " and " . $this->db->fieldParam(ClaimHeaderTable::C_EMP);
// 			$datas[] = $this->db->valueParam(ClaimHeaderTable::C_EMP,$_SESSION[SE_USERID]) ;
// 		}
		
		if(trim($fromDate," ") != "" && trim($toDate," ") != "") {
			$fromDateFormat = DateTime::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
			$toDateFormat 	= DateTime::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');
			$filter 	   .= " and " . $this->db->btwAndFieldParam(ClaimHeaderTable::C_DATE, 
																	$fromDateFormat, $toDateFormat);
		}

		if($top >0)
			$rows = $cls->getTableTop($filter,ClaimHeaderTable::C_DATE,$datas,NUM_CLAIM_SHOW) ;
		else 
			$rows = $cls->getTableTop($filter,ClaimHeaderTable::C_DATE,$datas) ;
		
		$list = array() ;
		foreach ($rows as $row) {
			$data = array() ;
			$data['id'] 	= $row[ClaimHeaderTable::C_ID];
			$data['desc'] 	= $row[ClaimHeaderTable::C_DESC];
			$data['type'] 	= $row[ClaimHeaderTable::C_TYPE] == 0 ? 'Personal' : 'Business' ;
			$dte 			= date_create($row[ClaimHeaderTable::C_DATE]);
			$data['date']	= date_format($dte, 'd/m/Y') ;
			$data['amount'] = $row[ClaimHeaderTable::C_AMOUNT] ;
			
			if ($data['amount'] == ".00") {
				$data['amount'] = "0.00";
			}
			
			$data['status'] = $cls->convertStatusStr($row[ClaimHeaderTable::C_STATUS]);
			$data['approved_amount'] = $row[ClaimHeaderTable::C_APPROVED_AMT];
			if ($data['approved_amount'] == ".00") {
				$data['approved_amount'] = "0.00";
			}
			$list[] = $data;
			unset($data);
		}
	
		unset($rows) ;
		unset($cls) ;
		unset($cls_emp) ;
		return $list;
	}
	
	private function getDepartment() {
		$claim = new Claim();
		return $claim->getDeptGroup();
	}
	
	private function getTravelPlan() {
		$claim = new Claim();
		return $claim->getTravelPlan();
	}

	
/* NAVIGATION PURPOSE ONLY */
/*
	private function getAddClaimNavigation() {
		$arrName 		= array();
		$arrURL	 		= array();
		$arrMenuActive  = array();
		
		$arrName[]	= "Home";
		$arrURL[] 	= "index.pzx?c=" . Util::convertLink("Home") . "&t=" . PORTAL_HOME . "&d=" . time() ;
		$arrMenuActive[]= MenuName::HomeMenu;
		
		$arrName[]	= "Claim";
		$arrURL[]	=  "index.pzx?c=" . Util::convertLink("Home") . "&t=" . PORTAL_CLAIM . "&d=" . time() ;
		$arrMenuActive[]= MenuName::ClaimMenu;
		
		$arrName[]	= "Add";
		$arrURL[]	=  "index.pzx?c=" . Util::convertLink("Home") . "&t=" . PORTAL_CLAIM_ADD_VIEW . "&d=" . time() ;
		$arrMenuActive[]= MenuName::ClaimMenu;
		
		return array($arrName, $arrURL, $arrMenuActive);
	}
*/
	private function getHomeNavigation() {
		$arrName 		= array();
		$arrURL	 		= array();
		$arrMenuActive  = array();
		
		$arrName[]	= "Home";
		$arrURL[] 	= "index.pzx?c=" . Util::convertLink("Home") . "&t=" . PORTAL_HOME . "&d=" . time() ;
		$arrMenuActive[]= MenuName::HomeMenu;
		
		$arrName[]	= "Dashboard";
		$arrURL[]	=  "" ;
		$arrMenuActive[]= "";
		return array($arrName, $arrURL, $arrMenuActive);
	}
	
	private function getClaimNavigation() {
		$arrName 		= array();
		$arrURL	 		= array();
		$arrMenuActive  = array();
	
		$arrName[]	= "Home";
		$arrURL[] 	= "index.pzx?c=" . Util::convertLink("Home") . "&t=" . PORTAL_HOME . "&d=" . time() ;
		$arrMenuActive[]= MenuName::HomeMenu;
	
		$arrName[]	= "Claim";
		$arrURL[]	=  "" ;
		$arrMenuActive[]= "";
		return array($arrName, $arrURL, $arrMenuActive);
	}
	
	private function getLeaveNavigation() {
		$arrName 		= array();
		$arrURL	 		= array();
		$arrMenuActive  = array();
	
		$arrName[]	= "Home";
		$arrURL[] 	= "index.pzx?c=" . Util::convertLink("Home") . "&t=" . PORTAL_HOME . "&d=" . time() ;
		$arrMenuActive[]= MenuName::HomeMenu;
	
		$arrName[]	= "Leave";
		$arrURL[]	=  "" ;
		$arrMenuActive[]= "";
		return array($arrName, $arrURL, $arrMenuActive);
	}
	
	private function getCalendarNavigation() {
		$arrName 		= array();
		$arrURL	 		= array();
		$arrMenuActive  = array();
	
		$arrName[]	= "Home";
		$arrURL[] 	= "index.pzx?c=" . Util::convertLink("Home") . "&t=" . PORTAL_HOME . "&d=" . time() ;
		$arrMenuActive[]= MenuName::HomeMenu;
	
		$arrName[]	= "Calendar";
		$arrURL[]	=  "" ;
		$arrMenuActive[]= "";
		return array($arrName, $arrURL, $arrMenuActive);
	}
}
?>