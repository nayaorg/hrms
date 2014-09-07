<?php
require_once (PATH_TABLES . "claims/CountryTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class CountryClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = CountryTable::C_TABLE ;
		$this->fldid = CountryTable::C_ID ;
		$this->flddesc = CountryTable::C_DESC ;
		$this->fldorg = CountryTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>