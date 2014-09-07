<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "admin/UserClass.php") ;

class ChangePwd extends ControllerBase {
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
				case REQ_CHANGE:
					$this->changePassword($params) ;
					break ;
				case REQ_GET:
					$this->getUserKey() ;
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
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "general/ChangePwdView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getUserKey() {
		$this->sendJsonResponse(Status::Ok,"",$_SESSION[SE_USERKEY],$this->type) ;
	}
	private function getUserName() {
		return $_SESSION[SE_USERNAME] ;
	}
	private function getFullName() {
		return $_SESSION[SE_FULLNAME] ;
	}
	private function changePassword($params=null) {
		if (isset($params['old']) && isset($params['new'])) {
			if ($params['old'] == "") {
				$this->sendJsonResponse(Status::Error,"Blank old password. Please try again.","",$this->type);
				return ;
			}
			if ($params['new'] == "") {
				$this->sendJsonResponse(Status::Error,"New password can not be blank. Please try again.","",$this->type);
				return ;
			}
			$old = Util::decryptData($params['old'],$_SESSION[SE_USERKEY]) ;
			$new = Util::decryptData($params['new'],$_SESSION[SE_USERKEY]) ;
			$id = $_SESSION[SE_USERID] ;
			$cls = new UserClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid user id. Please try again.",$id,$this->type);
			} else {
				if ($row[UserTable::C_PWD1] == $cls->encryptPwd1($old)) {
					$cls->changePassword($id,$new) ;
					$this->sendJsonResponse(Status::Ok,"Your password had successfuly changed. You must use the new password for next signin.","",$this->type) ;
				} else {
					$this->sendJsonResponse(Status::Error,"Invalid Old Password. Please try again.","",$this->type) ; }
			}
			unset($cls) ;
			
		} else {
			$this->sendJsonResponse(Status::Error,"Old/New password not found. Please try again.","",$this->type);
		}
	}
}
?>