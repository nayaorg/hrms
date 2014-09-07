<?php
require_once (PATH_TABLES . "attendance/TimeCardTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class TimeCardClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = TimeCardTable::C_TABLE ;
		$this->fldid = TimeCardTable::C_ID ;
		$this->flddesc = TimeCardTable::C_DESC ;
		$this->fldorg = TimeCardTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>