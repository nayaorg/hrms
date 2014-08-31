<?php
require_once (PATH_TABLES . "attendance/ShiftGroupTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class ShiftGroupClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ShiftGroupTable::C_TABLE ;
		$this->fldid = ShiftGroupTable::C_ID ;
		$this->flddesc = ShiftGroupTable::C_DESC ;
		$this->fldorg = ShiftGroupTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>