<?php
require_once (PATH_TABLES . "attendance/AttendanceTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;
require_once (PATH_TABLES . "attendance/ShiftDetailTable.php") ;
require_once (PATH_MODELS . "attendance/ShiftUpdateClass.php") ;
require_once (PATH_MODELS . "attendance/EmployeeShiftClass.php") ;
require_once (PATH_TABLES . "attendance/TimeCardTable.php") ;
require_once (PATH_MODELS . "attendance/AttendanceProjectClass.php") ;
require_once (PATH_MODELS . "attendance/OvertimeClass.php") ;

class AttendanceClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = AttendanceTable::C_TABLE ;
		$this->fldid = AttendanceTable::C_ID ;
		$this->flddesc = AttendanceTable::C_EMP_ID ;
		$this->fldorg = AttendanceTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function checkOverlap($empid, $date, $time_start, $time_end, $seq_number=-1){
		$sql = "SELECT * FROM " . AttendanceProjectTable::C_TABLE . 
				" WHERE " . AttendanceProjectTable::C_EMP_ID . " = " . $empid .
				" AND " . AttendanceProjectTable::C_DATE . " = '" . $date . "'";
		if($seq_number != -1){
			$sql .= " AND " . AttendanceProjectTable::C_SEQ_NUMBER . " <> " . $seq_number;
		}
		
		$rows = $this->db->getTable($sql);
		
		$time_start = strtotime($time_start);
		$time_end = strtotime($time_end);
		
		foreach($rows as $row){
			$temp_start = strtotime(substr($date, 0, 10) . " " . $row[AttendanceProjectTable::C_TIME_START]);
			$temp_end = strtotime(substr($date, 0, 10) . " " . $row[AttendanceProjectTable::C_TIME_END]);
			
			if($time_start < $temp_start){
				if($time_end >= $temp_start){
					return false;
				}
			} else if($time_start > $temp_end) {
				
			} else {
				return false;
			}
		}
		return true;
	}
	function updateDetailAttendance($empid, $date, $shift_start, $shift_end, $tolerance, $limit_before, $limit_after){
		$sql = "SELECT * FROM " . AttendanceProjectTable::C_TABLE . 
				" WHERE " . AttendanceProjectTable::C_EMP_ID . " = " . $empid .
				" AND " . AttendanceProjectTable::C_DATE . " = '" . $date . "'" . 
				" ORDER BY " . AttendanceProjectTable::C_TIME_START . " ASC";
		
		$row = $this->db->getTable($sql);
		
		$time_start = strtotime(substr($date, 0, 11) . $row[0][AttendanceProjectTable::C_TIME_START]);
		
		$sql = "SELECT * FROM " . AttendanceProjectTable::C_TABLE . 
				" WHERE " . AttendanceProjectTable::C_EMP_ID . " = " . $empid .
				" AND " . AttendanceProjectTable::C_DATE . " = '" . $date . "'" . 
				" ORDER BY " . AttendanceProjectTable::C_TIME_END . " DESC";
		
		$row_2 = $this->db->getTable($sql);
		
		$time_end = strtotime(substr($date, 0, 11) . $row_2[0][AttendanceProjectTable::C_TIME_END]);
		
		$countLateIn = 0;
		$countEarlyOut = 0;
		$countOvertimeIn = 0;
		$countOvertimeOut = 0;
		
		$shift_start_time = strtotime($shift_start);
		$shift_end_time = strtotime($shift_end);
		
		if($time_start > $shift_start_time){
			$minute = ($time_start - $shift_start_time) / 60;
			if($minute > $tolerance){
				$countLateIn = $countLateIn + ($minute - $tolerance);
			}
		} else {
			$minute = ($shift_start_time - $time_start) / 60;
			$minute = ($minute < $tolerance) ? 0 : $minute;
			$countOvertimeIn = $countOvertimeIn + ( ($minute > $limit_before * 60) ? ($limit_before * 60) : $minute);
		}
		
		if($time_end < $shift_end_time){
			$minute = ($shift_end_time - $time_end) / 60;
			if($minute > $tolerance){
				$countEarlyOut = $countEarlyOut + ($minute - $tolerance);
			}
		} else {
			$minute = ($time_end - $shift_end_time) / 60;
			$minute = ($minute < $tolerance) ? 0 : $minute;
			$countOvertimeOut = $countOvertimeOut + (( ($minute > $limit_after * 60) ? ($limit_after * 60) : $minute));
		}
		
		$datas_at = array();
		$datas_at[] = $this->db->fieldValue(AttendanceTable::C_LATE_IN,$countLateIn) ;
		$datas_at[] = $this->db->fieldValue(AttendanceTable::C_EARLY_OUT,$countEarlyOut) ;
		$datas_at[] = $this->db->fieldValue(AttendanceTable::C_TIME_START,$row[0][AttendanceProjectTable::C_TIME_START]) ;
		$datas_at[] = $this->db->fieldValue(AttendanceTable::C_TIME_END,$row_2[0][AttendanceProjectTable::C_TIME_END]) ;
		
		$this->updateAttendanceRecord($empid, $date, $datas_at);
		
		$cls_ot = new OvertimeClass($this->db);
		
		if($countOvertimeIn > 0){
			if(is_null($cls_ot->getOvertimeRecord($empid, $date, -1))){
				$datas_ot = array() ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_EMP_ID,$empid) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_DATE,$date, "") ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_OVERTIME_ID, -1, "") ;
				
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_HOUR, $countOvertimeIn) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_PROJECT_ID, $row[0][AttendanceProjectTable::C_PROJECT_ID]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_DESC, "Attendance's Overtime In") ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_TIME_START,$row[0][AttendanceProjectTable::C_TIME_START]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_TIME_END,substr($shift_start, 11, 7)) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_WS_ID,$row[0][AttendanceProjectTable::C_WS_ID]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_MODIFY_BY,$row[0][AttendanceProjectTable::C_MODIFY_BY]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_CREATE_BY,$row[0][AttendanceProjectTable::C_CREATE_BY]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_MODIFY_DATE,$row[0][AttendanceProjectTable::C_MODIFY_DATE]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_CREATE_DATE,$row[0][AttendanceProjectTable::C_CREATE_DATE]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_ORG_ID,$row[0][AttendanceProjectTable::C_ORG_ID]) ;
				
				$ot_id = $cls_ot->addRecord($datas_ot);
			}
			else {
				$datas_ot = array();
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_HOUR,$countOvertimeIn) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_TIME_START,$row[0][AttendanceProjectTable::C_TIME_START]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_TIME_END,substr($shift_start, 11, 7)) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_PROJECT_ID,$row[0][AttendanceProjectTable::C_PROJECT_ID]) ;
				
				$cls_ot->updateOvertimeRecord($empid, $date, $datas_ot, -1);
			}
		} else {
			$cls_ot->deleteOvertimeRecord($empid, $date, -1);
		}
		
		if($countOvertimeOut > 0){
			if(is_null($cls_ot->getOvertimeRecord($empid, $date, -2))){
				$datas_ot = array() ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_EMP_ID,$empid) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_DATE,$date, "") ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_OVERTIME_ID, -2, "") ;
				
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_HOUR, $countOvertimeOut) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_PROJECT_ID, $row_2[0][AttendanceProjectTable::C_PROJECT_ID]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_DESC, "Attendance's Overtime Out") ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_TIME_START,substr($shift_end, 11, 7)) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_TIME_END,$row_2[0][AttendanceProjectTable::C_TIME_END]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_WS_ID,$row_2[0][AttendanceProjectTable::C_WS_ID]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_MODIFY_BY,$row_2[0][AttendanceProjectTable::C_MODIFY_BY]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_CREATE_BY,$row_2[0][AttendanceProjectTable::C_CREATE_BY]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_MODIFY_DATE,$row_2[0][AttendanceProjectTable::C_MODIFY_DATE]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_CREATE_DATE,$row_2[0][AttendanceProjectTable::C_CREATE_DATE]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_ORG_ID,$row_2[0][AttendanceProjectTable::C_ORG_ID]) ;
				
				$ot_id = $cls_ot->addRecord($datas_ot);
			} else {
				$datas_ot = array();
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_HOUR,$countOvertimeOut) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_TIME_START,substr($shift_end, 11, 7)) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_TIME_END,$row_2[0][AttendanceProjectTable::C_TIME_END]) ;
				$datas_ot[] = $this->db->fieldValue(OvertimeTable::C_PROJECT_ID,$row_2[0][AttendanceProjectTable::C_PROJECT_ID]) ;
				$cls_ot->updateOvertimeRecord($empid, $date, $datas_ot, -2);
			}
		} else {
			$cls_ot->deleteOvertimeRecord($empid, $date, -2);
		}
		
		
		unset($cls_ot);
	}
	function addAttendanceRecord($datas) {
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
	function updateAttendanceRecord($empid,$date,$datas) {
		$sql = "update " . $this->tbl . " set " ;
		$fs = "" ;
		$params = array() ;
		if (is_array($datas) && count($datas) > 0) {
			foreach ($datas as $data) {
				$sql .= $fs . $this->db->fieldParam($data['field']) ;
				$fs = ", " ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
			}
			$sql .= " where " . $this->db->fieldParam(AttendanceTable::C_EMP_ID) ;
			$sql .= " and " . $this->db->fieldParam(AttendanceTable::C_DATE) ;
			
			$params[] = $this->db->valueParam(AttendanceTable::C_EMP_ID,$empid) ;
			$params[] = $this->db->valueParam(AttendanceTable::C_DATE,$date) ;
			return $this->db->updateRow($sql,$params) ;
		} else  {
			return false ;
		}
	}
	function deleteAttendanceRecord($empid,$date) {
		$sql = "delete from " . $this->tbl 
			. " where " . $this->db->fieldParam(AttendanceTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(AttendanceTable::C_DATE);

		$params = array() ;
		$params[] = $this->db->valueParam(AttendanceTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(AttendanceTable::C_DATE,$date) ;
		
		return $this->db->deleteRows($sql,$params) ;
	}
	function getAttendanceRecord($empid, $date) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(AttendanceTable::C_EMP_ID) 
			. " and " . $this->db->fieldParam(AttendanceTable::C_DATE);
		$params = array() ;
		$params[] = $this->db->valueParam(AttendanceTable::C_EMP_ID,$empid) ;
		$params[] = $this->db->valueParam(AttendanceTable::C_DATE,$date) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
	function getHourDetail($row, $day) {

		$dte = date_create('now')->format('Y-m-d') ;
		$day2 = '';
		
		if($row[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Weekly){
			$day2=date('w', strtotime($day));
			$day2=($day2==0)?"07":str_pad($day2, "2", "0",STR_PAD_LEFT);
		}else{
			$day2=date('d', strtotime($day));
			$day2=str_pad($day2, "2", "0",STR_PAD_LEFT);
		}
		
		$datas = array() ;
			
		$datas['shift01'] = $row[ShiftDetailTable::C_SHIFT_01];
		$datas['shift02'] = $row[ShiftDetailTable::C_SHIFT_02];
		$datas['shift03'] = $row[ShiftDetailTable::C_SHIFT_03];
		$datas['shift04'] = $row[ShiftDetailTable::C_SHIFT_04];
		$datas['shift05'] = $row[ShiftDetailTable::C_SHIFT_05];
		$datas['shift06'] = $row[ShiftDetailTable::C_SHIFT_06];
		$datas['shift07'] = $row[ShiftDetailTable::C_SHIFT_07];
		$datas['shift08'] = $row[ShiftDetailTable::C_SHIFT_08];
		$datas['shift09'] = $row[ShiftDetailTable::C_SHIFT_09];
		$datas['shift10'] = $row[ShiftDetailTable::C_SHIFT_10];
		$datas['shift11'] = $row[ShiftDetailTable::C_SHIFT_11];
		$datas['shift12'] = $row[ShiftDetailTable::C_SHIFT_12];
		$datas['shift13'] = $row[ShiftDetailTable::C_SHIFT_13];
		$datas['shift14'] = $row[ShiftDetailTable::C_SHIFT_14];
		$datas['shift15'] = $row[ShiftDetailTable::C_SHIFT_15];
		$datas['shift16'] = $row[ShiftDetailTable::C_SHIFT_16];
		$datas['shift17'] = $row[ShiftDetailTable::C_SHIFT_17];
		$datas['shift18'] = $row[ShiftDetailTable::C_SHIFT_18];
		$datas['shift19'] = $row[ShiftDetailTable::C_SHIFT_19];
		$datas['shift20'] = $row[ShiftDetailTable::C_SHIFT_20];
		$datas['shift21'] = $row[ShiftDetailTable::C_SHIFT_21];
		$datas['shift22'] = $row[ShiftDetailTable::C_SHIFT_22];
		$datas['shift23'] = $row[ShiftDetailTable::C_SHIFT_23];
		$datas['shift24'] = $row[ShiftDetailTable::C_SHIFT_24];
		$datas['shift25'] = $row[ShiftDetailTable::C_SHIFT_25];
		$datas['shift26'] = $row[ShiftDetailTable::C_SHIFT_26];
		$datas['shift27'] = $row[ShiftDetailTable::C_SHIFT_27];
		$datas['shift28'] = $row[ShiftDetailTable::C_SHIFT_28];
		$datas['shift29'] = $row[ShiftDetailTable::C_SHIFT_29];
		$datas['shift30'] = $row[ShiftDetailTable::C_SHIFT_30];
		$datas['shift31'] = $row[ShiftDetailTable::C_SHIFT_31];
			
		$sql = "select * from " . TimeCardTable::C_TABLE 
		. " where ".$this->db->fieldParam(TimeCardTable::C_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TimeCardTable::C_ID, $datas['shift'.$day2]) ;
		$rows = $this->db->getRow($sql,$params) ;
	
		return $rows[0];
	}
	function getHour($day, $month, $empId) {
		$sql_emp = "select * from " . ShiftUpdateTable::C_TABLE 
			. " where " . $this->db->fieldParam(ShiftUpdateTable::C_MONTH) 
			. " and " . $this->db->fieldParam(ShiftUpdateTable::C_EMP_ID) ;
		$params_emp = array() ;
		$params_emp[] = $this->db->valueParam(ShiftUpdateTable::C_MONTH, $month) ;
		$params_emp[] = $this->db->valueParam(ShiftUpdateTable::C_EMP_ID, $empId) ;
		
		$rows_emp = $this->db->getRow($sql_emp,$params_emp) ;
		
		if(!(is_null($rows_emp) || count($rows_emp) == 0)){
			return $this->getHourDetail($rows_emp[0], $day);
		} 
		
		
		$sql_emp = "select * from " . EmployeeShiftTable::C_TABLE 
			. " where " . $this->db->fieldParam(EmployeeShiftTable::C_ID) ;
			
		$params_emp = array() ;
		$params_emp[] = $this->db->valueParam(EmployeeShiftTable::C_ID, $empId) ;
		
		$rows_emp = $this->db->getRow($sql_emp,$params_emp) ;
		
		$type = ShiftType::Daily;
		$group = 1;
		foreach($rows_emp as $row_emp){
			$type = $row_emp[EmployeeShiftTable::C_SHIFT_TYPE];
			$group = $row_emp[EmployeeShiftTable::C_SHIFT_GROUP_ID];
		}
		
		$sql_emp = "select * from " . ShiftUpdateTable::C_TABLE 
			. " where " . $this->db->fieldParam(ShiftUpdateTable::C_MONTH) 
			. " and " . $this->db->fieldParam(ShiftUpdateTable::C_SHIFT_GROUP_ID) 
			. " and " . $this->db->fieldParam(ShiftUpdateTable::C_SHIFT_TYPE);
			
		$params_emp = array() ;
		$params_emp[] = $this->db->valueParam(ShiftUpdateTable::C_MONTH, $month) ;
		$params_emp[] = $this->db->valueParam(ShiftUpdateTable::C_SHIFT_GROUP_ID, $group) ;
		$params_emp[] = $this->db->valueParam(ShiftUpdateTable::C_SHIFT_TYPE, $type) ;
		
		$rows_emp = $this->db->getRow($sql_emp,$params_emp) ;
		
		if(!(is_null($rows_emp) || count($rows_emp) == 0)){
			return $this->getHourDetail($rows_emp[0], $day);
		} 
		
		
		
		
		$sql = "select * from " . ShiftDetailTable::C_TABLE 
			. " where " . $this->db->fieldParam(ShiftDetailTable::C_SHIFT_TYPE) 
			. " and " . $this->db->fieldParam(ShiftDetailTable::C_SHIFT_GROUP_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ShiftDetailTable::C_SHIFT_TYPE, $type) ;
		$params[] = $this->db->valueParam(ShiftDetailTable::C_SHIFT_GROUP_ID, $group) ;
		$rows = $this->db->getRow($sql,$params) ;
		
		if (is_null($rows) || count($rows) == 0){
			return null ;
		}
		else{
			return $this->getHourDetail($rows[0], $day);
		}
			
	}
	
}
?>