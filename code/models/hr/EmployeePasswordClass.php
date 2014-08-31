<?php
require_once (PATH_TABLES . "hr/EmployeePasswordTable.php") ;
require_once (PATH_TABLES . "hr/EmployeeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class EmployeePasswordClass extends MasterBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = EmployeePasswordTable::C_TABLE ;
		$this->fldid = EmployeePasswordTable::C_ID ;
		$this->flddesc = EmployeePasswordTable::C_CODE ;
	}
	function __destruct() {
	}
	function updateTable(){
		//delete
		$sql = "DELETE FROM " . EmployeePasswordTable::C_TABLE . " WHERE ";
		$sql .= EmployeePasswordTable::C_ID . " NOT IN (SELECT " . EmployeeTable::C_ID . " FROM " . EmployeeTable::C_TABLE . ")" ;
		
		$this->db->deleteRows($sql) ;
		
		//insert
		$sql = "SELECT * FROM " . EmployeeTable::C_TABLE . " WHERE ";
		$sql .= EmployeeTable::C_ID . " NOT IN (SELECT " . EmployeePasswordTable::C_ID . " FROM " . EmployeePasswordTable::C_TABLE . ")" ;
		
		$rows = $this->db->getRow($sql) ;
		
		if(is_null($rows) || count($rows) == 0){
		}else {
			foreach($rows as $r){
				$datas = array() ;
				$datas[] = $this->db->fieldValue(EmployeePasswordTable::C_ID,$r[EmployeeTable::C_ID]) ;
				$datas[] = $this->db->fieldValue(EmployeePasswordTable::C_CODE,$r[EmployeeTable::C_CODE]) ;
				$datas[] = $this->db->fieldValue(EmployeePasswordTable::C_PASSWORD,"") ;

				$this->addRecord($datas);
			}
		}
		
		//update code
		$sql = "SELECT E.* FROM " . EmployeeTable::C_TABLE . " E ";
		$sql .= "LEFT OUTER JOIN " . EmployeePasswordTable::C_TABLE . " P on (E." . EmployeeTable::C_ID . " = P." . EmployeePasswordTable::C_ID . ") ";
		$sql .= "WHERE E." . EmployeeTable::C_CODE . " <> P." . EmployeePasswordTable::C_CODE;
		
		$rows = $this->db->getRow($sql) ;
		
		if(is_null($rows) || count($rows) == 0){
		}else {
			foreach($rows as $r){
				$datas = array();

				$datas[] = $this->db->fieldValue(EmployeePasswordTable::C_CODE,$r[EmployeeTable::C_CODE]) ;
				$this->updateRecord($r[EmployeeTable::C_ID], $datas);
			}
		}
	}
}
?>