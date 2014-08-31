<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "admin/UserGroupClass.php") ;
require_once (PATH_MODELS . "admin/UserRight.php") ;

class UserGroup extends ControllerBase {
	private $type = "" ;
	private $clsgroup ;
	private $clsright ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->clsgroup = new UserGroupClass($this->db) ;
		$this->clsright = new UserRight() ;
	}
	function __destruct() {
		unset($this->db) ;
		unset($this->clsgroup) ;
		unset($this->clsright) ;
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
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP];
		
		$datas[] = $this->db->fieldValue(UserGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(UserGroupTable::C_RIGHTS,$this->convertRights($params)) ;
		$datas[] = $this->db->fieldValue(UserGroupTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(UserGroupTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(UserGroupTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(UserGroupTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(UserGroupTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(UserGroupTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $this->clsgroup->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"User group successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new user group to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[UserGroup]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;

				$datas[] = $this->db->fieldValue(UserGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(UserGroupTable::C_RIGHTS,$this->convertRights($params)) ;
				$datas[] = $this->db->fieldValue(UserGroupTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(UserGroupTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(UserGroupTable::C_MODIFY_DATE,$modifydate) ;
				$this->clsgroup->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"User group detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[UserGroup]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating user group detail to the system.","",$this->type) ;
			}
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the user group id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			try {
				$this->clsgroup->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"User group successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[UserGroup]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting user group record from the system.","",$this->type) ;
			}
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the user group id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$filter = $this->db->fieldParam(UserGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(UserGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $this->clsgroup->getTable($filter,UserGroupTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[UserGroupTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[UserGroupTable::C_DESC] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editUserGroup(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteUserGroup(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$row = $this->clsgroup->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid user group id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[UserGroupTable::C_DESC];
				if (is_null($row[UserGroupTable::C_RIGHTS]))
					$this->clsright->initRights();
				else
					$this->clsright->toRights($row[UserGroupTable::C_RIGHTS]); ;
				$datas['hr'] = $this->getHrRights($this->clsright->getHrRight()) ;
				$datas['admin'] = $this->getAdminRights($this->clsright->getAdminRight()) ;
				$datas['payroll'] = $this->getPayrollRights($this->clsright->getPayrollRight()) ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
		} else {
			$this->sendJsonResponse(Status::Error,"Missing user group id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "admin/UserGroupView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function convertRights($params) {
		$this->clsright->initRights() ;
		if (isset($params['admin'])) {
			$r = array() ;
			$rs = $params['admin'] ;
			if (isset($rs['setting']))
				$r[UserRight::C_ADMIN_SETTING] = $this->clsright->bin2Access(str_repeat($rs['setting'],6));
			if (isset($rs['company']))
				$r[UserRight::C_ADMIN_COY] = $this->clsright->bin2Access(str_repeat($rs['company'],6)) ;
			if (isset($rs['user']))
				$r[UserRight::C_ADMIN_USER] = $this->clsright->bin2Access(str_repeat($rs['user'],6)) ;
			if (isset($rs['group']))
				$r[UserRight::C_ADMIN_GROUP] = $this->clsright->bin2Access(str_repeat($rs['group'],6)) ;
			if (isset($rs['reset']))
				$r[UserRight::C_ADMIN_RESET] = $this->clsright->bin2Access(str_repeat($rs['reset'],6)) ;
			$this->clsright->setAdminRight($r) ;
		}
		if (isset($params['hr'])) {
			$r = array() ;
			$rs = $params['hr'] ;
			if (isset($rs['employee']))
				$r[UserRight::C_HR_EMP] = $this->clsright->bin2Access(str_repeat($rs['employee'],6)) ;
			if (isset($rs['type']))
				$r[UserRight::C_HR_TYPE] = $this->clsright->bin2Access(str_repeat($rs['type'],6)) ;
			if (isset($rs['job']))
				$r[UserRight::C_HR_JOB] = $this->clsright->bin2Access(str_repeat($rs['job'],6)) ;
			if (isset($rs['dept']))
				$r[userRight::C_HR_DEPT] = $this->clsright->bin2Access(str_repeat($rs['dept'],6)) ;
			if (isset($rs['nat']))
				$r[UserRight::C_HR_NAT] = $this->clsright->bin2Access(str_repeat($rs['nat'],6)) ;
			if (isset($rs['race']))
				$r[UserRight::C_HR_RACE] = $this->clsright->bin2Access(str_repeat($rs['race'],6)) ;
			if (isset($rs['permit']))
				$r[UserRight::C_HR_PERMIT] = $this->clsright->bin2Access(str_repeat($rs['permit'],6));
			$this->clsright->setHrRight($r) ;
		}
		if (isset($params['payroll'])) {
			$r = array() ;
			$rs = $params['payroll'] ;
			if (isset($rs['bank']))
				$r[UserRight::C_PAYROLL_BANK] = $this->clsright->bin2Access(str_repeat($rs['bank'],6));
			if (isset($rs['employee']))
				$r[UserRight::C_PAYROLL_EMP] = $this->clsright->bin2Access(str_repeat($rs['employee'],6));
			if (isset($rs['type']))
				$r[UserRight::C_PAYROLL_TYPE] = $this->clsright->bin2Access(str_repeat($rs['type'],6)) ;
			if (isset($rs['cpf']))
				$r[UserRight::C_PAYROLL_CPF] = $this->clsright->bin2Access(str_repeat($rs['cpf'],6));
			if (isset($rs['create'])) 
				$r[UserRight::C_PAYROLL_CREATE] = $this->clsright->bin2Access(str_repeat($rs['create'],6));
			if (isset($rs['entry']))
				$r[UserRight::C_PAYROLL_ENTRY] = $this->clsright->bin2Access(str_repeat($rs['entry'],6)) ;
			if (isset($rs['paylist']))
				$r[UserRight::C_PAYROLL_PAYLIST] = $this->clsright->bin2Access(str_repeat($rs['paylist'],6));
			if (isset($rs['payslip']))
				$r[UserRight::C_PAYROLL_PAYSLIP] = $this->clsright->bin2Access(str_repeat($rs['payslip'],6)) ;
			if (isset($rs['cpflist']))
				$r[UserRight::C_PAYROLL_CPFLIST] = $this->clsright->bin2Access(str_repeat($rs['cpflist'],6)) ;
			if (isset($rs['cpfentry']))
				$r[UserRight::C_PAYROLL_CPFENTRY] = $this->clsright->bin2Access(str_repeat($rs['cpfentry'],6)) ;
			if (isset($rs['incomeyear']))
				$r[UserRight::C_PAYROLL_INCOMEYEAR] = $this->clsright->bin2Access(str_repeat($rs['incomeyear'],6)) ;
			$this->clsright->setPayrollRight($r) ;
		}
		return $this->clsright->toString() ;
	}
	private function getHrRights($rights) {
		Log::write("rights: " . $rights) ;
		$arr = array() ;
		$arr['employee'] = $rights[UserRight::C_HR_EMP][UserRight::C_ENABLE];
		$arr['type'] = $rights[UserRight::C_HR_TYPE][UserRight::C_ENABLE];
		$arr['job'] = $rights[UserRight::C_HR_JOB][UserRight::C_ENABLE];
		$arr['dept'] = $rights[UserRight::C_HR_DEPT][UserRight::C_ENABLE] ;
		$arr['nat'] = $rights[UserRight::C_HR_NAT][UserRight::C_ENABLE] ;
		$arr['race'] = $rights[UserRight::C_HR_RACE][UserRight::C_ENABLE];
		$arr['permit'] = $rights[UserRight::C_HR_PERMIT][UserRight::C_ENABLE] ;
		return $arr ;
	}
	private function getAdminRights($rights) {
		$arr = array() ;
		$arr['setting'] = $rights[UserRight::C_ADMIN_SETTING][UserRight::C_ENABLE];
		$arr['company'] = $rights[UserRight::C_ADMIN_COY][UserRight::C_ENABLE] ;
		$arr['user'] = $rights[UserRight::C_ADMIN_USER][UserRight::C_ENABLE];
		$arr['group'] = $rights[UserRight::C_ADMIN_GROUP][UserRight::C_ENABLE];
		$arr['reset'] = $rights[UserRight::C_ADMIN_RESET][UserRight::C_ENABLE];
		return $arr ;
	}
	private function getPayrollRights($rights) {
		$arr = array() ;
		$arr['employee'] = $rights[UserRight::C_PAYROLL_EMP][UserRight::C_ENABLE];
		$arr['create'] = $rights[UserRight::C_PAYROLL_CREATE][UserRight::C_ENABLE] ;
		$arr['type'] = $rights[UserRight::C_PAYROLL_TYPE][UserRight::C_ENABLE] ;
		$arr['cpf'] = $rights[UserRight::C_PAYROLL_CPF][UserRight::C_ENABLE] ;
		$arr['cpflist'] = $rights[UserRight::C_PAYROLL_CPFLIST][UserRight::C_ENABLE];
		$arr['entry'] = $rights[UserRight::C_PAYROLL_ENTRY][UserRight::C_ENABLE];
		$arr['paylist'] = $rights[UserRight::C_PAYROLL_PAYLIST][UserRight::C_ENABLE];
		$arr['payslip'] = $rights[UserRight::C_PAYROLL_PAYSLIP][UserRight::C_ENABLE] ;
		$arr['bank'] = $rights[UserRight::C_PAYROLL_BANK][UserRight::C_ENABLE] ;
		$arr['cpfentry'] = $rights[UserRight::C_PAYROLL_CPFENTRY][UserRight::C_ENABLE] ;
		$arr['incomeyear'] = $rights[UserRight::C_PAYROLL_INCOMEYEAR][UserRight::C_ENABLE] ;
		return $arr ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$filter = $this->db->fieldParam(UserGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(UserGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $this->clsgroup->getTable($filter,UserGroupTable::C_DESC,$params) ;
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[UserGroupTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[UserGroupTable::C_DESC],200) ;
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("User Group Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('usergroup.pdf', 'I');
		unset($rows) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>