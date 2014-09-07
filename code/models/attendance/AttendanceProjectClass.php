<?php
require_once (PATH_MODELS . "base/MasterBase.php") ;
require_once (PATH_TABLES . "attendance/AttendanceProjectTable.php") ;

class AttendanceProjectClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = AttendanceProjectTable::C_TABLE ;
		$this->fldid = AttendanceProjectTable::C_ID ;
		$this->flddesc = AttendanceProjectTable::C_EMP_ID ;
		$this->fldorg = AttendanceProjectTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function getAttendanceProjectRecords($empid, $date) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(AttendanceProjectTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(AttendanceProjectTable::C_DATE)
			. " order by " . AttendanceProjectTable::C_TIME_START;
		$params = array() ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_DATE,$date) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows ;
	}
	function updateAttendanceProjectRecord($empid,$date, $seq_number,$datas) {
		$sql = "update " . $this->tbl . " set " ;
		$fs = "" ;
		$params = array() ;
		if (is_array($datas) && count($datas) > 0) {
			foreach ($datas as $data) {
				$sql .= $fs . $this->db->fieldParam($data['field']) ;
				$fs = ", " ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
			}
			$sql .= " where " . $this->db->fieldParam(AttendanceProjectTable::C_EMP_ID) ;
			$sql .= " and " . $this->db->fieldParam(AttendanceProjectTable::C_DATE) ;
			$sql .= " and " . $this->db->fieldParam(AttendanceProjectTable::C_SEQ_NUMBER) ;
			
			$params[] = $this->db->valueParam(AttendanceProjectTable::C_EMP_ID,$empid) ;
			$params[] = $this->db->valueParam(AttendanceProjectTable::C_DATE,$date) ;
			$params[] = $this->db->valueParam(AttendanceProjectTable::C_SEQ_NUMBER,$seq_number) ;
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
		}
	}
	function getAttendanceProjectRecord($empid, $date, $seq_number) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(AttendanceProjectTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(AttendanceProjectTable::C_DATE)
			. " and " . $this->db->fieldParam(AttendanceProjectTable::C_SEQ_NUMBER);
		$params = array() ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_DATE,$date) ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_SEQ_NUMBER,$seq_number) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
	function deleteAttendanceProjectRecord($empid,$date, $seq_number) {
		$sql = "delete from " . $this->tbl 
			. " where " . $this->db->fieldParam(AttendanceProjectTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(AttendanceProjectTable::C_SEQ_NUMBER) 
			. " and " . $this->db->fieldParam(AttendanceProjectTable::C_DATE);

		$params = array() ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_DATE,$date) ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_SEQ_NUMBER,$seq_number) ;
		
		return $this->db->deleteRows($sql,$params) ;
	}
	function getLastSeqNumber($empid, $date) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(AttendanceProjectTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(AttendanceProjectTable::C_DATE)
			. " order by " . AttendanceProjectTable::C_SEQ_NUMBER . " DESC";
		$params = array() ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(AttendanceProjectTable::C_DATE,$date) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return 1 ;
		else 
			return $rows[0][AttendanceProjectTable::C_SEQ_NUMBER] + 1 ;
	}
	
}
?>