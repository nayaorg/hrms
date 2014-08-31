<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "claims/TravelPlanClass.php") ;
require_once (PATH_MODELS . "claims/CountryClass.php") ;
class TravelPlan extends ControllerBase {
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = TravelPlanTable::C_ORG_ID ;
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
		$cls = new TravelPlanClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$start = date_create('now')->format('Y-m-d') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;

		$datas[] = $this->db->fieldValue(TravelPlanTable::C_TITLE,$this->getParam($params,'title',"")) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_COUNTRY,$this->getParamInt($params,'country',0)) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_COY_ID,0) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_ORG_ID,$orgid) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_START,$this->getParamDate($params,'start',$start) . " 00:00:00") ;
		$datas[] = $this->db->fieldValue(TravelPlanTable::C_END,$this->getParamDate($params,'expiry',MAX_DATE)) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Travel Plan successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Travel Plan to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[Travel Plan]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TravelPlanClass($this->db) ;
			
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				$start = date_create('now')->format('Y-m-d') ;
				
				$datas[] = $this->db->fieldValue(TravelPlanTable::C_TITLE,$this->getParam($params,'title',"")) ;
				$datas[] = $this->db->fieldValue(TravelPlanTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(TravelPlanTable::C_COUNTRY,$this->getParamInt($params,'country',0)) ;
				$datas[] = $this->db->fieldValue(TravelPlanTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(TravelPlanTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(TravelPlanTable::C_MODIFY_DATE,$modifydate) ;
				$datas[] = $this->db->fieldValue(TravelPlanTable::C_START,$this->getParamDate($params,'start',$start) . " 00:00:00") ;
				$datas[] = $this->db->fieldValue(TravelPlanTable::C_END,$this->getParamDate($params,'expiry',MAX_DATE)) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Travel Plan successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Travel Plan]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Travel Plan to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Travel Plan you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TravelPlanClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Travel Plan successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Travel Plan from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Travel you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new TravelPlanClass($this->db) ;
		$country = new CountryClass($this->db) ;
		$filter = $this->db->fieldParam(TravelPlanTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TravelPlanTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getCountryTable($filter,TravelPlanTable::C_ID,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[TravelPlanTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[TravelPlanTable::C_TITLE] . "</td>" ;
			$list .= "<td>" . $row[TravelPlanTable::C_DESC] . "</td>" ;
			//$list .= "<td>" . $row[TravelPlanTable::C_COUNTRY] . "</td>" ;
			$list .= "<td>" . $row[CountryTable::C_DESC] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editTravelPlan(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteTravelPlan(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TravelPlanClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Travel Plan. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['title'] = $row[TravelPlanTable::C_TITLE];
				$datas['desc'] = $row[TravelPlanTable::C_DESC];
				$datas['country'] = $row[TravelPlanTable::C_COUNTRY];
				
				$dte = date_create($row[TravelPlanTable::C_START]);
				$datas['start'] = date_format($dte, 'd/m/Y'); 
				$dte = date_create($row[TravelPlanTable::C_END]) ;
				$datas['expiry'] = date_format($dte, 'd/m/Y'); 
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Travel Plan. Please try again.","",$this->type);
		}
	}
	private function getCountry() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CountryTable::C_TABLE, CountryTable::C_ID, CountryTable::C_DESC,array('code'=>'','desc'=>'--- Select a Country ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "claims/TravelPlanView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new TravelPlanClass($this->db) ;
		$filter = $this->db->fieldParam(TravelPlanTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TravelPlanTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,TravelPlanTable::C_ID,$params) ;
		if (count($rows) > 0) {
			$i = 'items';
			$nr = 'newrow';
			$np = 'newpage';
			$datas = array() ;
			foreach ($rows as $row) {
				$items = array() ;
				$items[$i][] = $this->createPdfItem($row[TravelPlanTable::C_ID],30) ;
				$items[$i][] = $this->createPdfItem($row[TravelPlanTable::C_TITLE],150) ;
				$items[$i][] = $this->createPdfItem($row[TravelPlanTable::C_DESC],200) ;
				$items[$i][] = $this->createPdfItem($row[TravelPlanTable::C_COUNTRY],70) ;
				$items[$nr] = "1" ;
				$datas[] = $items ;
				$firstpage = "0" ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",30,0,"C","B");
			$cols[] = $this->createPdfItem("Title",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;
			$cols[] = $this->createPdfItem("Country",70,0,"C","B") ;
			$pdf = new ListPdf('P');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Travel Plan") ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->render($datas) ;
			$pdf->Output('travel_plan.pdf', 'I');
			unset($rows) ;
			unset($cls) ;
			unset($datas) ;
			unset($params) ;
			unset($items) ;
			unset($cols) ;
		} else {
			echo "<tr><td colspan='12'>No Record Found.</td></tr>" ;
			return;
		}
	}
	public function getTitle($id) {
		$cls = new TravelPlanClass($this->db) ;
		$row = $cls->getRecord($id) ;
		$title = $row[TravelPlanTable::C_TITLE];
		unset($cls) ;
		return $title ;
	}
}
?>