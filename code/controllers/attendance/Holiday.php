<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/HolidayClass.php") ;

class Holiday extends ControllerBase {
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
		$cls = new HolidayClass($this->db) ;
		$datas = array() ;
		$dte = date_create('now')->format('Y-m-d') ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		$datas[] = $this->db->fieldValue(HolidayTable::C_DATE,$this->getParamDate($params,'date',$dte). " 00:00:00", "") ;
		$datas[] = $this->db->fieldValue(HolidayTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(HolidayTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(HolidayTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(HolidayTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(HolidayTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(HolidayTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(HolidayTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Holiday successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Holiday to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[Holiday]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new HolidayClass($this->db) ;
			try {
				$datas = array() ;
				$date = date_create('now')->format('Y-m-d') ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;

				$datas[] = $this->db->fieldValue(HolidayTable::C_DATE,$this->getParamDate($params,'date',$date). " 00:00:00");
				$datas[] = $this->db->fieldValue(HolidayTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(HolidayTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(HolidayTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(HolidayTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Holiday detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[Holiday]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating Holiday detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the Holiday id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new HolidayClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Holiday successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[Holiday]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting Holiday record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the Holiday id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new HolidayClass($this->db) ;
		$filter = $this->db->fieldParam(HolidayTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(HolidayTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,HolidayTable::C_DATE,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[HolidayTable::C_ID] ;
			$dte = date_create($row[HolidayTable::C_DATE]);
			
			$list .= "<tr>" ;
			$list .= "<td>" . $row[HolidayTable::C_ID] . "</td>" ;
			$list .= "<td>" . date_format($dte, 'd/m/Y') . "</td>" ;
			$list .= "<td>" . $row[HolidayTable::C_DESC] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editHoliday(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteHoliday(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new HolidayClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid Holiday id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$dte = date_create($row[HolidayTable::C_DATE]);
				
				$datas['id'] = $id ;
				$datas['date'] = date_format($dte, 'd/m/Y') ;
				$datas['desc'] = $row[HolidayTable::C_DESC] ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Holiday id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/HolidayView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new HolidayClass($this->db) ;
		$filter = $this->db->fieldParam(HolidayTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(HolidayTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,HolidayTable::C_DATE,$params) ;

		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$dte = date_create($row[HolidayTable::C_DATE]);
			
			$items[$i][] = $this->createPdfItem($row[HolidayTable::C_ID],20) ;
			$items[$i][] = $this->createPdfItem(date_format($dte, 'd/m/Y'),50) ;
			$items[$i][] = $this->createPdfItem($row[HolidayTable::C_DESC],200) ;
			$items[$nr] = "1";
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",20,0,"C","B") ;
		$cols[] = $this->createPdfItem("Date",50,0,"C","B") ;
		$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Holiday Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('Holiday.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>