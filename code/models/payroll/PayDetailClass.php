<?php
require_once (PATH_TABLES . "payroll/PayDetailTable.php") ;
require_once (PATH_MODELS . "base/PayBase.php") ;
require_once (PATH_MODELS . "payroll/PayTypeClass.php") ;

class PayDetailClass extends PayBase {
	private $clstype ;
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = PayDetailTable::C_TABLE ;
		$this->fldid = PayDetailTable::C_EMP_ID ;
		$this->flddate = PayDetailTable::C_DATE ;
		$this->fldcoy = PayDetailTable::C_COY_ID ;
		$this->fldorg = PayDetailTable::C_ORG_ID ;
		$this->clstype = new PayTypeClass($db) ;
	}
	function __destruct() {
		unset($this->clstype) ;
	}
	function updateDetail($empid,$date,$orgid,$coyid,$income="",$deduct="") {
		$incamt = array('income'=>0,'deduct'=>0,'ow'=>0,'aw'=>0) ;
		if ($income != "") {
			$types = explode("|",$income) ;
			for ($i= 0;$i < count($types) ;$i++) {
				$type = explode(":",$types[$i]) ;
				if (count($type) == 2) {
					if (is_numeric($type[0]) && is_numeric($type[1])) {
						$paytype = $type[0];
						$value = $type[1];
						$trow = $this->clstype->getRecord($paytype) ;
						
						if (is_null($trow) || count($trow) == 0) {
							$wagetype = 0 ;	//0-none 1-ow 2-aw
							$taxtype = 1;	//0-no tax 1-salary 2-bonus 3-director's fee 4-other
						} else {
							$wagetype = $trow[PayTypeTable::C_WAGE_TYPE] ;
							$taxtype = $trow[PayTypeTable::C_TAX_TYPE];
						}
						$datas = array() ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_EMP_ID,$empid);
						$datas[] = $this->db->fieldValue(PayDetailTable::C_DATE,$date) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_WAGE_TYPE,$wagetype) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_TAX_TYPE,$taxtype) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_VALUE,$value) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_TYPE,$paytype) ;	
						$datas[] = $this->db->fieldValue(PayDetailTable::C_QTY,1) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_INCOME_TYPE,0) ;	//0-income 1-deductions
						$datas[] = $this->db->fieldValue(PayDetailTable::C_ORG_ID,$orgid) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_COY_ID,$coyid) ; 
						$this->addRecord($datas) ;
						$incamt['income'] += $value ;
						if ($wagetype == 1)
							$incamt['ow'] += $value ;
						else if ($wagetype == 2)
							$incamt['aw'] += $value ;
					}
				}
			}
		}
		if ($deduct != "") {
			$types = explode("|",$deduct) ;
			for ($i= 0;$i < count($types) ;$i++) {
				$type = explode(":",$types[$i]) ;
				if (count($type) == 2) {
					if (is_numeric($type[0]) && is_numeric($type[1])) {
						$paytype = $type[0];
						$value = $type[1];
						$trow = $this->clstype->getRecord($paytype) ;
						if (is_null($trow) || count($trow) == 0) {
							$wagetype = 0 ;
							$taxtype = 1 ;
						} else {
							$wagetype = $trow[PayTypeTable::C_WAGE_TYPE] ;
							$taxtype = $trow[PayTypeTable::C_TAX_TYPE];
						}
						$datas = array() ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_EMP_ID,$empid);
						$datas[] = $this->db->fieldValue(PayDetailTable::C_DATE,$date) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_WAGE_TYPE,$wagetype) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_TAX_TYPE,$taxtype) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_VALUE,$value) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_TYPE,$paytype) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_QTY,-1) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_INCOME_TYPE,1) ;	//0-additions 1-deductions
						$datas[] = $this->db->fieldValue(PayDetailTable::C_ORG_ID,$orgid) ;
						$datas[] = $this->db->fieldValue(PayDetailTable::C_COY_ID,$coyid) ; 
						$this->addRecord($datas) ;
						$incamt['deduct'] += $value ;
						if ($wagetype == 1)
							$incamt['ow'] -= $value ;
						else if ($wagetype == 2)
							$incamt['aw'] -= $value ;
					}
				}
			}
		}
		return $incamt ;
	}
	function getIncomeSummary($id,$year) {
		$filter = PayDetailTable::C_EMP_ID . " = " . $id . " and year(" . PayDetailTable::C_DATE . ") = " . $year 
			. " and " . PayDetailTable::C_TAX_TYPE . " <> " . TaxType::NoTax ;
			
		$sql = " select sum(" . PayDetailTable::C_QTY . " * " . PayDetailTable::C_VALUE . ") as " . PayDetailTable::C_VALUE . ","
			. PayDetailTable::C_TAX_TYPE
			. " from " . PayDetailTable::C_TABLE 
			. " where " . $filter 
			. " group by " . PayDetailTable::C_TAX_TYPE ;

		return $this->db->getTable($sql) ;
	}
}
?>