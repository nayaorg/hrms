<?php
require_once (PATH_TABLES . "claims/ClaimLimitTable.php") ;
require_once (PATH_MODELS . "base/ClaimBase.php") ;

class ClaimLimitClass extends ClaimBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ClaimLimitTable::C_TABLE ;
		$this->fldid = ClaimLimitTable::C_EXPENSE ;
		$this->fldid2 = ClaimLimitTable::C_GROUP ;
		$this->fldorg = ClaimLimitTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}
	
	function getExpenseLimit($id,$orderby="") {
		$sql = "select * from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldid) .
			" order by " ;
			
		if ($orderby != "")
			$sql .= $orderby ;
		else
			$sql .= ClaimLimitTable::C_GROUP ;
			
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		return $this->db->getRow($sql,$params) ;
	}
	function deleteExpenseLimit($id) {
		$sql = "delete from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldid) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		return $this->db->deleteRows($sql,$params) ;
	}
}
?>