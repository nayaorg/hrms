<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/PayHeaderClass.php") ;
require_once (PATH_MODELS . "payroll/PayFundLevyClass.php") ;
require_once (PATH_MODELS . "payroll/PayDetailClass.php") ;
require_once (PATH_MODELS . "payroll/PayCpfClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_MODELS . "payroll/PayTypeClass.php") ;

class PaySlip extends ControllerBase {
	
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
		include (PATH_VIEWS . "payroll/PaySlipView.php") ;
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
			. " from " . PayHeaderTable::C_TABLE . "  as h "
			. " left join " . EmployeeTable::C_TABLE . " as e "
			. " on h." . PayHeaderTable::C_EMP_ID . " = e." . EmployeeTable::C_ID
			. " where " . $filter 
			. " order by h." . PayHeaderTable::C_COY_ID . ",e." . EmployeeTable::C_DEPT . ",e." . EmployeeTable::C_NAME ;

		return $this->db->getTable($sql,$params) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'PagePdf.php');
		$month = $this->getParamInt($params,'month',0) ;
		$year = $this->getParamInt($params,'year',0) ;
		if ($month == 0 || $year == 0) {
			echo "<h2>Invalid Pay date.</h2>" ;
			return ;
		}
		$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
		$filter = "" ;
		$coyid = $this->getParam($params,'coy',"");
		$deptid = $this->getParam($params,'dept',"") ;
		$rows = $this->getData($date,$coyid,$deptid) ;
		
		if (count($rows) > 0) {
			$datas = array() ;
			$w = 'width' ;
			$h = 'height' ;
			$a = 'align' ;
			$t = 'text' ;
			$np = 'newpage';
			$nr = 'newrow' ;
			$i = 'items';
			$clsfund = new PayFundLevyClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clsdetail = new PayDetailClass($this->db) ;
			$clstype = new PayTypeClass($this->db) ;
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
				$funds = $clsfund->getTotal($id,$date,$_SESSION[SE_ORGID]); ;
				$netpay = $basic + $income - $deduct - $funds - $cpfemp ;
				$drows = $clsdetail->getRecord($id,$date,PayDetailTable::C_TYPE) ;
				$items = array() ;
				$items[$np] = "1" ;
				$items[$i][] = $this->createItem("Pay Date",100) ;
				$items[$i][] = $this->createItem(":",10) ; 
				$items[$i][] = $this->createItem($date,200,0,"L","1") ;
				$items[$i][] = $this->createItem("Employee",100);
				$items[$i][] = $this->createItem(":",10);
				$items[$i][] = $this->createItem($id . " - " . $empname,200,0,"L","1") ;
				$items[$i][] = $this->createItem("Company",100) ;
				$items[$i][] = $this->createItem(":",10) ;
				$items[$i][] = $this->createItem($clscoy->getDescription($row[EmployeeTable::C_COY_ID]),200,0,"L","1") ;
				$items[$i][] = $this->createItem("Department",100);
				$items[$i][] = $this->createItem(":",10);
				$items[$i][] = $this->createItem($clsdept->getDescription($deptid),200,0,"L","1") ;
				$items[$i][] = $this->createItem("Income",100,0,"L","1") ;
				$items[$i][] = $this->createItem("   Basic Pay",100);
				$items[$i][] = $this->createItem(":",10);
				$items[$i][] = $this->createItem(number_format($basic ,2,'.',','),60,0,"R","1");
				if (!is_null($drows) && count($drows) > 0) {
					foreach ($drows as $drow) {
						if ($drow[PayDetailTable::C_INCOME_TYPE] == 0 && $drow[PayDetailTable::C_TYPE] != 0) {
							$items[$i][] = $this->createItem("   " . $clstype->getDescription($drow[PayDetailTable::C_TYPE]),100);
							$items[$i][] = $this->createItem(":",10);
							$items[$i][] = $this->createItem(number_format($drow[PayDetailTable::C_VALUE],2,'.',','),60,0,"R","1");
						}
					}
				}
				$items[$i][] = $this->createItem("   Total Income",100);
				$items[$i][] = $this->createItem(":",10) ;
				$items[$i][] = $this->createItem(number_format($basic+$income,2,'.',','),120,0,"R","1");
				if (($deduct + $funds + $cpfemp) != 0 ) {
					$items[$i][] = $this->createItem("Deductions",100,0,"L","1");
					if ($deduct != 0) {
						if (!is_null($drows) && count($drows) > 0) {
							foreach ($drows as $drow) {
								if ($drow[PayDetailTable::C_INCOME_TYPE] == 1) {
									$items[$i][] = $this->createItem("   " . $clstype->getDescription($drow[PayDetailTable::C_TYPE]),100);
									$items[$i][] = $this->createItem(":",10);
									$items[$i][] = $this->createItem(number_format($drow[PayDetailTable::C_VALUE],2,'.',','),60,0,"R","1");
								}
							}
						}
					}
					if ($funds != 0) {
						$frows = $clsfund->getRecord($id,$date) ;
						if (!is_null($frows) && count($frows) > 0 ) {
							foreach ($frows as $frow) {
								$items[$i][] = $this->createItem("   " . $clsfund->getDesc($frow[PayFundLevyTable::C_TYPE]),100);
								$items[$i][] = $this->createItem(":",10);
								$items[$i][] = $this->createItem(number_format($frow[PayFundLevyTable::C_AMOUNT],2,'.',','),60,0,"R","1");
							}
						}
					}
					if ($cpfemp != 0) {
						$items[$i][] = $this->createItem("   CPF - Employee",100);
						$items[$i][] = $this->createItem(":",10);
						$items[$i][] = $this->createItem(number_format($cpfemp, 2, '.', ','),60,0,"R","1");
					}
					$items[$i][] = $this->createItem("   Total Deductions",100);
					$items[$i][] = $this->createItem(":",10) ;
					$items[$i][] = $this->createItem(number_format($deduct+$funds+$cpfemp,2,'.',','),120,0,"R","1");
				}
				$items[$i][] = $this->createItem("Total Net Pay",100) ;
				$items[$i][] = $this->createItem(":",10);
				$items[$i][] = $this->createItem(number_format($netpay,2,'.',','),120,0,"R","1");
				if ($cpfcoy != 0) {
					$items[$i][] = $this->createItem("CPF - Employer",100) ;
					$items[$i][] = $this->createItem(":",10) ;
					$items[$i][] = $this->createItem(number_format($cpfcoy,2,'.',','),60,0,"R","1");
				}
				$datas[] = $items ;
			}
			$pdf = new PagePdf('P');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Pay Slip") ;
			$pdf->render($datas) ;
			$pdf->Output('payslip.pdf', 'I');
			unset($clsfund) ;
			unset($clscoy) ;
			unset($clsdept) ;
			unset($datas) ;
			unset($params) ;
			unset($items) ;
			unset($cols) ;
		} else {
			echo "<h2>No Employee Record Found.</h2>" ;
			return;
		}
		unset($rows) ;
	}
	private function createItem($t,$w=0,$h=0,$a="L",$nr="0") {
		return array('width'=>$w,'height'=>$h,'align'=>$a,'text'=>$t,'newrow'=>$nr);
	}
}
?>