<?php
require_once (PATH_TABLES . "hr/DepartmentTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class DepartmentClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = DepartmentTable::C_TABLE ;
		$this->fldid = DepartmentTable::C_ID ;
		$this->flddesc = DepartmentTable::C_DESC ;
		$this->fldorg = DepartmentTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>