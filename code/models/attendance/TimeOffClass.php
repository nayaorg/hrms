<?php
require_once (PATH_TABLES . "attendance/TimeOffTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class TimeOffClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = TimeOffTable::C_TABLE ;
		$this->fldid = TimeOffTable::C_ID ;
		$this->flddesc = TimeOffTable::C_DESC ;
		$this->fldorg = TimeOffTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function updateTimeOffRecord($empid,$date,$datas) {
		$sql = "update " . $this->tbl . " set " ;
		$fs = "" ;
		$params = array() ;
		if (is_array($datas) && count($datas) > 0) {
			foreach ($datas as $data) {
				$sql .= $fs . $this->db->fieldParam($data['field']) ;
				$fs = ", " ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
			}
			$sql .= " where " . $this->db->fieldParam(TimeOffTable::C_EMP_ID) ;
			$sql .= " and " . $this->db->fieldParam(TimeOffTable::C_DATE_OFF) ;
			
			$params[] = $this->db->valueParam(TimeOffTable::C_EMP_ID,$empid) ;
			$params[] = $this->db->valueParam(TimeOffTable::C_DATE_OFF,$date) ;
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
		}
	}
	function deleteTimeOffRecord($empid,$date) {
		$sql = "delete from " . $this->tbl 
			. " where " . $this->db->fieldParam(TimeOffTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(TimeOffTable::C_DATE_OFF);

		$params = array() ;
		$params[] = $this->db->valueParam(TimeOffTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(TimeOffTable::C_DATE_OFF,$date) ;
		
		return $this->db->deleteRows($sql,$params) ;
	}
	function getTimeOffRecord($empid, $date) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(TimeOffTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(TimeOffTable::C_DATE_OFF);
		$params = array() ;
		$params[] = $this->db->valueParam(TimeOffTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(TimeOffTable::C_DATE_OFF,$date) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
}
?>