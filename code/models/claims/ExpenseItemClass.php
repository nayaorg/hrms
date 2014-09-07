<?php
require_once (PATH_TABLES . "claims/ExpenseItemTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class ExpenseItemClass extends MasterBase {

	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ExpenseItemTable::C_TABLE ;
		$this->fldid = ExpenseItemTable::C_ID ;
		$this->flddesc = ExpenseItemTable::C_DESC ;
		$this->fldorg = ExpenseItemTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}
	
	function getExpenseItem($id){
		$sql = "SELECT * FROM " . ExpenseItemTable::C_TABLE . " ";
		$sql .= "WHERE " . ExpenseItemTable::C_ID . " = " . $id;
		
		$rows = $this->db->getTable($sql);
		
		if(is_null($rows) || count($rows) == 0){
			return null;
		} else {			
			return $rows[0];
		}
	}
}
?>