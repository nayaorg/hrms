<?php
require_once (PATH_TABLES . "claims/ClaimGroupTable.php") ;
require_once (PATH_TABLES . "claims/ClaimGroupHeadTable.php") ;
require_once (PATH_TABLES . "claims/ClaimGroupEmpTable.php") ;
require_once (PATH_TABLES . "hr/EmployeeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class ClaimGroupClass extends MasterBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ClaimGroupTable::C_TABLE ;
		$this->fldid = ClaimGroupTable::C_ID ;
		$this->flddesc = ClaimGroupTable::C_DESC ;
		$this->fldorg = ClaimGroupTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}
	
	function getGroupHeadTable($id) {		
		$sql = "SELECT e.EMP_ID , e.EMP_NAME FROM " . $this->tbl . 
		" t , " . EmployeeTable::C_TABLE." c WHERE c.CLAIM_GROUP_EMP_ID = e.EMP_ID AND CLAIM_GROUP_ID = 8";
		return $this->db->getTable($sql) ;
	}
	
	function getEmpListByManager($manager_id){
		$sql = "SELECT H." . ClaimGroupHeadTable::C_ID . " ";
		$sql .= "FROM " . ClaimGroupHeadTable::C_TABLE . " H ";
		$sql .= "WHERE " . ClaimGroupHeadTable::C_EMP . " = " . $manager_id;
		
		$rows = $this->db->getTable($sql);
		
		$listEmp = array();
		if(!is_null($rows) && count($rows) > 0){
			foreach($rows as $row){
				$sql = " SELECT E." . ClaimGroupEmpTable::C_CLAIM_GROUP_EMP_ID . " ";
				$sql .= " FROM " . ClaimGroupEmpTable::C_CLAIM_GROUP_EMP_TABLE . " E ";
				$sql .= " WHERE E." . ClaimGroupEmpTable::C_CLAIM_GROUP_ID . " = " . $row[ClaimGroupHeadTable::C_ID];
				
				$rows_emp = $this->db->getTable($sql);
				
				if(!is_null($rows_emp) && count($rows_emp) > 0){
					foreach($rows_emp as $row_emp){
						$listEmp[] = $row_emp[ClaimGroupEmpTable::C_CLAIM_GROUP_EMP_ID];
					}
				}
			}		
		}
		if(count($listEmp) > 0){
			$listEmp = array_unique($listEmp);
		}
		return $listEmp;
	}
}
?>