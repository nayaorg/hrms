<?php
require_once (PATH_TABLES . "attendance/OverTimeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class OverTimeClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = OverTimeTable::C_TABLE ;
		$this->fldid = OverTimeTable::C_ID ;
		$this->flddesc = OverTimeTable::C_DESC ;
		$this->fldorg = OverTimeTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function updateOvertimeRecord($empid,$date,$datas,$overtimeId=-1) {
		$sql = "update " . $this->tbl . " set " ;
		$fs = "" ;
		$params = array() ;
		if (is_array($datas) && count($datas) > 0) {
			foreach ($datas as $data) {
				$sql .= $fs . $this->db->fieldParam($data['field']) ;
				$fs = ", " ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
			}
			$sql .= " where " . $this->db->fieldParam(OvertimeTable::C_EMP_ID) ;
			$sql .= " and " . $this->db->fieldParam(OvertimeTable::C_DATE) ;
			$sql .= " and " . $this->db->fieldParam(OvertimeTable::C_OVERTIME_ID) ;
			
			$params[] = $this->db->valueParam(OvertimeTable::C_EMP_ID,$empid) ;
			$params[] = $this->db->valueParam(OvertimeTable::C_DATE,$date) ;
			$params[] = $this->db->valueParam(OvertimeTable::C_OVERTIME_ID,$overtimeId) ;
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
		}
	}
	function deleteOvertimeRecord($empid,$date,$overtimeId=-1) {
		$sql = "delete from " . $this->tbl 
			. " where " . $this->db->fieldParam(OvertimeTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(OvertimeTable::C_DATE)
			. " and " . $this->db->fieldParam(OvertimeTable::C_OVERTIME_ID) ;

		$params = array() ;
		
		$params[] = $this->db->valueParam(OvertimeTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_DATE,$date) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_OVERTIME_ID,$overtimeId) ;
		
		return $this->db->deleteRows($sql,$params) ;
	}
	function getOvertimeRecord($empid, $date, $overtimeId=-1) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(OvertimeTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(OvertimeTable::C_DATE)
			. " and " . $this->db->fieldParam(OvertimeTable::C_OVERTIME_ID);
			
		$params = array() ;
		
		$params[] = $this->db->valueParam(OvertimeTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_DATE,$date) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_OVERTIME_ID,$overtimeId) ;
			
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
	function getOvertimeRecords($empid, $date) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(OvertimeTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(OvertimeTable::C_DATE)
			. " and " . OvertimeTable::C_OVERTIME_ID . " >= 0";
			
		$params = array() ;
		
		$params[] = $this->db->valueParam(OvertimeTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_DATE,$date) ;
			
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows ;
	}
	function getMaxOvertimeId($empid,$date) {
		$sql = "select * from " . $this->tbl 
			. " where " . $this->db->fieldParam(OvertimeTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(OvertimeTable::C_DATE)
			. " order by " . OvertimeTable::C_OVERTIME_ID . " DESC";

		$params = array() ;
		
		$params[] = $this->db->valueParam(OvertimeTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_DATE,$date) ;
		
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return 1 ;
		else 
			return ($rows[0][OvertimeTable::C_OVERTIME_ID] + 1);
	}
	function getOvertimeHour($empid,$date, $overtimeId=-1) {
		$sql = "select * from " . $this->tbl 
			. " where " . $this->db->fieldParam(OvertimeTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(OvertimeTable::C_DATE)
			. " and " . $this->db->fieldParam(OvertimeTable::C_OVERTIME_ID);

		$params = array() ;
		
		$params[] = $this->db->valueParam(OvertimeTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_DATE,$date) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_OVERTIME_ID,$overtimeId) ;
		
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return 0 ;
		else 
			return ($rows[0][OvertimeTable::C_HOUR]);
	}
}
?>