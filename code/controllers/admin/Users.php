<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "admin/UserClass.php") ;
require_once (PATH_MODELS . "admin/UserGroupClass.php") ;

class Users extends ControllerBase {
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
		$cls = new UserClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$start = date_create('now')->format('Y-m-d') ;
		$ws = $_SESSION[SE_REMOTE_IP];
		$name = $this->getParam($params,'name',"") ;
		if ($cls->isNameFound($orgid,$name)) {
			$this->sendJsonResponse(Status::Error,"User name already exist. Please choose other name.",$name,$this->type) ;
			return ;
		}
		$datas[] = $this->db->fieldValue(UserTable::C_NAME,$name) ;
		$datas[] = $this->db->fieldValue(UserTable::C_FULL_NAME,$this->getParam($params,'fullname',"")) ;
		$datas[] = $this->db->fieldValue(UserTable::C_EMAIL,$this->getParam($params,'email',"")) ;
		$datas[] = $this->db->fieldValue(UserTable::C_GROUP,$this->getParamInt($params,'group',0));
		$datas[] = $this->db->fieldValue(UserTable::C_BLOCKED,$this->getParamInt($params,'block',0));
		$datas[] = $this->db->fieldValue(UserTable::C_COMMENTS,$this->getParam($params,'comments',""));
		$datas[] = $this->db->fieldValue(UserTable::C_START_DATE,$this->getParamDate($params,'start',$start) . " 00:00:00") ;
		$datas[] = $this->db->fieldValue(UserTable::C_EXPIRY_DATE,$this->getParamDate($params,'expiry',MAX_DATE)) ;
		$datas[] = $this->db->fieldValue(UserTable::C_LAST_LOGIN,NULL_DATE);
		$datas[] = $this->db->fieldValue(UserTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(UserTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(UserTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(UserTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(UserTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(UserTable::C_ORG_ID,$orgid) ;
	
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"User successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new user to the system.",$id, $this->type) ;
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
			$cls = new UserClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP];
				$orgid = $_SESSION[SE_ORGID] ;
				$start = date_create('now')->format('Y-m-d') ;
				$name = $this->getParam($params,'name',"") ;
				if ($cls->isNameFound($orgid,$name)) {
					$tempid = $cls->getIdByName($orgid,$name) ;
					if ($tempid != $id) {
						$this->sendJsonResponse(Status::Error,"User name already exist. Please choose other name.",$name,$this->type) ;
						return ;
					}
				}
				$datas[] = $this->db->fieldValue(UserTable::C_NAME,$name) ;
				$datas[] = $this->db->fieldValue(UserTable::C_FULL_NAME,$this->getParam($params,'fullname',"")) ;
				$datas[] = $this->db->fieldValue(UserTable::C_EMAIL,$this->getParam($params,'email',"")) ;
				$datas[] = $this->db->fieldValue(UserTable::C_GROUP,$this->getParamInt($params,'group',0));
				$datas[] = $this->db->fieldValue(UserTable::C_BLOCKED,$this->getParamInt($params,'block',0));
				$datas[] = $this->db->fieldValue(UserTable::C_COMMENTS,$this->getParam($params,'comments',""));
				$datas[] = $this->db->fieldValue(UserTable::C_START_DATE,$this->getParamDate($params,'start',$start) . " 00:00:00") ;
				$datas[] = $this->db->fieldValue(UserTable::C_EXPIRY_DATE,$this->getParamDate($params,'expiry',MAX_DATE)) ;
				$datas[] = $this->db->fieldValue(UserTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(UserTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(UserTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"User detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Users]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating user detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the user id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new UserClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"User successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[Users]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting user record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the user id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new UserClass($this->db) ;
		$grp = new UserGroupClass($this->db) ;
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
			$list .= "<td>" . $grp->getDescription($row[UserTable::C_GROUP]) . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editUser(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteUser(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
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
				$datas['email'] = $row[UserTable::C_EMAIL] ;
				if ($row[UserTable::C_GROUP] == 0)
					$datas['group'] = "";
				else
					$datas['group'] = $row[UserTable::C_GROUP] ;
				$datas['block'] = $row[UserTable::C_BLOCKED];
				if (is_null($row[UserTable::C_COMMENTS]))
					$datas['comments'] = "" ;
				else 
					$datas['comments'] = $row[UserTable::C_COMMENTS];
				$dte = date_create($row[UserTable::C_START_DATE]);
				$datas['start'] = date_format($dte, 'd/m/Y'); 
				$dte = date_create($row[UserTable::C_EXPIRY_DATE]) ;
				if ($dte == date_create(MAX_DATE))
					$datas['expiry'] = "" ;
				else
					$datas['expiry'] = date_format($dte,'d/m/Y') ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing user id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "admin/UsersView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getUserGroup() {
		$cls = new UserGroupClass($this->db) ;
		$vls = array() ;
		$vls[] = array ('code'=>'','desc'=>'--- Select a Group ---' ) ;
		$rows = $cls->getValueList($_SESSION[SE_ORGID]) ;
		if (!is_null($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$vls[] = array ('code'=>$row['code'],'desc'=>$row['desc']) ;
			}
		}
		return Util::createOptionValue($vls) ;
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
			$old = $params['old'] ;
			$new = $params['new'] ;
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
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new UserClass($this->db) ;
		$grp = new UserGroupClass($this->db) ;
		$filter = $this->db->fieldParam(UserTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(UserTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,UserTable::C_FULL_NAME,$params) ;
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[UserTable::C_ID],50) ;
			$items[$i][] = $this->createPdfItem($row[UserTable::C_NAME],100) ;
			$items[$i][] = $this->createPdfItem($row[UserTable::C_FULL_NAME],200) ;
			$items[$i][] = $this->createPdfItem($grp->getDescription($row[UserTable::C_GROUP]),100) ;
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("User ID",50,0,"C","B");
		$cols[] = $this->createPdfItem("User Name",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("Full Name",200,0,"C","B") ;
		$cols[] = $this->createPdfItem("Group",100,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("User Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('user.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>