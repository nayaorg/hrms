<?php
require_once (PATH_TABLES . "hr/RaceTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class RaceClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = RaceTable::C_TABLE ;
		$this->fldid = RaceTable::C_ID ;
		$this->flddesc = RaceTable::C_DESC ;
		$this->fldorg = RaceTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>