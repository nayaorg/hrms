<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/PayHeaderClass.php") ;
require_once (PATH_MODELS . "payroll/PayDetailClass.php") ;
require_once (PATH_MODELS . "payroll/PayCpfClass.php") ;
require_once (PATH_MODELS . "payroll/PayFundLevyClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_MODELS . "hr/NationalityClass.php") ;
require_once (PATH_MODELS . "admin/CompanyOptions.php") ;

class IncomeYear extends ControllerBase {
	
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
		$year = $this->getParamInt($datas,'year',0) ;
		if ($year == 0) {
			echo "<h2>Invalid Year.</h2>" ;
			return ;
		} 
		$filter = "" ;
		$coyid = "";
		$deptid = "" ;
		if (isset($datas['coy']))
			$coyid = $datas['coy'] ;
		if (isset($datas['dept']))
			$deptid = $datas['dept'] ;
		$rows = $this->getData($year,$coyid,$deptid) ;
		$list = "" ;
		if (count($rows) > 0) {
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clscpf = new PayCpfClass($this->db) ;
			$clsdetail = new PayDetailClass($this->db) ;
			foreach ($rows as $row) {
				$id = $row[PayHeaderTable::C_EMP_ID] ;
				$salary = 0 ;
				$bonus = 0 ;
				$director = 0 ;
				$others = 0 ;
				$tot = 0 ;
				$drows = $clsdetail->getIncomeSummary($id,$year) ;
				if (!is_null($drows) && count($drows) > 0) {
					foreach ($drows as $drow) {
						$taxtype = $drow[PayDetailTable::C_TAX_TYPE] ;
						$amt = $drow[PayDetailTable::C_VALUE] ;
						switch ($taxtype) {
							case TaxType::Salary:
								$salary += $amt ;
								break ;
							case TaxType::Bonus:
								$bonus += $amt ;
								break ;
							case TaxType::Director:
								$director += $amt ;
								break ;
							case TaxType::Commission:
							case TaxType::TpAllowance:
							case TaxType::EntAllowance:
							case TaxType::OtherAllowance:
							case TaxType::Retirement:
							case TaxType::Compensation:
								$others += $amt ;
								break ;
						}
					}
				}
				//$crows = $clscpf->getRecord($id,$date) ;
				//if (is_null($crows) || count($crows) == 0) {
					//$cpfemp = 0 ;
					//$cpfcoy = 0 ;
				//} else {
					//$cpfemp = $crows[0][PayCpfTable::C_CPF_EMP] ;
					//$cpfcoy = $crows[0][PayCpfTable::C_CPF_COY] ;
				//}
				$tot = $salary + $bonus + $director + $others ;
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;

				$list .= "<tr>" ;
				$list .= "<td>" . $id . "</td>" ;
				$list .= "<td>" . $empname . "</td>" ;
				$list .= "<td>" . $clscoy->getDescription($row[EmployeeTable::C_COY_ID]) . "</td>" ;
				$list .= "<td>" . $clsdept->getDescription($deptid) . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($salary, 2, '.', ',') . "</td>";
				$list .= "<td style='text-align:right'>" . number_format($bonus, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($director,2,'.',',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($others,2,'.',',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($tot, 2, '.', ',') . "</td>" ;
				//$list .= "<td style='text-align:center'><a href='javascript:' onclick='detailPayList(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
				$list .= "</tr>" ;
			}
			unset($clsdetail) ;
			unset($clscoy) ;
			unset($clsdept) ;
			unset($clscpf) ;
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
		include (PATH_VIEWS . "payroll/IncomeYearView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	
	private function getData($year,$coyid="",$deptid="") {
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.")
			. " and year(" . PayHeaderTable::C_END . ") = " . $year ;
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;

		if ($coyid != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_COY_ID,"=","e.") ;
			$params[] = $this->db->valueParam(EmployeeTable::C_COY_ID,$coyid) ;
		}
		if ($deptid != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$deptid) ;
		}
		$sql = " select DISTINCT h." . PayHeaderTable::C_EMP_ID . ",e." . EmployeeTable::C_COY_ID
			. ",e." . EmployeeTable::C_NAME . ",e." . EmployeeTable::C_DEPT
			. " from " . PayHeaderTable::C_TABLE . "  as h "
			. " left join " . EmployeeTable::C_TABLE . " as e "
			. " on h." . PayHeaderTable::C_EMP_ID . " = e." . EmployeeTable::C_ID
			. " where " . $filter 
			. " order by e." . EmployeeTable::C_COY_ID . ",e." . EmployeeTable::C_DEPT . ",e." . EmployeeTable::C_NAME ;

		return $this->db->getTable($sql,$params) ;
	}
	private function getExport($params=null) {
		$year = $this->getParamInt($params,'year',0) ;
		if ($year == 0) {
			echo "<h2>Invalid year.</h2>" ;
			return ;
		}
		$filter = "" ;
		$coyid = $this->getParam($params,'coy',"");
		$clscoy = new CompanyClass($this->db) ;
		$coyrow = $clscoy->getRecord($coyid) ;
		if (is_null($coyrow) || count($coyrow) == 0) {
			echo "<h2>Invalid Company ID.</h2>" ;
			return ;
		} else {
			
			$coyname = $coyrow[CompanyTable::C_DESC] ;
			if (!is_null($coyrow[CompanyTable::C_OPTIONS]) && $coyrow[CompanyTable::C_OPTIONS] != "") {
				$opt = new CompanyOptions() ;
				$opt->loadXml($coyrow[CompanyTable::C_OPTIONS]) ;
				$op = $opt->getOption() ;
				$regno = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_TAX_ID] ;
				$regtype = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_ID_TYPE] ;
				$authname = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_NAME] ;
				$authtitle = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_DEST] ;
				$authtelno = $op[CompanyOptions::C_IRAS][CompanyOptions::C_IRAS_CONTACT] ;
			} else {
				$authname = "" ;
				$authtelno = "" ;
				$authtitle = "" ;
				$regtype = "" ;
				$regno = "" ;
			}
		}		
			
		$rows = $this->getData($year,$coyid) ;
		
		if (count($rows) > 0) {
			$datas = "" ;
			$details = "" ;
			$expfile = "" ;
			$recs = 0 ;
			$totsalary = 0 ;
			$totbonus = 0 ;
			$totdirfee = 0 ;
			$totothers = 0 ;
			$totcpf = 0 ;
			$tot = 0 ;
			
			$clsdetail = new PayDetailClass($this->db) ;
			$clscpf = new PayCpfClass($this->db) ;
			$clsemp = new EmployeeClass($this->db) ;
			$clsnat = new NationalityClass($this->db) ;
			$clsfund = new PayFundLevyClass($this->db) ;
			foreach ($rows as $row) {
				$id = $row[PayHeaderTable::C_EMP_ID] ;
				$salary = 0 ;
				$bonus = 0;
				$dirfee = 0 ;
				$comm = 0 ;
				$tpallow = 0 ;
				$entallow = 0 ;
				$otherallow = 0 ;
				$retire = 0 ;
				$comp = 0 ;
				$totemp = 0 ;
				$others = 0 ;
				$donation = 0 ;
				
				$drows = $clsdetail->getIncomeSummary($id,$year) ;
				if (!is_null($drows) && count($drows) > 0) {
					foreach ($drows as $drow) {
						$taxtype = $drow[PayDetailTable::C_TAX_TYPE] ;
						$amt = $drow[PayDetailTable::C_VALUE] ;
						if ($taxtype == TaxType::Salary)
							$salary += $amt ;
						else if ($taxtype == TaxType::Bonus)
							$bonus += $amt ;
						else if ($taxtype == TaxType::Director)
							$dirfee += $amt ;
						else if ($taxtype == TaxType::Commission)
							$comm += $amt ;
						else if ($taxtype == TaxType::TpAllowance)
							$tpallow += $amt ;
						else if ($taxtype == TaxType::EntAllowance)
							$entallow += $amt ;
						else if ($taxtype == TaxType::OtherAllowance)
							$otherallow += $amt ;
						else if ($taxtype == TaxType::Retirement)
							$retire += $amt ;
						else if ($taxtype == TaxType::Compensation)
							$comp += $amt ;
					}
				}
				
				$cpfrows = $clscpf->getCpfTotal($id,$year) ;
				if (is_null($cpfrows) || count($cpfrows) == 0) {
					$cpfemp = 0 ;
				} else {
					$cpfemp = $cpfrows[0][PayCpfTable::C_CPF_EMP] ;
				}
				$others = $comm + $tpallow + $entallow + $otherallow + $retire + $comp;
				$totemp = $salary + $bonus + $dirfee + $others ;
				$donation = $clsfund->getFundTotal($id,$year) ;
				$from = $year . "0101" ;
				$to = $year . "1231";
				$joindate = str_repeat(" ",8) ;
				$resigndate = str_repeat(" ",8) ;
				$resigned = "N" ;
				$emprow = $clsemp->getRecord($id) ;
				if (is_null($emprow) || count($emprow) == 0) {
					$empname = "" ;
					$idtype = " " ;
					$idno = "" ;
					$natcode = "" ;
					$sex = " " ;
					$dob = str_repeat(" ",8) ;
					
				} else {
					$empname = $emprow[EmployeeTable::C_NAME] ;
					$idtype = str_pad($emprow[EmployeeTable::C_ID_TYPE],1," ",STR_PAD_RIGHT) ;
					$idno = $emprow[EmployeeTable::C_ID_NO] ;
					$natrow = $clsnat->getRecord($emprow[EmployeeTable::C_NATIONALITY]) ;
					if (is_null($natrow) || count($natrow) == 0) {
						$natcode = "" ;
					} else {
						$natcode = $natrow[NationalityTable::C_REF] ;
					}
					$sex = str_pad($emprow[EmployeeTable::C_GENDER],1," ",STR_PAD_RIGHT) ;
					$dte = date_create($emprow[EmployeeTable::C_DOB]) ;
					$dob = $dte->format("Ymd") ;
					$dte = date_create($emprow[EmployeeTable::C_RESIGN]) ;
					if ($dte <= date_create($year."-12-31")) {
						$resigned = "Y" ;
						$to = $dte->format('Ymd') ;
						$resigndate = $to ;
					}
					$dte = date_create($emprow[EmployeeTable::C_JOIN]) ;
					if ($resigned == "Y") {
						$joindate = $dte->format("Ymd") ;
					} else {
						if ($dte > date_create($year."-01-01")) 
							$from = $dte->format("Ymd") ;
					}
				}
				
				if (strlen($idno) > 12)
					$idno = substr($idno,0,12) ;
				else
					$idno = str_pad($idno,12," ",STR_PAD_RIGHT) ;
				if (strlen($empname) > 40) {
					$empname1 = substr($empname,0,40) ;
					$empname2 = str_pad(substr($empname,40,strlen($empname)-40),40," ",STR_PAD_RIGHT) ;
				}
				else {
					$empname1 = str_pad($empname,40," ",STR_PAD_RIGHT) ;
					$empname2 = str_repeat(" ",40) ;
				}
				
				$details .= "1" . $idtype . $idno . $empname1 . $empname2 ;	// 1 to 4
				$details .= "N" . str_repeat(" ",155) ; //5 and 6
				$details .=	str_pad($natcode,3," ",STR_PAD_RIGHT) . $sex . $dob ; //7 to 9
				$details .= $this->formatAmount($totemp,9) ; //10. total of 16-19
				$details .= $from . $to ; //11
				$details .= str_repeat(" ",5) .	$this->formatAmount($donation,5) ; //12 and 13
				$details .= $this->formatAmount($cpfemp,7) . str_repeat(" ",5); // 14 and 15
				$details .=  $this->formatAmount($salary,9) . $this->formatAmount($bonus,9) ; // 16, 17
				$details .= $this->formatAmount($dirfee,9) . $this->formatAmount($others,9) ; // 18,19
				$details .= str_repeat(" ",36) . "NNNN" ; //19a, 20 ,21,22,23,24,25,26
				if ($comp > 0) {
					$details .= "YN" . str_repeat(" ",8) ; //27,27a,27b
				} else {
					$details .= str_repeat(" ",10) ; //27,27a,27b
				}
				$details .= $resigned . "NN" . " " ;//28 to 30a
				if ($comm > 0) {
					$details .= $this->formatDecimal($comm,11) . $from . $to . "B" ; //31 to 33
				} else {
					$details .= str_repeat(" ",28) ; //31 to 33
				}
				$details .= str_repeat(" ",11) ;	//34
				$details .= $this->formatDecimal($tpallow,11) ; //35
				$details .= $this->formatDecimal($entallow,11) ; //36
				$details .= $this->formatDecimal($otherallow,11) ; //37
				$details .= str_repeat(" ",11) ; //38 . 26 must be 'N'
				if ($comp > 0) {
					$details .= $this->formatDecimal($comp,11) ; //38a. link to 27
				} else {
					$details .= str_repeat(" ",11) ;//38a. link to 27
				}
				$details .= str_repeat(" ",73) ; // 39 to 45
				$details .= str_repeat(" ",30) ; //46
				$details .= $joindate . $resigndate . $year . "1231" . $year . "1231" . str_repeat(" ",120) ; //47 to 52
				$details .= str_repeat(" ",9) . str_repeat(" ",393) . str_repeat(" ",50) . "\r\n" ; //53 to 56
				
				$totsalary += $salary ;
				$totbonus += $bonus ;
				$totdirfee += $dirfee ;
				$totothers += $others ;
				$totcpf += $cpfemp ;
				$tot += $totemp ;
				$recs++ ;
			}
			unset($clsdetail) ;
			unset($clscpf) ;
			$expfile = "IR8A" . "-" . $year . ".txt" ;
			$datas = $this->getIrasExpHeader($year,$regtype,$regno,$authname,$authtitle,$coyname,$authtelno) ;
			$datas .= $details ;
			$datas .= $this->getIrasExpTrailer($recs,$tot,$totsalary,$totbonus,$totdirfee,$totothers,$totcpf) ;
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
	private function getIrasExpHeader($year,$regtype,$regno,$authname,$jobtitle,$coy,$telno) {
		return "16" . $year . "08" . str_pad($regtype,1," ",STR_PAD_RIGHT) . str_pad($regno,12," ",STR_PAD_RIGHT)
			. str_pad($authname,30," ", STR_PAD_RIGHT) . str_pad($jobtitle,30," ",STR_PAD_RIGHT)
			. str_pad($coy,60," ",STR_PAD_RIGHT) . str_pad($telno,20," ",STR_PAD_RIGHT)
			. str_repeat(" ",60) . "O" . date('Ymd') . str_repeat(" ",30) . str_pad("IR8A",10," ",STR_PAD_RIGHT)
			. str_repeat(" ",930) . "\r\n"  ;
	}
	private function getIrasExpTrailer($recs,$totamt,$salary,$bonus,$dirfee,$others,$cpf) {
		return "2" . str_pad($recs,6,"0",STR_PAD_LEFT) . $this->formatAmount($totamt,12) 
			. $this->formatAmount($salary,12) . $this->formatAmount($bonus,12)
			. $this->formatAmount($dirfee,12) . $this->formatAmount($others,12)
			. str_repeat("0",12) . str_repeat("0",12) . str_repeat("0",12) . str_repeat("0",12)
			. $this->formatAmount($cpf,12) . str_repeat("0",12) . str_repeat("0",12) . str_repeat(" ",1049) ;
	}
	private function formatAmount($amt,$n) {
		$a = Util::roundOff($amt);
		//$a = number_format($a,0,'','') ;
		return str_pad($a,$n,"0",STR_PAD_LEFT) ;
	}
	private function formatDecimal($amt,$n) {
		$a = number_format($amt * 100,0,'','') ;
		return str_pad($a,$n,"0",STR_PAD_LEFT) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		$year = $this->getParamInt($params,'year',0) ;
		if ($year == 0) {
			echo "<h2>Invalid year.</h2>" ;
			return ;
		}
		$filter = "" ;
		$coyid = "";
		$deptid = "" ;
		if (isset($params['coy']))
			$coyid = $params['coy'] ;
		if (isset($params['dept']))
			$deptid = $params['dept'] ;
			
		$rows = $this->getData($year,$coyid,$deptid) ;
		
		if (count($rows) > 0) {
			$datas = array() ;
			$nr = 'newrow';
			$np = 'newpage';
			$ph = "pageheader";
			$i = 'items';
			$clsdetail = new PayDetailClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			//$clscpf = new PayCpfClass($this->db) ;
			$coyid = -1;
			foreach ($rows as $row) {
				$id = $row[PayHeaderTable::C_EMP_ID] ;
				$salary = 0 ;
				$bonus = 0 ;
				$director = 0;
				$others = 0 ;
				$tot = 0 ;
				$drows = $clsdetail->getIncomeSummary($id,$year) ;
				
				if (!is_null($drows) && count($drows) > 0) {
					foreach ($drows as $drow) {
						$taxtype = $drow[PayDetailTable::C_TAX_TYPE] ;
						$amt = $drow[PayDetailTable::C_VALUE] ;
						switch ($taxtype) {
							case TaxType::Salary:
								$salary += $amt ;
								break ;
							case TaxType::Bonus:
								$bonus += $amt ;
								break ;
							case TaxType::Director:
								$director += $amt ;
								break ;
							case TaxType::Commission:
							case TaxType::TpAllowance:
							case TaxType::EntAllowance:
							case TaxType::OtherAllowance:
							case TaxType::Retirement:
							case TaxType::Compensation:
								$others += $amt ;
								break ;
						}
					}
				}
				//$crows = $clscpf->getRecord($id,$date) ;
				//if (is_null($crows) || count($crows) == 0) {
					//$cpfemp = 0 ;
					//$cpfcoy = 0 ;
				//} else {
					//$cpfemp = $crows[0][PayCpfTable::C_CPF_EMP] ;
					//$cpfcoy = $crows[0][PayCpfTable::C_CPF_COY] ;
				//}
				
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;
				//$funds = $clsfund->getTotal($id,$date,$_SESSION[SE_ORGID]); ;
				//$netpay = $basic + $income - $deduct - $funds - $cpfemp ;
				$tot = $salary + $bonus + $director + $others ;
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
				$items[$i][] = $this->createPdfItem(number_format($salary ,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($bonus,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($director,2,'.',','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($others, 2, '.', ','),70,0,"R");
				$items[$i][] = $this->createPdfItem(number_format($tot,2,'.',','),70,0,"R");
				$items[$nr] = "1" ;
				$datas[] = $items ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",30,0,"C","B");
			$cols[] = $this->createPdfItem("Name",100,0,"C","B") ;
			//$cols[] = $this->createPdfItem("Company",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Department",100,0,"C","B") ;
			$cols[] = $this->createPdfItem("Salary",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Bonus",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Director's Fee",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Others",70,0,"C","B") ;
			$cols[] = $this->createPdfItem("Total",70,0,"C","B") ;
			$headers = array() ;
			$headers[] = "Compnay : %=COMPANY=%" ;
			$pdf = new ListPdf('L');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Employee Income Listing for Year - " . $year) ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->setHeaders($headers) ;
			$pdf->setHeaderHeight(135) ;
			$pdf->render($datas) ;
			$pdf->Output('income.pdf', 'I');
			unset($clscoy) ;
			unset($clsdept) ;
			//unset($clscpf) ;
			unset($clsdetail) ;
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