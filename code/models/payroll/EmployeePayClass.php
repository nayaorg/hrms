<?php
require_once (PATH_TABLES . "payroll/EmployeePayTable.php") ;

class EmployeePayClass {
	private $db ;
	private $tbl ;
	private $fldid ;
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = EmployeePayTable::C_TABLE ;
		$this->fldid = EmployeePayTable::C_ID ;
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
	function updateRecord($empid,$datas) {
		$sql = "update " . $this->tbl . " set " ;
		$fs = "" ;
		$params = array() ;
		if (is_array($datas) && count($datas) > 0) {
			foreach ($datas as $data) {
				$sql .= $fs . $this->db->fieldParam($data['field']) ;
				$fs = ", " ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
			}
			$sql .= " where " . $this->db->fieldParam($this->fldid) ;
			$params[] = $this->db->valueParam($this->fldid,$empid) ;
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
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
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
	function isFound($empid) {
		$filter = $this->db->fieldParam($this->fldid) ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;
		if ($this->db->getRowsCount($this->tbl,$filter,$params) > 0 )
			return true ;
		else 
			return false ;
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