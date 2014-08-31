<?php
include (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "admin/UserClass.php") ;

class Login extends ControllerBase {
	private $user ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->user = new UserClass($this->db) ;
	}
	function __destruct() {
		unset($this->user) ;
	}
	public function processRequest($params) {
		$username = "" ;
		$pwd = "" ;
		$type = REQ_VIEW ;
		try {
			$this->db->open() ;
			if (isset($params) && count($params) > 0) {
				if (isset($params['type']))
					$type = $params['type'] ;
				if (isset($params['id']))
					$username = $params['id'];
				if (isset($params['pwd'])) {
					$pwd = Util::decryptData($params['pwd'],$_SESSION[SE_USERKEY]) ;
				}
			}

			switch ($type) {
				case REQ_SIGNIN:
					$this->processLogin($username,$pwd) ;
					break ;
				case REQ_SIGNOUT:
					$this->processLogout($username) ;
					break ;
				case REQ_QUERY:
					$this->processQuery($username) ;
					break ;
				case REQ_NEW:
					$this->processLogin($username,$pwd,true) ;
					break ;
				case REQ_VIEW:
					$this->processView() ;
					break ;
				default:
					$this->sendJsonResponse(Status::Error,"invalid request.","") ;
					break ;
			}
			$this->db->close() ;
			return true ;
		} catch (Exception $e) {
			$this->db->close() ;
			die ($e->getMessage()) ;
		}
	}
	private function processLogin($username,$pwd,$firsttime=false) {
		$status = "" ;
		$mesg = "" ;
		$data = "" ;
		$id = "" ;
		$orgid = -1 ;
		$row = $this->user->getRecordByName(0,$username);
		if (is_null($row) || count($row) == 0) {
			$status = Status::Error ;
			$mesg = "Invalid user name.";
		} else {
			$id = $row[UserTable::C_ID];
			$cnt = $row[UserTable::C_FAIL_COUNT] ;
			$group = $row[UserTable::C_GROUP] ;
			$name = $row[UserTable::C_FULL_NAME] ;
			$orgid = $row[UserTable::C_ORG_ID] ;
			if ($cnt > 2) {
				$status = Status::Error;
				$mesg = "Your account have been disabled. Please contact your system administrator for assistance.";
			}
			if ($status == "") {
				$start = strtotime($row[UserTable::C_START_DATE]) ;
				if ($row[UserTable::C_START_DATE] > time()) {
					$status = Status::Error;
					$mesg = "You can only login after " . date('Y-m-d',$row[UserTable::C_START_DATE]) ;
				} 
			}
			if ($status == "") {
				$dte = date_create($row[UserTable::C_EXPIRY_DATE]) ;
				if ($dte != date_create(MAX_DATE)) {
					if ($row[UserTable::C_EXPIRY_DATE] < time()) {
						$status = Status::Error ;
						$mesg = "Yours account have already expired. Please contact your system administrator for assistance.";
					}
				}
			}
		}
		if ($status == "") {
			if ($firsttime) {
				$this->user->changePassword($id,$pwd) ;
			} else {
				if ($this->user->encryptPwd1($pwd) != $row[UserTable::C_PWD1]) {
					$status = Status::Error ;
					$mesg = "Invalid password. Please try again." ;
					$this->user->updateFailCount($id,$cnt + 1) ;
				}
			}
		}
		if ($status == "") {
			$status = Status::Ok ;
			$this->user->updateLogin($id) ;
			$this->setLogin($id,$username,$name,$group,$orgid) ;
			$data = "index.pzx?c=" . Util::convertLink("Main");
		} else {
			
		}
		$this->sendJsonResponse($status,$mesg,$data);
	}
	private function processQuery($username) {
		$status = "" ;
		$mesg = "" ;
		$data = "" ;
		$id = "" ;
		$chngpwd = false ;
		$key = "" ;
		$row = $this->user->getRecordByName(0,$username);
		if (is_null($row) || count($row) == 0) {
			$status = Status::Error ;
			$mesg = "Invalid user name.";
		} else {
			$id = $row[UserTable::C_ID];
			$cnt = $row[UserTable::C_FAIL_COUNT] ;
			$group = $row[UserTable::C_GROUP] ;
			$name = $row[UserTable::C_NAME] ;
			$chngpwd = $row[UserTable::C_CHANGE_PWD] ;
			if ($cnt > 2) {
				$status = Status::Error;
				$mesg = "You account have been disabled. Please contact your system administrator for assistance.";
			}
			if ($status == "") {
				$start = strtotime($row[UserTable::C_START_DATE]) ;
				if ($row[UserTable::C_START_DATE] > time()) {
					$status = Status::Error;
					$mesg = "You can only login after " . date('Y-m-d',$row[UserTable::C_START_DATE]) ;
				} 
			}
			if ($status == "") {
				$dte = date_create($row[UserTable::C_EXPIRY_DATE]) ;
				if ($dte != date_create(MAX_DATE)) {
					if ($row[UserTable::C_EXPIRY_DATE] < time()) {
						$status = Status::Error ;
						$mesg = "Yours account have already expired. Please contact your system administrator for assistance.";
					}
				}
			}
		}
		if ($status == "") {
			$mesg = "" ;
			$key = Util::createMD5(time().$username,"","",true) ;
			$_SESSION[SE_USERKEY] = $key ;
			$data = $key ;
			if ($chngpwd) {
				$status = Status::Info ;
			} else {
				$status = Status::Confirm ;
			}
		}

		$this->sendJsonResponse($status,$mesg,$data);
	}
	private function processLogout($params) {
		$this->setLogin("","","","",-1) ;
		$_SESSION[SE_USERKEY] = "" ;
		$this->sendJsonResponse(Status::Ok,"","index.pzx") ;
	}
	private function setLogin($userid,$username,$fullname,$usergroup,$orgid) {
		$_SESSION[SE_USERID] = $userid ;
		$_SESSION[SE_USERNAME] = $username ;
		$_SESSION[SE_USERGROUP] = $usergroup ;
		$_SESSION[SE_FULLNAME] = $fullname ;
		$_SESSION[SE_MENU] = "";
		$_SESSION[SE_ORGID] = $orgid ;
		$_SESSION[SE_ORGNAME] = "";
	}
	private function processView() {
		ob_start() ;
		include (PATH_VIEWS . "general/LoginView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function createMenu($usergroup) {
		//$menu = new Menu() ;
		//return $menu->Render() ;
		return "" ;
	}
}
?>