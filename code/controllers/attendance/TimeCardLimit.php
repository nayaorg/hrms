<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/TimeCardLimitClass.php") ;

class TimeCardLimit extends ControllerBase {
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
				case REQ_UPDATE:
					$this->updateRecord($params) ;
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
			$cls = new TimeCardLimitClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;

				$datas[] = $this->db->fieldValue(TimeCardLimitTable::C_BEFORE,$this->getParam($params,'before',"")) ;
				$datas[] = $this->db->fieldValue(TimeCardLimitTable::C_AFTER,$this->getParam($params,'after',"")) ;
				
				$datas[] = $this->db->fieldValue(TimeCardLimitTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(TimeCardLimitTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(TimeCardLimitTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Time card limit successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[TimeCardLimit]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating time card limit to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the time card limit id you wish to update. Please try again.","",$this->type);
		}
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new TimeCardLimitClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid time card id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['before'] = $row[TimeCardLimitTable::C_BEFORE];
				$datas['after'] = $row[TimeCardLimitTable::C_AFTER];
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing time card limit id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/TimeCardLimitView.php") ;
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