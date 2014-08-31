<?php
require_once (PATH_TABLES . "claims/ClaimGroupHeadTable.php") ;
require_once (PATH_MODELS . "base/ClaimBase.php") ;

class ClaimGroupHeadClass extends ClaimBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ClaimGroupHeadTable::C_TABLE ;
		$this->fldid = ClaimGroupHeadTable::C_ID ;
		$this->fldid2 = ClaimGroupHeadTable::C_EMP ;
		$this->fldorg = ClaimGroupHeadTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}
	
	function getGroupHeadTable($id) {		
		$sql = "SELECT e.EMP_ID , e.EMP_NAME FROM " . $this->tbl . 
		" c , " . EmployeeTable::C_TABLE." e WHERE c.CLAIM_GROUP_EMP_ID = e.EMP_ID AND c.CLAIM_GROUP_ID = " . $id;
		return $this->db->getTable($sql) ;
	}
}
?>