<?php
require_once (PATH_TABLES . "hr/EmployeeTypeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class EmployeeTypeClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = EmployeeTypeTable::C_TABLE ;
		$this->fldid = EmployeeTypeTable::C_ID ;
		$this->flddesc = EmployeeTypeTable::C_DESC ;
		$this->fldorg = EmployeeTypeTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>