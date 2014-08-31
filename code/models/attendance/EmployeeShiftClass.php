<?php
require_once (PATH_TABLES . "attendance/EmployeeShiftTable.php") ;
require_once (PATH_TABLES . "hr/EmployeeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class EmployeeShiftClass extends MasterBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = EmployeeShiftTable::C_TABLE ;
		$this->fldid = EmployeeShiftTable::C_ID ;
		$this->fldorg = EmployeeShiftTable::C_ORG_ID ;
		$this->flddesc = EmployeeShiftTable::C_SHIFT_TYPE ;
	}
	function __destruct() {
	}
	function getRateGroupId($emp_id){
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(EmployeeShiftTable::C_ID) ;
			
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeShiftTable::C_ID,$emp_id) ;
			
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0][EmployeeShiftTable::C_RATE_ID] ;
	}
	function getTimeCardId($emp_id){
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(EmployeeShiftTable::C_ID) ;
			
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeShiftTable::C_ID,$emp_id) ;
			
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0][EmployeeShiftTable::C_TIMECARD_ID] ;
	}
	function fillEmployeeShift(){
		$sql = "select E.* from " . EmployeeTable::C_TABLE . " E "
			. "LEFT OUTER JOIN " . EmployeeShiftTable::C_TABLE . " ES "
			. "ON (E." . EmployeeTable::C_ID . " = ES." . EmployeeShiftTable::C_ID . ") "
			. "WHERE ES." . EmployeeShiftTable::C_ID . " is null";
			
		$rows = $this->db->getRow($sql) ;
		
		foreach($rows as $row){
			$datas = array() ;
			$orgid = $_SESSION[SE_ORGID] ;
			$modifyby = $_SESSION[SE_USERID] ;
			$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
			$ws = $_SESSION[SE_REMOTE_IP] ;
			
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_ID,$row[EmployeeTable::C_ID]) ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_SHIFT_TYPE,ShiftType::Daily) ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_SHIFT_GROUP_ID,'0') ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_RATE_ID,0) ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_TIMECARD_ID,'0') ;
			
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_WS_ID,$ws) ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_MODIFY_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_CREATE_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_MODIFY_DATE,$modifydate) ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_CREATE_DATE,$modifydate) ;
			$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_ORG_ID,$orgid) ;
			
			$this->addRecord($datas);
		}
	}
}
?>