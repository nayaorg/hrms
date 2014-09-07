<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "hr/EmployeeTypeClass.php") ;

class EmployeeType extends ControllerBase {
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
		$cls = new EmployeeTypeClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP];
		
		$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_REF,$this->getParam($params,'refno',"")) ;
		$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Employee Type successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new employee type to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[EmployeeType]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeTypeClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;

				$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_REF,$this->getParam($params,'refno',"")) ;
				$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(EmployeeTypeTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Employee type detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[EmployeeType]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating employee type detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"Oops! You must supply the employee type id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeTypeClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Employee type successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[EmployeeType]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting employee type record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee type id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new EmployeeTypeClass($this->db) ;
		$filter = $this->db->fieldParam(EmployeeTypeTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTypeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,EmployeeTypeTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[EmployeeTypeTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[EmployeeTypeTable::C_DESC] . "</td>" ;
			$list .= "<td>" . $row[EmployeeTypeTable::C_REF] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editEmpType(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteEmpType(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeTypeClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid employee type id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[EmployeeTypeTable::C_DESC];
				$datas['refno'] = $row[EmployeeTypeTable::C_REF] ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing employee type id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "hr/EmployeeTypeView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new EmployeeTypeClass($this->db) ;
		$filter = $this->db->fieldParam(EmployeeTypeTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTypeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,EmployeeTypeTable::C_DESC,$params) ;
		$w = 'width' ;
		$h = 'height' ;
		$a = 'align';
		$t = 'text';
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[EmployeeTypeTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[EmployeeTypeTable::C_DESC],200) ;
			$items[$i][] = $this->createPdfItem($row[EmployeeTypeTable::C_REF],100);
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;
		$cols[] = $this->createPdfItem("Ref",100,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Employee Type Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('employeetype.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>