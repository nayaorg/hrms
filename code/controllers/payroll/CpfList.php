<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/PayCpfClass.php") ;
require_once (PATH_MODELS . "payroll/PayFundLevyClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "payroll/EmployeePayClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_MODELS . "admin/CompanyOptions.php") ;

class CpfList extends ControllerBase {
	
	private $type = "" ;
	const C_CPF_CONT = "01" ;
	const C_CPF_MBMF = "02";
	const C_CPF_SINDA = "03";
	const C_CPF_CDAC = "04";
	const C_CPF_ECF = "05";
	const C_CPF_INT = "07" ;
	const C_CPF_FWL = "08";
	const C_CPF_FWL_INT = "09";
	const C_CPF_CC = "10";
	const C_CPF_SDF = "11" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = PayCpfTable::C_ORG_ID ;
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
		
		if (!isset($datas['month']) || !isset($datas['year'])) {
			return "<tr><td colspan='9'>Invalid request. Missing month/year info.</td></tr>" ;
		}
		$month = $this->getParamInt($datas,'month',0) ;
		$year = $this->getParamInt($datas,'year',0) ;
		if ($month == 0 || $year == 0) {
			return "<tr><td colspan='9'>Invalid request. Invalid month/year info.</td></tr>" ;
		}
		$date = $year .'-' . $month . '-' . Util::getLastDay($year,$month) ;
		if (isset($datas['coy'])) {
			$coyid = $datas['coy'] ;
		} else {
			$coyid = "" ;
		}

		$rows = $this->getData($date,$coyid) ;
		$list = "" ;
		if (count($rows) > 0) {
			$clsfund = new PayFundLevyClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			foreach ($rows as $row) {
				$ow = $row[PayCpfTable::C_OW] ;
				$aw = $row[PayCpfTable::C_AW] ;
				$cpfemp = $row[PayCpfTable::C_CPF_EMP] ;
				$cpfcoy = $row[PayCpfTable::C_CPF_COY] ;
				$id = $row[PayCpfTable::C_EMP_ID] ;
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;
				$dob = date_create($row[EmployeeTable::C_DOB]) ;
				$funds = $clsfund->getTotal($id,$date,$_SESSION[SE_ORGID]); 
				$age = Util::calculateCpfAge($dob,date_create($date)) ;
				$list .= "<tr>" ;
				$list .= "<td>" . $id . "</td>" ;
				$list .= "<td>" . $empname . "</td>" ;
				$list .= "<td>" . $clscoy->getDescription($row[EmployeeTable::C_COY_ID]) . "</td>" ;
				$list .= "<td>" . $clsdept->getDescription($deptid) . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($ow + $aw,2,'.',',') . "</td>";
				$list .= "<td style='text-align:right'>" . number_format($funds,2,'.',',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($cpfemp,2,'.',',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($cpfcoy, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($cpfemp + $cpfcoy,2,'.',',') . "</td>" ;
				//$list .= "<td style='text-align:center'>" . $age . "</td>" ;
				//$list .= "<td>" . $dob->format('Y-m-d') . "</td>" ;
				//$list .= "<td style='text-align:center'><a href='javascript:' onclick='detailPayList(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
				$list .= "</tr>" ;
			}
			unset($clsfund) ;
			unset($clscoy) ;
			unset($clsdept) ;
		} else {
			$list .= "<tr><td colspan='9'>No Record Found.</td></tr>" ;
		}
		unset($rows) ;
		
		return $list ;
	}
	private function getData($date,$coyid) {
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.") 
			. " and " . $this->db->fieldParam(PayCpfTable::C_DATE,"=","h.") ;
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$params[] = $this->db->valueParam(PayCpfTable::C_DATE,$date) ;
		
		if ($coyid != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_COY_ID,"=","e.") ;
			$params[] = $this->db->valueParam(EmployeeTable::C_COY_ID,$coyid) ;
		}
			
		$sql = " select h.*,e." . EmployeeTable::C_NAME . ",e." . EmployeeTable::C_DEPT
			. ",e." . EmployeeTable::C_ID_NO . ",e." . EmployeeTable::C_JOIN . ",e." 
			. EmployeeTable::C_RESIGN . ",e." . EmployeeTable::C_DOB . ",e." . EmployeeTable::C_COY_ID
			. " from " . PayCpfTable::C_TABLE . "  as h "
			. " left join " . EmployeeTable::C_TABLE . " as e "
			. " on h." . PayCpfTable::C_EMP_ID . " = e." . EmployeeTable::C_ID
			. " where " . $filter 
			. " order by e." . EmployeeTable::C_COY_ID . ",e." . EmployeeTable::C_DEPT . ",e." . EmployeeTable::C_NAME ;
		
		return $this->db->getTable($sql,$params) ;
	}
	private function getCompany() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CompanyTable::C_TABLE, CompanyTable::C_COY_ID, CompanyTable::C_DESC,array('code'=>'','desc'=>'All Company'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/CpfListView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getExport($params=null) {
		if (!isset($params['month']) || !isset($params['year'])) {
			echo "<h2>Invalid request. Missing month/year info.</h2>" ;
			return ;
		}
		if (!isset($params['coy'])) {
			echo "<h2>Invalid request. Missing company id/code.</h2>" ;
			return ;
		}
		
		$month = $this->getParamInt($params,'month',0) ;
		$year = $this->getParamInt($params,'year',0) ;
		if ($month == 0 || $year == 0) {
			echo "<h2>Invalid request. Invalid month/year info.</h2>" ;
			return ;
		}
		$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
		
		$coyid = $this->getParam($params,'coy',"") ;
		if ($coyid == "") {
			echo "<h2>Invalid request. You must select a Company for the export.</h2>" ;
			return ;
		}
		$rows = $this->getData($date,$coyid) ;
		$stryear = str_pad($year,4,"0", STR_PAD_LEFT) ;
		$strmth = str_pad($month,2,"0",STR_PAD_LEFT) ;
		
		if (count($rows) > 0) {
			$clsfund = new PayFundLevyClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clspay = new EmployeePayClass($this->db) ;
			
			$coyrow = $clscoy->getRecord($coyid) ;
			if (is_null($coyrow)) {
				echo "<h2>Invalid company id/code. </h2>";
				return ;
			} else {
				$csn = "" ;
				$advcode = "01" ;
				if (!is_null($coyrow[CompanyTable::C_OPTIONS]) && $coyrow[CompanyTable::C_OPTIONS] != "") {
					$opt = new CompanyOptions() ;
					$opt->loadXml($coyrow[CompanyTable::C_OPTIONS]) ;
					$op = $opt->getOption() ;
					$csn = $op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_CPF_NO] ;
					$advcode = str_pad($op[CompanyOptions::C_SETTING][CompanyOptions::C_SET_CPF_REF],2,"0",STR_PAD_LEFT) ;
					unset($opt) ;
				}
				if ($csn == "") {
					echo "<h2>Missing CPF CSN No. </h2>" ;
					return ;
				}
			}
			$recs = 2 ;	//header and trailer 
			$totcpf = 0 ;
			$totsinda = 0 ;
			$totcdac = 0 ;
			$totmbmf = 0 ;
			$totecf = 0 ;
			$totfwl = 0 ;
			$totsdf = 0 ;
			$cntsinda = 0 ;
			$cntcdac = 0 ;
			$cntmbmf = 0 ;
			$cntecf = 0 ;
			$details = "" ;
			$d = "F1" . $csn . " " . $advcode . $stryear . $strmth ;
			$expfile = $csn . strtoupper(Util::getMonthShortName($strmth)) . $stryear . $advcode . ".dat" ;
			foreach ($rows as $row) {
				$ow = $row[PayCpfTable::C_OW] ;
				$aw = $row[PayCpfTable::C_AW] ;
				$cpfemp = $row[PayCpfTable::C_CPF_EMP] ;
				$cpfcoy = $row[PayCpfTable::C_CPF_COY] ;
				$id = $row[PayCpfTable::C_EMP_ID] ;
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = trim($row[EmployeeTable::C_NAME]) ;
				//$cpfno = trim($row[EmployeeTable::C_NRIC]) ;
				$cpfno = "" ;
				$empstatus = "E" ;	//existing employee
				$payrow = $clspay->getRecord($id) ;
				if (!is_null($payrow)) {
					$cpfno = trim($payrow[EmployeePayTable::C_CPF_NO]) ;
					$dtestart = date_create($payrow[EmployeePayTable::C_START]) ;
					$dteend = date_create($payrow[EmployeePayTable::C_END]) ;
					if ($dtestart->format('Ym') == $stryear.$strmth && $dteend->format('Ym') == $stryear.$strmth) {
						//join and resigned at the same month
						$empstatus = "O" ;
					} else {
						if ($dteend->format('Ym') == $stryear.$strmth) {
							//resigned
							$empstatus = "L";
						} else {
							if ($dtestart->format('Ym') == $stryear.$strmth) {
								//new employee. first cpf contribution
								$empstatus = "N" ;
							}
						}
					}
				}
				
				if (strlen($cpfno) > 9)
					$cpfno = substr($cpfno,0,9) ;
				else
					$cpfno = str_pad($cpfno,9," ",STR_PAD_RIGHT) ;
				if (strlen($empname) > 22)
					$empname = substr($empname,0,22) ;
				else
					$empname = str_pad($empname,22," ",STR_PAD_RIGHT) ;
				
				$details .= $d . self::C_CPF_CONT . $cpfno . $this->formatDecimal($cpfemp+$cpfcoy,12) . $this->formatDecimal($ow,10)
					. $this->formatDecimal($aw,10) . $empstatus . $empname . str_repeat(" ",58) . "\r\n" ;
				$frows = $clsfund->getRecord($id,$date) ;
				foreach ($frows as $fr) {
					$t = $fr[PayFundLevyTable::C_TYPE] ;
					$a = $fr[PayFundLevyTable::C_AMOUNT] ;
					$p = "" ;
					if ($t == FUND_MBMF) {
						$p = self::C_CPF_MBMF ;
						$totmbmf += $a ;
						$cntmbmf++ ;
					} elseif ($t == FUND_SINDA) {
						$p = self::C_CPF_SINDA ;
						$totsinda += $a ;
						$cntsinda++ ;
					} elseif ($t == FUND_CDAC) {
						$p = self::C_CPF_CDAC ;
						$totcdac += $a ;
						$cntcdac++ ;
					} elseif ($t == FUND_ECF) {
						$p = self::C_CPF_ECF ;
						$totecf += $a ;
						$cntecf++ ;
					} elseif ($t == LEVY_FWL) {
						$totfwl += $a ;
					} elseif ($t == LEVY_SDL) {
						$totsdf += $a ;
					}
					if ($p !="") {
						$details .= $d . $p . $cpfno . $this->formatDecimal($a,12) . str_repeat("0",20) . " " . $empname . str_repeat(" ",58) . "\r\n";
						$recs++ ;
					}
				}
				$recs++ ;
				$totcpf += $cpfemp + $cpfcoy ;
			}
			$datas = "" ;
			$datas .= $this->getCpfExpHeader($csn,$advcode) . "\r\n";
			$datas .= $this->getCpfExpSummary($csn,$advcode,$stryear,$strmth,self::C_CPF_CONT,$totcpf,0) . "\r\n" ;
			$datas .= $this->getCpfExpSummary($csn,$advcode,$stryear,$strmth,self::C_CPF_MBMF,$totmbmf,$cntmbmf) . "\r\n" ;
			$datas .= $this->getCpfExpSummary($csn,$advcode,$stryear,$strmth,self::C_CPF_SINDA,$totsinda,$cntsinda) . "\r\n" ;
			$datas .= $this->getCpfExpSummary($csn,$advcode,$stryear,$strmth,self::C_CPF_CDAC,$totcdac,$cntcdac) . "\r\n" ;
			$datas .= $this->getCpfExpSummary($csn,$advcode,$stryear,$strmth,self::C_CPF_ECF,$totecf,$cntecf) . "\r\n" ;
			$datas .= $this->getCpfExpSummary($csn,$advcode,$stryear,$strmth,self::C_CPF_FWL,$totfwl,0) . "\r\n";
			$datas .= $this->getCpfExpSummary($csn,$advcode,$stryear,$strmth,self::C_CPF_CC,0,0) . "\r\n" ;
			$datas .= $this->getCpfExpSummary($csn,$advcode,$stryear,$strmth,self::C_CPF_SDF,$totsdf,0) . "\r\n" ;
			$recs += 8 ;	//summary
			$datas .= $details ;
			$totamt = $totcpf + $totmbmf + $totsinda + $totcdac + $totecf + $totfwl + $totsdf ;
			$datas .= $this->getCpfExpTrailer($csn,$advcode,$recs,$totamt) ;
			unset($clsfund) ;
			unset($clscoy) ;
			unset($clsdept) ;
			unset($clspay) ;
			header('Content-disposition: attachment; filename=' . $expfile);
			header('Content-type: text/plain');
			header("Content-Length: ".strlen($datas));
			echo $datas ;
		} else {
			echo "<tr><td colspan='12'>No Record Found.</td></tr>" ;
			return;
		}
		unset($rows) ;
	}
	private function getCpfExpHeader($csn,$advcode) {
		return "F " . $csn . " " . $advcode . date('YmdHis') . str_pad("FTP.DTL",13," ",STR_PAD_RIGHT) . str_repeat(" ",103)  ;
	}
	private function getCpfExpSummary($csn,$advcode,$year,$month,$paycode,$payamt,$paycount) {
		return "F0" . $csn . " " . $advcode . $year . $month . $paycode . $this->formatDecimal($payamt,12) . str_pad($paycount,7,"0",STR_PAD_LEFT) . str_repeat(" ",103) ;
	}
	private function getCpfExpTrailer($csn,$advcode,$recs,$totamt) {
		return "F9" . $csn . " " . $advcode . str_pad($recs,7,"0",STR_PAD_LEFT) . $this->formatDecimal($totamt,15) . str_repeat(" ",108)  ;
	}
	private function formatDecimal($amt,$n) {
		$a = number_format($amt * 100,0,'','') ;
		return str_pad($a,$n,"0",STR_PAD_LEFT) ;
	}
	
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		if (!isset($params['month']) || !isset($params['year'])) {
			echo "<h2>Invalid request. Missing month/year info.</h2>" ;
			return ;
		}
		$month = $this->getParamInt($params,'month',0) ;
		$year = $this->getParamInt($params,'year',0) ;
		if ($month == 0 || $year == 0) {
			echo "<h2>Invalid request. Invalid month/year info.</h2>" ;
			return ;
		}
		$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
		if (isset($datas['coy'])) {
			$coyid = $datas['coy'] ;
		} else {
			$coyid = "" ;
		}
		$rows = $this->getData($date,$coyid) ;
		
		if (count($rows) > 0) {
			$datas = array() ;
			$nr = 'newrow';
			$np = 'newpage';
			$ph = "pageheader";
			$i = 'items';
			$clsfund = new PayFundLevyClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$coyid = -1 ;
			foreach ($rows as $row) {
				$ow = $row[PayCpfTable::C_OW] ;
				$aw = $row[PayCpfTable::C_AW] ;
				$cpfemp = $row[PayCpfTable::C_CPF_EMP] ;
				$cpfcoy = $row[PayCpfTable::C_CPF_COY] ;
				$id = $row[PayCpfTable::C_EMP_ID] ;
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;
				$funds = $clsfund->getTotal($id,$date,$_SESSION[SE_ORGID]); ;
				$items = array() ;
				if ($coyid != $row[EmployeeTable::C_COY_ID]) {
					$items[$np] = "1" ;
					$coyid = $row[EmployeeTable::C_COY_ID] ;
					$items[$ph]['tag'] = '%=COMPANY=%' ;
					$items[$ph]['text'] = $clscoy->getDescription($coyid) ;	
				}
				$items[$i][] = $this->createPdfItem($id,30) ;
				$items[$i][] = $this->createPdfItem($empname,100) ;
				//$items[$i][] = $this->createPdfItem($clscoy->getDescription($row[EmployeeTable::C_COY_ID]),150) ;
				$items[$i][] = $this->createPdfItem($clsdept->getDescription($deptid),100) ;
				$items[$i][] = $this->createPdfItem(number_format($ow + $aw,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($funds,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($cpfemp,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($cpfcoy, 2, '.', ','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($cpfemp + $cpfcoy,2,'.',','),70,0,"R");
				$items[$nr] = "1" ;
				$datas[] = $items ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",30,0,"C","B");
			$cols[] = $this->createPdfItem("Name",100,0,"C","B") ;
			//$cols[] = $this->createPdfItem("Company",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Department",100,0,"C","B") ;
			$cols[] = $this->createPdfItem("Total Wages",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Fund/Levy",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("CPF Emp",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("CPF Coy",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Total CPF",70,0,"C","B") ;
			$headers = array() ;
			$headers[] = "Compnay : %=COMPANY=%" ;
			$pdf = new ListPdf('L');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("CPF Listing - " . Util::getMonthShortName($month) . ' ' . $year) ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->setHeaders($headers) ;
			$pdf->setHeaderHeight(135) ;
			$pdf->render($datas) ;
			$pdf->Output('cpflist.pdf', 'I');
			unset($clsfund) ;
			unset($clscoy) ;
			unset($clsdept) ;
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