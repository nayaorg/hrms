<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/ExpenseGroupClass.php") ;

class ExpenseGroup extends ControllerBase {
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = ExpenseGroupTable::C_ORG_ID ;
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
		$cls = new ExpenseGroupClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;

		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_REF,$this->getParam($params,'ref',"")) ;
		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_COY_ID,0) ;
		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_ORG_ID,$orgid) ;
		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_CREATE_DATE,$modifydate) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Expense Item Group successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Expense Item Group to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[Expense Item Group]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ExpenseGroupClass($this->db) ;
			
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_REF,$this->getParam($params,'ref',"")) ;
				$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(ExpenseGroupTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Expense Item Group successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Expense Item Group]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Expense Item Group to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Expense Item Group you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ExpenseGroupClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Expense Item Group successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Expense Item Group from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Expense Item Group you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new ExpenseGroupClass($this->db) ;
		$filter = $this->db->fieldParam(ExpenseGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ExpenseGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ExpenseGroupTable::C_ID,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[ExpenseGroupTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[ExpenseGroupTable::C_DESC] . "</td>" ;
			$list .= "<td>" . $row[ExpenseGroupTable::C_REF] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editExpenseGroup(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteExpenseGroup(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ExpenseGroupClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Expense Item Group. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['ref'] = $row[ExpenseGroupTable::C_REF];
				$datas['desc'] = $row[ExpenseGroupTable::C_DESC];
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Expense Item Group. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "claims/ExpenseGroupView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}

	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new ExpenseGroupClass($this->db) ;
		$filter = $this->db->fieldParam(ExpenseGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ExpenseGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ExpenseGroupTable::C_ID,$params) ;
		if (count($rows) > 0) {
			$i = 'items';
			$nr = 'newrow';
			$np = 'newpage';
			$datas = array() ;
			foreach ($rows as $row) {
				$items = array() ;
				$items[$i][] = $this->createPdfItem($row[ExpenseGroupTable::C_ID],30) ;
				$items[$i][] = $this->createPdfItem($row[ExpenseGroupTable::C_DESC],150) ;
				$items[$i][] = $this->createPdfItem($row[ExpenseGroupTable::C_REF],80) ;
				$items[$nr] = "1" ;
				$datas[] = $items ;
				$firstpage = "0" ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",30,0,"C","B");
			$cols[] = $this->createPdfItem("Description",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Ref",80,0,"C","B") ;
			$pdf = new ListPdf('P');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Expense Group") ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->render($datas) ;
			$pdf->Output('expensegroup.pdf', 'I');
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
	public function getName($id) {
		$cls = new ExpenseGroupClass($this->db) ;
		$row = $cls->getRecord($id) ;
		$name = $row[ExpenseGroupTable::C_DESC];
		unset($cls) ;
		return $name ;
	}
}
?>