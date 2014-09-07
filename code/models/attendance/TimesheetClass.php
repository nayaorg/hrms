<?php
require_once (PATH_TABLES . "attendance/TimesheetTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class TimesheetClass extends MasterBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = TimesheetTable::C_TABLE ;
		$this->fldid = TimesheetTable::C_ID ;
		$this->flddesc = TimesheetTable::C_DESC ;
		$this->fldorg = TimesheetTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>