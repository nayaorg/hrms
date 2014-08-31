<?php
require_once (PATH_TABLES . "claims/ClaimDetailTable.php") ;
require_once (PATH_TABLES . "claims/ClaimHeaderTable.php") ;
require_once (PATH_TABLES . "claims/ExpenseItemTable.php") ;
require_once (PATH_TABLES . "claims/ClaimLimitTable.php") ;
require_once (PATH_TABLES . "claims/ClaimGroupTable.php") ;
require_once (PATH_TABLES . "claims/ClaimGroupEmpTable.php") ;
require_once (PATH_MODELS . "base/ClaimBase.php") ;

class ClaimDetailClass extends ClaimBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ClaimDetailTable::C_TABLE ;
		$this->fldid = ClaimDetailTable::C_ID ;
		$this->fldid2 = ClaimDetailTable::C_LINE_NO ;
		$this->flddesc = ClaimDetailTable::C_DESC ;
		$this->fldorg = ClaimDetailTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}

	function getItems($id) {		
		$sql = "SELECT * FROM " .ClaimDetailTable::C_TABLE . 
		" WHERE " . ClaimDetailTable::C_ID . " = " . $id;
		return $this->db->getTable($sql) ;
	}
	
	function getLimit($exp_id, $claim_id){
		
	
		//find employee id and claim date
		$sql = "SELECT H." . ClaimHeaderTable::C_EMP . ", H." . ClaimHeaderTable::C_DATE . " ";
		$sql .= "FROM " . ClaimHeaderTable::C_TABLE . " H ";
		$sql .= "WHERE H." . ClaimHeaderTable::C_ID . " = " . $claim_id;
		
		$rows = $this->db->getTable($sql);
		
		$emp_id = 0;
		$date = strtotime('now');
		
		if(is_null($rows) || count($rows) == 0){
		} else {
			$emp_id = $rows[0][ClaimHeaderTable::C_EMP];
			$date = strtotime($rows[0][ClaimHeaderTable::C_DATE]);
		}
		
		//find claim group id
		$sql = "SELECT E." . ClaimGroupEmpTable::C_CLAIM_GROUP_ID . " ";
		$sql .= "FROM " . ClaimGroupEmpTable::C_CLAIM_GROUP_EMP_TABLE . " E ";
		$sql .= "WHERE E." . ClaimGroupEmpTable::C_CLAIM_GROUP_EMP_ID . " = " . $emp_id;
		
		$rows = $this->db->getTable($sql);
		
		$claim_group_id = -1;
		if(is_null($rows) || count($rows) == 0){
		} else {
			$claim_group_id = $rows[0][ClaimGroupEmpTable::C_CLAIM_GROUP_ID];
		}
		
		//find limit and type per expense item per claim group
		$sql = "SELECT L." . ClaimLimitTable::C_AMOUNT . ", L." . ClaimLimitTable::C_TYPE . " ";
		$sql .= "FROM " . ClaimLimitTable::C_TABLE . " L ";
		$sql .= "WHERE L." . ClaimLimitTable::C_EXPENSE . " = " . $exp_id . " ";
		$sql .= "AND L." . ClaimLimitTable::C_GROUP . " = " . $claim_group_id . " ";
		
		$rows = $this->db->getTable($sql);
		
		$limit_type = 0;
		$limit_amount = 0;
		
		if(is_null($rows) || count($rows) == 0){
		} else {
			$limit_type = $rows[0][ClaimLimitTable::C_TYPE];
			$limit_amount = $rows[0][ClaimLimitTable::C_AMOUNT];
		}
		
		//Log::write($limit_type . ' ' . $limit_amount);
	
	
		//find amount that has been used
		$sql = "SELECT H." . ClaimHeaderTable::C_ID . ", D." . ClaimDetailTable::C_APPROVED_AMT . " ";
		$sql .= "FROM " . ClaimHeaderTable::C_TABLE . " H ";
		$sql .= "LEFT OUTER JOIN " . ClaimDetailTable::C_TABLE . " D ";
		$sql .= "ON H." . ClaimHeaderTable::C_ID . " = D." . ClaimDetailTable::C_ID . " ";
		$sql .= "WHERE H." . ClaimHeaderTable::C_ID . " <> " . $claim_id . " ";
		$sql .= "AND H." . ClaimHeaderTable::C_STATUS . " = 1 ";
		$sql .= "AND D." . ClaimDetailTable::C_EXPENSE . " = " . $exp_id . " ";
		$sql .= "AND D." . ClaimDetailTable::C_STATUS . " = 2 ";
		
		if($limit_type == 0){
			$sql .= "AND 1 = 2";
		} else if($limit_type == 3){
			$lower_limit = date('Y-m', $date) . "-01 00:00:00.000";
			$upper_limit = date('Y-m', strtotime("+1 month", $date)) . "-01 00:00:00.000";
			$sql .= "AND H." . ClaimHeaderTable::C_DATE . " >= '" . $lower_limit . "' ";
			$sql .= "AND H." . ClaimHeaderTable::C_DATE . " < '" . $upper_limit . "' ";
		} else if($limit_type == 4){
			$lower_limit = date('Y', $date) . "-01-01 00:00:00.000";
			$upper_limit = date('Y', strtotime("+1 year", $date)) . "-01-01 00:00:00.000";
			$sql .= "AND H." . ClaimHeaderTable::C_DATE . " >= '" . $lower_limit . "' ";
			$sql .= "AND H." . ClaimHeaderTable::C_DATE . " < '" . $upper_limit . "' ";
		}
		
		$has_been_used = 0;
		
		$rows = $this->db->getTable($sql);
		
		if(is_null($rows) || count($rows) == 0){
		} else {
			foreach($rows as $row){
				$has_been_used += $row[ClaimDetailTable::C_APPROVED_AMT];
			}
		}
		
		return ($limit_amount - $has_been_used);
		//return true;
	}
}
?>