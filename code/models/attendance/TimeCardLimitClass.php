<?php
require_once (PATH_TABLES . "attendance/TimeCardLimitTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class TimeCardLimitClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = TimeCardLimitTable::C_TABLE ;
		$this->fldid = TimeCardLimitTable::C_ID ;
		$this->flddesc = "" ;
		$this->fldorg = TimeCardLimitTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>