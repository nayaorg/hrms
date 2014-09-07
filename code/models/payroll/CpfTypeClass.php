<?php
require_once (PATH_TABLES . "payroll/CpfTypeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class CpfTypeClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = CpfTypeTable::C_TABLE ;
		$this->fldid = CpfTypeTable::C_ID ;
		$this->flddesc = CpfTypeTable::C_DESC ;
		$this->fldorg = CpfTypeTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>