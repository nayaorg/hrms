<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimLimitClass.php") ;

class ClaimLimit extends ControllerBase {
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
		$cls = new ClaimLimitClass($this->db) ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		$claim_limit_list = explode("|",$params['data']);
		
		for ($i=0;$i<sizeof($claim_limit_list);$i++) {
			$datas = array() ;
			$claim_limit_list_item = explode("--",$claim_limit_list[$i]);
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_EXPENSE_ITEM_ID,$params['expense_item']) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_CLAIM_GROUP_ID,$claim_limit_list_item[0]) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_CLAIM_LIMIT_TYPE,$claim_limit_list_item[1]) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_CLAIM_LIMIT_AMOUNT,$claim_limit_list_item[2]) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_COY_ID,0) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_ORG_ID,$orgid) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_WS_ID,$ws) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_MODIFY_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_MODIFY_DATE,$modifydate) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_CREATE_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(ClaimLimitTable::C_CREATE_DATE,$modifydate) ;
			
			try {
				$id = $cls->addRecord($datas) ;
			} catch (Exception $e) {
				Log::write('[Claim Limit]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
			}
		}
		
		$this->sendJsonResponse(Status::Ok,"Claim Limit successfully added to the system.","",$this->type);
		
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['grp_id']) && isset($params['expense_item'])) {
			$grp_id = $params['grp_id'] ;
			$expense_item = $params['expense_item'] ;
			$cls = new ClaimLimitClass($this->db) ;
			
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(ClaimLimitTable::C_CLAIM_GROUP_ID,$this->getParam($params,'grp_id',"")) ;
				$datas[] = $this->db->fieldValue(ClaimLimitTable::C_CLAIM_LIMIT_TYPE,$this->getParam($params,'limit_type',"")) ;
				$datas[] = $this->db->fieldValue(ClaimLimitTable::C_CLAIM_LIMIT_AMOUNT,$this->getParam($params,'limit_amt',"")) ;
				$datas[] = $this->db->fieldValue(ClaimLimitTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(ClaimLimitTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(ClaimLimitTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($expense_item,$datas,$grp_id) ;
				$this->sendJsonResponse(Status::Ok,"Claim Limit successfully updated to the system.",$grp_id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Claim Limit]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Claim Limit to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim Group ID and Expense Item ID if you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['grp_id']) && isset($params['expense_item'])) {
			$expense_item = $params['expense_item'] ;
			$grp_id = $params['grp_id'] ;
			$cls = new ClaimLimitClass($this->db) ;
			try {
				$cls->deleteRecord($expense_item,$grp_id) ; 
				$this->sendJsonResponse(Status::Ok,"Claim Limit successfully deleted from the system.",$grp_id,$this->type);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Limit from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else if (isset($params['expense_item'])) {
			$expense_item = $params['expense_item'] ;
			$cls = new ClaimLimitClass($this->db) ;
			try {
				$cls->deleteRecord($expense_item) ; 
				$this->sendJsonResponse(Status::Ok,"Claim Limit successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Limit from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim Group ID and/or Expense Item ID if you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($params) {
		$cls = new ClaimLimitClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimLimitTable::C_EXPENSE_ITEM_ID) . " and " . $this->db->fieldParam(ClaimLimitTable::C_ORG_ID) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimLimitTable::C_EXPENSE_ITEM_ID,$params['expense_item']) ;
		$datas[] = $this->db->valueParam(ClaimLimitTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ClaimLimitTable::C_CLAIM_GROUP_ID,$datas) ;
		$list = array() ;
		$ctrl = new ClaimGroup();
		foreach ($rows as $row) {
			$data = array() ;
			$data['expense_item'] = $row[ClaimLimitTable::C_EXPENSE_ITEM_ID] ;
			$data['grp'] = $ctrl->getDesc($row[ClaimLimitTable::C_CLAIM_GROUP_ID]) ;
			$data['grp_id'] = $row[ClaimLimitTable::C_CLAIM_GROUP_ID] ;
			$data['limit_type'] = $row[ClaimLimitTable::C_CLAIM_LIMIT_TYPE] ;
			$data['limit_amt'] = $row[ClaimLimitTable::C_CLAIM_LIMIT_AMOUNT] ;
			$list[] = $data;
			unset($data);
		}
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type);
		unset($ctrl);
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
	}
	private function getRecord($params=null) {
		if (isset($params['id']) && isset($params['expense_item'])) {
			$grp_id = $params['grp_id'] ;
			$expense_item = $params['expense_item'];
			$cls = new ClaimLimitClass($this->db) ;
			$row = $cls->getRecord($expense_item,$grp_id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Claim Group ID and Expense Item ID. Please try again.",$grp_id,$this->type);
			} else {
				$datas = array() ;
				$datas['expense_item'] = $row[ClaimLimitTable::C_EXPENSE_ITEM_ID] ;
				$datas['grp_id'] = $row[ClaimLimitTable::C_CLAIM_GROUP_ID] ;
				$datas['limit_type'] = $row[ClaimLimitTable::C_CLAIM_LIMIT_TYPE] ;
				$datas['limit_amt'] = $row[ClaimLimitTable::C_CLAIM_LIMIT_AMOUNT] ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Claim Limit id and/or line number. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/ClaimLimitView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	public function limitReached($expense_item,$claim_by) {
		$ctrl_ci = new ClaimItem();
		$ctrl_c = new Claim();
		$ctrl_cgm = new ClaimGroupEmployee();
		$cls = new ClaimLimitClass($this->db) ;
		$grp_id = $ctrl_cgm->getGroupId($claim_by);
		
		$row = $cls->getRecord($expense_item,$grp_id) ;
		$limit_type = $row[ClaimLimitTable::C_TYPE] ;
		$limit_amt = $row[ClaimLimitTable::C_AMOUNT] ;
		
		if ($limit_amt == "" || $limit_type == "") {
			return "Claim Limit has not been assigned to this expense item for your Claim Group." ;
		} else {
			Log::Write("Limit Amount" . $limit_amt);
		}
		
		$claim_group_id_list = array();
		$data = array();
		$data['id'] = $grp_id;
		$claim_group_id_list[] = $data;
		unset($data);
		$member_list = $ctrl_cgm->getAllMembersOfClaimGroups($claim_group_id_list);
		$claim_group_member_id_list = array();
		foreach ($member_list as $member) {
			$claim_group_member_id_list[] = (int) $member['member_id'];
			Log::Write("Employee : " . (int) $member['member_id']);
		}
		
		$claim_id_list = $ctrl_c->getAllClaimIdOfClaimGroupMembers($claim_group_member_id_list);
		$total_amount = (float) $ctrl_ci->getTotalAmountByExpenseItemId($expense_item,$claim_id_list,$limit_type);
		
		unset($row);
		unset($cls);
		unset($member_list);
		unset($claim_group_id_list);
		unset($claim_group_member_id_list);
		unset($claim_id_list);
		unset($grp_id);
		unset($limit_type);
		unset($ctrl_ci);
		unset($ctrl_c);
		unset($ctrl_cgm);
		
		if ($total_amount >= $limit_amt) {
			return "The claim limit for the current expense item has been reached by your claim group.";
		}
		return false;
	}
}
?>