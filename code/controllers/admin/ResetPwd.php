<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "admin/UserClass.php") ;

class ResetPwd extends ControllerBase {
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
				case REQ_NEW:
					$this->resetPassword($params) ;
					break ;
				case REQ_GET:
					$this->getRecord($params) ;
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
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new UserClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid user id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['name'] = $row[UserTable::C_NAME];
				$datas['fullname'] = $row[UserTable::C_FULL_NAME] ;
				$dte = date_create($row[UserTable::C_LAST_LOGIN]) ;
				if ($dte == date_create(NULL_DATE))
					$datas['login'] = "" ;
				else
					$datas['login'] = date_format($dte,'d-M-Y H:i:s') ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing user id. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new UserClass($this->db) ;
		$filter = $this->db->fieldParam(UserTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(UserTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,UserTable::C_FULL_NAME,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[UserTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[UserTable::C_NAME] . "</td>" ;
			$list .= "<td>" . $row[UserTable::C_FULL_NAME] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editResetPwd(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "admin/ResetPwdView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function resetPassword($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new UserClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid user id. Please try again.",$id,$this->type);
			} else {
				$cls->resetPassword($id) ;
				$this->sendJsonResponse(Status::Ok,"Password successfuly reseted.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing user id. Please try again.","",$this->type);
		}
	}
}
?>