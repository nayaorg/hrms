<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimHeaderClass.php") ;
require_once (PATH_MODELS . "claims/ClaimDetailClass.php") ;
require_once (PATH_MODELS . "admin/UserClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "claims/TravelPlanClass.php") ;
require_once (PATH_MODELS . "claims/CurrencyClass.php") ;
require_once (PATH_MODELS . "claims/ExpenseItemClass.php") ;

class ClaimApproval extends ControllerBase {
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
				case REQ_UPDATE:
					$this->updateRecord($params) ;
					break ;
				case REQ_GET:
					$this->getRecord($params) ;
					break ;
				case REQ_LIST:
					$this->getList($params) ;
					break ;
				case "CANCEL":
					$this->cancelRecord($params) ;
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
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimHeaderClass($this->db) ;
			
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				
				$this->updateItems($id, $params['item_data']);
				
				$this->updateStatusClaim($id);
				
				$this->sendJsonResponse(Status::Ok,"Claim detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Claim Approval]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Claim detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim id you wish to update. Please try again.","",$this->type);
		}
	}
	private function cancelRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimHeaderClass($this->db) ;
			
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_STATUS,ClaimStatus::Cancelled) ;
				$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				
				$this->sendJsonResponse(Status::Ok,"Claim detail is succesfully cancelled.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Claim Approval]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in cancelling Claim detail.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim id you wish to cancel. Please try again.","",$this->type);
		}
	}
	private function getList($params) {
		$cls = new ClaimHeaderClass($this->db) ;
		$cls_travel = new TravelPlanClass($this->db);
		$cls_emp = new EmployeeClass($this->db);
		$filter = $this->db->fieldParam(ClaimHeaderTable ::C_ORG_ID) ;
		$datas = array() ;
		$datas[] = $this->db->valueParam(ClaimHeaderTable ::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		
		$ctrl = new ClaimGroup($this->db) ;
		
		$cls_user = new UserClass($this->db);
		$row_user = $cls_user->getRecord($params['current_user']);
		
		$cls_claim_group = new ClaimGroupClass($this->db);
		$list = $cls_claim_group->getEmpListByManager($row_user[UserTable::C_EMP_ID]);
		
		if(!is_null($list) && count($list) > 0){
			$filter = $filter . " AND (";
			$first = true;
			foreach($list as $l){
				if($first) $first = false;
				else $filter .= " OR ";
				$filter .= " " . ClaimHeaderTable::C_EMP . " = " . $l;
			}
			$filter = $filter . ")";
		}
		
		$filter .= " AND " . ClaimHeaderTable::C_STATUS . " IN (" . ClaimStatus::Pending . ", " . ClaimStatus::Cancelled . ", " . ClaimStatus::Rejected . ", " . ClaimStatus::Approved . ")";
		
		if (isset($params['filter_conditions'])) {
			$filter = $filter . " AND " . $params['filter_conditions'];
		}
		
		$rows = $cls->getTable($filter,ClaimHeaderTable::C_ID,$datas) ;
		$list = array() ;
		foreach ($rows as $row) {
			$data = array() ;
			$data['id'] = $row[ClaimHeaderTable::C_ID] ;
			$data['desc'] = $row[ClaimHeaderTable::C_DESC];
			$data['type'] = $row[ClaimHeaderTable::C_TYPE] == 0 ? 'Personal' : 'Business' ;
			$dte = date_create($row[ClaimHeaderTable::C_DATE]);
			$data['date'] = date_format($dte, 'd/m/Y') ;
			$data['amount'] = $row[ClaimHeaderTable::C_AMOUNT] ;
			if ($data['amount'] == ".00") {
				$data['amount'] = "0.00";
			}
			$row_emp = $cls_emp->getRecord($row[ClaimHeaderTable::C_EMP]);
			$data['claim_by'] = $row_emp[EmployeeTable::C_NAME] ;
			
			$data['status'] = $cls->convertStatusStr($row[ClaimHeaderTable::C_STATUS]) ;
			
			if($row[ClaimHeaderTable::C_STATUS] == ClaimStatus::Pending || $row[ClaimHeaderTable::C_STATUS] == ClaimStatus::Approved){
				$data['status'] .= " (T:" . $cls->getCountItem('', $row[ClaimHeaderTable::C_ID]) . ", A:" . $cls->getCountItem('Approved', $row[ClaimHeaderTable::C_ID]) . ", R:" . $cls->getCountItem('Rejected', $row[ClaimHeaderTable::C_ID]) . ")";
			}
			
			$data['approved_amount'] = $row[ClaimHeaderTable::C_APPROVED_AMT] ;
			if ($data['approved_amount'] == ".00") {
				$data['approved_amount'] = "0.00";
			}
			if ($row[ClaimHeaderTable::C_TRAVEL] == "0") {
				$data['travel_plan'] = "None" ;
			} else {
				$row_travel = $cls_travel->getRecord($row[ClaimHeaderTable::C_TRAVEL]);
				$data['travel_plan'] = $row_travel[TravelPlanTable::C_TITLE] ;
			}
			$list[] = $data;
			unset($data);
		}
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type);
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
		unset($cls_user) ;
		unset($cls_emp) ;
		unset($cls_travel) ;
		unset($cls_claim_group);
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimHeaderClass($this->db) ;
			$cls_emp = new EmployeeClass($this->db);
			$cls_travel = new TravelPlanClass($this->db);
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Claim id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[ClaimHeaderTable::C_DESC];
				$datas['type'] = $row[ClaimHeaderTable::C_TYPE] == 0 ? 'Personal' : 'Business';
				$dte = date_create($row[ClaimHeaderTable::C_DATE]);
				$datas['date'] = date_format($dte, 'd/m/Y') ;
				$datas['amount'] = $row[ClaimHeaderTable::C_AMOUNT] ;
				if ($datas['amount'] == ".00") {
					$datas['amount'] = "0.00";
				}
				
				$row_emp = $cls_emp->getRecord($row[ClaimHeaderTable::C_EMP]);
				
				$datas['claim_by'] = $row_emp[EmployeeTable::C_NAME] ;
				$datas['status'] = $cls->convertStatusStr($row[ClaimHeaderTable::C_STATUS]);
				$datas['approved_amount'] = $row[ClaimHeaderTable::C_APPROVED_AMT] ;
				if ($datas['approved_amount'] == ".00") {
					$datas['approved_amount'] = "0.00";
				}
				if ($row[ClaimHeaderTable::C_TRAVEL] == "0") {
					$datas['travel_plan'] = "None" ;
				} else {
					$row_travel = $cls_travel->getRecord($row[ClaimHeaderTable::C_TRAVEL]);
					$datas['travel_plan'] = $row_travel[TravelPlanTable::C_TITLE] ;
				}
				
				$datas['item_list'] = $this->getItemList($id);
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
			unset($cls_emp) ;
			unset($cls_travel) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Claim id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "claims/ClaimApprovalView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getItemList($id){
		$cls = new ClaimDetailClass($this->db) ;
		$cls_exp = new ExpenseItemClass($this->db);
		$cls_cur = new CurrencyClass($this->db);
		$rows = $cls->getItems($id) ;
		$list = "";
		if (is_null($rows) || count($rows) == 0) {	
		}else{
			$list = "<tr>";
			$list .= "<th>No.</th>";
			$list .= "<th>Desc.</th>";
			$list .= "<th>Exp. Item</th>";
			$list .= "<th>Amount</th>";
			$list .= "<th>Currency</th>";
			$list .= "<th>DOC ID</th>";
			$list .= "<th>Status</th>";
			$list .= "</tr>";
			
			foreach($rows as $row){
				$line_no = $row[ClaimDetailTable::C_LINE_NO];
				$list .= "<tr>";
				$list .= "<td style='display:none' id='lblClaimID" . $line_no . "'>" . $id  . "</td>";
				$list .= "<td style='text-align:center' id='lblLineNO" . $line_no . "'>" . $line_no . "</td>";
				$list .= "<td style='width:200px' id='lblDescription" . $line_no . "'>" . $row[ClaimDetailTable::C_DESC]  . "</td>";
				
				$list .= "<td style='display:none' id='lblExpenseID" . $line_no . "'>" . $row[ClaimDetailTable::C_EXPENSE]  . "</td>";
				$row_exp = $cls_exp->getExpenseItem($row[ClaimDetailTable::C_EXPENSE]);
				
				$list .= "<td id='lblExpenseItem" . $line_no . "'>" . $row_exp[ExpenseItemTable::C_REF]  . "</td>";
				$list .= "<td style='text-align:right' id='lblAmount" . $line_no . "'>" . round($row[ClaimDetailTable::C_AMOUNT], 2)  . "</td>";
				
				$row_cur = $cls_cur->getRecord($row[ClaimDetailTable::C_CURRENCY]);
				
				$list .= "<td style='text-align:center' id='lblCurrency" . $line_no . "'>" . $row_cur[CurrencyTable::C_REF]  . "</td>";
				$list .= "<td id='lblDocID" . $line_no . "'>" . $row[ClaimDetailTable::C_DOC]  . "</td>";
				$list .= "<td style='width:100px'><select id='cobStatus" . $line_no . "'>";
				$list .= "<option value='0' " . ($row[ClaimDetailTable::C_STATUS] == 0 ? "selected" : "") . ">Pending</option>";
				$list .= "<option value='1' " . ($row[ClaimDetailTable::C_STATUS] == 1 ? "selected" : "") . ">Rejected</option>";
				$list .= "<option value='2' " . ($row[ClaimDetailTable::C_STATUS] == 2 ? "selected" : "") . ">Approved</option></select></td>";
				$list .= "</tr>";
			}
		}
		unset($cls);
		unset($cls_exp);
		unset($cls_cur);
		return $list;
	}
	
	private function updateItems($id,$items) {
		$cls = new ClaimDetailClass($this->db);
		if ($items != "") {
			$lines = explode("|",$items) ;
			
			for ($i= 0;$i < count($lines) ;$i++) {
				$r = explode('^', $lines[$i]);
				$datas = array() ;
				
				$line_no = $r[1];
				
				if($r[2] == '1'){
					$datas[] = $this->db->fieldValue(ClaimDetailTable::C_APPROVED_AMT, 0);
				}
				
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_STATUS , $r[2]);
				
				$cls->updateRecord($id, $datas, $line_no) ;
			}
		}
		unset($cls);
	}
	
	private function updateStatusClaim($id){
		$cls = new ClaimDetailClass($this->db);
		$cls_header = new ClaimHeaderClass($this->db);
		
		$row_header = $cls_header->getRecord($id);
		
		if($row_header[ClaimHeaderTable::C_STATUS] != ClaimStatus::Cancelled){
			$rows_item = $cls->getItems($id);
			
			$count_total = count($rows_item);
			$count_rejected = 0;
			$count_approved = 0;
			foreach($rows_item as $row_item){
				if($row_item[ClaimDetailTable::C_STATUS] == 2) $count_approved += 1;
				else if($row_item[ClaimDetailTable::C_STATUS] == 1) $count_rejected += 1;
			}
			
			$status = ClaimStatus::Pending;
			if($count_rejected + $count_approved < $count_total) $status = ClaimStatus::Pending;
			else if($count_rejected == $count_total) $status = ClaimStatus::Rejected;
			else if($count_approved > 0) $status = ClaimStatus::Approved;
			
			$datas = array();
			
			$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_STATUS,$status) ;
			$cls_header->updateRecord($id,$datas) ;
		}
		unset($cls);
		unset($cls_header);
	}
}
?>