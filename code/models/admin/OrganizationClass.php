<?php
require_once (PATH_TABLES . "admin/OrganizationTable.php") ;

class OrganizationClass {
	
	private $db ;
	private $tbl = "" ;
	private $fldid = "";
	private $fldcode ;
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = OrganizationTable::C_TABLE ;
		$this->fldid = OrganizationTable::C_ORG_ID ;
		$this->fldcode = OrganizationTable::C_CODE ;
	}
	function __destruct() {
	}
	function addRecord($datas) {
		$id = 0 ;
		$sql = "insert into " . $this->tbl  ;
		$fld = "" ;
		$val = "" ;
		$fs = "" ;
		$params = array() ;
		if (is_array($datas) && count($datas) > 0) {
			foreach ($datas as $data) {
				$fld .= $fs . $data['field'] ;
				$val .= $fs . $this->db->formatValueParam($data['field']) ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
				$fs = ", " ;
			}
			$sql .= " (" . $fld . ") values (" . $val . ")";
			$id = $this->db->insertRowGetId($sql,$params) ;
		}
		return $id ;
	}
	function updateRecord($id,$datas) {
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
			$params[] = $this->db->valueParam($this->fldid,$id) ;
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
		}
	}
	function deleteRecord($id) {
		$sql = "delete from " . $this->tbl 
			. " where " . $this->db->fieldParam($this->fldid) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function getRecord($id) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam($this->fldid) ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
	function getValueList() {
		$sql = "select " . $this->fldid . " as code, " . $this->flddesc . " as [desc] "
			. " from " . $this->tbl 
			. " order by " . $this->flddesc ;
		$params = array() ;
		return $this->db->getTable($sql,$params) ;
	}
	function isFound($id) {
		$filter = $this->db->fieldParam($this->fldid) ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		if ($this->db->getRowsCount($this->tbl,$filter,$params) > 0 )
			return true ;
		else 
			return false ;
	}
	function initTable($condition) {
		return $this->db->initTable($this->tbl,$condition) ;
	}
	function getTable($filter="",$orderby="",$params=null) {
		$sql = "select * from " . $this->tbl ;
		if (!empty($filter))
			$sql .= " where " . $filter ;
		if (!empty($orderby))
			$sql .= " order by " . $orderby ;
			
		return $this->db->getTable($sql,$params) ;
	}
	function getRecordByCode($code) {
		$sql = "select * from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldcode)  ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldcode,$code) ;

		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
	function isCodeFound($code) {
		$filter = $this->db->fieldParam($this->fldcode);
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldcode,$code) ;

		if ($this->db->getRowsCount($this->tbl,$filter,$params) > 0 )
			return true ;
		else 
			return false ;
	}
}
?>