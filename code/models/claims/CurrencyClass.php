<?php
require_once (PATH_TABLES . "claims/CurrencyTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class CurrencyClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = CurrencyTable::C_TABLE ;
		$this->fldid = CurrencyTable::C_ID ;
		$this->flddesc = CurrencyTable::C_DESC ;
		$this->fldorg = CurrencyTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>