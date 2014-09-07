<?php
require_once (PATH_TABLES . "claims/ClaimHeaderTable.php") ;
require_once (PATH_TABLES . "claims/ClaimDetailTable.php") ;
require_once (PATH_MODELS . "base/ClaimBase.php") ;

class ClaimHeaderClass extends ClaimBase {

	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ClaimHeaderTable::C_TABLE ;
		$this->fldid = ClaimHeaderTable::C_ID ;
		$this->flddesc = ClaimHeaderTable::C_DESC ;
		$this->fldorg = ClaimHeaderTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}
	
	function convertStatusStr($status){
		if($status == ClaimStatus::Open) return 'Open';
		if($status == ClaimStatus::Pending) return 'Pending';
		if($status == ClaimStatus::Approved) return 'Approved';
		if($status == ClaimStatus::Rejected) return 'Rejected';
		if($status == ClaimStatus::Cancelled) return 'Cancelled';
	}
	
	function getCountItem($type, $id){
		$sql = "SELECT COUNT(*) AS TOTAL, " . ClaimDetailTable::C_ID . " ";
		$sql .= "FROM " . ClaimDetailTable::C_TABLE . " H ";
		$sql .= "WHERE " . ClaimDetailTable::C_ID . " = " . $id . " ";
		
		if($type == 'Rejected') $sql .= "AND H." . ClaimDetailTable::C_STATUS . " = 1 ";
		else if($type == 'Approved') $sql .= "AND H." . ClaimDetailTable::C_STATUS . " = 2 ";
		
		$sql .= "GROUP BY " . ClaimDetailTable::C_ID . " ";
		
		$row = $this->db->getTable($sql);
		
		if(is_null($row) || count($row) == 0){
			return 0;
		}else{
			return $row[0]['TOTAL'];
		}
	}
}
?>