<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "leave/EmployeeLeaveClass.php") ;
require_once (PATH_MODELS . "leave/EmployeeLeaveTypeClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_TABLES . "leave/LeaveGroupTable.php") ;

class EmployeeLeave extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = EmployeeLeaveTable::C_ORG_ID ;
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
					echo $this->getList($params) ;
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
			$clsleave = new EmployeeLeaveClass($this->db) ;
			$clsemp = new EmployeeClass($this->db) ;
			$clstype = new EmployeeLeaveTypeClass($this->db) ;
			try {
				$emprow = $clsemp->getRecord($id) ;
				if (!is_null($emprow) && count($emprow) > 0) {
					$coyid = $emprow[EmployeeTable::C_COY_ID] ;
					$start = $emprow[EmployeeTable::C_JOIN] ;
					$end = $emprow[EmployeeTable::C_RESIGN] ;
				} else {
					$this->sendJsonResponse(Status::Error,"Employee id not found in the system.",$id,$this->type) ;
					return ;
				}
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				$leaves = $this->getParam($params,'types',"") ;
				
				$this->db->beginTran() ;
				$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_START,$this->getParamDate($params,'start',$start)) ;
				$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_END, $this->getParamDate($params,'end',$end));
				$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_GROUP,$this->getParamInt($params,'group',0)) ;
				$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_MODIFY_DATE,$modifydate) ;
				$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_ORG_ID,$this->orgid) ;
				$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_COY_ID,$coyid) ;
				if ($clsleave->isFound($id))
					$clsleave->updateRecord($id,$datas) ;
				else {
					$datas[] = $this->db->fieldValue(EmployeeLeaveTable::C_ID,$id) ;
					$clsleave->addRecord($datas) ;
				}
				$clstype->deleteRecord($id) ;
				if ($income != "")
					$this->updateLeave($leaves,$id,$coyid) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Empooyee leave detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[EmployeeLeave]' . $e->getMessage()) ;
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating employee leave detail to the system.","",$this->type) ;
			}
			unset($clstype) ;
			unset($clsemp);
			unset($clspay) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id you wish to update. Please try again.","",$this->type);
		}
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$clsemp = new EmployeeClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clstype = new EmployeeLeaveTypeClass($this->db) ;
			$clsleave = new EmployeeLeaveClass($this->db) ;
			
			$emprow = $clsemp->getRecord($id) ;
			$coydesc = "" ;
			$deptdesc = "" ;
			$leaves = "" ;
			if (!is_null($emprow) && count($emprow) > 0) {
				$name = $emprow[EmployeeTable::C_NAME] ;
				$coydesc = $clscoy->getDescription($emprow[EmployeeTable::C_COY_ID]) ;
				$deptdesc = $clsdept->getDescription($emprow[EmployeeTable::C_DEPT]) ;
				
				$leaverow = $clsleave->getRecord($id) ;
				if (is_null($leaverow)) {
					$dte = date_create($emprow[EmployeeTable::C_JOIN]) ;
					$start = date_format($dte,'d/m/Y') ;
					$dte = date_create($emprow[EmployeeTable::C_RESIGN]) ;
					if ($dte == date_create(MAX_DATE))
						$end = "" ;
					else
						$end = date_format($dte,'d/m/Y') ;
					$group = "" ;
				} else {
					$datas = array() ;
					$datas['id'] = $id ;
					$dte =  date_create($payrow[EmployeeLeaveTable::C_START]) ;
					$start = date_format($dte,'d/m/Y');
					$dte = date_create($payrow[EmployeeLeaveTable::C_END]) ;
					if ($dte == date_create(MAX_DATE))
						$end = "" ;
					else
						$end = date_format($dte,'d/m/Y') ;
					if ($leaverow[EmployeeLeaveTable::C_GROUP] == 0)
						$group = "" ;
					else 
						$group = $leaverow[EmployeeLeaveTable::C_GROUP] ;
					
					$drows = $clstype->getRecord($id) ;
					$leaves = "" ;
					if (!is_null($drows) || count($drows) > 0) {
						foreach ($drows as $drow) {
							if (strlen($leaves) > 0)
								$leaves .= "|" ;
							$leaves .= $drow[EmployeeLeaveTypeTable::C_TYPE] . ":" 
							. number_format($drow[EmployeeLeaveTypeTable::C_START],2,'.','') . ":"
							. number_format($drow[EmployeeLeaveTypeTable::C_END],2,'.','') . ":"
							. number_format($drow[EmployeeLeaveTypeTable::C_VALUE],2,'.','')  ;
						}							
					}
				}
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['name'] = $name ;
				$datas['coy'] = $coydesc ;
				$datas['dept'] = $deptdesc ;
				$datas['start'] = $start;
				$datas['end'] = $end ;
				$datas['group'] = $group  ;
				$datas['types'] = $leaves ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
				unset($cls) ;
				unset($row);
				unset($rows) ;
			} else {
				$this->sendJsonResponse(Status::Error,"Invalid Employee id. Please try again.","",$this->type) ;
			}
			unset($emp) ;
			unset($emprow) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Employee id. Please try again.","",$this->type);
		}
	}
	private function getList($datas=null) {
		$cls = new EmployeeClass($this->db) ;
		$coy = new CompanyClass($this->db) ;
		$dept = new DepartmentClass($this->db) ;
		$filter = "" ;
		$cond = "" ;
		$params = array() ;
		if (!is_null($datas) && count($datas) > 0) {
			if (isset($datas['coy']) && $datas['coy'] != "") {
				$filter = $this->db->fieldParam(EmployeeTable::C_COY_ID) ;
				$params[] = $this->db->valueParam(EmployeeTable::C_COY_ID,$datas['coy']) ;
				$cond = " and " ;
			}
			if (isset($datas['dept']) && $datas['dept'] != "") {
				$filter .= $cond . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
				$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$datas['dept']) ;
				$cond = " and " ;
			}
		}
		$filter .= $cond . $this->db->fieldParam(EmployeeTable::C_ORG_ID) ;
		
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,EmployeeTable::C_NAME,$params) ;
		$list = "" ;
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$id = $row[EmployeeTable::C_ID] ;
				$list .= "<tr>" ;
				$list .= "<td>" . $id . "</td>" ;
				$list .= "<td>" . $row[EmployeeTable::C_NAME] . "</td>" ;
				$list .= "<td>" . $coy->getDescription($row[EmployeeTable::C_COY_ID]) . "</td>" ;
				$list .= "<td>" . $dept->getDescription($row[EmployeeTable::C_DEPT]) . "</td>" ;
				$list .= "<td style='text-align:center'><a href='javascript:' onclick='editEmpLeave(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
				$list .= "</tr>" ;
			}
		} else {
			$list .= "<tr><td colspan='5'>No Employee Found.</td></tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getCompany() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CompanyTable::C_TABLE, CompanyTable::C_COY_ID, CompanyTable::C_DESC,array('code'=>'','desc'=>'All Company'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(LeaveGroupTable::C_TABLE, LeaveGroupTable::C_ID, LeaveGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function updateLeave($leaves,$empid,$coyid) {
		$clstype = new EmployeeLeaveTypeClass($this->db) ;
		if ($leaves != "") {
			$types = explode("|",$leaves) ;
			for ($i= 0;$i < count($types) ;$i++) {
				$type = explode(":",$types[$i]) ;
				if (count($type) == 2) {
					if (is_numeric($type[0]) && is_numeric($type[1])) {
						$datas = array() ;
						$datas[] = $this->db->fieldValue(EmployeeLeaveTypeTable::C_ID,$empid);
						$datas[] = $this->db->fieldValue(EmployeeLeaveTypeTable::C_TYPE,$type[0]) ;
						$datas[] = $this->db->fieldValue(EmployeeLeaveTypeTable::C_START,$type[1]) ;
						$datas[] = $this->db->fieldValue(EmployeeLeaveTypeTable::C_END,$type[2]) ;
						$datas[] = $this->db->fieldValue(EmployeeLeaveTypeTable::C_VALUE,$type[3]) ;
						$datas[] = $this->db->fieldValue(EmployeeLeaveTypeTable::C_ORG_ID,$this->orgid) ;
						$datas[] = $this->db->fieldValue(EmployeeLeaveTypeTable::C_COY_ID,$coyid) ;
						$clstype->addRecord($datas) ;
					}
				}
			}
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "leave/EmployeeLeaveView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
}
?>