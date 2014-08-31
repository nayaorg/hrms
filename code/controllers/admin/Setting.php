<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "admin/OrganizationClass.php") ;
require_once (PATH_MODELS . "admin/OrganizationOptions.php") ;

class Setting extends ControllerBase {
	private $type = "" ;
	private $datas = array() ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
	}
	function __destruct() {
		unset($this->db) ;
	}
	public function processRequest($params) {
		$this->type = "v" ;
		
		try {
			$this->db->open() ;
			if (isset($params) && count($params) > 0) {
				if (isset($params['type']))
					$this->type = $params['type'] ;
			}
			switch ($this->type) {
				case REQ_UPDATE:
					$this->updateRecord($params) ;
					break ;
				case REQ_GET:
					$this->getRecord($params) ;
					break ;
				case REQ_DELETE:
					$this->removeLogo($params) ;
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
	private function updateRecord($params) {
		$cls = new OrganizationClass($this->db) ;
		$opts = new OrganizationOptions() ;
		
		try {
			$datas = array() ;
			$modifyby = $_SESSION[SE_USERID] ;
			$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
			$ws = $_SERVER['REMOTE_ADDR']; 
			$op = array() ;
			$op[OrganizationOptions::C_CONTACT] = $this->getContactOpt($params) ;
			$op[OrganizationOptions::C_SETTING] = $this->getSettingOpt($params) ;
			$opts->setOption($op) ;
			$datas[] = $this->db->fieldValue(OrganizationTable::C_CODE, $this->getParam($params,'code',"")) ;
			$datas[] = $this->db->fieldValue(OrganizationTable::C_OPTIONS,$opts->getXml());
			$datas[] = $this->db->fieldValue(OrganizationTable::C_WS_ID,$ws) ;
			$datas[] = $this->db->fieldValue(OrganizationTable::C_MODIFY_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(OrganizationTable::C_MODIFY_DATE,$modifydate) ;
			$cls->updateRecord($_SESSION[SE_ORGID],$datas) ;
			$this->sendJsonResponse(Status::Ok,"System Setting successfully updated to the system.","",$this->type) ;
		} catch (Exception $e) {
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating system setting to the system.","",$this->type) ;
		}
		unset($coy) ;
	}
	private function getContactOpt($params) {
		$op = array() ;
		$op[OrganizationOptions::C_CONT_NAME1] = $this->getParam($params,'name1',"") ;
		$op[OrganizationOptions::C_CONT_NAME2] = $this->getParam($params,'name2',"") ;
		$op[OrganizationOptions::C_CONT_ADDR1] = $this->getParam($params,'addr1',"") ;
		$op[OrganizationOptions::C_CONT_ADDR2] = $this->getParam($params,'addr2',"") ;
		$op[OrganizationOptions::C_CONT_ADDR3] = $this->getParam($params,'addr3',"") ;
		$op[OrganizationOptions::C_CONT_ADDR4] = $this->getParam($params,'addr4',"") ;
		$op[OrganizationOptions::C_CONT_ADDR5] = $this->getParam($params,'addr5',"") ;
		$op[OrganizationOptions::C_CONT_TEL] = $this->getParam($params,'telno',"") ;
		$op[OrganizationOptions::C_CONT_FAX] = $this->getParam($params,'faxno',"") ;
		$op[OrganizationOptions::C_CONT_EMAIL] = $this->getParam($params,'email',"");
		$op[OrganizationOptions::C_CONT_WEB] = $this->getParam($params,'website',"");
		
		return $op ;
	}
	private function getSettingOpt($params) {
		$op = array() ;
		$op[OrganizationOptions::C_SET_FAIL_COUNT] = $this->getParamInt($params,'failcount',3) ;
		$op[OrganizationOptions::C_SET_REF_NO] = $this->getParam($params,'refno',"");
		$op[OrganizationOptions::C_SET_LIC] = "" ;
		return $op ;
	}
	private function getRecord() {
		$cls = new OrganizationClass($this->db) ;
		$row = $cls->getRecord($_SESSION[SE_ORGID]) ;
		$this->datas['name1'] = "";
		$this->datas['name2'] = "" ;
		$this->datas['addr1'] = "" ;
		$this->datas['addr2'] = "";
		$this->datas['addr3'] = "" ;
		$this->datas['addr4'] = "";
		$this->datas['addr5'] = "" ;
		$this->datas['failcount'] = "3";
		$this->datas['code'] = "";
		$this->datas['refno'] = "" ;
		$this->datas['telno'] = "" ;
		$this->datas['faxno'] = "" ;
		if (!is_null($row)) {
			if (!is_null($row[OrganizationTable::C_OPTIONS]) && $row[OrganizationTable::C_OPTIONS] != "") {
				$this->datas['code'] = $row[OrganizationTable::C_CODE] ;
				$opt = new OrganizationOptions() ;
				$opt->loadXml($row[OrganizationTable::C_OPTIONS]) ;
				$op = $opt->getOption() ;
				$this->datas['name1'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_NAME1] ;
				$this->datas['name2'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_NAME2] ;
				$this->datas['addr1'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_ADDR1] ;
				$this->datas['addr2'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_ADDR2] ;
				$this->datas['addr3'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_ADDR3] ;
				$this->datas['addr4'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_ADDR4] ;
				$this->datas['addr5'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_ADDR5] ;
				$this->datas['telno'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_TEL] ;
				$this->datas['faxno'] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_FAX] ;
				$this->datas['failcount'] = $op[OrganizationOptions::C_SETTING][OrganizationOptions::C_SET_FAIL_COUNT] ;
				$this->datas['refno'] = $op[OrganizationOptions::C_SETTING][OrganizationOptions::C_SET_REF_NO] ;
				
				unset($opt) ;
			}
		}
		unset($cls) ;
		unset($row) ;
	}
	private function getName1() {
		return $this->datas['name1'] ;
	}
	private function getName2() {
		return $this->datas['name2'] ;
	}
	private function getAddr1() {
		return $this->datas['addr1'] ;
	}
	private function getAddr2() {
		return $this->datas['addr2'] ;
	}
	private function getAddr3() {
		return $this->datas['addr3'] ;
	}
	private function getAddr4() {
		return $this->datas['addr4'] ;
	}
	private function getAddr5() {
		return $this->datas['addr5'] ;
	}
	private function getFailCount() {
		return $this->datas['failcount'] ;
	}
	private function getCode() {
		return $this->datas['code'] ;
	}
	private function getRefNo() {
		return $this->datas['refno'] ;
	}
	private function getTelNo() {
		return $this->datas['telno'] ;
	}
	private function getFaxNo() {
		return $this->datas['faxno'] ;
	}
	private function getLogo() {
		$fn = Util::getLogoFile(PATH_PICTURE, $_SESSION[SE_ORGID]) ;
		if ($fn == "")
			return "" ;
		else 
			return "picture/" . $_SESSION[SE_ORGID] . "/" . $fn ;
	}
	private function removeLogo($params=null) {
		Util::removeLogoFile(PATH_PICTURE, $_SESSION[SE_ORGID]) ;
		$this->sendJsonResponse(Status::Ok,"Logo successfully removed from the system.","",$this->type) ;
	}
	private function getView() {
		$this->getRecord();
		ob_start() ;
		include (PATH_VIEWS . "admin/SettingView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
}
?>