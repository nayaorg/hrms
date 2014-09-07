<?php
require_once (PATH_TABLES . "hr/JobTitleTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class JobTitleClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = JobTitleTable::C_TABLE ;
		$this->fldid = JobTitleTable::C_ID ;
		$this->flddesc = JobTitleTable::C_DESC ;
		$this->fldorg = JobTitleTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>