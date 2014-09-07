<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_MODELS . "payroll/BankClass.php") ;
require_once (PATH_MODELS . "admin/CompanyOptions.php") ;

class Company extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = CompanyTable::C_ORG_ID ;
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
		$cls = new CompanyClass($this->db) ;
		$opts = new CompanyOptions() ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP];
		$op = array() ;
		$op[CompanyOptions::C_CONTACT] = $this->getContactOpt($params) ;
		$op[CompanyOptions::C_SETTING] = $this->getSettingOpt($params) ;
		$op[CompanyOptions::C_IRAS] = $this->getIrasOpt($params) ;
		$opts->setOption($op) ;
		$datas[] = $this->db->fieldValue(CompanyTable::C_DESC,$this->getParam($params,'name',"")) ;
		$datas[] = $this->db->fieldValue(CompanyTable::C_OPTIONS,$opts->getXml());
		$datas[] = $this->db->fieldValue(CompanyTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(CompanyTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(CompanyTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(CompanyTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(CompanyTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(CompanyTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			$this->sendJsonResponse(Status::Ok,"Company successfully added to the system.",$id,$this->type);
		} catch (Exception $e) {
			Log::write('[Company]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Exception, there is a error in database operation.","",$this->type) ;
		}
		unset($cls);
		unset($opts) ;
		unset($datas) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new CompanyClass($this->db) ;
			$opts = new CompanyOptions() ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				$op = array() ;
				$op[CompanyOptions::C_CONTACT] = $this->getContactOpt($params) ;
				$op[CompanyOptions::C_SETTING] = $this->getSettingOpt($params) ;
				$op[CompanyOptions::C_IRAS] = $this->getIrasOpt($params) ;
				$opts->setOption($op) ;
				$datas[] = $this->db->fieldValue(CompanyTable::C_DESC,$this->getParam($params,'name',"")) ;
				$datas[] = $this->db->fieldValue(CompanyTable::C_OPTIONS,$opts->getXml());
				$datas[] = $this->db->fieldValue(CompanyTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(CompanyTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(CompanyTable::C_MODIFY_DATE,$modifydate) ;

				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Company detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Company]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating company detail to the system.","",$this->type) ;
			}
			unset($cls) ;
			unset($datas) ;
			unset($opts) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the company id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new CompanyClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Company successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[Company]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting company record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the company id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new CompanyClass($this->db) ;
		$filter = $this->db->fieldParam(CompanyTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(CompanyTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,CompanyTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[CompanyTable::C_COY_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[CompanyTable::C_DESC] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editCompany(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteCompany(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new CompanyClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid company id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['name'] = $row[CompanyTable::C_DESC] ;
				
				if (!is_null($row[CompanyTable::C_OPTIONS]) && $row[CompanyTable::C_OPTIONS] != "") {
					$opt = new CompanyOptions() ;
					$opt->loadXml($row[CompanyTable::C_OPTIONS]) ;
					$op = $opt->getOption() ;
					$datas['name2'] = $op[CompanyOptions::C_CONTACT][CompanyOptions::C_CONT_NAME2] ;
					$datas['addr1'] = $op[CompanyOptions::C_CONTACT][CompanyOptions::C_CONT_ADDR1] ;
					$datas['addr2'] = $op[CompanyOptions::C_CONTACT][CompanyOptions::C_CONT_ADDR2] ;
					$datas['addr3'] = $op[CompanyOptions::C_CONTACT][CompanyOptions::C_CONT_ADDR3] ;
					$datas['addr4'] = $op[CompanyOptions::C_CONTACT][CompanyOptions::C_CONT_ADDR4] ;
					$datas['addr5'] = $op[CompanyOptions::C_CONTACT][CompanyOptions::C_CONT_ADDR5] ;
					$datas['tel'] = $op[CompanyOptions::C_CONTACT][CompanyOptions::C_CONT_TEL] ;
					$datas['fax'] = $op[CompanyOptions::C_CONTACT][CompanyOptions::C_CONT_FAX] ;
					
					$datas['cpfno'] = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_CPF_NO] ;
					$datas['cpfref'] = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_CPF_REF] ;
					$datas['refno'] = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_REF_NO] ;
					$datas['bank'] = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_BANK] ;
					$datas['acctno'] = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_BANK_ACCT] ;
					
					$datas['regno'] = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_TAX_ID] ;
					$datas['regtype'] = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_ID_TYPE] ;
					$datas['authname'] = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_NAME] ;
					$datas['authtitle'] = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_DEST] ;
					$datas['authtelno'] = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_CONTACT] ;
					$datas['egmdate'] = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_EGM] ;
					$datas['bonusdate'] = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_BONUS] ;
					unset($opt) ;
				} else {
					
				}
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
			
		} else {
			$this->sendJsonResponse(Status::Error,"Missing company id. Please try again.","",$this->type);
		}
		
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "admin/CompanyView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getBank() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(BankTable::C_TABLE, BankTable::C_ID, BankTable::C_DESC,array('code'=>'','desc'=>'--- Select a Bank ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getRegType() {
		$arr = array() ;
		$arr[] = array ('code'=>'','desc'=>'--- Select Registeration No Type ---') ;
		$arr[] = array ('code'=>'7','desc'=>'Business Registeration number' );
		$arr[] = array ('code'=>'8','desc'=>'Local Company Registeration number' ) ;
		$arr[] = array ('code'=>'A','desc'=>'IRAS Tax Reference number' ) ;
		$arr[] = array ('code'=>'I','desc'=>'IRAS Income Tax Reference number') ;
		$arr[] = array ('code'=>'G','desc'=>'IRAS GST number') ;
		$arr[] = array ('code'=>'U','desc'=>'Others Unique Entity Number') ;
		return Util::createOptionValue($arr) ;
	}
	private function getContactOpt($params) {
		$op = array() ;
		$op[CompanyOptions::C_CONT_NAME2] = $this->getParam($params,'name2',"") ;
		$op[CompanyOptions::C_CONT_ADDR1] = $this->getParam($params,'addr1',"") ;
		$op[CompanyOptions::C_CONT_ADDR2] = $this->getParam($params,'addr2',"") ;
		$op[CompanyOptions::C_CONT_ADDR3] = $this->getParam($params,'addr3',"") ;
		$op[CompanyOptions::C_CONT_ADDR4] = $this->getParam($params,'addr4',"") ;
		$op[CompanyOptions::C_CONT_ADDR5] = $this->getParam($params,'addr5',"") ;
		$op[CompanyOptions::C_CONT_TEL] = $this->getParam($params,'tel',"") ;
		$op[CompanyOptions::C_CONT_FAX] = $this->getParam($params,'fax',"") ;
		$op[CompanyOptions::C_CONT_EMAIL] = $this->getParam($params,'email',"");
		$op[CompanyOptions::C_CONT_WEB] = $this->getParam($params,'website',"");
		
		return $op ;
	}
	private function getSettingOpt($params) {
		$op = array() ;
		$op[CompanyOptions::C_SET_REF_NO] = $this->getParam($params,'refno',"");
		$op[CompanyOptions::C_SET_CPF_NO] = $this->getParam($params,'cpfno',"") ;
		$op[CompanyOptions::C_SET_CPF_REF] = $this->getParam($params,'cpfref',"");
		$op[CompanyOptions::C_SET_BANK] = $this->getParam($params,'bank',"") ;
		$op[CompanyOptions::C_SET_BANK_ACCT] = $this->getParam($params,'acctno',"");
		return $op ;
	}
	private function getIrasOpt($params) {
		$op = array() ;
		$op[CompanyOptions::C_IRAS_NAME] = $this->getParam($params,'authname',"");
		$op[CompanyOptions::C_IRAS_DEST] = $this->getParam($params,'authtitle',"") ;
		$op[CompanyOptions::C_IRAS_CONTACT] = $this->getParam($params,'authtelno',"") ;
		$op[CompanyOptions::C_IRAS_EGM] = $this->getParam($params,'egmdate',"") ;
		$op[CompanyOptions::C_IRAS_BONUS] = $this->getParam($params,'bonusdate',"") ;
		$op[CompanyOptions::C_IRAS_TAX_ID] = $this->getParam($params,'regno',"") ;
		$op[CompanyOptions::C_IRAS_ID_TYPE] = $this->getParam($params,'regtype',"") ;
		return $op ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new CompanyClass($this->db) ;
		$clsbank = new BankClass($this->db) ;
		$filter = $this->db->fieldParam(CompanyTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(CompanyTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,CompanyTable::C_DESC,$params) ;
		$w = 'width';
		$h = 'height';
		$a = 'align';
		$t = 'text';
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[CompanyTable::C_COY_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[CompanyTable::C_DESC],200) ;
			if (!is_null($row[CompanyTable::C_OPTIONS]) && $row[CompanyTable::C_OPTIONS] != "") {
				$opt = new CompanyOptions() ;
				$opt->loadXml($row[CompanyTable::C_OPTIONS]) ;
				$op = $opt->getOption() ;
				$b = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_BANK] ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_REF_NO],70) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_CPF_NO],100) ;
				$items[$i][] = $this->createPdfItem($clsbank->getDescription($b),80) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_EGM],60) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_DEST],100) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_CONTACT],80) ;
				
				$items[$i][] = $this->createPdfItem("",30,0,"L","0","1") ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_ID_TYPE],200) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_CPF_REF],70) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_TAX_ID],100) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_BANK_ACCT],80) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_BONUS],60) ;
				$items[$i][] = $this->createPdfItem($op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_NAME],100) ;
				$items[$i][] = $this->createPdfItem("",80) ;
				
			} else {
				$items[$i][] = $this->createPdfItem("",70) ;
				$items[$i][] = $this->createPdfItem("",100) ;
				$items[$i][] = $this->createPdfItem("",80) ;
				$items[$i][] = $this->createPdfItem("",60) ;
				$items[$i][] = $this->createPdfItem("",100) ;
				$items[$i][] = $this->createPdfItem("",80) ;
				
				$items[$i][] = $this->createPdfItem("",30,0,"L","0","1") ;
				$items[$i][] = $this->createPdfItem("",200) ;
				$items[$i][] = $this->createPdfItem("",70) ;
				$items[$i][] = $this->createPdfItem("",100) ;
				$items[$i][] = $this->createPdfItem("",80) ;
				$items[$i][] = $this->createPdfItem("",60) ;
				$items[$i][] = $this->createPdfItem("",100) ;
				$items[$i][] = $this->createPdfItem("",80) ;
			}
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30);
		$cols[] = $this->createPdfItem("Description",200) ;
		$cols[] = $this->createPdfItem("Ref",70) ;
		$cols[] = $this->createPdfItem("CPF No",100) ;
		$cols[] = $this->createPdfItem("Bank",80) ;
		$cols[] = $this->createPdfItem("EGM Date",60) ;
		$cols[] = $this->createPdfItem("Designation",100) ;
		$cols[] = $this->createPdfItem("Contact No",80) ;
		
		$cols[] = $this->createPdfItem("",30,0,"L","B","1");
		$cols[] = $this->createPdfItem("Tax Ref No Type",200,0,"L","B") ;
		$cols[] = $this->createPdfItem("CPF Ref",70,0,"L","B") ;
		$cols[] = $this->createPdfItem("Tax Ref No",100,0,"L","B") ;
		$cols[] = $this->createPdfItem("Acct. No",80,0,"L","B") ;
		$cols[] = $this->createPdfItem("Bonus Date",60,0,"L","B") ;
		$cols[] = $this->createPdfItem("Auth. Person",100,0,"L","B") ;
		$cols[] = $this->createPdfItem("",80,0,"L","B") ;
		
		$pdf = new ListPdf('L');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Company Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->setHeaderHeight(125) ;
		$pdf->render($datas) ;
		$pdf->Output('company.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>