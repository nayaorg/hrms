<?php
require_once (PATH_TABLES . "hr/WorkPermitTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class WorkPermitClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = WorkPermitTable::C_TABLE ;
		$this->fldid = WorkPermitTable::C_ID ;
		$this->flddesc = WorkPermitTable::C_DESC ;
		$this->fldorg = WorkPermitTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>