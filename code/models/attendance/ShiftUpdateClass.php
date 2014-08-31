<?php
require_once (PATH_TABLES . "attendance/ShiftUpdateTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;
require_once (PATH_TABLES . "attendance/ShiftDetailTable.php") ;
require_once (PATH_TABLES . "attendance/EmployeeShiftTable.php") ;
require_once (PATH_TABLES . "attendance/TimeCardTable.php") ;

class ShiftUpdateClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ShiftUpdateTable::C_TABLE ;
		$this->fldid = ShiftUpdateTable::C_ID ;
		$this->fldtype = ShiftUpdateTable::C_SHIFT_TYPE ;
		$this->fldgroup = ShiftUpdateTable::C_SHIFT_GROUP_ID ;
		$this->fldorg = ShiftUpdateTable::C_ORG_ID ;

	}
	function __destruct() {
	}
	function getShiftUpdateRecord($group, $shiftType, $groupId, $empId, $month) {
		
		$sql = "select * from " . ShiftUpdateTable::C_TABLE 
			. " where " . $this->db->fieldParam(ShiftUpdateTable::C_MONTH) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ShiftUpdateTable::C_MONTH, $month) ;
		
		if($group=='C'){
			$sql.= " and " . $this->db->fieldParam(ShiftUpdateTable::C_SHIFT_GROUP_ID) 
				. " and " . $this->db->fieldParam(ShiftUpdateTable::C_SHIFT_TYPE); 
				
			$params[] = $this->db->valueParam(ShiftUpdateTable::C_SHIFT_GROUP_ID, $groupId) ;
			$params[] = $this->db->valueParam(ShiftUpdateTable::C_SHIFT_TYPE, $shiftType) ;
		
		}else if($group=='I'){
			$sql.= " and " . $this->db->fieldParam(ShiftUpdateTable::C_EMP_ID) ; 
			$params[] = $this->db->valueParam(ShiftUpdateTable::C_SHIFT_EMP_ID, $empId) ;
		}
		
			
		$rows = $this->db->getRow($sql,$params) ;
		
		if (is_null($rows) || count($rows) == 0){
			//return getDefaultShiftUpdateRecord($group, $shiftType, $groupId, $empId);
			return null;
		}else{
			return $rows[0];
		}
			
	}
	
	function getDefaultShiftUpdateRecord($group, $shiftType, $groupId, $empId){
		/*if($group=='I'){
			$from .=", " . EmployeeShiftTable::C_TABLE 
			$where.= " and " . $this->db->fieldParam(EmployeeShiftTable::C_EMP_ID) ; 
			$params[] = $this->db->valueParam(EmployeeShiftTable::C_EMP_ID, $empId) ;
		}*/
		$from="";
		$where="";
		
		$sql = "select * from " . ShiftDetailTable::C_TABLE . $from
			. " where " . $this->db->fieldParam(ShiftDetailTable::C_SHIFT_GROUP_ID) 
			. " and " . $this->db->fieldParam(ShiftDetailTable::C_SHIFT_TYPE) . $where ;
		$params = array() ;
	
		$params[] = $this->db->valueParam(ShiftDetailTable::C_SHIFT_GROUP_ID, $groupId) ;
		$params[] = $this->db->valueParam(ShiftDetailTable::C_SHIFT_TYPE, $shiftType) ;
	
		$rows = $this->db->getRow($sql,$params) ;
		
		if (is_null($rows) || count($rows) == 0)
			return null;
		else{
			
			return $rows[0] ;
		}
	}
	
	function checkShiftUpdate($emp_id, $month, $year, $shift_group_id, $shift_type){
		//Log::write($emp_id . " a " . $month . " b " . $year . " c " . $shift_group_id . " d " . $shift_type);
		$sql = "SELECT * FROM " . ShiftUpdateTable::C_TABLE . " SU ";
		$sql .= "WHERE SU." . ShiftUpdateTable::C_MONTH . " = '" . $month . $year . "'";
		
		$rows = $this->db->getTable($sql);
		
		if(is_null($rows) || count($rows) == 0){
			return -1;
		} else {
			if($emp_id != ''){
				$sql = "SELECT * FROM " . ShiftUpdateTable::C_TABLE . " SU ";
				$sql .= "WHERE SU." . ShiftUpdateTable::C_MONTH . " = '" . $month . $year . "' ";
				$sql .= "AND SU." . ShiftUpdateTable::C_EMP_ID . " = " . $emp_id . " ";
				$sql .= "AND SU." . ShiftUpdateTable::C_EMP_ID . " > 0";
				
				$rows = $this->db->getTable($sql);
				if(is_null($rows) || count($rows) == 0){
					return -1;
				} else {
					return $rows[0][ShiftUpdateTable::C_ID];
				}
			} else {
				$sql = "SELECT * FROM " . ShiftUpdateTable::C_TABLE . " SU ";
				$sql .= "WHERE SU." . ShiftUpdateTable::C_MONTH . " = '" . $month . $year . "' ";
				$sql .= "AND SU." . ShiftUpdateTable::C_SHIFT_GROUP_ID . " = " . $shift_group_id . " ";
				$sql .= "AND SU." . ShiftUpdateTable::C_SHIFT_TYPE . " = " . $shift_type;
				
				$rows = $this->db->getTable($sql);
				if(is_null($rows) || count($rows) == 0){
					return -1;
				}else{
					return $rows[0][ShiftUpdateTable::C_ID];
				}
			}
		}
		return -1;
	}
}
?>