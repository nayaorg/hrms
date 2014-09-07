<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimDetailClass.php") ;

class ClaimItem extends ControllerBase {
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
		$ctrl = new ClaimLimit();
		$limitReached = $ctrl->limitReached($params['expense_item'],$_SESSION[SE_USERID]);
		Log::Write("Limit Reached : " . $limitReached);
		if ($limitReached == false) {
			$cls = new ClaimDetailClass($this->db) ;
			$datas = array() ;
			$orgid = $_SESSION[SE_ORGID] ;
			$modifyby = $_SESSION[SE_USERID] ;
			$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
			$ws = $_SESSION[SE_REMOTE_IP] ;

			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_ID,$this->getParam($params,'id',"")) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_LINE_NO,$this->getParamInt($params,'line_no',0)) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_DESC,$this->getParam($params,'desc',"")) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_ITEM,$this->getParamInt($params,'expense_item',0)) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_AMOUNT,$this->getParamNumeric($params,'amt',0)) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_CURRENCY,$this->getParamInt($params,'curr',0)) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_APPROVED_AMT,0.00) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_DOC,$this->getParamInt($params,'doc_id',0)) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_COY_ID,0) ;
			$datas[] = $this->db->fieldValue(ClaimDetailTable::C_ORG_ID,$orgid) ;
			// $datas[] = $this->db->fieldValue(ClaimDetailTable::C_WS_ID,$ws) ;
			// $datas[] = $this->db->fieldValue(ClaimDetailTable::C_MODIFY_BY,$modifyby) ;
			// $datas[] = $this->db->fieldValue(ClaimDetailTable::C_MODIFY_DATE,$modifydate) ;
			// $datas[] = $this->db->fieldValue(ClaimDetailTable::C_CREATE_BY,$modifyby) ;
			// $datas[] = $this->db->fieldValue(ClaimDetailTable::C_CREATE_DATE,$modifydate) ;
			
			try {
				$id = $cls->addRecord($datas) ;
				$this->sendJsonResponse(Status::Ok,"Claim Item successfully added to the system.",$id,$this->type);
				$this->updateTotalAmountOfClaim($this->getParam($params,'id',""));
			} catch (Exception $e) {
				Log::write('[Claim Item]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,$limitReached,"",$this->type) ;
		}
		unset($ctrl);
	}
	private function updateRecord($params) {
		$ctrl = new ClaimLimit();
		$limitReached = $ctrl->limitReached($params['expense_item'],$_SESSION[SE_USERID]);
		if ($limitReached == false) {
			if (isset($params['id']) && isset($params['line_no'])) {
				$id = $params['id'] ;
				$line_no = $params['line_no'] ;
				$cls = new ClaimDetailClass($this->db) ;
				
				try {
					$datas = array() ;
					$modifyby = $_SESSION[SE_USERID] ;
					$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
					$ws = $_SESSION[SE_REMOTE_IP] ;
					
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_DESC,$this->getParam($params,'desc',"")) ;
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_EXPENSE,$this->getParamInt($params,'expense_item',0)) ;
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_AMOUNT,$this->getParamNumeric($params,'amt',0)) ;
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_CURRENCY,$this->getParamInt($params,'curr',0)) ;
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_DOC,$this->getParamInt($params,'doc_id',0)) ;
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_WS_ID,$ws) ;
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_MODIFY_BY,$modifyby) ;
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_MODIFY_DATE,$modifydate) ;
					$cls->updateRecord($id,$datas,$line_no) ;
					$this->sendJsonResponse(Status::Ok,"Claim Item successfully updated to the system.",$id,$this->type) ;
					$this->updateTotalAmountOfClaim($id);
				} catch (Exception $e) {
					Log::write('[Claim Item]' . $e->getMessage());
					$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Claim Item to the system.","",$this->type) ;
				}
				unset($cls) ;
			} else {
				$this->sendJsonResponse(Status::Error,"You must supply the Claim Item id and line number if you wish to update. Please try again.","",$this->type);
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,$limitReached,"",$this->type) ;
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id']) && isset($params['line_no'])) {
			$id = $params['id'] ;
			$line_no = $params['line_no'] ;
			$cls = new ClaimDetailClass($this->db) ;
			try {
				$cls->deleteRecord($id,$line_no) ; 
				$this->sendJsonResponse(Status::Ok,"Claim Item successfully deleted from the system.","",$this->type);
				$this->updateTotalAmountOfClaim($id);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Item from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim Item id and line number if you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($params) {
		$cls = new ClaimDetailClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimDetailTable::C_ID) . " and " . $this->db->fieldParam(ClaimDetailTable::C_ORG_ID) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimDetailTable::C_ID,$params['id']) ;
		$datas[] = $this->db->valueParam(ClaimDetailTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ClaimDetailTable::C_LINE_NO,$datas) ;
		$list = array() ;
		foreach ($rows as $row) {
			$data = array() ;
			$data['id'] = $row[ClaimDetailTable::C_ID] ;
			$data['line_no'] = $row[ClaimDetailTable::C_LINE_NO] ;
			$data['desc'] = $row[ClaimDetailTable::C_DESC] ;
			$ctrl = new ExpenseItemClass();
			$expense_item_name = $ctrl->getDescription($row[ClaimDetailTable::C_EXPENSE]);
			$data['expense_item'] = $expense_item_name;
			$data['amt'] = $row[ClaimDetailTable::C_AMOUNT] ;
			if ($data['amt'] == ".00") {
				$data['amt'] = "0.00";
			}
			$data['curr'] = $row[ClaimDetailTable::C_CURRENCY] ;
			$data['approved_amt'] = $row[ClaimDetailTable::C_APPROVED_AMT] ;
			if ($data['approved_amt'] == ".00") {
				$data['approved_amt'] = "0.00";
			}
			$data['doc_id'] = $row[ClaimDetailTable::C_DOC] ;
			$list[] = $data;
			unset($data);
		}
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type);
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
	}
	private function getRecord($params=null) {
		if (isset($params['id']) && isset($params['line_no'])) {
			$id = $params['id'] ;
			$line_no = $params['line_no'];
			$cls = new ClaimDetailClass($this->db) ;
			$row = $cls->getRecord($id,$line_no) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Claim Item id and line number. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $row[ClaimDetailTable::C_ID] ;
				$datas['line_no'] = $row[ClaimDetailTable::C_LINE_NO] ;
				$datas['desc'] = $row[ClaimDetailTable::C_DESC] ;
				$datas['expense_item'] = $row[ClaimDetailTable::C_EXPENSE] ;
				$datas['amt'] = $row[ClaimDetailTable::C_AMOUNT] ;
				$datas['curr'] = $row[ClaimDetailTable::C_CURRENCY] ;
				$datas['approved_amt'] = $row[ClaimDetailTable::C_APPROVED_AMT] ;
				$datas['doc_id'] = $row[ClaimDetailTable::C_DOC] ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Claim Item id and/or line number. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "claims/ClaimItemView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	public function getTotalAmountByExpenseItemId($expense_item_id,$claim_id_list=null,$limit_type=null) {
		Log::Write("Limit Type :" . $limit_type);
		$sql = "";
		$cls = new ClaimDetailClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimDetailTable::C_EXPENSE) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimDetailTable::C_EXPENSE,$expense_item_id) ;
		if (isset($limit_type)) {
			if ($limit_type == "Monthly" || $limit_type == 3 ) {
				$sql = "SELECT * FROM " . ClaimDetailTable::C_TABLE . " WHERE " . $filter . " AND DATEPART(month," . ClaimDetailTable::C_CREATE_DATE . ") = " . date("m") . " ORDER BY " . ClaimDetailTable::C_ID ;
			} else if ($limit_type == "Yearly"  || $limit_type == 4) {
				$sql = "SELECT * FROM " . ClaimDetailTable::C_TABLE . " WHERE " . $filter . " AND DATEPART(year," . ClaimDetailTable::C_CREATE_DATE . ") = " . date("Y") . " ORDER BY " . ClaimDetailTable::C_ID ;
			}
			
			Log::Write("TotalAmtByExpenses SQL : " . $sql);
			
			$rows = $this->db->getTable($sql,$datas) ;
		} else {
			$rows = $cls->getTable($filter,ClaimDetailTable::C_ID,$datas);
		}
		
		$total_amount = 0.00;
		foreach ($rows as $row) {
			if (isset($claim_id_list)) {
				if (in_array($row[ClaimDetailTable::C_ID],$claim_id_list)) {
					$total_amount += (double) $row[ClaimDetailTable::C_AMOUNT] ;
				}
			} else {
				$total_amount += (double) $row[ClaimDetailTable::C_AMOUNT] ;
			}
		}
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
		return $total_amount;
	}
	public function updateTotalAmountOfClaim($claim_id) {
		$cls = new ClaimDetailClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimDetailTable::C_ID) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimDetailTable::C_ID,$claim_id) ;
		$rows = $cls->getTable($filter,ClaimDetailTable::C_LINE_NO,$datas) ;
		$total_amount = 0.00;
		foreach ($rows as $row) {
			$total_amount += (float) $row[ClaimDetailTable::C_AMOUNT] ;
		}
		$ctrl = new Claim();
		$ctrl->updateTotalAmountOfClaim($claim_id,$total_amount);
		unset($ctrl);
		unset($rows) ;
		unset($cls) ;
	}
}
?>