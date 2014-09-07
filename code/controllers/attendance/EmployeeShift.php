<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/EmployeeShiftClass.php") ;
require_once (PATH_MODELS . "attendance/RateGroupClass.php") ;
require_once (PATH_MODELS . "attendance/ShiftGroupClass.php") ;
require_once (PATH_MODELS . "attendance/TimeCardClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;

class EmployeeShift extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = EmployeeShiftTable::C_ORG_ID ;
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
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeShiftClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_SHIFT_TYPE,$this->getParam($params,'shifttype',"")) ;
				$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_SHIFT_GROUP_ID,$this->getParam($params,'groupid',"")) ;
				$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_RATE_ID,$this->getParam($params,'rateid',"")) ;
				$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_TIMECARD_ID,$this->getParam($params,'timecardid',"")) ;
				
				
				$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(EmployeeShiftTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Employee Shift successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[EmployeeShift]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating employee shift to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id you wish to update. Please try again.","",$this->type);
		}
	}
	
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeShiftClass($this->db) ;
			$cls_emp = new EmployeeClass($this->db);
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid employee shift id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				
				$datas['id'] = $id ;
				$datas['shifttype']= $row[EmployeeShiftTable::C_SHIFT_TYPE] ;
				$datas['groupid']= $row[EmployeeShiftTable::C_SHIFT_GROUP_ID] ;
				$datas['rateid']= $row[EmployeeShiftTable::C_RATE_ID] ;
				$datas['timecardid']= $row[EmployeeShiftTable::C_TIMECARD_ID] ;
			
				$row_emp = $cls_emp->getRecord($row[EmployeeShiftTable::C_ID]);
				
				$datas['emp_name']= $row_emp[EmployeeTable::C_NAME] ;
				
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
			unset($cls_emp) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing employee shift id. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new EmployeeShiftClass($this->db) ;
		
		$cls->fillEmployeeShift();
		
		$cls_emp = new EmployeeClass($this->db);
		$sfg = new ShiftGroupClass($this->db) ;
		$rg = new RateGroupClass($this->db);
		$filter = $this->db->fieldParam(EmployeeShiftTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeShiftTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,EmployeeShiftTable::C_ID,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$type = "";
			if($row[EmployeeShiftTable::C_SHIFT_TYPE]==ShiftType::Daily)
				$type='Daily';
			elseif($row[EmployeeShiftTable::C_SHIFT_TYPE]==ShiftType::Weekly)
				$type='Weekly';
			$id = $row[EmployeeShiftTable::C_ID] ;
			
			$row_emp = $cls_emp->getRecord($row[EmployeeShiftTable::C_ID]);
			
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= '<td>' . $row_emp[EmployeeTable::C_NAME] . "</td>" ;
			$list .= "<td>" . $type . "</td>" ;
			$list .= "<td>" . $sfg->getDescription($row[EmployeeShiftTable::C_SHIFT_GROUP_ID]) . "</td>" ;
			$list .= "<td>" . $rg->getDescription($row[EmployeeShiftTable::C_RATE_ID]) . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editEmployeeShift(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		unset($cls_emp);
		unset($sfg);
		unset($rg);
		return $list ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/EmployeeShiftView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	
	private function getShiftGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ShiftGroupTable::C_TABLE, ShiftGroupTable::C_ID, ShiftGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Shift Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getTimeCard() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(TimeCardTable::C_TABLE, TimeCardTable::C_ID, TimeCardTable::C_DESC,array('code'=>'','desc'=>'--- Select a Time Card ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	
	private function getRateGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(RateGroupTable::C_TABLE, RateGroupTable::C_ID, RateGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Rate Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
}
?>