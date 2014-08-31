<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/TimeCardClass.php") ;

class TimeCard extends ControllerBase {
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
		$cls = new TimeCardClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		$datas[] = $this->db->fieldValue(TimeCardTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_REF,$this->getParam($params,'refno',"")) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_TOLERANCE,$this->getParam($params,'tolerance',"")) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_TIME_BREAK,$this->getParam($params,'time_break',"")) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_BREAK_START,$this->getParam($params,'break_start',"")) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_BREAK_END,$this->getParam($params,'break_end',"")) ;
		
		$datas[] = $this->db->fieldValue(TimeCardTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(TimeCardTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Time card successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new time card to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[TimeCard]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TimeCardClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;

				$datas[] = $this->db->fieldValue(TimeCardTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_REF,$this->getParam($params,'refno',"")) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_TIME_START,$this->getParam($params,'time_start',"")) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_TIME_END,$this->getParam($params,'time_end',"")) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_TOLERANCE,$this->getParam($params,'tolerance',"")) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_TIME_BREAK,$this->getParam($params,'time_break',"")) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_BREAK_START,$this->getParam($params,'break_start',"")) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_BREAK_END,$this->getParam($params,'break_end',"")) ;
				
				$datas[] = $this->db->fieldValue(TimeCardTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(TimeCardTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Time card detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[TimeCard]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating time card detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the time card id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TimeCardClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Time card successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[TimeCard]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting time card record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the time card id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new TimeCardClass($this->db) ;
		$filter = $this->db->fieldParam(TimeCardTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TimeCardTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,TimeCardTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[TimeCardTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[TimeCardTable::C_DESC] . "</td>" ;
			$list .= "<td>" . $row[TimeCardTable::C_REF] . "</td>" ;
			$list .= "<td>" . substr($row[TimeCardTable::C_TIME_START], 0, 5) . "</td>" ;
			$list .= "<td>" . substr($row[TimeCardTable::C_TIME_END], 0, 5) . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editTimeCard(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteTimeCard(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TimeCardClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid time card id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[TimeCardTable::C_DESC];
				$datas['refno'] = $row[TimeCardTable::C_REF];
				$datas['time_start'] = $row[TimeCardTable::C_TIME_START];
				$datas['time_end'] = $row[TimeCardTable::C_TIME_END];
				$datas['tolerance'] = $row[TimeCardTable::C_TOLERANCE];
				$datas['time_break'] = $row[TimeCardTable::C_TIME_BREAK];
				$datas['break_start'] = $row[TimeCardTable::C_BREAK_START];
				$datas['break_end'] = $row[TimeCardTable::C_BREAK_END];
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing time card id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/TimeCardView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new TimeCardClass($this->db) ;
		$filter = $this->db->fieldParam(TimeCardTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(TimeCardTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,TimeCardTable::C_DESC,$params) ;

		$i = 'items';
		$nr = 'newrow';
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[TimeCardTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[TimeCardTable::C_DESC],100) ;
			
			$timestart = new DateTime($row[TimeCardTable::C_TIME_START]);
			$timeend = new DateTime($row[TimeCardTable::C_TIME_END]);
			
			$items[$i][] = $this->createPdfItem($timestart->format('H:i'),40, 0, "C") ;
			$items[$i][] = $this->createPdfItem($timeend->format('H:i'),40, 0, "C") ;
			
			$items[$i][] = $this->createPdfItem($row[TimeCardTable::C_TOLERANCE],40) ;
			
			if($row[TimeCardTable::C_TIME_START] != $row[TimeCardTable::C_TIME_END]){
				$timestart = new DateTime($row[TimeCardTable::C_TIME_START]);
				$timeend = new DateTime($row[TimeCardTable::C_TIME_END]);
				
				$items[$i][] = $this->createPdfItem($timestart->format('H:i'),40, 0, "C") ;
				$items[$i][] = $this->createPdfItem($timeend->format('H:i'),40, 0, "C") ;
			} else {
				$items[$i][] = $this->createPdfItem("",40) ;
				$items[$i][] = $this->createPdfItem("",40) ;
			}
			
			$items[$nr] = "1";
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Description",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("Start",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("End",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Tolerance",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("Start",40,0,"C","B") ;
		$cols[] = $this->createPdfItem("End",40,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("TimeCard Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('timecard.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>