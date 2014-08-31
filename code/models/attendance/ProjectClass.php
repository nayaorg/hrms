<?php
require_once (PATH_TABLES . "attendance/ProjectTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class ProjectClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ProjectTable::C_TABLE ;
		$this->fldid = ProjectTable::C_ID ;
		$this->flddesc = ProjectTable::C_DESC ;
		$this->fldorg = ProjectTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>