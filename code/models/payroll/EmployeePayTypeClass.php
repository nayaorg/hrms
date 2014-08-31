<?php
require_once (PATH_TABLES . "payroll/EmployeePayTypeTable.php") ;

class EmployeePayTypeClass {
	private $db ;
	private $tbl ;
	private $fldid ;
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = EmployeePayTypeTable::C_TABLE ;
		$this->fldid = EmployeePayTypeTable::C_ID ;
	}
	function __destruct() {
	}
	function addRecord($datas) {
		$sql = "insert into " . $this->tbl ; ;
		$fld = "" ;
		$val = "" ;
		$fs = "" ;
		$params = array() ;
		try {
			if (is_array($datas) && count($datas) > 0) {
				foreach ($datas as $data) {
					$fld .= $fs . $data['field'] ;
					$val .= $fs . $this->db->formatValueParam($data['field']) ;
					$params[] = $this->db->valueParam($data['field'],$data['value']) ;
					$fs = ", " ;
				}
				$sql .= " (" . $fld . ") values (" . $val . ")";
				$this->db->insertRow($sql,$params) ;
				return $sql ;
			} else {
				return "error" ;
			}
		} catch (Exception $e) {
			return $e->getMessage(); ;
		}
	}
	function deleteRecord($empid) {
		$sql = "delete from " . $this->tbl . " where " . $this->db->fieldParam($this->fldid) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function getRecord($empid) {
		$sql = "select * from " . $this->tbl . " where " . $this->db->fieldParam($this->fldid) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;
		return $this->db->getRow($sql,$params) ;
	}
	function initTable($condition) {
		return $this->db->initTable($this->tbl,$condition) ;
	}
	function getTable($filter,$orderby,$params) {
		$sql = "select * from " . $this->tbl ;
		if (!empty($filter))
			$sql .= " where " . $filter ;
		if (!empty($orderby))
			$sql .= " order by " . $orderby ;
			
		return $this->db->getTable($sql,$params) ;
	}
}
?>