<?php
require_once (PATH_TABLES . "admin/CompanyTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class CompanyClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = CompanyTable::C_TABLE ;
		$this->fldid = CompanyTable::C_COY_ID ;
		$this->fldorg = CompanyTable::C_ORG_ID ;
		$this->flddesc = CompanyTable::C_DESC ;
	}
	function __destruct() {
	}
}
?>