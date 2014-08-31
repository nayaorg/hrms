<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/CpfTypeClass.php") ;
require_once (PATH_MODELS . "payroll/CpfRateClass.php") ;

class CpfType extends ControllerBase {
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
		$cls = new CpfTypeClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_DESC,$this->getParam($params,'desc',"")) ;
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_OW,$this->getParamNumeric($params,'ow',0));
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_AW,$this->getParamNumeric($params,'aw',0));
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(CpfTypeTable::C_ORG_ID,$orgid) ;
		
		$empfix = $this->getParam($params,'empfix',"") ;
		$emprate = $this->getParam($params,'emprate',"") ;
		$empoff = $this->getParam($params,'empoff',"") ;
		$coyfix = $this->getParam($params,'coyfix',"") ;
		$coyrate = $this->getParam($params,'coyrate',"") ;
		$coyoff = $this->getParam($params,'coyoff',"") ;
		try {
			$this->db->beginTran() ;
			$id = $cls->addRecord($datas) ;
			
			if ($id > 0) {
				$this->updateRate($orgid,$id,$empfix,$emprate,$empoff,$coyfix,$coyrate,$coyoff) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"CPF type successfully added to the system.",$id,$this->type);
			} else {
				$this->db->rollbackTran() ;
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new CPF type to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			$this->db->rollbackTran() ;
			Log::write('[CpfType]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, we are unable to process your request as there is a error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$clstype = new CpfTypeClass($this->db) ;
			$clsrate = new CpfRateClass($this->db) ;
			$orgid = $_SESSION[SE_ORGID] ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				$empfix = $this->getParam($params,'empfix',"") ;
				$emprate = $this->getParam($params,'emprate',"") ;
				$empoff = $this->getParam($params,'empoff',"") ;
				$coyfix = $this->getParam($params,'coyfix',"") ;
				$coyrate = $this->getParam($params,'coyrate',"") ;
				$coyoff = $this->getParam($params,'coyoff',"") ;
				$datas[] = $this->db->fieldValue(CpfTypeTable::C_DESC,$this->getParam($params,'desc',"")) ;
				$datas[] = $this->db->fieldValue(CpfTypeTable::C_OW,$this->getParamNumeric($params,'ow',0));
				$datas[] = $this->db->fieldValue(CpfTypeTable::C_AW,$this->getParamNumeric($params,'aw',0));
				$datas[] = $this->db->fieldValue(CpfTypeTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(CpfTypeTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(CpfTypeTable::C_MODIFY_DATE,$modifydate) ;
				$this->db->beginTran() ;
				$clstype->updateRecord($id,$datas) ;
				$clsrate->deleteType($id) ;
				$this->updateRate($orgid,$id,$empfix,$emprate,$empoff,$coyfix,$coyrate,$coyoff) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"CPF type detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[CpfType]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating CPF type detail to the system.","",$this->type) ;
			}
			unset($clstype) ;
			unset($clsrate);
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the CPF type id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$clstype = new CpfTypeClass($this->db) ;
			$clsrate = new CpfRateClass($this->db) ;
			try {
				$this->db->beginTran() ;
				$clstype->deleteRecord($id) ; 
				$clsrate->deleteType($id) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"CPF type successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[CpfType]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting CPF type record from the system.","",$this->type) ;
			}
			unset($clstype) ;
			unset($clsrate) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the CPF type id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new CpfTypeClass($this->db) ;
		$filter = $this->db->fieldParam(CpfTypeTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(CpfTypeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,CpfTypeTable::C_DESC,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[CpfTypeTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[CpfTypeTable::C_DESC] . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editCpfType(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteCpfType(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new CpfTypeClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid CPF type id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['desc'] = $row[CpfTypeTable::C_DESC];
				if ($row[CpfTypeTable::C_OW] === 0)
					$datas['ow'] = "";
				else
					$datas['ow'] = number_format($row[CpfTypeTable::C_OW],2,'.','');
				if ($row[CpfTypeTable::C_AW] === 0)
					$datas['aw'] = "";
				else
					$datas['aw'] = number_format($row[CpfTypeTable::C_AW],2,'.','');
				$rates = $this->getRates($id) ;
				$datas['empfix'] = $rates['empfix'] ;
				$datas['emprate'] = $rates['emprate'] ;
				$datas['empoff'] = $rates['empoff'] ;
				$datas['coyfix'] = $rates['coyfix'] ;
				$datas['coyrate'] = $rates['coyrate'] ;
				$datas['coyoff'] = $rates['coyoff'] ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing CPF type id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/CpfTypeView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new CpfTypeClass($this->db) ;
		$filter = $this->db->fieldParam(CpfTypeTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(CpfTypeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,CpfTypeTable::C_DESC,$params) ;
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[CpfTypeTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[CpfTypeTable::C_DESC],200) ;
			$items[$i][] = $this->createPdfItem(number_format($row[CpfTypeTable::C_OW],2,'.',','),100,0,"R") ;
			$items[$i][] = $this->createPdfItem(number_format($row[CpfTypeTable::C_AW],2,'.',','),100,0,"R") ;

			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Description",200,0,"C","B") ;
		$cols[] = $this->createPdfItem("OW Ceiling",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("AW Ceiling",100,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("CPF Type Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('cpftype.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
	private function updateRate($orgid,$id,$empfix,$emprate,$empoff,$coyfix,$coyrate,$coyoff) {
		$clsrate = new CpfRateClass($this->db) ;
		$datas = array() ;
		$emp_fix = array();
		$emp_rate = array() ;
		$emp_off = array() ;
		$coy_fix = array() ;
		$coy_rate = array() ;
		$coy_off = array() ;
		for ($i = 1;$i < 7 ;$i++) {
			$emp_fix[$i] = array_fill(1,6,0);
			$emp_rate[$i] = array_fill(1,6,0) ;
			$emp_off[$i] = array_fill(1,6,0) ;
			$coy_fix[$i] = array_fill(1,6,0) ;
			$coy_rate[$i] = array_fill(1,6,0) ;
			$coy_off[$i] = array_fill(1,6,0) ;
		}
		if ($empfix != "") {
			$age = explode("|",$empfix) ;
			for ($i= 0;$i < count($age) ;$i++) {
				$income = explode(":",$age[$i]) ;
				$idx = 1 ;
				foreach ($income as $v) {
					$emp_fix[$i+1][$idx] = (is_numeric($v) ? $v : 0) ;
					$idx++;
				}
			}
		}
		if ($emprate != "") {
			$age = explode("|",$emprate) ;
			for ($i= 0;$i < count($age) ;$i++) {
				$income = explode(":",$age[$i]) ;
				$idx = 1 ;
				foreach ($income as $v) {
					$emp_rate[$i+1][$idx] = (is_numeric($v) ? $v : 0) ;
					$idx++;
				}
			}
		}
		if ($empoff != "") {
			$age = explode("|",$empoff) ;
			for ($i= 0;$i < count($age) ;$i++) {
				$income = explode(":",$age[$i]) ;
				$idx = 1 ;
				foreach ($income as $v) {
					$emp_off[$i+1][$idx] = (is_numeric($v) ? $v : 0) ;
					$idx++;
				}
			}
		}
		if ($coyfix != "") {
			$age = explode("|",$coyfix) ;
			for ($i= 0;$i < count($age) ;$i++) {
				$income = explode(":",$age[$i]) ;
				$idx = 1 ;
				foreach ($income as $v) {
					$coy_fix[$i+1][$idx] = (is_numeric($v) ? $v : 0) ;
					$idx++;
				}
			}
		}
		if ($coyrate != "") {
			$age = explode("|",$coyrate) ;
			for ($i= 0;$i < count($age) ;$i++) {
				$income = explode(":",$age[$i]) ;
				$idx = 1 ;
				foreach ($income as $v) {
					$coy_rate[$i+1][$idx] = (is_numeric($v) ? $v : 0 ) ;
					$idx++;
				}
			}
		}
		if ($coyoff != "") {
			$age = explode("|",$coyoff) ;
			for ($i= 0;$i < count($age) ;$i++) {
				$income = explode(":",$age[$i]) ;
				$idx = 1 ;
				foreach ($income as $v) {
					$coy_off[$i+1][$idx] = (is_numeric($v) ? $v : 0 ) ;
					$idx++;
				}
			}
		}
		for ($i=1 ;$i < 7;$i++) {
			for ($j =1 ;$j < 7;$j++) {
				$datas = array() ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_TYPE_ID,$id);
				$datas[] = $this->db->fieldValue(CpfRateTable::C_AGE_ID,$i) ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_WAGE_ID,$j) ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_START,'2000-01-01 00:00:00');
				$datas[] = $this->db->fieldValue(CpfRateTable::C_END,MAX_DATE) ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_EMP_FIX,$emp_fix[$i][$j]) ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_EMP_RATE,$emp_rate[$i][$j]);
				$datas[] = $this->db->fieldValue(CpfRateTable::C_EMP_OFFSET,$emp_off[$i][$j]) ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_COY_FIX,$coy_fix[$i][$j]) ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_COY_RATE,$coy_rate[$i][$j]);
				$datas[] = $this->db->fieldValue(CpfRateTable::C_COY_OFFSET,$coy_off[$i][$j]) ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_ORG_ID,$orgid) ;
				$datas[] = $this->db->fieldValue(CpfRateTable::C_COY_ID,0) ;
				$clsrate->addRecord($datas) ;
			}
		}
	}
	private function getRates($typeid) {
		$clsrate = new CpfRateClass($this->db) ;
	
		$rates = array() ;
		$rates['empfix'] = "" ;
		$rates['emprate'] = "" ;
		$rates['empoff'] = "" ;
		$rates['coyfix'] = "" ;
		$rates['coyrate'] = "" ;
		$rates['coyoff'] = "" ;
		$emp_fix = array() ;
		$emp_rate = array() ;
		$emp_off = array() ;
		$coy_fix = array() ;
		$coy_rate = array() ;
		$coy_off = array() ;
		try {
		for ($i = 1;$i < 7; $i++) {
			$emp_fix[$i] = array_fill(1,6,0) ;
			$emp_rate[$i] = array_fill(1,6,0) ;
			$emp_off[$i] = array_fill(1,6,0) ;
			$coy_fix[$i] = array_fill(1,6,0) ;
			$coy_rate[$i] = array_fill(1,6,0) ;
			$coy_off[$i] = array_fill(1,6,0) ;
		}
		$rows = $clsrate->getType($typeid) ;
		if (!is_null($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$ageid = $row[CpfRateTable::C_AGE_ID] ;
				$wageid = $row[CpfRateTable::C_WAGE_ID] ;
				if ($ageid > 0 && $ageid < 7) {
					if ($wageid > 0 && $wageid < 7) {
						$emp_fix[$ageid][$wageid] = $row[CpfRateTable::C_EMP_FIX] ;
						$emp_rate[$ageid][$wageid] = $row[CpfRateTable::C_EMP_RATE] ;
						$emp_off[$ageid][$wageid] = $row[CpfRateTable::C_EMP_OFFSET] ;
						$coy_fix[$ageid][$wageid] = $row[CpfRateTable::C_COY_FIX] ;
						$coy_rate[$ageid][$wageid] = $row[CpfRateTable::C_COY_RATE] ;
						$coy_off[$ageid][$wageid] = $row[CpfRateTable::C_COY_OFFSET] ;
					}
				}
			}
		}
		$sep2 = "";
		for ($i = 1; $i < 7;$i++) {
			$sep1 = "" ;
			$ef = "" ;
			$er = "" ;
			$eo = "" ;
			$cf = "" ;
			$cr = "" ;
			$co = "" ;
			for ($j = 1; $j < 7; $j++) {
				$ef = $ef . $sep1 . ($emp_fix[$i][$j] == 0 ? "" : number_format($emp_fix[$i][$j],3,'.',''));
				$er = $er . $sep1 . ($emp_rate[$i][$j] == 0 ? "" : number_format($emp_rate[$i][$j],2,'.','')) ;
				$eo = $eo . $sep1 . ($emp_off[$i][$j] == 0 ? "" : number_format($emp_off[$i][$j],2,'.',''));
				$cf = $cf . $sep1 . ($coy_fix[$i][$j] == 0 ? "" : number_format($coy_fix[$i][$j],3,'.',''));
				$cr = $cr . $sep1 . ($coy_rate[$i][$j] == 0 ? "" : number_format($coy_rate[$i][$j],2,'.',''));
				$co = $co . $sep1 . ($coy_off[$i][$j] == 0 ? "" : number_format($coy_off[$i][$j],2,'.','')) ;
				$sep1 = ":" ;
			}
			$rates['empfix'] = $rates['empfix'] . $sep2 . $ef ;
			$rates['emprate'] = $rates['emprate'] . $sep2 . $er ;
			$rates['empoff'] = $rates['empoff'] . $sep2 . $eo ;
			$rates['coyfix'] = $rates['coyfix'] . $sep2 . $cf ;
			$rates['coyrate'] = $rates['coyrate'] . $sep2 . $cr ;
			$rates['coyoff'] = $rates['coyoff'] . $sep2 . $co ;
			$sep2 = "|" ;
		}
		return $rates ;
		} catch (Exception $e) {
			Log::write('[CpfType][getRates]' . $e->getMessage()) ;
			die("") ;
		}
	}
}
?>