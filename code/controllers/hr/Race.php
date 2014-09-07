<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "hr/RaceClass.php") ;

class Race extends ControllerBase {
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
		$cls = new RaceClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP];
		
		$datas[] = $this->db->fieldValue(RaceTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(RaceTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(RaceTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(RaceTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(RaceTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(RaceTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(RaceTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Race successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new race to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[Rate]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new RaceClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;

				$datas[] = $this->db->fieldValue(RaceTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(RaceTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(RaceTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(RaceTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Race detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Race]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating race detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the race id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new RaceClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Race successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[Race]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting race record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the race id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($params=null) {
		$cls = new RaceClass($this->db) ;
		$filter = $this->db->fieldParam(RaceTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(RaceTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,RaceTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[RaceTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[RaceTable::C_DESC] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editRace(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteRace(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new RaceClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid race id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[RaceTable::C_DESC];
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing race id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "hr/RaceView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new RaceClass($this->db) ;
		$filter = $this->db->fieldParam(RaceTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(RaceTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,RaceTable::C_DESC,$params) ;
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[RaceTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[RaceTable::C_DESC],200) ;
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;

		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Race Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('race.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>