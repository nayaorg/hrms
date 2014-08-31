<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/ShiftUpdateClass.php") ;
require_once (PATH_MODELS . "attendance/ShiftGroupClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "attendance/TimeCardClass.php") ;

class ShiftUpdate extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = ShiftUpdateTable::C_ORG_ID ;
	}
	function __destruct() {
		unset($this->db) ;
	}
	public function processRequest($params) {
		$this->type = REQ_VIEW ;
		try {
			$this->db->open() ;
			if (isset($params) && count($params) > 0) {
				if (isset($params['type']))
					$this->type = $params['type'] ;
			}
			switch ($this->type) {
				case REQ_ADD:
					$this->addRecord($params) ;
					break ;
				case REQ_UPDATE:
					$this->updateRecord($params) ;
					break ;
				case REQ_DELETE:
					$this->deleteRecord($params) ;
					break ;
				case REQ_GET:
					$this->getRecord($params) ;
					break ;
				case REQ_GET . "_EMP":
					$this->getEmployee($params) ;
					break ;
				case REQ_LIST:
					$this->getList($params) ;
					break ;
				case REQ_REPORT:
					$this->getReport($params) ;
					break ;
				case REQ_VIEW:
					$this->getView() ;
					break ;
				default:
					$this->sendJsonResponse(Status::Error,"invalid request.","",$this->type) ;
					break ;
			}
			$this->db->close() ;
			return true ;
		} catch (Exception $e) {
			$this->db->close() ;
			die ($e->getMessage()) ;
		}
	}
	private function addRecord($params) {
		$cls = new ShiftUpdateClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		if($this->getParam($params,'updatetype',"") == 'C'){
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_TYPE,$this->getParam($params,'shifttype',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_GROUP_ID,$this->getParam($params,'groupid',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_EMP_ID,0) ;
		} else if ($this->getParam($params,'updatetype',"") == 'I'){
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_TYPE,-1) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_GROUP_ID,0) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_EMP_ID,$this->getParam($params,'emp_id',"")) ;
		}
		$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_MONTH,$this->getParam($params,'month',"") . $this->getParam($params,'year',"")) ;
		if($this->getParam($params,'shifttype',"")==ShiftType::Daily){
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_01,$this->getParam($params,'shift01',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_02,$this->getParam($params,'shift02',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_03,$this->getParam($params,'shift03',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_04,$this->getParam($params,'shift04',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_05,$this->getParam($params,'shift05',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_06,$this->getParam($params,'shift06',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_07,$this->getParam($params,'shift07',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_08,$this->getParam($params,'shift08',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_09,$this->getParam($params,'shift09',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_10,$this->getParam($params,'shift10',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_11,$this->getParam($params,'shift11',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_12,$this->getParam($params,'shift12',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_13,$this->getParam($params,'shift13',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_14,$this->getParam($params,'shift14',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_15,$this->getParam($params,'shift15',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_16,$this->getParam($params,'shift16',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_17,$this->getParam($params,'shift17',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_18,$this->getParam($params,'shift18',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_19,$this->getParam($params,'shift19',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_20,$this->getParam($params,'shift20',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_21,$this->getParam($params,'shift21',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_22,$this->getParam($params,'shift22',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_23,$this->getParam($params,'shift23',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_24,$this->getParam($params,'shift24',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_25,$this->getParam($params,'shift25',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_26,$this->getParam($params,'shift26',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_27,$this->getParam($params,'shift27',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_28,$this->getParam($params,'shift28',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_29,$this->getParam($params,'shift29',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_30,$this->getParam($params,'shift30',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_31,$this->getParam($params,'shift31',"")) ;
		}else if($this->getParam($params,'shifttype',"")==ShiftType::Weekly){
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_01,$this->getParam($params,'shiftMon',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_02,$this->getParam($params,'shiftTue',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_03,$this->getParam($params,'shiftWed',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_04,$this->getParam($params,'shiftThu',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_05,$this->getParam($params,'shiftFri',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_06,$this->getParam($params,'shiftSat',"")) ;
			$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_07,$this->getParam($params,'shiftSun',"")) ;
		}
		$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Shift detail successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is an error in adding new shift detail to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[ShiftUpdate]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is an error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['month']) && isset($params['year'])) {
			$cls = new ShiftUpdateClass($this->db) ;
			$id = $cls->checkShiftUpdate($params['emp_id'], 
										$params['month'],
										$params['year'],
										$params['groupid'],
										$params['shifttype']);
			if($id >= 0){
				try {
					$datas = array() ;
					$modifyby = $_SESSION[SE_USERID] ;
					$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
					$ws = $_SESSION[SE_REMOTE_IP] ;

					
					$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_TYPE,$this->getParam($params,'shifttype',"")) ;
					$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_GROUP_ID,$this->getParam($params,'groupid',"")) ;
					$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_EMP_ID,$this->getParam($params,'emp_id',"")) ;
					
					if($this->getParam($params,'shifttype',"")==ShiftType::Daily){
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_01,$this->getParam($params,'shift01',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_02,$this->getParam($params,'shift02',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_03,$this->getParam($params,'shift03',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_04,$this->getParam($params,'shift04',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_05,$this->getParam($params,'shift05',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_06,$this->getParam($params,'shift06',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_07,$this->getParam($params,'shift07',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_08,$this->getParam($params,'shift08',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_09,$this->getParam($params,'shift09',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_10,$this->getParam($params,'shift10',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_11,$this->getParam($params,'shift11',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_12,$this->getParam($params,'shift12',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_13,$this->getParam($params,'shift13',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_14,$this->getParam($params,'shift14',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_15,$this->getParam($params,'shift15',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_16,$this->getParam($params,'shift16',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_17,$this->getParam($params,'shift17',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_18,$this->getParam($params,'shift18',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_19,$this->getParam($params,'shift19',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_20,$this->getParam($params,'shift20',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_21,$this->getParam($params,'shift21',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_22,$this->getParam($params,'shift22',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_23,$this->getParam($params,'shift23',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_24,$this->getParam($params,'shift24',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_25,$this->getParam($params,'shift25',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_26,$this->getParam($params,'shift26',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_27,$this->getParam($params,'shift27',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_28,$this->getParam($params,'shift28',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_29,$this->getParam($params,'shift29',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_30,$this->getParam($params,'shift30',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_31,$this->getParam($params,'shift31',"")) ;
					}else if($this->getParam($params,'shifttype',"")==ShiftType::Weekly){
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_01,$this->getParam($params,'shiftMon',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_02,$this->getParam($params,'shiftTue',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_03,$this->getParam($params,'shiftWed',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_04,$this->getParam($params,'shiftThu',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_05,$this->getParam($params,'shiftFri',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_06,$this->getParam($params,'shiftSat',"")) ;
						$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_SHIFT_07,$this->getParam($params,'shiftSun',"")) ;
					}
					
					$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_WS_ID,$ws) ;
					$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_MODIFY_BY,$modifyby) ;
					$datas[] = $this->db->fieldValue(ShiftUpdateTable::C_MODIFY_DATE,$modifydate) ;
					$cls->updateRecord($id,$datas) ;
					$this->sendJsonResponse(Status::Ok,"Shift detail successfully updated to the system.",$id,$this->type) ;
				} catch (Exception $e) {
					Log::write('[ShiftUpdate]' . $e->getMessage());
					$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating shift detail to the system.","",$this->type) ;
				}				
			} else {
				$this->addRecord($params);
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the shift detail id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ShiftUpdateClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Shift detail successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[ShiftUpdate]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting shift detail record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the shift detail id you wish to delete. Please try again.","",$this->type);
		}
	}
	
	private function getRecord($params=null) {
		//if (isset($params['id'])) {
		
			$group=$params['groupType'];
			$shiftType=$params['shiftType'];
			$groupId=$params['groupId'];
			$empId=$params['empId'];
			$month=$params['month'];
			$year=$params['year'];
			
			$datas = array() ;
				
			$cls = new ShiftUpdateClass($this->db) ;
			$row = $cls->getShiftUpdateRecord($group, $shiftType, $groupId, $empId, $month.$year) ;
			if (is_null($row)) {
				$row2 = $cls->getDefaultShiftUpdateRecord($group, $shiftType, $groupId, $empId) ;
				if (is_null($row2)) {
					$this->sendJsonResponse(Status::Error,"Invalid detail. Please try again.","",$this->type);
				}else{
					if($row2[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Daily){
						$datas['shift01'] = $row2[ShiftDetailTable::C_SHIFT_01];
						$datas['shift02'] = $row2[ShiftDetailTable::C_SHIFT_02];
						$datas['shift03'] = $row2[ShiftDetailTable::C_SHIFT_03];
						$datas['shift04'] = $row2[ShiftDetailTable::C_SHIFT_04];
						$datas['shift05'] = $row2[ShiftDetailTable::C_SHIFT_05];
						$datas['shift06'] = $row2[ShiftDetailTable::C_SHIFT_06];
						$datas['shift07'] = $row2[ShiftDetailTable::C_SHIFT_07];
						$datas['shift08'] = $row2[ShiftDetailTable::C_SHIFT_08];
						$datas['shift09'] = $row2[ShiftDetailTable::C_SHIFT_09];
						$datas['shift10'] = $row2[ShiftDetailTable::C_SHIFT_10];
						$datas['shift11'] = $row2[ShiftDetailTable::C_SHIFT_11];
						$datas['shift12'] = $row2[ShiftDetailTable::C_SHIFT_12];
						$datas['shift13'] = $row2[ShiftDetailTable::C_SHIFT_13];
						$datas['shift14'] = $row2[ShiftDetailTable::C_SHIFT_14];
						$datas['shift15'] = $row2[ShiftDetailTable::C_SHIFT_15];
						$datas['shift16'] = $row2[ShiftDetailTable::C_SHIFT_16];
						$datas['shift17'] = $row2[ShiftDetailTable::C_SHIFT_17];
						$datas['shift18'] = $row2[ShiftDetailTable::C_SHIFT_18];
						$datas['shift19'] = $row2[ShiftDetailTable::C_SHIFT_19];
						$datas['shift20'] = $row2[ShiftDetailTable::C_SHIFT_20];
						$datas['shift21'] = $row2[ShiftDetailTable::C_SHIFT_21];
						$datas['shift22'] = $row2[ShiftDetailTable::C_SHIFT_22];
						$datas['shift23'] = $row2[ShiftDetailTable::C_SHIFT_23];
						$datas['shift24'] = $row2[ShiftDetailTable::C_SHIFT_24];
						$datas['shift25'] = $row2[ShiftDetailTable::C_SHIFT_25];
						$datas['shift26'] = $row2[ShiftDetailTable::C_SHIFT_26];
						$datas['shift27'] = $row2[ShiftDetailTable::C_SHIFT_27];
						$datas['shift28'] = $row2[ShiftDetailTable::C_SHIFT_28];
						$datas['shift29'] = $row2[ShiftDetailTable::C_SHIFT_29];
						$datas['shift30'] = $row2[ShiftDetailTable::C_SHIFT_30];
						$datas['shift31'] = $row2[ShiftDetailTable::C_SHIFT_31];
						$datas['shifttype'] = 'D';
					}else if($row2[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Weekly){
						$datas['shiftMon'] = $row2[ShiftDetailTable::C_SHIFT_01];
						$datas['shiftTue'] = $row2[ShiftDetailTable::C_SHIFT_02];
						$datas['shiftWed'] = $row2[ShiftDetailTable::C_SHIFT_03];
						$datas['shiftThu'] = $row2[ShiftDetailTable::C_SHIFT_04];
						$datas['shiftFri'] = $row2[ShiftDetailTable::C_SHIFT_05];
						$datas['shiftSat'] = $row2[ShiftDetailTable::C_SHIFT_06];
						$datas['shiftSun'] = $row2[ShiftDetailTable::C_SHIFT_07];
						$datas['shifttype'] = 'W';					
					}
					$datas['shifttype'] = $row2[ShiftDetailTable::C_SHIFT_TYPE];
					$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
				}
			} else {
				
				if($row[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Daily){
					$datas['shift01'] = $row[ShiftUpdateTable::C_SHIFT_01];
					$datas['shift02'] = $row[ShiftUpdateTable::C_SHIFT_02];
					$datas['shift03'] = $row[ShiftUpdateTable::C_SHIFT_03];
					$datas['shift04'] = $row[ShiftUpdateTable::C_SHIFT_04];
					$datas['shift05'] = $row[ShiftUpdateTable::C_SHIFT_05];
					$datas['shift06'] = $row[ShiftUpdateTable::C_SHIFT_06];
					$datas['shift07'] = $row[ShiftUpdateTable::C_SHIFT_07];
					$datas['shift08'] = $row[ShiftUpdateTable::C_SHIFT_08];
					$datas['shift09'] = $row[ShiftUpdateTable::C_SHIFT_09];
					$datas['shift10'] = $row[ShiftUpdateTable::C_SHIFT_10];
					$datas['shift11'] = $row[ShiftUpdateTable::C_SHIFT_11];
					$datas['shift12'] = $row[ShiftUpdateTable::C_SHIFT_12];
					$datas['shift13'] = $row[ShiftUpdateTable::C_SHIFT_13];
					$datas['shift14'] = $row[ShiftUpdateTable::C_SHIFT_14];
					$datas['shift15'] = $row[ShiftUpdateTable::C_SHIFT_15];
					$datas['shift16'] = $row[ShiftUpdateTable::C_SHIFT_16];
					$datas['shift17'] = $row[ShiftUpdateTable::C_SHIFT_17];
					$datas['shift18'] = $row[ShiftUpdateTable::C_SHIFT_18];
					$datas['shift19'] = $row[ShiftUpdateTable::C_SHIFT_19];
					$datas['shift20'] = $row[ShiftUpdateTable::C_SHIFT_20];
					$datas['shift21'] = $row[ShiftUpdateTable::C_SHIFT_21];
					$datas['shift22'] = $row[ShiftUpdateTable::C_SHIFT_22];
					$datas['shift23'] = $row[ShiftUpdateTable::C_SHIFT_23];
					$datas['shift24'] = $row[ShiftUpdateTable::C_SHIFT_24];
					$datas['shift25'] = $row[ShiftUpdateTable::C_SHIFT_25];
					$datas['shift26'] = $row[ShiftUpdateTable::C_SHIFT_26];
					$datas['shift27'] = $row[ShiftUpdateTable::C_SHIFT_27];
					$datas['shift28'] = $row[ShiftUpdateTable::C_SHIFT_28];
					$datas['shift29'] = $row[ShiftUpdateTable::C_SHIFT_29];
					$datas['shift30'] = $row[ShiftUpdateTable::C_SHIFT_30];
					$datas['shift31'] = $row[ShiftUpdateTable::C_SHIFT_31];
				}else if($row[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Weekly){
					$datas['shiftMon'] = $row[ShiftUpdateTable::C_SHIFT_01];
					$datas['shiftTue'] = $row[ShiftUpdateTable::C_SHIFT_02];
					$datas['shiftWed'] = $row[ShiftUpdateTable::C_SHIFT_03];
					$datas['shiftThu'] = $row[ShiftUpdateTable::C_SHIFT_04];
					$datas['shiftFri'] = $row[ShiftUpdateTable::C_SHIFT_05];
					$datas['shiftSat'] = $row[ShiftUpdateTable::C_SHIFT_06];
					$datas['shiftSun'] = $row[ShiftUpdateTable::C_SHIFT_07];				
				}	
				$datas['shifttype'] = $row[ShiftDetailTable::C_SHIFT_TYPE];
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			//}
			unset($cls) ;
		}
		/*} else {
			$this->sendJsonResponse(Status::Error,"Missing shift detail id. Please try again.","",$this->type);
		}*/
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/ShiftUpdateView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	
	private function getShiftGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ShiftGroupTable::C_TABLE, ShiftGroupTable::C_ID, ShiftGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Shift Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getTimeCard() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(TimeCardTable::C_TABLE, TimeCardTable::C_ID, TimeCardTable::C_DESC,array('code'=>'','desc'=>''),$filter) ;
		return Util::createOptionValue($vls) ;
	}

	private function getEmployee($params) {
		$id = $params['id'] ;
		
		$cls = new EmployeeClass($this->db) ;
		if($id == 0){
			$rows = $cls->getTable() ;
		}else {
			$filter = $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$datas = array() ;
			$datas[] = $this->db->valueParam(EmployeeTable::C_DEPT,$id) ;
			$rows = $cls->getTable($filter,EmployeeTable::C_NAME,$datas) ;		
		}
		$lines = "" ;
		if (!is_null($rows) || count($rows) > 0) {
			foreach ($rows as $row) {
				
				if (strlen($lines) > 0)
					$lines .= "|" ;
				$lines .= $row[EmployeeTable::C_ID] . ":" . $row[EmployeeTable::C_NAME] ;
			}							
		}
		
		$datas = array() ;
		$datas['empList'] =  $lines ;
		
		$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
		unset($rows) ;
		unset($list) ;
		unset($cls) ;
	}
	private function getDefaultShift($params=null){
	//group id
	//emp id
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ShiftUpdateClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid shift detail id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				
				$datas['id'] = $id ;
				$datas['shifttype']= $row[ShiftUpdateTable::C_SHIFT_TYPE] ;
				$datas['groupid']= $row[ShiftUpdateTable::C_SHIFT_GROUP_ID] ;
				
				if($datas['shifttype']==ShiftType::Daily){
					$datas['shift01'] = $row[ShiftUpdateTable::C_SHIFT_01];
					$datas['shift02'] = $row[ShiftUpdateTable::C_SHIFT_02];
					$datas['shift03'] = $row[ShiftUpdateTable::C_SHIFT_03];
					$datas['shift04'] = $row[ShiftUpdateTable::C_SHIFT_04];
					$datas['shift05'] = $row[ShiftUpdateTable::C_SHIFT_05];
					$datas['shift06'] = $row[ShiftUpdateTable::C_SHIFT_06];
					$datas['shift07'] = $row[ShiftUpdateTable::C_SHIFT_07];
					$datas['shift08'] = $row[ShiftUpdateTable::C_SHIFT_08];
					$datas['shift09'] = $row[ShiftUpdateTable::C_SHIFT_09];
					$datas['shift10'] = $row[ShiftUpdateTable::C_SHIFT_10];
					$datas['shift11'] = $row[ShiftUpdateTable::C_SHIFT_11];
					$datas['shift12'] = $row[ShiftUpdateTable::C_SHIFT_12];
					$datas['shift13'] = $row[ShiftUpdateTable::C_SHIFT_13];
					$datas['shift14'] = $row[ShiftUpdateTable::C_SHIFT_14];
					$datas['shift15'] = $row[ShiftUpdateTable::C_SHIFT_15];
					$datas['shift16'] = $row[ShiftUpdateTable::C_SHIFT_16];
					$datas['shift17'] = $row[ShiftUpdateTable::C_SHIFT_17];
					$datas['shift18'] = $row[ShiftUpdateTable::C_SHIFT_18];
					$datas['shift19'] = $row[ShiftUpdateTable::C_SHIFT_19];
					$datas['shift20'] = $row[ShiftUpdateTable::C_SHIFT_20];
					$datas['shift21'] = $row[ShiftUpdateTable::C_SHIFT_21];
					$datas['shift22'] = $row[ShiftUpdateTable::C_SHIFT_22];
					$datas['shift23'] = $row[ShiftUpdateTable::C_SHIFT_23];
					$datas['shift24'] = $row[ShiftUpdateTable::C_SHIFT_24];
					$datas['shift25'] = $row[ShiftUpdateTable::C_SHIFT_25];
					$datas['shift26'] = $row[ShiftUpdateTable::C_SHIFT_26];
					$datas['shift27'] = $row[ShiftUpdateTable::C_SHIFT_27];
					$datas['shift28'] = $row[ShiftUpdateTable::C_SHIFT_28];
					$datas['shift29'] = $row[ShiftUpdateTable::C_SHIFT_29];
					$datas['shift30'] = $row[ShiftUpdateTable::C_SHIFT_30];
					$datas['shift31'] = $row[ShiftUpdateTable::C_SHIFT_31];
				}else if($datas['shifttype']==ShiftType::Weekly){
					$datas['shiftMon'] = $row[ShiftUpdateTable::C_SHIFT_01];
					$datas['shiftTue'] = $row[ShiftUpdateTable::C_SHIFT_02];
					$datas['shiftWed'] = $row[ShiftUpdateTable::C_SHIFT_03];
					$datas['shiftThu'] = $row[ShiftUpdateTable::C_SHIFT_04];
					$datas['shiftFri'] = $row[ShiftUpdateTable::C_SHIFT_05];
					$datas['shiftSat'] = $row[ShiftUpdateTable::C_SHIFT_06];
					$datas['shiftSun'] = $row[ShiftUpdateTable::C_SHIFT_07];					
				}
				
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing shift detail id. Please try again.","",$this->type);
		}	
	}
}
?>