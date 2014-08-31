<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "leave/LeaveGroupClass.php") ;
require_once (PATH_MODELS . "leave/LeaveGroupTypeClass.php") ;
require_once (PATH_TABLES . "leave/LeaveTypeTable.php");

class LeaveGroup extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID];
		$this->fldorg = LeaveGroupTable::C_ORG_ID ;
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
	private function addRecord($params) {
		$cls = new LeaveGroupClass($this->db) ;
		$datas = array() ;

		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_REF,$this->getParam($params,'ref',"")) ;
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_TYPE,$this->getParamInt($params,'grptype',0));
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(LeaveGroupTable::C_ORG_ID,$this->orgid) ;
		
		$opts = $this->getParam($params,'options',"") ;
		try {
			$this->db->beginTran() ;
			$id = $cls->addRecord($datas) ;
			
			if ($id > 0) {
				$this->updateType($this->orgid,$id,$opts) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Leave Group successfully added to the system.",$id,$this->type);
			} else {
				$this->db->rollbackTran() ;
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Leave Group to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			$this->db->rollbackTran() ;
			Log::write('[LeaveGroup]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$clsgroup = new LeaveGroupClass($this->db) ;
			$clstype = new LeaveGroupTypeClass($this->db) ;

			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				$opts = $this->getParam($params,'options',"") ;
				$datas[] = $this->db->fieldValue(LeaveGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(LeaveGroupTable::C_REF,$this->getParam($params,'ref',"")) ;
				$datas[] = $this->db->fieldValue(LeaveGroupTable::C_TYPE,$this->getParamInt($params,'grptype',0));
				$datas[] = $this->db->fieldValue(LeaveGroupTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(LeaveGroupTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(LeaveGroupTable::C_MODIFY_DATE,$modifydate) ;
				$this->db->beginTran() ;
				$clsgroup->updateRecord($id,$datas) ;
				$clstype->deleteType($id) ;
				$this->updateType($this->orgid,$id,$opts) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Leave Group detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[LeaveGroup]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Leave Group detail to the system.","",$this->type) ;
			}
			unset($clstype) ;
			unset($clsgroup);
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Leave Group id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$clsgroup = new LeaveGroupClass($this->db) ;
			$clstype = new LeaveGroupTypeClass($this->db) ;
			try {
				$this->db->beginTran() ;
				$clsgroup->deleteRecord($id) ; 
				$clstype->deleteType($id) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Leave Group successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[LeaveGroup]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Leave Group record from the system.","",$this->type) ;
			}
			unset($clstype) ;
			unset($clsgroup) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Leave Group id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new LeaveGroupClass($this->db) ;
		$filter = $this->db->fieldParam(LeaveGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(LeaveGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,LeaveGroupTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[LeaveGroupTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[LeaveGroupTable::C_DESC] . "</td>" ;
			$list .= "<td>" . $row[LeaveGroupTable::C_REF] . "</td>";
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editLeaveGroup(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteLeaveGroup(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new LeaveGroupClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Leave Group id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[LeaveGroupTable::C_DESC];
				$datas['ref'] = $row[LeaveGroupTable::C_REF];
				$datas['grptype'] = $row[LeaveGroupTable::C_TYPE] ;
				$opts = $this->getOptions($id) ;
				$datas['options'] = $opts ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Leave Group id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "leave/LeaveGroupView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new LeaveGroupClass($this->db) ;
		$filter = $this->db->fieldParam(LeaveGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(LeaveGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,LeaveGroupTable::C_DESC,$params) ;
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[LeaveGroupTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[LeaveGroupTable::C_DESC],200) ;
			$items[$i][] = $this->createPdfItem($row[LeaveGroupTable::C_REF],100) ;
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;
		$cols[] = $this->createPdfItem("Ref",100,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Leave Group Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('leavegroup.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
	private function updateType($orgid,$id,$opts) {
		$clstype = new LeaveGroupTypeClass($this->db) ;
		if ($opts != "") {
			$types = explode("|",$opts) ;
			for ($i= 0;$i < count($types) ;$i++) {
				$type = explode(":",$types[$i]) ;
				if (count($type) == 2) {
					if (is_numeric($type[0]) && is_numeric($type[1])) {
						$datas = array() ;
						$datas[] = $this->db->fieldValue(LeaveGroupTypeTable::C_ID,$id);
						$datas[] = $this->db->fieldValue(LeaveGroupTypeTable::C_TYPE,$type[0]) ;
						$datas[] = $this->db->fieldValue(LeaveGroupTypeTable::C_OPTIONS,$type[1]) ;
						$datas[] = $this->db->fieldValue(LeaveGroupTypeTable::C_ORG_ID,$orgid) ;
						$datas[] = $this->db->fieldValue(LeaveGroupTypeTable::C_COY_ID,0) ;
						$clstype->addRecord($datas) ;
					}
				}
			}
		}
	}
	private function getLeaveType() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(LeaveTypeTable::C_TABLE, LeaveTypeTable::C_ID, LeaveTypeTable::C_DESC,array('code'=>'','desc'=>''),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getOptions($groupid) {
		$cls = new LeaveGroupTypeClass($this->db) ;
		try {
			$drows = $cls->getRecord($id) ;
			$opts = "" ;
			if (!is_null($drows) || count($drows) > 0) {
				foreach ($drows as $drow) {
					if (strlen($types) > 0)
						$opts .= "|" ;
					$opts .= $drow[LeaveGroupTypeTable::C_TYPE] . ":" ;
					if (!is_null($drow[LeaveGroupTable::C_OPTIONS]))
						$opts .= $drow[LeaveGroupTypeTable::C_OPTIONS] ;
				}							
			}
			return $opts ;
		} catch (Exception $e) {
			Log::write('[LeaveGroup][getTypes]' . $e->getMessage()) ;
			die("") ;
		}
	}
}
?>