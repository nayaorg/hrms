<?php
require_once (PATH_TABLES . "payroll/PayHeaderTable.php") ;
require_once (PATH_MODELS . "base/PayBase.php") ;

class PayHeaderClass extends PayBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = PayHeaderTable::C_TABLE ;
		$this->fldid = PayHeaderTable::C_EMP_ID ;
		$this->flddate = PayHeaderTable::C_END ;
		$this->fldcoy = PayHeaderTable::C_COY_ID ;
		$this->fldorg = PayHeaderTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function updateRecord($empid,$date,$datas) {
		$sql = "update " . $this->tbl . " set " ;
		$fs = "" ;
		$params = array() ;
		if (is_array($datas) && count($datas) > 0) {
			foreach ($datas as $data) {
				$sql .= $fs . $this->db->fieldParam($data['field']) ;
				$fs = ", " ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
			}
			$sql .= " where " . $this->db->fieldParam($this->fldid) . 
				" and " . $this->db->fieldParam($this->flddate) ;
			$params[] = $this->db->valueParam($this->fldid,$empid) ;
			$params[] = $this->db->valueParam($this->flddate,$date) ;
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
		}
	}
}
?>