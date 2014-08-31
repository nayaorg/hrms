<?php
require_once (PATH_TABLES . "payroll/PayCpfTable.php") ;
require_once (PATH_MODELS . "base/PayBase.php") ;
require_once (PATH_MODELS . "payroll/CpfRateClass.php") ;
require_once (PATH_MODELS . "payroll/CpfAgeClass.php") ;
require_once (PATH_MODELS . "payroll/CpfWageClass.php") ;
require_once (PATH_MODELS . "payroll/EmployeePayClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php");
require_once (PATH_MODELS . "payroll/CpfTypeClass.php") ;

class PayCpfClass extends PayBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = PayCpfTable::C_TABLE ;
		$this->fldid = PayCpfTable::C_EMP_ID ;
		$this->flddate = PayCpfTable::C_DATE ;
		$this->fldcoy = PayCpfTable::C_COY_ID ;
		$this->fldorg = PayCpfTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function updateRecord($empid,$date,$datas) {
		$sql = "update " . $this->tbl . " set " ;
		$fs = "" ;
		$params = array() ;
		if (is_array($datas) && count($datas) > 0) {
			foreach ($datas as $data) {
				$sql .= $fs . $this->db->fieldParam($data['field']) ;
				$fs = ", " ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
			}
			$sql .= " where " . $this->db->fieldParam($this->fldid) . 
				" and " . $this->db->fieldParam($this->flddate) ;
			$params[] = $this->db->valueParam($this->fldid,$empid) ;
			$params[] = $this->db->valueParam($this->flddate,$date) ;
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
		}
	}
	function getAwTotal($empid,$year=0,$month=99) {
		if ($month==0)
			return 0 ;
			
		if ($year == 0)
			$year = date('Y') ;
		
		$start = $year . "-01-01" ;
		if ($month==99)
			$end = $year . "-12-31" ;
		else
			$end = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ; 
		$filter = $this->db->fieldParam($this->fldorg) 
			. " and " . $this->db->fieldParam($this->fldid) 
			. " and " . PayCpfTable::C_DATE . " between '" . $start . "' and '" . $end . "'" ;
			
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldorg,$_SESSION[SE_ORGID]) ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;

		$sql = "select sum(" . PayCpfTable::C_AW . ") as amt "
			. " from " . PayCpfTable::C_TABLE 
			. " where " . $filter ;
		
		$row = $this->db->getRow($sql,$params) ;

		if (is_null($row) || count($row) == 0)
			return 0 ;
		else {
			if (is_null($row[0]['amt']))
				return 0 ;
			else
				return $row[0]['amt'] ;
		}
	}
	function getAwPayTotal($empid,$year=0,$month=99) {
		if ($month==0)
			return 0 ;
			
		if ($year == 0)
			$year = date('Y') ;
		
		$start = $year . "-01-01" ;
		if ($month==99)
			$end = $year . "-12-31" ;
		else
			$end = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
		$filter = $this->db->fieldParam($this->fldorg) 
			. " and " . $this->db->fieldParam($this->fldid) 
			. " and " . PayCpfTable::C_DATE . " between '" . $start . "' and '" . $end . "'" ;
			
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldorg,$_SESSION[SE_ORGID]) ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;

		$sql = "select sum(" . PayCpfTable::C_AW_PAY . ") as amt "
			. " from " . PayCpfTable::C_TABLE 
			. " where " . $filter ;
		
		$row = $this->db->getRow($sql,$params) ;

		if (is_null($row) || count($row) == 0)
			return 0 ;
		else {
			if (is_null($row[0]['amt']))
				return 0 ;
			else
				return $row[0]['amt'] ;
		}
	}
	function getOwTotal($empid,$year=0,$month=99) {
		if ($month==0)
			return 0 ;
		if ($year == 0)
			$year = date('Y') ;
		$start = $year . "-01-01" ;
		if ($month==99)
			$end = $year . "-12-31" ;
		else
			$end = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ; 
		$filter = $this->db->fieldParam($this->fldorg) 
			. " and " . $this->db->fieldParam($this->fldid) 
			. " and " . PayCpfTable::C_DATE . " between '" . $start. "' and '" . $end . "'" ;
			
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldorg,$_SESSION[SE_ORGID]) ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;

		$sql = "select sum(" . PayCpfTable::C_OW . ") as amt "
			. " from " . PayCpfTable::C_TABLE 
			. " where " . $filter ;

		$row = $this->db->getRow($sql,$params) ;
		if (is_null($row) || count($row) == 0)
			return 0 ;
		else {
			if (is_null($row[0]['amt']))
				return 0 ;
			else
				return $row[0]['amt'] ;
		}
	}
	function getOwPayTotal($empid,$year=0,$month=99) {
		if ($month==0)
			return 0 ;
		if ($year == 0)
			$year = date('Y') ;
		$start = $year . "-01-01" ;
		if ($month==99)
			$end = $year . "-12-31" ;
		else
			$end = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ; 
		$filter = $this->db->fieldParam($this->fldorg) 
			. " and " . $this->db->fieldParam($this->fldid) 
			. " and " . PayCpfTable::C_DATE . " between '" . $start. "' and '" . $end . "'" ;
			
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldorg,$_SESSION[SE_ORGID]) ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;

		$sql = "select sum(" . PayCpfTable::C_OW_PAY . ") as amt "
			. " from " . PayCpfTable::C_TABLE 
			. " where " . $filter ;

		$row = $this->db->getRow($sql,$params) ;
		if (is_null($row) || count($row) == 0)
			return 0 ;
		else {
			if (is_null($row[0]['amt']))
				return 0 ;
			else
				return $row[0]['amt'] ;
		}
	}
	function getCpfTotal($empid,$year=0) {
		if ($year == 0)
			$year = date('Y') ;
		
		$filter = PayCpfTable::C_EMP_ID . " = " . $empid
			. " and year(" . PayCpfTable::C_DATE . ") = " . $year ;
			
		$sql = "select sum(" . PayCpfTable::C_CPF_EMP . ") as " . PayCpfTable::C_CPF_EMP . ","
			. "sum(" . PayCpfTable::C_CPF_COY . ") as " . PayCpfTable::C_CPF_COY
			. " from " . PayCpfTable::C_TABLE 
			. " where " . $filter ;

		return $this->db->getRow($sql) ;
	}
	function calculateCpf($empid=0,$cpfid=0,$paydate,$ow=0,$aw=0) {
		$cpfamt = array('coy'=>0,'emp'=>0,'ow'=>0,'aw'=>0) ;
		if (($ow + $aw) == 0) { return $cpfamt ; }
		if ($cpfid == 0 || $empid == 0) { return $cpfamt ; }
		if (is_null($paydate) || $paydate == "")
			$paydate = new DateTime('now') ;
		else
			$paydate = new DateTime($paydate) ;
		$clsemp = new EmployeeClass($this->db);
		$clscpf = new CpfTypeClass($this->db) ;
		$clsage = new CpfAgeClass($this->db) ;
		$clswage = new CpfWageClass($this->db) ;
		$clspay = new EmployeePayClass($this->db) ;
		
		$emprow = $clsemp->getRecord($empid) ;
		if (is_null($emprow) || count($emprow) == 0)
			return $cpfamt ;
		
		$dob = date_create($emprow[EmployeeTable::C_DOB]) ;
		
		$cpfrow = $clscpf->getRecord($cpfid) ;
		if (is_null($cpfrow) || count($cpfrow) == 0)
			return $cpfamt ;
		$owceiling = $cpfrow[CpfTypeTable::C_OW] ;
		$awceiling = $cpfrow[CpfTypeTable::C_AW] ;
		if ($ow > $owceiling)
			$cpfow = $owceiling ;
		else
			$cpfow = $ow ;
		
		$cpfaw = 0 ;
		$y = $paydate->format('Y');
		$m = $paydate->format('m');
		$totaw = $this->getAwTotal($empid,$y,$m-1) ;
		if ($aw > 0) {
			$payrow = $clspay->getRecord($empid) ;
			$start = date_create($payrow[EmployeePayTable::C_START]) ;
			$end = date_create($payrow[EmployeePayTable::C_END]) ;
			$balaw = $awceiling - $totaw ;
			if ($m==12) {
				//last month of the year. get current year ow total as at Nov + current ow (up to the ow ceiling).
				$totow = $this->getOwTotal($empid,$y,$m-1) + $cpfow ;
			} else {
				//not last month of the year. 
				if ($end <= $paydate) {
					//employee resigned. get current year ow total as at previous month + current ow (up to the ow ceiling)
					$totow = $this->getOwTotal($empid,$y,$m-1) + $cpfow ;
				} else {
					//to prevent over payment use last year full year ow as an
					//estimate to compute the awceiling. any shortfall will be adjusted
					//at last month of the year.
					if ($start <= date_create($y-1 . '-01-01')) {
						//last year full year ow detail available.
						$totow = $this->getOwTotal($empid,$y-1,99) ;
					} else {
						//no full year detail available. use current up to date ow total.
						$totow = $this->getOwTotal($empid,$y,$m-1) + $cpfow ;
					}
				}
			}
			$balaw -= $totow ;
			if ($balaw <= 0) {
				$cpfaw = 0 ;
			}
			else {
				if ($aw > $balaw)
					$cpfaw = $balaw ;
				else 
					$cpfaw = $aw ;
			}			
		} else {
			if ($m==12) {
				//last month of the year. check is there any aw for the year.
				//if yes, check to see is there any short fall. if yes, adjust
				//the cpf for the missing aw amount.
				if ($totaw > 0) {
					$totow = $this->getOwTotal($empid,$y,$m-1) + $cpfow ;
					$balaw = $awceiling - $totaw - $totow;
					if ($balaw > 0) {
						//still got balance. check for shortfall
						$totawpay = $this->getAwPayTotal($empid,$y,$m-1) ;
						$a = $totawpay - $totaw ;
						if ($a > 0) {
							//not all aw have been computed for cpf.
							//need to determine the missing aw amount.
							if ($a > $balaw)
								$cpfaw = $balaw ;
							else
								$cpfaw = $a ;
						}	
					} else {
						//over calculate cpf. to manually submit refund request to cpf by user.
						$cpfaw = 0; 
					}
				}
			}
		}
		$pay = $cpfow + $cpfaw ;
		$ageid = $clsage->getAgeId($clsage->calculateAge($dob,$paydate)) ;
		$wageid = $clswage->getWageId($pay) ;
		
		if ($ageid == 0 || $wageid == 0)
			return $cpfamt ;
		
		$clsrate = new CpfRateClass($this->db) ;
		$raterow = $clsrate->getRecord($cpfid,$ageid,$wageid) ;
		if (is_null($raterow) || count($raterow) == 0)
			return $cpfamt ;
			
		$empfix = $raterow[CpfRateTable::C_EMP_FIX] ;
		$emprate = $raterow[CpfRateTable::C_EMP_RATE] / 100 ;
		$empoff = $raterow[CpfRateTable::C_EMP_OFFSET] ;
		$coyfix = $raterow[CpfRateTable::C_COY_FIX];
		$coyrate = $raterow[CpfRateTable::C_COY_RATE] / 100 ;
		$coyoff = $raterow[CpfRateTable::C_COY_OFFSET] ;
		
		$cpfempow = 0 ;
		$cpfempaw = 0 ;
		$cpfcoyow = 0 ;
		$cpfcoyaw = 0 ;
		
		if ($empfix == 0 && $empoff == 0) {
			$cpfempow = $cpfow * $emprate ;
			$cpfempaw = $cpfaw * $emprate ;
		} else {
			$cpfempow = $empfix + ($emprate * ($pay - $empoff)) ; 
		}
		if ($coyfix == 0 && $coyoff == 0) {
			$cpfcoyow = $cpfow * $coyrate ;
			$cpfcoyaw = $cpfaw * $coyrate ;
		} else {
			$cpfcoyow = $coyfix + ($coyrate * ($pay - $coyoff)) ;
		}
		$cpftot = round($cpfcoyow + $cpfcoyaw + $cpfempow + $cpfempaw) ;
		$cpfemp = Util::roundOff($cpfempow + $cpfempaw) ;

		$cpf['coy'] = $cpftot - $cpfemp ;
		$cpf['emp'] = $cpfemp ;
		$cpf['ow'] = $cpfow ;
		$cpf['aw'] = $cpfaw ;
		return $cpf ;
	}
}
?>