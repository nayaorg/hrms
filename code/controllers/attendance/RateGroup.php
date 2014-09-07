<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/RateGroupClass.php") ;
require_once (PATH_MODELS . "admin/UserClass.php") ;

class RateGroup extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
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
		$cls = new RateGroupClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		$datas[] = $this->db->fieldValue(RateGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
		
		$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_TYPE,$this->getParam($params,'ratetype',"")) ;
		
		$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_NORMAL,$params['ratenormalnormal']) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_NORMAL_OT,$params['ratenormalot']) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_WEEKEND,$params['rateweekendnormal']) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_WEEKEND_OT,$params['rateweekendot']) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_HOLIDAY,$params['rateholidaynormal']) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_HOLIDAY_OT,$params['rateholidayot']) ;
		
		$datas[] = $this->db->fieldValue(RateGroupTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(RateGroupTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Rate group successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new rate group to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[RateGroup]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new RateGroupClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;

				$datas[] = $this->db->fieldValue(RateGroupTable::C_DESC,$this->getParam($params,'desc',"")) ;
		
				$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_TYPE,$this->getParam($params,'ratetype',"")) ;
				
				$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_NORMAL,$this->getParam($params,'ratenormalnormal',"")) ;
				$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_NORMAL_OT,$this->getParam($params,'ratenormalot',"")) ;
				$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_WEEKEND,$this->getParam($params,'rateweekendnormal',"")) ;
				$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_WEEKEND_OT,$this->getParam($params,'rateweekendot',"")) ;
				$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_HOLIDAY,$this->getParam($params,'rateholidaynormal',"")) ;
				$datas[] = $this->db->fieldValue(RateGroupTable::C_RATE_HOLIDAY_OT,$this->getParam($params,'rateholidayot',"")) ;
				
				$datas[] = $this->db->fieldValue(RateGroupTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(RateGroupTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(RateGroupTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Rate group detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[RateGroup]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating rate group detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the rate group id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new RateGroupClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Rate group successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[RateGroup]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting rate group record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the rate group id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new RateGroupClass($this->db) ;
		$filter = $this->db->fieldParam(RateGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(RateGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,RateGroupTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[RateGroupTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[RateGroupTable::C_DESC] . "</td>" ;
			if($row[RateGroupTable::C_RATE_TYPE] == RateType::Hourly){
				$list .= "<td>Hourly</td>" ;
			} else {
				$list .= "<td>Daily</td>" ;
			}
			$list .= "<td>" . number_format($row[RateGroupTable::C_RATE_NORMAL],2) . "</td>" ;
			$list .= "<td>" . number_format($row[RateGroupTable::C_RATE_NORMAL_OT],2) . "</td>" ;
			$list .= "<td>" . number_format($row[RateGroupTable::C_RATE_WEEKEND],2) . "</td>" ;
			$list .= "<td>" . number_format($row[RateGroupTable::C_RATE_WEEKEND_OT],2) . "</td>" ;
			$list .= "<td>" . number_format($row[RateGroupTable::C_RATE_HOLIDAY],2) . "</td>" ;
			$list .= "<td>" . number_format($row[RateGroupTable::C_RATE_HOLIDAY_OT],2) . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editRateGroup(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteRateGroup(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new RateGroupClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid rate group id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[RateGroupTable::C_DESC];
				
				$datas['ratetype'] = $row[RateGroupTable::C_RATE_TYPE];
				
				$datas['ratenormalnormal'] = $row[RateGroupTable::C_RATE_NORMAL];
				$datas['ratenormalot'] = $row[RateGroupTable::C_RATE_NORMAL_OT];
				$datas['rateweekendnormal'] = $row[RateGroupTable::C_RATE_WEEKEND];
				$datas['rateweekendot'] = $row[RateGroupTable::C_RATE_WEEKEND_OT];
				$datas['rateholidaynormal'] = $row[RateGroupTable::C_RATE_HOLIDAY];
				$datas['rateholidayot'] = $row[RateGroupTable::C_RATE_HOLIDAY_OT];
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing rate group id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/RateGroupView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	
	private function getRateGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(RateGroupTable::C_TABLE, RateGroupTable::C_ID, RateGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Rate Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new RateGroupClass($this->db) ;
		$clsUser = new UserClass($this->db);
		$filter = $this->db->fieldParam(RateGroupTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(RateGroupTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,RateGroupTable::C_DESC,$params) ;

		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[RateGroupTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[RateGroupTable::C_DESC],200) ;
			
			
			$idUser = $row[RateGroupTable::C_MODIFY_BY];
			$rowUser = $clsUser->getRecord($idUser) ;
			
			if (is_null($rowUser)) {
			} else {
				$items[$i][] = $this->createPdfItem($rowUser[UserTable::C_NAME],100) ;
			}
			
			$items[$nr] = "1";
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;
		$cols[] = $this->createPdfItem("Last Update By",100,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("RateGroup Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('rategroup.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($clsUser);
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>