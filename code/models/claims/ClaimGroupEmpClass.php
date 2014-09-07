<?php
require_once (PATH_TABLES . "claims/ClaimGroupEmpTable.php") ;
require_once (PATH_MODELS . "base/ClaimBase.php") ;

class ClaimGroupEmpClass extends ClaimBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ClaimGroupEmpTable::C_CLAIM_GROUP_EMP_TABLE ;
		$this->fldid = ClaimGroupEmpTable::C_CLAIM_GROUP_ID ;
		$this->fldid2 = ClaimGroupEmpTable::C_CLAIM_GROUP_EMP_ID ;
		$this->fldorg = ClaimGroupEmpTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}

	function getGroupEmpTable($id) {		
		$sql = "SELECT e.EMP_ID , e.EMP_NAME FROM " . $this->tbl . 
		" c , " . EmployeeTable::C_TABLE." e WHERE c.CLAIM_GROUP_EMP_ID = e.EMP_ID AND c.CLAIM_GROUP_ID = " . $id;
		return $this->db->getTable($sql) ;
	}
}
?>