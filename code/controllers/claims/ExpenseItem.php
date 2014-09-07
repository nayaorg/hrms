<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ExpenseItemClass.php") ;
require_once (PATH_MODELS . "claims/ExpenseGroupClass.php");
require_once (PATH_MODELS . "claims/ClaimGroupClass.php") ;
require_once (PATH_MODELS . "claims/ClaimLimitClass.php") ;

class ExpenseItem extends ControllerBase {
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = ExpenseItemTable::C_ORG_ID ;
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
		$cls = new ExpenseItemClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;

		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_REF,$this->getParam($params,'ref',"")) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_GROUP,$this->getParamInt($params,'group',0)) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_TYPE,$this->getParamInt($params,'type',0)) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_COY_ID,0) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_ORG_ID,$orgid) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ExpenseItemTable::C_CREATE_DATE,$modifydate) ;
		$limits = $this->getParam($params,'limits',"") ;
		try {
			$this->db->beginTran() ;
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->addLimits($id,$limits) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Expense Item successfully added to the system.",$id,$this->type);
			} else {
				$this->db->rollbackTran() ;
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Expense Item to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			$this->db->rollbackTran() ;
			Log::write('[Expense Item]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ExpenseItemClass($this->db) ;
			$clslimit = new ClaimLimitClass($this->db);
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(ExpenseItemTable::C_REF,$this->getParam($params,'ref',"")) ;
				$datas[] = $this->db->fieldValue(ExpenseItemTable::C_GROUP,$this->getParamInt($params,'group',0)) ;
				$datas[] = $this->db->fieldValue(ExpenseItemTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(ExpenseItemTable::C_TYPE,$this->getParamInt($params,'type',0)) ;
				$datas[] = $this->db->fieldValue(ExpenseItemTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(ExpenseItemTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(ExpenseItemTable::C_MODIFY_DATE,$modifydate) ;
				$limits = $this->getParam($params,'limits',"") ;
				$this->db->beginTran() ;
				$cls->updateRecord($id,$datas) ;
				$clslimit->deleteExpenseLimit($id) ;
				$this->addLimits($id,$limits) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Expense Item successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[Expense Item]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Expense Item to the system.","",$this->type) ;
			}
			unset($cls) ;
			unset($clslimit);
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Expense Item you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ExpenseItemClass($this->db) ;
			$clslimit = new ClaimLimitClass($this->db);
			try {
				$this->db->beginTran() ;
				$cls->deleteRecord($id) ; 
				$clslimit->deleteExpenseLimit($id) ;
				$this->db->commitTran() ; 
				$this->sendJsonResponse(Status::Ok,"Expense Item successfully deleted from the system.",$id,$this->type);
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Expense Item from the system.","",$this->type) ;
			}
			unset($cls) ;
			unset($clslimit) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Expense Item you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new ExpenseItemClass($this->db) ;
		$clsgrp = new ExpenseGroupClass($this->db);
		$filter = $this->db->fieldParam(ExpenseItemTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ExpenseItemTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ExpenseItemTable::C_ID,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[ExpenseItemTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[ExpenseItemTable::C_DESC] . "</td>" ;
			$list .= "<td>" . $clsgrp->getDescription($row[ExpenseItemTable::C_GROUP]) . "</td>" ;
			$list .= "<td>" . $row[ExpenseItemTable::C_REF] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editExpenseItem(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteExpenseItem(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		unset($grp) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ExpenseItemClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Expense Item. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['ref'] = $row[ExpenseItemTable::C_REF];
				if ($row[ExpenseItemTable::C_GROUP] == 0)
					$datas['group'] = "";
				else 
					$datas['group'] = $row[ExpenseItemTable::C_GROUP];
				$datas['desc'] = $row[ExpenseItemTable::C_DESC];
				$datas['type'] = $row[ExpenseItemTable::C_TYPE];
				$datas['limits'] = $this->getLimits($id) ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Expense Item. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "claims/ExpenseItemView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new ExpenseItemClass($this->db) ;
		$grp = new ExpenseGroupClass($this->db) ;
		$filter = $this->db->fieldParam(ExpenseItemTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ExpenseItemTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ExpenseItemTable::C_ID,$params) ;
		if (count($rows) > 0) {
			$i = 'items';
			$nr = 'newrow';
			$np = 'newpage';
			$datas = array() ;
			foreach ($rows as $row) {
				$items = array() ;
				$items[$i][] = $this->createPdfItem($row[ExpenseItemTable::C_ID],30) ;
				$items[$i][] = $this->createPdfItem($row[ExpenseItemTable::C_DESC],150) ;
				$items[$i][] = $this->createPdfItem($grp->getDescription($row[ExpenseItemTable::C_GROUP]),100) ;
				$items[$i][] = $this->createPdfItem($row[ExpenseItemTable::C_REF],80) ;
				$items[$nr] = "1" ;
				$datas[] = $items ;
				$firstpage = "0" ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",30,0,"C","B");
			$cols[] = $this->createPdfItem("Description",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Group",100,0,"C","B") ;
			$cols[] = $this->createPdfItem("Ref",80,0,"C","B") ;
			$pdf = new ListPdf('P');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Expense Item Listing") ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->render($datas) ;
			$pdf->Output('expenseitem.pdf', 'I');
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
	private function getLimitType() {
		$arr = array() ;
		$arr[] = array ('code'=>'0','desc'=>'Per Claim' );
		$arr[] = array ('code'=>'3','desc'=>'Monthly' ) ;
		$arr[] = array ('code'=>'4','desc'=>'Yearly' ) ;
		return Util::createOptionValue($arr) ;
	}
	private function getExpenseType() {
		$arr = array() ;
		$arr[] = array ('code'=>'0','desc'=>'Personal' );
		$arr[] = array ('code'=>'1','desc'=>'Business' ) ;
		$arr[] = array ('code'=>'2','desc'=>'Project' ) ;
		$arr[] = array ('code'=>'3','desc'=>'Other' ) ;
		return Util::createOptionValue($arr) ;
	}
	private function getExpenseGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ExpenseGroupTable::C_TABLE, ExpenseGroupTable::C_ID, ExpenseGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Expense Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getClaimGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ClaimGroupTable::C_TABLE, ClaimGroupTable::C_ID, ClaimGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Claim Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	
	private function addLimits($id,$limits) {
		$cls = new ClaimLimitClass($this->db) ;
		$amt = 0 ;
		if ($limits != "") {
			$lines = explode("|",$limits) ;
			for ($i= 0;$i < count($lines) ;$i++) {
				$line = explode(":",$lines[$i]) ;
				if (count($line) == 3) {
					if (is_numeric($line[0]) && is_numeric($line[1])) {
						if (is_numeric($line[2])) {
							$amt = $line[2] ;
						} else {
							$amt = 0 ;
						}
						
						$datas = array() ;
						$datas[] = $this->db->fieldValue(ClaimLimitTable::C_EXPENSE,$id);
						$datas[] = $this->db->fieldValue(ClaimLimitTable::C_GROUP,$line[0]) ;
						$datas[] = $this->db->fieldValue(ClaimLimitTable::C_TYPE,$line[1]) ;
						$datas[] = $this->db->fieldValue(ClaimLimitTable::C_AMOUNT,$amt);
						
						$cls->addRecord($datas) ;
					}
				}
			}
		}
		unset($cls) ;
	}
	private function getLimits($id) {
		$cls = new ClaimLimitClass($this->db) ;
		$drows = $cls->getExpenseLimit($id) ;
		$lines = "" ;
		$amt = "" ;
		if (!is_null($drows) || count($drows) > 0) {
			foreach ($drows as $drow) {
				if ($drow[ClaimLimitTable::C_AMOUNT] == 0)
					$amt = "" ;
				else 
					$amt = number_format($drow[ClaimLimitTable::C_AMOUNT], 2, '.', '') ;
				
				if (strlen($lines) > 0)
					$lines .= "|" ;
				$lines .= $drow[ClaimLimitTable::C_GROUP] . ":" . $drow[ClaimLimitTable::C_TYPE] . ":" . $amt ;
			}							
		}
		unset($cls) ;
		return $lines ;
	}
}
?>