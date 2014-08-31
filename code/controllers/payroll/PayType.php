<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/PayTypeClass.php") ;

class PayType extends ControllerBase {
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
		$cls = new PayTypeClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;

		$datas[] = $this->db->fieldValue(PayTypeTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_REF,$this->getParam($params,'refno',"")) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_TEXT,$this->getParam($params,'text',"")) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_WAGE_TYPE,$this->getParamInt($params,'wagetype',0)) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_TAX_TYPE,$this->getParamInt($params,'taxtype',0)) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_INCOME_TYPE,$this->getParamInt($params,'incometype',0)) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(PayTypeTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Pay Type successfully added to the system.",$id,$this->type);
			} else {
				Log::write('[PayType]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Pay Type to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new PayTypeClass($this->db) ;
			
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(PayTypeTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(PayTypeTable::C_REF,$this->getParam($params,'refno',"")) ;
				$datas[] = $this->db->fieldValue(PayTypeTable::C_TEXT,$this->getParam($params,'text',"")) ;
				$datas[] = $this->db->fieldValue(PayTypeTable::C_WAGE_TYPE,$this->getParamInt($params,'wagetype',0)) ;
				$datas[] = $this->db->fieldValue(PayTypeTable::C_TAX_TYPE,$this->getParamInt($params,'taxtype',0)) ;
				$datas[] = $this->db->fieldValue(PayTypeTable::C_INCOME_TYPE,$this->getParamInt($params,'incometype',0));
				$datas[] = $this->db->fieldValue(PayTypeTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(PayTypeTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(PayTypeTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Pay type detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[PayType]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating pay type detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the pay type id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new PayTypeClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Pay type successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[PayType]' . $e->getMessage()) ;
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting pay type record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the pay type id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new PayTypeClass($this->db) ;
		$filter = $this->db->fieldParam(PayTypeTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(PayTypeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,PayTypeTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[PayTypeTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[PayTypeTable::C_DESC] . "</td>" ;
			$list .= "<td>" . $row[PayTypeTable::C_REF] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editPayType(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deletePayType(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new PayTypeClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Pay Type id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[PayTypeTable::C_DESC];
				$datas['refno'] = $row[PayTypeTable::C_REF] ;
				$datas['text'] = $row[PayTypeTable::C_TEXT] ;
				$datas['wagetype'] = $row[PayTypeTable::C_WAGE_TYPE] ;
				$datas['taxtype'] = $row[PayTypeTable::C_TAX_TYPE] ;
				$datas['incometype'] = $row[PayTypeTable::C_INCOME_TYPE] ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Pay Type id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/PayTypeView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getIncomeType() {
		$arr = array() ;
		$arr[] = array ('code'=>'0','desc'=>'Income' );
		$arr[] = array ('code'=>'1','desc'=>'Deduction' ) ;
		return Util::createOptionValue($arr) ;
	}
	private function getTaxType() {
		$arr = array() ;
		$arr[] = array ('code'=>'0','desc'=>'No Tax') ;
		$arr[] = array ('code'=>'1','desc'=>'Salary' );
		$arr[] = array ('code'=>'2','desc'=>'Bonus' ) ;
		$arr[] = array ('code'=>'3','desc'=>'Director\'s Fee') ;
		$arr[] = array ('code'=>'4','desc'=>'Commission') ;
		$arr[] = array ('code'=>'5','desc'=>'Transport Allowance') ;
		$arr[] = array ('code'=>'6','desc'=>'Entertainment Allowance' );
		$arr[] = array ('code'=>'7','desc'=>'Other Allowance' ) ;
		//$arr[] = array ('code'=>'8','desc'=>'Retirement') ;
		$arr[] = array ('code'=>'9','desc'=>'Compensation') ;
		return Util::createOptionValue($arr) ;
	}
	private function getWageType() {
		$arr = array() ;
		$arr[] = array('code'=>'0','desc'=>'None') ;
		$arr[] = array('code'=>'1','desc'=>'Ordinary Wages') ;
		$arr[] = array('code'=>'2','desc'=>'Additional Wages') ;
		return Util::createOptionValue($arr) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new PayTypeClass($this->db) ;
		$filter = $this->db->fieldParam(PayTypeTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(PayTypeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,PayTypeTable::C_DESC,$params) ;
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			if ($row[PayTypeTable::C_INCOME_TYPE] == 0)
				$inc = "Income";
			else 
				$inc= "Deduction" ;
			if ($row[PayTypeTable::C_WAGE_TYPE] == 2)
				$wage = "Additional Wages" ;
			else if ($row[PayTypeTable::C_WAGE_TYPE] == 1)
				$wage = "Ordinary Wages";
			else
				$wage = "None";
			$tt = $row[PayTypeTable::C_TAX_TYPE] ;
			if ($tt == TaxType::Salary )
				$tax = "Salary" ;
			else if ($tt == TaxType::Bonus)
				$tax = "Bonus";
			else if ($tt == TaxType::Director)
				$tax = "Director's Fee";
			else if ($tt == TaxType::Commission)
				$tax = "Commission" ;
			else if ($tt == TaxType::TpAllowance)
				$tax = "Transport Allowance" ;
			else if ($tt == TaxType::EntAllowance)
				$tax = "Entertainment Allowance" ;
			else if ($tt == TaxType::OtherAllowance)
				$tax = "Other Allowance" ;
			else if ($tt == TaxType::Retirement)
				$tax = "Retirement" ;
			else if ($tt == TaxType::Compensation)
				$tax = "Compensation" ;
			else 
				$tax = "No Tax" ;
			$items[$i][] = $this->createPdfItem($row[PayTypeTable::C_ID],20) ;
			$items[$i][] = $this->createPdfItem($row[PayTypeTable::C_DESC],120) ;
			$items[$i][] = $this->createPdfItem($row[PayTypeTable::C_REF],60) ;
			$items[$i][] = $this->createPdfItem($row[PayTypeTable::C_TEXT],120) ;
			$items[$i][] = $this->createPdfItem($inc,120) ;
			$items[$i][] = $this->createPdfItem($wage,120) ;
			$items[$i][] = $this->createPdfItem($tax,120) ;
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		//for ($j = 1;$j < 100;$j++) {
			//$items = array() ;
			//$items[$i][] = $this->createPdfItem($j,20) ;
			//$items[$i][] = $this->createPdfItem("Description ". $j,120) ;
			//$items[$i][] = $this->createPdfItem("Ref " . $j,60) ;
			//$items[$i][] = $this->createPdfItem("",120) ;
			//$items[$nr] = "1";
			//$datas[] = $items ;
		//}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",20,0,"C","B");
		$cols[] = $this->createPdfItem("Description",120,0,"C","B") ;
		$cols[] = $this->createPdfItem("Ref",60,0,"C","B") ;
		$cols[] = $this->createPdfItem("Text",120,0,"C","B");
		$cols[] = $this->createPdfItem("Income Type",120,0,"C","B");
		$cols[] = $this->createPdfItem("Wage Type",120,0,"C","B") ;
		$cols[] = $this->createPdfItem("Tax Type",120,0,"C","B");
		$pdf = new ListPdf('L');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Pay Type Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('paytype.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>