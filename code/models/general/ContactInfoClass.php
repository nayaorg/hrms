<?php
require_once (PATH_TABLES . "general/ContactInfoTable.php") ;

class ContactInfoClass {
	private $db ;
	function __construct($db) {
		$this->db = $db ;
	}
	function __destruct() {
	}
	function addRecord($datas) {
		$sql = "insert into " . ContactInfoTable::C_TABLE ;
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
				return $sql ;
			} else {
				return "error" ;
			}
		} catch (Exception $e) {
			return $e->getMessage(); ;
		}
	}
	function deleteRecord($code,$id) {
		$sql = "delete from " . ContactInfoTable::C_TABLE . " where " . $this->db->fieldParam(ContactInfoTable::C_ID)
			. " and " . $this->db->fieldParam(ContactInfoTable::C_CODE);
		$params = array() ;
		$params[] = $this->db->valueParam(ContactInfoTable::C_ID,$id) ;
		$params[] = $this->db->valueParam(ContactInfoTable::C_CODE,$code) ;
		return $this->db->deleteRows($sql,$params) ;
	}
	function getRecord($code,$id) {
		$sql = "select * from " . ContactInfoTable::C_TABLE . " where " . $this->db->fieldParam(ContactInfoTable::C_ID) 
			. " and " . $this->db->fieldParam(ContactInfoTable::C_CODE) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ContactInfoTable::C_ID,$id) ;
		$params[] = $this->db->valueParam(ContactInfoTable::C_CODE,$code) ;
		return $this->db->getRow($sql,$params) ;
	}
	function initTable($condition) {
		return $this->db->initTable(ContactInfoTable::C_TABLE,$condition) ;
	}
	function getTable($filter,$orderby,$params) {
		$sql = "select * from " . ContactInfoTable::C_TABLE ;
		if (!empty($filter))
			$sql .= " where " . $filter ;
		if (!empty($orderby))
			$sql .= " order by " . $orderby ;
			
		return $this->db->getTable($sql,$params) ;
	}
}
?>