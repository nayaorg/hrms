<?php
abstract class ClaimBase {
	protected $db ;
	protected $tbl = "" ;
	protected $fldid = "";
	protected $fldid2 = "";
	protected $flddesc = "" ;
	protected $fldorg = "" ;
	
	function __construct() {
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
	function updateRecord($id,$datas,$id2=null) {
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
			
			if (isset($id2)) {
				$sql .= " and " . $this->db->fieldParam($this->fldid2) ;
				$params[] = $this->db->valueParam($this->fldid2,$id2) ;
			}
			
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
		}
	}
	function deleteRecord($id,$id2=null) {
		$sql = "delete from " . $this->tbl 
			. " where " . $this->db->fieldParam($this->fldid) ;
			
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		
		if (isset($id2)) {
			$sql .= " and " . $this->db->fieldParam($this->fldid2) ;
			$params[] = $this->db->valueParam($this->fldid2,$id2) ;
		}
		
		return $this->db->deleteRows($sql,$params) ;
	}
	function getRecord($id,$id2=null) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam($this->fldid) ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		
		if (isset($id2)) {
			$sql .= " and " . $this->db->fieldParam($this->fldid2) ;
			$params[] = $this->db->valueParam($this->fldid2,$id2) ;
		}
		
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
	function getValueList($orgid) {
		$sql = "select " . $this->fldid . " as code, " . $this->flddesc . " as [desc] "
			. " from " . $this->tbl 
			. " where " . $this->db->fieldParam($this->fldorg) 
			. " order by " . $this->flddesc ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldorg,$orgid) ;
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
	function getDescription($id) {
		$sql = "select " . $this->flddesc . " from " . $this->tbl
			. " where " . $this->db->fieldParam($this->fldid) ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return "" ;
		else 
			return $rows[0][$this->flddesc] ;
	}
} 