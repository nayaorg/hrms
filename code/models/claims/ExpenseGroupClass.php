<?php
require_once (PATH_TABLES . "claims/ExpenseGroupTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class ExpenseGroupClass extends MasterBase {

	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ExpenseGroupTable::C_TABLE ;
		$this->fldid = ExpenseGroupTable::C_ID ;
		$this->flddesc = ExpenseGroupTable::C_DESC ;
		$this->fldorg = ExpenseGroupTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}
}
?>