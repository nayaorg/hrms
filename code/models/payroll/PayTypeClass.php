<?php
require_once (PATH_TABLES . "payroll/PayTypeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class PayTypeClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = PayTypeTable::C_TABLE ;
		$this->fldid = PayTypeTable::C_ID ;
		$this->fldorg = PayTypeTable::C_ORG_ID ;
		$this->flddesc = PayTypeTable::C_DESC ;
	}
	function __destruct() {
	}
}
?>