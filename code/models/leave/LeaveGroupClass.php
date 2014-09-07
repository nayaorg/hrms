<?php
require_once (PATH_TABLES . "leave/LeaveGroupTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class LeaveGroupClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = LeaveGroupTable::C_TABLE ;
		$this->fldid = LeaveGroupTable::C_ID ;
		$this->fldorg = LeaveGroupTable::C_ORG_ID ;
		$this->flddesc = LeaveGroupTable::C_DESC ;
	}
	function __destruct() {
	}
}
?>