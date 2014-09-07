<?php
require_once (PATH_TABLES . "leave/LeaveTypeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class LeaveTypeClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = LeaveTypeTable::C_TABLE ;
		$this->fldid = LeaveTypeTable::C_ID ;
		$this->fldorg = LeaveTypeTable::C_ORG_ID ;
		$this->flddesc = LeaveTypeTable::C_DESC ;
	}
	function __destruct() {
	}
}
?>