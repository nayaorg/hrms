<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/PayHeaderClass.php") ;
require_once (PATH_MODELS . "payroll/PayCpfClass.php") ;
require_once (PATH_MODELS . "payroll/PayFundLevyClass.php") ;
require_once (PATH_MODELS . "payroll/EmployeePayClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_MODELS . "admin/CompanyOptions.php") ;
require_once (PATH_MODELS . "payroll/BankClass.php") ;

class PayList extends ControllerBase {
	
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = PayHeaderTable::C_ORG_ID ;
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
				case REQ_EXPORT:
					$this->getExport($params) ;
					break ;
				case REQ_REPORT:
					$this->getReport($params) ;
					break ;
				case REQ_LIST:
					echo $this->getList($params) ;
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
	
	private function getList($datas=null) {
		$month = $this->getParamInt($datas,'month',0) ;
		$year = $this->getParamInt($datas,'year',0) ;
		if ($month == 0 || $year == 0) {
			echo "<h2>Invalid Pay date.</h2>" ;
			return ;
		} 
		$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
		$filter = "" ;
		$coyid = $this->getParam($datas,'coy',"") ;
		$deptid = $this->getParam($datas,'dept',"") ;
		$rows = $this->getData($date,$coyid,$deptid) ;
		$list = "" ;
		if (count($rows) > 0) {
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clsfund = new PayFundLevyClass($this->db) ;
			$clscpf = new PayCpfClass($this->db) ;
			foreach ($rows as $row) {
				$id = $row[PayHeaderTable::C_EMP_ID] ;
				$basic = $row[PayHeaderTable::C_BASIC] ;
				$income = $row[PayHeaderTable::C_INCOME] ;
				$deduct = $row[PayHeaderTable::C_DEDUCT];
				$crows = $clscpf->getRecord($id,$date) ;
				if (is_null($crows) || count($crows) == 0) {
					$cpfemp = 0 ;
					$cpfcoy = 0 ;
				} else {
					$cpfemp = $crows[0][PayCpfTable::C_CPF_EMP] ;
					$cpfcoy = $crows[0][PayCpfTable::C_CPF_COY] ;
				}
				
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;

				$funds = $clsfund->getTotal($id,$date,$_SESSION[SE_ORGID]) ;
				$netpay = $basic + $income - $deduct - $funds - $cpfemp ;
				$list .= "<tr>" ;
				$list .= "<td>" . $id . "</td>" ;
				$list .= "<td>" . $empname . "</td>" ;
				$list .= "<td>" . $clscoy->getDescription($row[EmployeeTable::C_COY_ID]) . "</td>" ;
				$list .= "<td>" . $clsdept->getDescription($deptid) . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($basic + $income, 2, '.', ',') . "</td>";
				$list .= "<td style='text-align:right'>" . number_format($deduct, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($funds,2,'.',',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($cpfemp,2,'.',',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($netpay, 2, '.', ',') . "</td>" ;
				//$list .= "<td style='text-align:center'><a href='javascript:' onclick='detailPayList(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
				$list .= "</tr>" ;
			}
			unset($clsfund) ;
			unset($clscoy) ;
			unset($clsdept) ;
		} else {
			$list .= "<tr><td colspan='9'>No Employee Found.</td></tr>" ;
		}
		unset($rows) ;
		
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
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/PayListView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getData($date,$coyid,$deptid) {
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.")
			. " and " . $this->db->fieldParam(PayHeaderTable::C_END) ;
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$params[] = $this->db->valueParam(PayHeaderTable::C_END,$date) ;
		if ($coyid != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_COY_ID,"=","e.") ;
			$params[] = $this->db->valueParam(EmployeeTable::C_COY_ID,$coyid) ;
		}
		if ($deptid != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$deptid) ;
		}
		$sql = " select h.*,e." . EmployeeTable::C_NAME . ",e." . EmployeeTable::C_DEPT
			. ",e." . EmployeeTable::C_COY_ID
			. " from " . PayHeaderTable::C_TABLE . "  as h "
			. " left join " . EmployeeTable::C_TABLE . " as e "
			. " on h." . PayHeaderTable::C_EMP_ID . " = e." . EmployeeTable::C_ID
			. " where " . $filter 
			. " order by e." . EmployeeTable::C_COY_ID . ",e." . EmployeeTable::C_DEPT . ",e." . EmployeeTable::C_NAME ;

		return $this->db->getTable($sql,$params) ;
	}
	private function getExport($params=null) {
		$month = $this->getParamInt($params,'month',0) ;
		$year = $this->getParamInt($params,'year',0) ;
		if ($month == 0 || $year == 0) {
			echo "<h2>Invalid Pay date.</h2>" ;
			return ;
		}
		$coyid = $this->getParam($params,'coy',"") ;
		if ($coyid == "") {
			echo "<h2>Missing company id.</h2>" ;
			return ;
		}
		$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
		$filter = "" ;
		$deptid = $this->getParam($params,'dept',"") ;
		$rows = $this->getData($date,$coyid,$deptid) ;
		
		if (count($rows) > 0) {
			$datas = "" ;
			$expfile = "" ;
			$acctno = "" ;
			$clsfund = new PayFundLevyClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clscpf = new PayCpfClass($this->db) ;
			$clsbank = new BankClass($this->db) ;
			$clspay = new EmployeePayClass($this->db) ;
			$coyrow = $clscoy->getRecord($coyid) ;
			if (!is_null($coyrow)) {
				if (!is_null($coyrow[CompanyTable::C_OPTIONS]) && $coyrow[CompanyTable::C_OPTIONS] != "") {
					$opt = new CompanyOptions() ;
					$opt->loadXml($coyrow[CompanyTable::C_OPTIONS]) ;
					$op = $opt->getOption() ;
					$bank = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_BANK] ;
					$acctno = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_BANK_ACCT] ;
					unset($opt) ;
					if ($bank != "") {
						$bankrow = $clsbank->getRecord($bank) ;
						if (!is_null($bankrow)) {
							$expfile = trim($bankrow[BankTable::C_FILE]) ;
							$acctno = trim($bankrow[BankTable::C_ACCT]) ;
						}
					}
				}
			}
			$datas .= $expfile . $acctno . "\r\n" ;
			foreach ($rows as $row) {
				$id = $row[PayHeaderTable::C_EMP_ID] ;
				$basic = $row[PayHeaderTable::C_BASIC] ;
				$income = $row[PayHeaderTable::C_INCOME] ;
				$deduct = $row[PayHeaderTable::C_DEDUCT];
				$crows = $clscpf->getRecord($id,$date) ;
				if (is_null($crows) || count($crows) == 0) {
					$cpfemp = 0 ;
					$cpfcoy = 0 ;
				} else {
					$cpfemp = $crows[0][PayCpfTable::C_CPF_EMP] ;
					$cpfcoy = $crows[0][PayCpfTable::C_CPF_COY] ;
				}
				$prows = $clspay->getRecord($id) ;
				if (is_null($prows))
					$acctno = "" ;
				else 
					$acctno = $prows[EmployeePayTable::C_ACCT];
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;
				$funds = $clsfund->getTotal($id,$date,$_SESSION[SE_ORGID]); ;
				$netpay = $basic + $income - $deduct - $funds - $cpfemp ;
				$datas .= $acctno . "," . $empname . "," . number_format($netpay,2,'.','') . "\r\n" ;
			}
			unset($clsfund) ;
			unset($clscoy) ;
			unset($clscpf) ;
			unset($clsbank) ;
			unset($clspay) ;
			if ($expfile == "")
				$expfile = "bank.txt" ;
			header('Content-disposition: attachment; filename='. $expfile);
			header('Content-type: text/plain');
			header("Content-Length: ".strlen($datas));
			echo $datas ;
		} else {
			echo "<tr><td colspan='12'>No Record Found.</td></tr>" ;
			return;
		}
		unset($rows) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		$month = $this->getParamInt($params,'month',0) ;
		$year = $this->getParamInt($params,'year',0) ;
		if ($month == 0 || $year == 0) {
			echo "<h2>Invalid Pay date.</h2>" ;
			return ;
		}
		$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
		$filter = "" ;
		$coyid = $this->getParam($params,'coy',"") ;
		$deptid = $this->getParam($params,'dept',"") ;
			
		$rows = $this->getData($date,$coyid,$deptid) ;
		
		if (count($rows) > 0) {
			$datas = array() ;
			$nr = 'newrow';
			$np = 'newpage';
			$ph = "pageheader";
			$i = 'items';
			$clsfund = new PayFundLevyClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clscpf = new PayCpfClass($this->db) ;
			$coyid = -1 ;
			foreach ($rows as $row) {
				$id = $row[PayHeaderTable::C_EMP_ID] ;
				$basic = $row[PayHeaderTable::C_BASIC] ;
				$income = $row[PayHeaderTable::C_INCOME] ;
				$deduct = $row[PayHeaderTable::C_DEDUCT];
				$crows = $clscpf->getRecord($id,$date) ;
				if (is_null($crows) || count($crows) == 0) {
					$cpfemp = 0 ;
					$cpfcoy = 0 ;
				} else {
					$cpfemp = $crows[0][PayCpfTable::C_CPF_EMP] ;
					$cpfcoy = $crows[0][PayCpfTable::C_CPF_COY] ;
				}
				
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;
				$funds = $clsfund->getTotal($id,$date,$_SESSION[SE_ORGID]); ;
				$netpay = $basic + $income - $deduct - $funds - $cpfemp ;
				$items = array() ;
				if ($coyid != $row[EmployeeTable::C_COY_ID]) {
					$items[$np] = "1" ;
					$coyid = $row[EmployeeTable::C_COY_ID] ;
					$items[$ph]['tag'] = '%=COMPANY=%' ;
					$items[$ph]['text'] = $clscoy->getDescription($coyid) ;	
				}
				
				$items[$i][] = $this->createPdfItem($id,30) ;
				$items[$i][] = $this->createPdfItem($empname,150) ;
				//$items[$i][] = $this->createPdfItem($clscoy->getDescription($row[EmployeeTable::C_COY_ID]),150) ;
				$items[$i][] = $this->createPdfItem($clsdept->getDescription($deptid),150) ;
				$items[$i][] = $this->createPdfItem(number_format($basic + $income ,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($deduct,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($funds,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($cpfemp, 2, '.', ','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($netpay,2,'.',','),70,0,"R");
				$items[$nr] = "1" ;
				$datas[] = $items ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",30,0,"C","B");
			$cols[] = $this->createPdfItem("Name",150,0,"C","B") ;
			//$cols[] = $this->createPdfItem("Company",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Department",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Income",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Deduction",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Fund/Levy",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("CPF Emp",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Net Pay",70,0,"C","B") ;
			$headers = array() ;
			$headers[] = "Compnay : %=COMPANY=%" ;
			$pdf = new ListPdf('L');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Pay Listing - " . Util::getMonthShortName($month) . "/" . $year) ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->setHeaders($headers) ;
			$pdf->setHeaderHeight(135) ;
			$pdf->render($datas) ;
			$pdf->Output('paylist.pdf', 'I');
			unset($clsfund) ;
			unset($clscoy) ;
			unset($clsdept) ;
			unset($clscpf) ;
			unset($datas) ;
			unset($params) ;
			unset($items) ;
			unset($cols) ;
		} else {
			echo "<tr><td colspan='12'>No Record Found.</td></tr>" ;
			return;
		}
		unset($rows) ;
	}
}
?>