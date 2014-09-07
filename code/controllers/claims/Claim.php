<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ClaimHeaderClass.php");
require_once (PATH_MODELS . "claims/ClaimDetailClass.php");
require_once (PATH_MODELS . "claims/ClaimDocumentClass.php");
require_once (PATH_MODELS . "claims/TravelPlanClass.php") ;
require_once (PATH_MODELS . "claims/ExpenseItemClass.php") ;
require_once (PATH_MODELS . "claims/CurrencyClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;

class Claim extends ControllerBase {
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = ClaimHeaderTable::C_ORG_ID ;
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
				case REQ_GET . '_EMP':
					$this->getEmployee($params) ;
					break ;
				case REQ_LIST:
					$this->getList($params) ;
					break ;
				case REQ_REPORT:
					$this->getReport($params) ;
					break ;
				case "emp":
					$this->getEmp($params);
					break;
				case 'DELETE':
					$this->deleteFiles($params) ;
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
		$cls = new ClaimHeaderClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		$claimdte = date_create('now')->format('Y-m-d') ;
		
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_TYPE,$this->getParamInt($params,'claim_type',0)) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_DATE,$this->getParamDate($params,'date',$claimdte)) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_AMOUNT,0.00) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_EMP,$this->getParamInt($params,'claim_by',0)) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_STATUS, ClaimStatus::Pending) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_APPROVED_AMT,0.00) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_TRAVEL,$this->getParamInt($params,'travel_plan',0)) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_COY_ID,0) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_ORG_ID,$orgid) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_CREATE_DATE,$modifydate) ;
		
		try {
			$this->db->beginTran() ;
			$id = $cls->addRecord($datas) ;
			
			if ($id > 0) {
			
				$this->addItems($id,$this->getParam($params,'items_data',""));
				$this->addDocs($id,$this->getParam($params,'docs_data',""));
				$this->moveFromTemp($id);
				
				$this->db->commitTran();
				$this->sendJsonResponse(Status::Ok,"Claim successfully added to the system.",$id,$this->type);
			} else {
				$this->db->rollbackTran();
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Claim to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			$this->db->rollbackTran();
			Log::write('[Claim]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimHeaderClass($this->db) ;
			//$clsDocs = new ClaimDocumentClass($this->db);
			$row = $cls->getRecord($id);
			try {
				if($row[ClaimHeaderTable::C_STATUS] == ClaimStatus::Pending){
					$datas = array() ;
					$modifyby = $_SESSION[SE_USERID] ;
					$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
					$ws = $_SESSION[SE_REMOTE_IP] ;
					$claimdte = date_create('now')->format('Y-m-d') ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_DESC,$this->getParam($params,'desc',"")) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_TYPE,$this->getParamInt($params,'claim_type',0)) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_DATE,$this->getParamDate($params,'date',$claimdte)) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_AMOUNT,$this->getParamNumeric($params,'amount',0)) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_EMP,$this->getParamInt($params,'claim_by',0)) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_STATUS,$this->getParamInt($params,'status',ClaimStatus::Pending)) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_APPROVED_AMT, $this->getParamNumeric($params,'approved_amount',0)) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_TRAVEL, $this->getParamInt($params,'travel_plan',0)) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_WS_ID,$ws) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_BY,$modifyby) ;
					$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_DATE,$modifydate) ;
					$cls->updateRecord($id,$datas) ;
					
					$this->deleteItems($id);
					$this->addItems($id,$this->getParam($params,'items_data',""));
					
					$this->deleteDocs($id);
					$this->addDocs($id,$this->getParam($params,'docs_data',""));
					$this->moveFromTemp($id);
					
					$this->sendJsonResponse(Status::Ok,"Claim detail successfully updated to the system.",$id,$this->type) ;
				}else {
					$status = strtolower($cls->convertStatusStr($row[ClaimHeaderTable::C_STATUS]));
					$this->sendJsonResponse(Status::Error,"The claim is already " . $status . ". Please try again.",$id,$this->type);
				}
			} catch (Exception $e) {
				Log::write('[Claim]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Claim detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimHeaderClass($this->db) ;
			try {
				$row = $cls->getRecord($id);
				if($row[ClaimHeaderTable::C_STATUS] == ClaimStatus::Pending){
					$cls->deleteRecord($id) ; 
					$this->deleteItems($id);
					$this->deleteDocs($id);
					$this->deleteDocsFiles($id);
					$this->sendJsonResponse(Status::Ok,"Claim successfully deleted from the system.","",$this->type);
				} else {
					$status = strtolower($cls->convertStatusStr($row[ClaimHeaderTable::C_STATUS]));
					$this->sendJsonResponse(Status::Error,"The claim is already " . $status . ". Please try again.",$id,$this->type);
				}
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Claim id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($params) {
		$cls = new ClaimHeaderClass($this->db) ;
		$cls_emp = new EmployeeClass($this->db) ;
		$datas = array() ;
		$filter = $this->db->fieldParam(ClaimHeaderTable::C_ORG_ID);
		$datas[] = $this->db->valueParam(ClaimHeaderTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		if ($_SESSION[SE_USERID] != 1) {
			$filter .= " and " . $this->db->fieldParam(ClaimHeaderTable::C_EMP);
			$datas[] = $this->db->valueParam(ClaimHeaderTable::C_EMP,$_SESSION[SE_USERID]) ;
		}
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
			$data['status'] = $cls->convertStatusStr($row[ClaimHeaderTable::C_STATUS]);
			$data['approved_amount'] = $row[ClaimHeaderTable::C_APPROVED_AMT];
			if ($data['approved_amount'] == ".00") {
				$data['approved_amount'] = "0.00";
			}
			$data['travel_plan'] = $row[ClaimHeaderTable::C_TRAVEL] ;
			$ctrl = new TravelPlan();
			if ($row[ClaimHeaderTable::C_TRAVEL] == "0") {
				$data['travel_plan_title'] = "None" ;
			} else {
				$data['travel_plan_title'] = $ctrl->getTitle($row[ClaimHeaderTable::C_TRAVEL]) ;
			}
			$list[] = $data;
			unset($data);
		}
		$this->sendJsonResponse(Status::Ok,"",$list,$this->type);
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
		unset($cls_emp) ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ClaimHeaderClass($this->db) ;
			$cls_emp = new EmployeeClass($this->db);
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Claim id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[ClaimHeaderTable::C_DESC];
				$datas['type'] = $row[ClaimHeaderTable::C_TYPE] ;
				$dte = date_create($row[ClaimHeaderTable::C_DATE]);
				$datas['date'] = date_format($dte, 'd/m/Y') ;
				$datas['amount'] = $row[ClaimHeaderTable::C_AMOUNT] ;
				
				$row_emp = $cls_emp->getRecord($row[ClaimHeaderTable::C_EMP]);
				
				$datas['claim_by'] = $row[ClaimHeaderTable::C_EMP] ;
				$datas['dept'] = $row_emp[EmployeeTable::C_DEPT] ;
				$datas['status'] = $row[ClaimHeaderTable::C_STATUS] ;
				$datas['approved_amount'] = $row[ClaimHeaderTable::C_APPROVED_AMT] ;
				$datas['travel_plan'] = $row[ClaimHeaderTable::C_TRAVEL] ;
				$datas['items'] = $this->getItems($id);
				
				$this->checkFolder($id);
				
				$datas['docs'] = $this->getDocs($id);
				
				//$this->copyToTemp($id);
				
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
			unset($cls_emp) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Claim id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "claims/ClaimView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new ClaimHeaderClass($this->db) ;
		$filter = $this->db->fieldParam(ClaimHeaderTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ClaimHeaderTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ClaimHeaderTable::C_ID,$params) ;
		if (count($rows) > 0) {
			$i = 'items';
			$nr = 'newrow';
			$np = 'newpage';
			$datas = array() ;
			foreach ($rows as $row) {
				$items = array() ;
				$items[$i][] = $this->createPdfItem($row[ClaimHeaderTable::C_ID],30) ;
				$items[$i][] = $this->createPdfItem($row[ClaimHeaderTable::C_DESC],150) ;
				$items[$i][] = $this->createPdfItem($row[ClaimHeaderTable::C_TYPE],50) ;
				$items[$i][] = $this->createPdfItem($row[ClaimHeaderTable::C_DATE],100) ;
				$items[$i][] = $this->createPdfItem($row[ClaimHeaderTable::C_AMOUNT],50) ;
				$items[$i][] = $this->createPdfItem($row[ClaimHeaderTable::C_EMP],60) ;
				$items[$i][] = $this->createPdfItem($row[ClaimHeaderTable::C_TRAVEL],50) ;
				$items[$nr] = "1" ;
				$datas[] = $items ;
				$firstpage = "0" ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",30,0,"C","B");
			$cols[] = $this->createPdfItem("Description",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Type",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Date",100,0,"C","B") ;
			$cols[] = $this->createPdfItem("Amount",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Claim By",60,0,"C","B") ;
			$cols[] = $this->createPdfItem("Travel Plan",50,0,"C","B") ;
			$pdf = new ListPdf('P');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Claim Listing") ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->render($datas) ;
			$pdf->Output('claim.pdf', 'I');
			unset($rows) ;
			unset($cls) ;
			unset($datas) ;
			unset($params) ;
			unset($items) ;
			unset($cols) ;
		} else {
			echo "<tr><td colspan='12'>No Record Found.</td></tr>" ;
			return;
		}
	}

	public function getDeptGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'--- Select Dept. ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	public function getTravelPlan() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(TravelPlanTable::C_TABLE, TravelPlanTable::C_ID, TravelPlanTable::C_DESC,array('code'=>'','desc'=>'----- Select a Travel Plan -----'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getExpenseItem() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ExpenseItemTable::C_TABLE, ExpenseItemTable::C_ID, ExpenseItemTable::C_DESC,array('code'=>'','desc'=>'----- Select an Expense -----'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getCurrency() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CurrencyTable::C_TABLE, CurrencyTable::C_ID, CurrencyTable::C_DESC,array('code'=>'','desc'=>'----- Select Currency -----'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	public function getAllClaimIdOfClaimGroupMembers($claim_group_member_id_list) {
		$cls = new ClaimHeaderClass($this->db) ;
		$filter = "" ;
		$datas = array() ;
		$rows = $cls->getTable($filter,ClaimHeaderTable::C_ID,$datas) ;
		$list = array() ;
		foreach ($rows as $row) {
			if (in_array($row[ClaimHeaderTable::C_EMP],$claim_group_member_id_list)) {
				$list[] = (int) $row[ClaimHeaderTable::C_ID];
				Log::Write("Claim ID :" . (int) $row[ClaimHeaderTable::C_ID]);
			}
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	public function updateTotalAmountOfClaim($claim_id,$total_amount) {
		$cls = new ClaimHeaderClass($this->db) ;
		
		try {
			$datas = array() ;
			$modifyby = $_SESSION[SE_USERID] ;
			$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
			$ws = $_SESSION[SE_REMOTE_IP] ;
			
			$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_AMOUNT,$total_amount) ;
			$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_STATUS, ClaimStatus::Pending) ;
			$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_APPROVED_AMT,0.00) ;
			$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_WS_ID,$ws) ;
			$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(ClaimHeaderTable::C_MODIFY_DATE,$modifydate) ;
			$cls->updateRecord($claim_id,$datas) ;
		} catch (Exception $e) {
			Log::write('[Claim] ' . $e->getMessage());
		}
		unset($cls) ;
	}
	
	private function addItems($id,$items) {
		$cls = new ClaimDetailClass($this->db);
		if ($items != "") {
			$lines = explode("|",$items) ;
			
			for ($i= 0;$i < count($lines) ;$i++) {
				$r = explode('^', $lines[$i]);
				$datas = array() ;
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_ID ,$id);
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_LINE_NO , $r[0]);
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_DESC , $r[1]);
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_EXPENSE , $r[2]);
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_STATUS , 0);
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_AMOUNT , $r[3]);
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_CURRENCY , $r[4]);
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_APPROVED_AMT , 0.00);
				$datas[] = $this->db->fieldValue(ClaimDetailTable::C_DOC , $r[5]);
				
				$cls->addRecord($datas) ;
			}
		}
		unset($cls);
	}
	
	private function addDocs($id,$docs) {
		$cls = new ClaimDocumentClass($this->db);
		if ($docs != "") {
			$lines = explode("|",$docs) ;
			
			for ($i= 0;$i < count($lines) ;$i++) {
				$r = explode('^', $lines[$i]);
				$datas = array() ;
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_ID ,$id);
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_DOC , $r[0]);
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_REF , $r[1]);
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_DESC , $r[2]);
				$datas[] = $this->db->fieldValue(ClaimDocumentTable::C_PATH , $r[3]);
				
				$cls->addRecord($datas) ;
			}
		}
		unset($cls);
	}
	
	private function getItems($id) {
		$cls = new ClaimDetailClass($this->db) ;
		$clsExpense = new ExpenseItemClass($this->db);
		$clsCurrency = new CurrencyClass($this->db);
		$rows = $cls->getItems($id) ;
		$lines = "" ;
		if (!is_null($rows) || count($rows) > 0) {
			foreach ($rows as $row) {
			
				$str = $row[ClaimDetailTable::C_LINE_NO];
				$str .= '^' . $row[ClaimDetailTable::C_DESC];
				$str .= '^' . $row[ClaimDetailTable::C_EXPENSE];
				
				$row_expense = $clsExpense->getRecord($row[ClaimDetailTable::C_EXPENSE]);
				
				$str .= '^' .  $row_expense[ExpenseItemTable::C_DESC];
				
				$str .= '^' . $row[ClaimDetailTable::C_AMOUNT];
				$str .= '^' . $row[ClaimDetailTable::C_CURRENCY];
				
				$row_currency = $clsCurrency->getRecord($row[ClaimDetailTable::C_CURRENCY]);
				
				$str .= '^' . $row_currency[CurrencyTable::C_DESC];
				
				$str .= '^' . $row[ClaimDetailTable::C_DOC];
				
				if (strlen($lines) > 0)
					$lines .= "|" ;
				$lines .= $str ;
			}
		}
		
		unset($rows) ;
		unset($cls) ;
		unset($clsExpense) ;
		unset($clsCurrency) ;
		return $lines;
	}
	
	private function getDocs($id) {
		$cls = new ClaimDocumentClass($this->db) ;
		$rows = $cls->getDocs($id) ;
		$lines = "" ;
		if (!is_null($rows) || count($rows) > 0) {
			foreach ($rows as $row) {
			
				$str = $row[ClaimDocumentTable::C_DOC];
				$str .= '^' . $row[ClaimDocumentTable::C_REF];
				$str .= '^' . $row[ClaimDocumentTable::C_DESC];
				$str .= '^' . $row[ClaimDocumentTable::C_PATH];
				$str .= '^' . $id;
				
				
				if (strlen($lines) > 0)
					$lines .= "|" ;
				$lines .= $str ;
			}
		}
		
		unset($rows) ;
		unset($cls) ;
		return $lines;
	}
	
	private function checkFolder($id) {
		$cls = new ClaimDocumentClass($this->db) ;
		$rows = $cls->getDocs($id) ;
		$lines = "" ;
		if (!is_null($rows) || count($rows) > 0) {
			foreach ($rows as $row) {
				if(!file_exists(PATH_CLAIMS . $id . '/' . $row[ClaimDocumentTable::C_PATH])){
					$cls->deleteDoc($id, $row[ClaimDocumentTable::C_DOC]);
				}
			}
		}
		
		unset($rows) ;
		unset($cls) ;
		return $lines;
	}
	
	private function deleteItems($id) {
		$cls = new ClaimDetailClass($this->db) ;
		try {
			$cls->deleteRecord($id) ; 
			//$this->sendJsonResponse(Status::Ok,"All Claim Group Head successfully deleted from the system.","",$this->type);
		} catch (Exception $e) {
			//$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Group Head from the system.","",$this->type) ;
		}
	}
	
	private function deleteDocs($id) {
		$cls = new ClaimDocumentClass($this->db) ;
		try {
			$cls->deleteRecord($id) ; 
			
		} catch (Exception $e) {
			//$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Claim Group Head from the system.","",$this->type) ;
		}
	}
	private function deleteDocsFiles($id){
		$directory = PATH_CLAIMS . $id . '/';
	
		$listOfFile = scandir($directory);
		
		foreach($listOfFile as $file){
			if($file != '.' && $file != '..')
				unlink($directory . $file);
		}
		rmdir(PATH_CLAIMS . $id);
	}
	private function deleteFiles($params){
		$temp = $this->getParam($params,'id',"") == '-1' ? 'temp' : $this->getParam($params,'id',"");
		$fn = $this->getParam($params,'n',"");
		$directory = PATH_CLAIMS . $temp . '/';
		unlink($directory . $fn);
		
		$this->sendJsonResponse(Status::Ok,"",$this->getParam($params,'idx',""),$this->type) ;
	}
	
	private function moveFromTemp($id){
		$directory = PATH_CLAIMS . $id . '/';
		$directorytemp = PATH_CLAIMS . 'temp/';
		if (!file_exists($directory)) {
			mkdir($directory, 0777, true);
		}
		
		$listOfFile = scandir($directorytemp);
		
		foreach($listOfFile as $file){
			if($file != '.' && $file != '..')
				rename($directorytemp . $file, $directory . $file);
		}
	}
	
	private function copyToTemp($id){
		$directory = PATH_CLAIMS . $id . '/';
		$directorytemp = PATH_CLAIMS . 'temp/';
		if (!file_exists($directorytemp)) {
			mkdir($directorytemp, 0777, true);
		}
		
		$listOfFile = scandir($directory);
		
		foreach($listOfFile as $file){
			if($file != '.' && $file != '..'){
				copy($directory . $file, $directorytemp . $file);
			}
		}
	}
	private function getEmp($params){
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeClass($this->db) ;
			$row = $cls->getRecord($id) ;
			
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid employee id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['name'] = $row[EmployeeTable::C_NAME];
				
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id. Please try again.","",$this->type);
		}
	}

	private function getEmployee($params) {
		$id = $params['id'] ;
		
		$cls = new EmployeeClass($this->db) ;
		if($id == 0){
			$rows = $cls->getTable() ;
		}else {
			$filter = $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$datas = array() ;
			$datas[] = $this->db->valueParam(EmployeeTable::C_DEPT,$id) ;
			$rows = $cls->getTable($filter,EmployeeTable::C_NAME,$datas) ;		
		}
		$lines = "" ;
		if (!is_null($rows) || count($rows) > 0) {
			foreach ($rows as $row) {
				
				if (strlen($lines) > 0)
					$lines .= "|" ;
				$lines .= $row[EmployeeTable::C_ID] . ":" . $row[EmployeeTable::C_NAME] ;
			}							
		}
		
		$datas = array() ;
		$datas['empList'] =  $lines ;
		
		$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
	}
}
?>