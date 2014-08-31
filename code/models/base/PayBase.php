<?php
abstract class PayBase {
	protected $db ;
	protected $tbl = "" ;
	protected $fldid = "";
	protected $flddate = "" ;
	protected $fldcoy = "" ;
	protected $fldorg = "" ;
	
	function __construct() {
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
				return true ;
			} else {
				return false ;
			}
		} catch (Exception $e) {
			return $e->getMessage(); ;
		}
	}
	function deleteRecord($empid,$date) {
		$sql = "delete from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldid) .
			" and " . $this->db->fieldParam($this->flddate) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;
		$params[] = $this->db->valueParam($this->flddate,$date) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function deleteDate($date) {
		$sql = "delete from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->flddate) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->flddate,$date) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function deleteCompany($coyid,$date) {
		$sql = "delete from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldcoy) .
			" and " . $this->db->fieldParam($this->flddate) ;

		$params = array() ;
		$params[] = $this->db->valueParam($this->fldcoy,$coyid) ;
		$params[] = $this->db->valueParam($this->flddate,$date) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function getRecord($empid,$date,$orderby="") {
		$sql = "select * from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldid) .
			" and " . $this->db->fieldParam($this->flddate) ;
			
		if ($orderby != "")
			$sql .= " order by " . $orderby ;
			
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$empid) ;
		$params[] = $this->db->valueParam($this->flddate,$date) ;
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