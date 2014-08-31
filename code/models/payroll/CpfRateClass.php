<?php
require_once (PATH_TABLES . "payroll/CpfRateTable.php") ;

class CpfRateClass {
	private $db ;
	private $tbl = "" ;
	private $fldtype = "";
	private $fldage = "" ;
	private $fldwage = "" ;
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = CpfRateTable::C_TABLE ;
		$this->fldtype = CpfRateTable::C_TYPE_ID ;
		$this->fldage = CpfRateTable::C_AGE_ID ;
		$this->fldwage = CpfRateTable::C_WAGE_ID ;
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
					$val .= $fs . ":" . $data['field'] ;
					$params[] = $this->db->valueParam($data['field'],$data['value']) ;
					$fs = ", " ;
				}
				$sql .= " (" . $fld . ") values (" . $val . ")";
				$this->db->insertRow($sql,$params) ;
				return true ;
			} else {
				return false ;
			}
		} catch (Exception $e) {
			return $e->getMessage(); ;
		}
	}
	function deleteRecord($typeid,$ageid,$wageid) {
		$sql = "delete from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldtype) .
			" and " . $this->db->fieldParam($this->fldage) .
			" and " . $this->db->fieldParam($this->fldwage) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldtype,$typeid) ;
		$params[] = $this->db->valueParam($this->fldage,$ageid) ;
		$params[] = $this->db->valueParam($this->fldwage,$wageid) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function deleteType($typeid) {
		$sql = "delete from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldtype) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldtype,$typeid) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function deleteAge($ageid) {
		$sql = "delete from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldage) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldage,$ageid) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function deleteWage($wageid) {
		$sql = "delete from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldwage) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldwage,$wageid) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function getRecord($typeid,$ageid,$wageid) {
		$sql = "select * from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldtype) .
			" and " . $this->db->fieldParam($this->fldage) .
			" and " . $this->db->fieldParam($this->fldwage) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldtype,$typeid) ;
		$params[] = $this->db->valueParam($this->fldage,$ageid) ;
		$params[] = $this->db->valueParam($this->fldwage,$wageid) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
	function getType($typeid) {
		$sql = "select * from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldtype) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldtype,$typeid) ;
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