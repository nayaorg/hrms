<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/EmployeePayClass.php") ;
require_once (PATH_MODELS . "payroll/EmployeePayTypeClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_TABLES . "payroll/PayTypeTable.php") ;
require_once (PATH_TABLES . "payroll/BankTable.php") ;
require_once (PATH_TABLES . "payroll/CpfTypeTable.php") ;

class EmployeePay extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = EmployeePayTable::C_ORG_ID ;
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
					echo $this->getList($params) ;
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
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$clspay = new EmployeePayClass($this->db) ;
			$clsemp = new EmployeeClass($this->db) ;
			$clstype = new EmployeePayTypeClass($this->db) ;
			try {
				$emprow = $clsemp->getRecord($id) ;
				if (!is_null($emprow) && count($emprow) > 0) {
					$coyid = $emprow[EmployeeTable::C_COY_ID] ;
					$start = $emprow[EmployeeTable::C_JOIN] ;
					$end = $emprow[EmployeeTable::C_RESIGN] ;
				} else {
					$this->sendJsonResponse(Status::Error,"Employee id not found in the system.",$id,$this->type) ;
					return ;
				}
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				$income = $this->getParam($params,'income',"") ;
				$fund = $this->getParam($params,'fund',"00000") . "00000" ;
				
				$fund = "" ;
				$fund .= $this->getParam($params,'sdl','0') ;
				$fund .= $this->getParam($params,'mbmf','0') ;
				$fund .= $this->getParam($params,'sinda','0') ;
				$fund .= $this->getParam($params,'cdac','0') ;
				$fund .= $this->getParam($params,'ecf','0') ;
				$fund .= "00000" ;
				$this->db->beginTran() ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_START,$this->getParamDate($params,'start',$start)) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_END, $this->getParamDate($params,'end',$end));
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_VALUE,$this->getParamNumeric($params,'value',0)) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_FUND,$fund) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_METHOD,$this->getParamInt($params,'method',0)) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_BANK,$this->getParamInt($params,'bank',0)) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_ACCT,$this->getParam($params,'acct',"")) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_CPF_TYPE,$this->getParamInt($params,'cpftype',0)) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_CPF_NO,$this->getParam($params,'cpfno',"")) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_MODIFY_DATE,$modifydate) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_ORG_ID,$this->orgid) ;
				$datas[] = $this->db->fieldValue(EmployeePayTable::C_COY_ID,$coyid) ;
				if ($clspay->isFound($id))
					$clspay->updateRecord($id,$datas) ;
				else {
					$datas[] = $this->db->fieldValue(EmployeePayTable::C_ID,$id) ;
					$clspay->addRecord($datas) ;
				}
				$clstype->deleteRecord($id) ;
				if ($income != "")
					$this->updateIncome($income,$id,$coyid) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Empooyee pay detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[EmployeePay]' . $e->getMessage()) ;
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating employee pay detail to the system.","",$this->type) ;
			}
			unset($clstype) ;
			unset($clsemp);
			unset($clspay) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id you wish to update. Please try again.","",$this->type);
		}
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$clsemp = new EmployeeClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clstype = new EmployeePayTypeClass($this->db) ;
			
			$emprow = $clsemp->getRecord($id) ;
			$coydesc = "" ;
			$deptdesc = "" ;
			if (!is_null($emprow) && count($emprow) > 0) {
				$name = $emprow[EmployeeTable::C_NAME] ;
				$coydesc = $clscoy->getDescription($emprow[EmployeeTable::C_COY_ID]) ;
				$deptdesc = $clsdept->getDescription($emprow[EmployeeTable::C_DEPT]) ;
				$clspay = new EmployeePayClass($this->db) ;
				$payrow = $clspay->getRecord($id) ;
				if (is_null($payrow)) {
					$dte = date_create($emprow[EmployeeTable::C_JOIN]) ;
					$start = date_format($dte,'d/m/Y') ;
					$dte = date_create($emprow[EmployeeTable::C_RESIGN]) ;
					if ($dte == date_create(MAX_DATE))
						$end = "" ;
					else
						$end = date_format($dte,'d/m/Y') ;
					$value = "" ;
					$sdl = "0" ;
					$mbmf = "0" ;
					$sinda = "0" ;
					$cdac = "0" ;
					$ecf = "0" ;
					$income = "" ;
					$bank = "" ;
					$acct = "" ;
					$cpf = "" ;
					$cpfno = $emprow[EmployeeTable::C_ID_NO] ;
					if (strlen($cpfno) > 9)
						$cpfno = substr($cpfno,0,9) ;
				} else {
					$datas = array() ;
					$datas['id'] = $id ;
					$dte =  date_create($payrow[EmployeePayTable::C_START]) ;
					$start = date_format($dte,'d/m/Y');
					$dte = date_create($payrow[EmployeePayTable::C_END]) ;
					if ($dte == date_create(MAX_DATE))
						$end = "" ;
					else
						$end = date_format($dte,'d/m/Y') ;
					if ($payrow[EmployeePayTable::C_BANK] == 0)
						$bank = "" ;
					else 
						$bank = $payrow[EmployeePayTable::C_BANK] ;
					if ($payrow[EmployeePayTable::C_CPF_TYPE] == 0)
						$cpf = "" ;
					else 
						$cpf = $payrow[EmployeePayTable::C_CPF_TYPE] ;
					$cpfno = $payrow[EmployeePayTable::C_CPF_NO];
					$acct = $payrow[EmployeePayTable::C_ACCT] ;
					$fund = $payrow[EmployeePayTable::C_FUND] ;
					$sdl = substr($fund,0,1)  ;
					$mbmf = substr($fund,1,1) ;
					$sinda = substr($fund,2,1) ;
					$cdac = substr($fund,3,1) ;
					$ecf = substr($fund,4,1) ;
					$value = number_format($payrow[EmployeePayTable::C_VALUE], 2, '.', '');
					
					$drows = $clstype->getRecord($id) ;
					$income = "" ;
					if (!is_null($drows) || count($drows) > 0) {
						foreach ($drows as $drow) {
							if (strlen($income) > 0)
								$income .= "|" ;
							$income .= $drow[EmployeePayTypeTable::C_TYPE] . ":" . number_format($drow[EmployeePayTypeTable::C_VALUE],2,'.','') ;
						}							
					}
				}
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['name'] = $name ;
				$datas['start'] = $start;
				$datas['end'] = $end ;
				$datas['sdl'] = $sdl  ;
				$datas['mbmf'] = $mbmf ;
				$datas['sinda'] = $sinda ;
				$datas['cdac'] = $cdac ;
				$datas['ecf'] = $ecf;
				$datas['value'] = $value;
				$datas['coy'] = $coydesc ;
				$datas['dept'] = $deptdesc ;
				$datas['income'] = $income ;
				$datas['bank'] = $bank ;
				$datas['acct'] = $acct ;
				$datas['cpftype'] = $cpf ;
				$datas['cpfno'] = $cpfno ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
				unset($cls) ;
				unset($row);
				unset($rows) ;
			} else {
				$this->sendJsonResponse(Status::Error,"Invalid Employee id. Please try again.","",$this->type) ;
			}
			unset($emp) ;
			unset($emprow) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Employee id. Please try again.","",$this->type);
		}
	}
	private function getList($datas=null) {
		$cls = new EmployeeClass($this->db) ;
		$coy = new CompanyClass($this->db) ;
		$dept = new DepartmentClass($this->db) ;
		$filter = "" ;
		$cond = "" ;
		$params = array() ;
		if (!is_null($datas) && count($datas) > 0) {
			if (isset($datas['coy']) && $datas['coy'] != "") {
				$filter = $this->db->fieldParam(EmployeeTable::C_COY_ID) ;
				$params[] = $this->db->valueParam(EmployeeTable::C_COY_ID,$datas['coy']) ;
				$cond = " and " ;
			}
			if (isset($datas['dept']) && $datas['dept'] != "") {
				$filter .= $cond . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
				$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$datas['dept']) ;
				$cond = " and " ;
			}
		}
		$filter .= $cond . $this->db->fieldParam(EmployeeTable::C_ORG_ID) ;
		
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,EmployeeTable::C_NAME,$params) ;
		$list = "" ;
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$id = $row[EmployeeTable::C_ID] ;
				$list .= "<tr>" ;
				$list .= "<td>" . $id . "</td>" ;
				$list .= "<td>" . $row[EmployeeTable::C_NAME] . "</td>" ;
				$list .= "<td>" . $coy->getDescription($row[EmployeeTable::C_COY_ID]) . "</td>" ;
				$list .= "<td>" . $dept->getDescription($row[EmployeeTable::C_DEPT]) . "</td>" ;
				$list .= "<td style='text-align:center'><a href='javascript:' onclick='editEmpPay(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
				$list .= "</tr>" ;
			}
		} else {
			$list .= "<tr><td colspan='5'>No Employee Found.</td></tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getCompany() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CompanyTable::C_TABLE, CompanyTable::C_COY_ID, CompanyTable::C_DESC,array('code'=>'','desc'=>'All Company'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getBank() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(BankTable::C_TABLE, BankTable::C_ID, BankTable::C_DESC,array('code'=>'','desc'=>'--- Select a Bank ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getCpfType() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CpfTypeTable::C_TABLE, CpfTypeTable::C_ID, CpfTypeTable::C_DESC,array('code'=>'','desc'=>'--- No CPF ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getPayMethod_unuse() {
		$arr = array() ;
		$arr[] = array ('code'=>'0','desc'=>'Monthly' );
		$arr[] = array ('code'=>'1','desc'=>'Daily' ) ;
		//$arr[] = array ('code'=>'2','desc'=>'Hourly' ) ;
		return Util::createOptionValue($arr) ;
	}
	private function getIncomeType($type) {
		$filter = array() ;
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$filter[] = array('field'=>PayTypeTable::C_INCOME_TYPE,'value'=>$type) ;
		if ($type == 0)
			$desc = "Pay Type" ;
		else 
			$desc = "Pay Type" ;
		$vls = $this->getValueList(PayTypeTable::C_TABLE, PayTypeTable::C_ID, PayTypeTable::C_DESC,array('code'=>'','desc'=>'--- Select a ' . $desc . ' ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function updateIncome($income,$empid,$coyid) {
		$clstype = new EmployeePayTypeClass($this->db) ;
		if ($income != "") {
			$types = explode("|",$income) ;
			for ($i= 0;$i < count($types) ;$i++) {
				$type = explode(":",$types[$i]) ;
				if (count($type) == 2) {
					if (is_numeric($type[0]) && is_numeric($type[1])) {
						$datas = array() ;
						$datas[] = $this->db->fieldValue(EmployeePayTypeTable::C_ID,$empid);
						$datas[] = $this->db->fieldValue(EmployeePayTypeTable::C_VALUE,$type[1]) ;
						$datas[] = $this->db->fieldValue(EmployeePayTypeTable::C_TYPE,$type[0]) ;
						$datas[] = $this->db->fieldValue(EmployeePayTypeTable::C_ORG_ID,$this->orgid) ;
						$datas[] = $this->db->fieldValue(EmployeePayTypeTable::C_COY_ID,$coyid) ;
						$clstype->addRecord($datas) ;
					}
				}
			}
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/EmployeePayView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
}
?>