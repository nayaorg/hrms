<?php
require_once (PATH_TABLES . "payroll/CpfWageTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class CpfWageClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = CpfWageTable::C_TABLE ;
		$this->fldid = CpfWageTable::C_ID ;
		$this->fldorg = CpfWageTable::C_ORG_ID ;
		$this->flddesc = CpfWageTable::C_DESC ;
	}
	function __destruct() {
	}
	function getWageId($wage) {
		$sql = " select * from " . $this->tbl 
			. " where " . CpfWageTable::C_FROM . " <= " . $wage
			. " and " . CpfWageTable::C_TO . " >= " . $wage ;

			$row = $this->db->getRow($sql) ;
		if (!is_null($row) && count($row) > 0) {
			return $row[0][$this->fldid] ;
		} else {
			return 0 ;
		}
	}
}
?>