<?php
require_once (PATH_TABLES . "hr/EmployeeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class EmployeeClass extends MasterBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = EmployeeTable::C_TABLE ;
		$this->fldid = EmployeeTable::C_ID ;
		$this->fldorg = EmployeeTable::C_ORG_ID ;
		$this->flddesc = EmployeeTable::C_NAME ;
	}
	function __destruct() {
	}
	function checkCode($code, $id){
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(EmployeeTable::C_CODE)
			. " AND " . EmployeeTable::C_ID . " <> " . $id;
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_CODE,$code) ;
		$rows = $this->db->getRow($sql,$params) ;
		
		return (is_null($rows) || count($rows) == 0);
	}
}
?>