<?php
require_once (PATH_TABLES . "payroll/BankTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class BankClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = BankTable::C_TABLE ;
		$this->fldid = BankTable::C_ID ;
		$this->fldorg = BankTable::C_ORG_ID ;
		$this->flddesc = BankTable::C_DESC ;
	}
	function __destruct() {
	}
}
?>