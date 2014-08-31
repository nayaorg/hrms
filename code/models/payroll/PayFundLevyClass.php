<?php
require_once (PATH_TABLES . "payroll/PayFundLevyTable.php") ;
require_once (PATH_MODELS . "base/PayBase.php") ;

define("FUND_CDAC", 1);
define("FUND_SINDA",2);
define("FUND_MBMF", 3);
define("FUND_ECF",  4) ;
define("LEVY_FWL",  5) ;
define("LEVY_SDL",  6) ;

class PayFundLevyClass extends PayBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = PayFundLevyTable::C_TABLE ;
		$this->fldid = PayFundLevyTable::C_EMP_ID ;
		$this->flddate = PayFundLevyTable::C_DATE ;
		$this->fldcoy = PayFundLevyTable::C_COY_ID ;
		$this->fldorg = PayFundLevyTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function updateFundLevy($fund,$empid,$date,$pay,$orgid,$coyid) {
		
		if (substr($fund,0,1) == 1) 
			$this->updateLevy(LEVY_SDL,$empid,$date,$pay,$orgid,$coyid) ;
						
		if (substr($fund,1,1) == 1) 
			$this->updateFund(FUND_MBMF,$empid,$date,$pay,$orgid,$coyid) ;
		if (substr($fund,2,1) == 1) 
			$this->updateFund(FUND_SINDA,$empid,$date,$pay,$orgid,$coyid) ;
		if (substr($fund,3,1) == 1) 
			$this->updateFund(FUND_CDAC,$empid,$date,$pay,$orgid,$coyid) ;
		if (substr($fund,4,1) == 1) 
			$this->updateFund(FUND_ECF,$empid,$date,$pay,$orgid,$coyid) ;
	}
	function updateFund($type,$empid,$date,$pay,$orgid,$coyid) {
		if ($type == FUND_MBMF)
			$amt = $this->calculateMbmf($pay) ;
		else if ($type == FUND_SINDA)
			$amt = $this->calculateSinda($pay) ;
		else if ($type == FUND_CDAC)
			$amt = $this->calculateCdac($pay) ;
		else if ($type == FUND_ECF)
			$amt = $this->calculateEcf($pay) ;
		else 
			$amt = 0 ;
	
		if ($amt != 0) {
			$datas = array() ;
			$datas[] = $this->db->fieldValue($this->fldid,$empid) ;
			$datas[] = $this->db->fieldValue($this->flddate,$date) ;
			$datas[] = $this->db->fieldValue(PayFundLevyTable::C_TYPE,$type) ;
			$datas[] = $this->db->fieldValue(PayFundLevyTable::C_AMOUNT,$amt) ;
			$datas[] = $this->db->fieldValue($this->fldcoy,$coyid) ;
			$datas[] = $this->db->fieldValue($this->fldorg,$orgid) ;
			$this->addRecord($datas) ;
		}	
	}
	function updateLevy($type,$empid,$date,$pay,$orgid,$coyid) {
		if ($type == LEVY_SDL)
			$amt = $this->calculateSdl($pay) ;
		else 
			$amt = 0 ;
	
		if ($amt != 0) {
			$datas = array() ;
			$datas[] = $this->db->fieldValue($this->fldid,$empid) ;
			$datas[] = $this->db->fieldValue($this->flddate,$date) ;
			$datas[] = $this->db->fieldValue(PayFundLevyTable::C_TYPE,$type) ;
			$datas[] = $this->db->fieldValue(PayFundLevyTable::C_AMOUNT,$amt) ;
			$datas[] = $this->db->fieldValue($this->fldcoy,$coyid) ;
			$datas[] = $this->db->fieldValue($this->fldorg,$orgid) ;
			$this->addRecord($datas) ;
		}	
	}
	function updateAmount($type,$empid,$date,$amt,$orgid,$coyid) {
		if ($amt != 0) {
			$datas = array() ;
			$datas[] = $this->db->fieldValue($this->fldid,$empid) ;
			$datas[] = $this->db->fieldValue($this->flddate,$date) ;
			$datas[] = $this->db->fieldValue(PayFundLevyTable::C_TYPE,$type) ;
			$datas[] = $this->db->fieldValue(PayFundLevyTable::C_AMOUNT,$amt) ;
			$datas[] = $this->db->fieldValue($this->fldcoy,$coyid) ;
			$datas[] = $this->db->fieldValue($this->fldorg,$orgid) ;
			$this->addRecord($datas) ;
		}	
	}
	function getTotal($empid,$date,$orgid) {
		$filter = $this->db->fieldParam($this->fldorg) 
			. " and " . $this->db->fieldParam($this->fldid) 
			. " and " . $this->db->fieldParam($this->flddate) ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldorg,$orgid) ;
		$params[] = $this->db->valueParam($this->flddate,$date) ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;
		
		$sql = "select sum(" . PayFundLevyTable::C_AMOUNT . ") as amt "
			. " from " . PayFundLevyTable::C_TABLE 
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
	function getFundTotal($empid,$year) {
		$filter = $this->fldid . " = " . $empid 
			. " and year(" . $this->flddate . ") = " . $year 
			. " and (" . PayFundLevyTable::C_TYPE . " = " . FUND_MBMF
			. " or " . PayFundLevyTable::C_TYPE . " = " . FUND_SINDA
			. " or " . PayFundLevyTable::C_TYPE . " = " . FUND_ECF 
			. " or " . PayFundLevyTable::C_TYPE . " = " . FUND_CDAC . ")" ;
		
		$sql = "select sum(" . PayFundLevyTable::C_AMOUNT . ") as amt "
			. " from " . PayFundLevyTable::C_TABLE 
			. " where " . $filter ;
		$row = $this->db->getRow($sql) ;
		if (is_null($row) || count($row) == 0)
			return 0 ;
		else {
			if (is_null($row[0]['amt']))
				return 0 ;
			else
				return $row[0]['amt'] ;
		}
	}
	function calculateSdl($pay) {
		if ($pay > 4500)
			$pay = 4500 ;
		$amt = round(($pay * 0.25)/100,2) ;
		if ($amt < 2)
			$amt = 2 ;
		
		return $amt ;
	}
	function calculateCdac($pay) {
		if ($pay > 2000)
			return 1;
		else 
			return 0.50 ;
	}
	function calculateSinda($pay) {
		if ($pay > 2500)
			return 7 ;
		else if ($pay > 1500)
			return 5 ;
		else if ($pay > 600)
			return 3 ;
		else
			return 1;
	}
	function calculateEcf($pay) {
		if ($pay > 4000)
			return 10 ;
		else if ($pay > 2500)
			return 8 ;
		else if ($pay > 1500)
			return 6 ;
		else if ($pay > 1000)
			return 4 ;
		else 
			return 2;
	}
	function calculateMbmf($pay) {
		if ($pay > 4000)
			return 16 ;
		else if ($pay > 3000)
			return 12.50;
		else if ($pay > 2000)
			return 5 ;
		else if ($pay > 1000)
			return 3.50 ;
		else if ($pay > 200)
			return 2 ;
		else
			return 0;
	}
	function getDesc($type) {
		if ($type == FUND_MBMF)
			return "MBMF" ;
		else if ($type == FUND_SINDA)
			return "SINDA";
		else if ($type == FUND_CDAC)
			return "CDAC" ;
		else if ($type == FUND_ECF)
			return "ECF" ;
		else if ($type == LEVY_SDL)
			return "SDL";
		else 
			return "" ;
	}
}
?>