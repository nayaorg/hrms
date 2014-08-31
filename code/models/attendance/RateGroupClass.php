<?php
require_once (PATH_TABLES . "attendance/RateGroupTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class RateGroupClass extends MasterBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = RateGroupTable::C_TABLE ;
		$this->fldid = RateGroupTable::C_ID ;
		$this->fldorg = RateGroupTable::C_ORG_ID ;
		$this->flddesc = RateGroupTable::C_DESC ;
	}
	function __destruct() {
	}
}
?>