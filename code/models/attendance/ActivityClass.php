<?php
require_once (PATH_TABLES . "attendance/ActivityTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class ActivityClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ActivityTable::C_TABLE ;
		$this->fldid = ActivityTable::C_ID ;
		$this->flddesc = ActivityTable::C_DESC ;
		$this->fldorg = ActivityTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>