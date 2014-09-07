<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "attendance/ShiftDetailClass.php") ;
require_once (PATH_MODELS . "attendance/ShiftGroupClass.php") ;
require_once (PATH_MODELS . "attendance/TimeCardClass.php") ;

class ShiftDetail extends ControllerBase {
	private $type = "" ;
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = ShiftDetailTable::C_ORG_ID ;
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
		$cls = new ShiftDetailClass($this->db) ;
		$datas = array() ;
		$orgid = $_SESSION[SE_ORGID] ;
		$modifyby = $_SESSION[SE_USERID] ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$ws = $_SESSION[SE_REMOTE_IP] ;
		
		$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_TYPE,$this->getParam($params,'shifttype',"")) ;
		$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_GROUP_ID,$this->getParam($params,'groupid',"")) ;
		if($this->getParam($params,'shifttype',"")==ShiftType::Daily){
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_01,$this->getParam($params,'shift01',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_02,$this->getParam($params,'shift02',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_03,$this->getParam($params,'shift03',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_04,$this->getParam($params,'shift04',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_05,$this->getParam($params,'shift05',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_06,$this->getParam($params,'shift06',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_07,$this->getParam($params,'shift07',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_08,$this->getParam($params,'shift08',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_09,$this->getParam($params,'shift09',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_10,$this->getParam($params,'shift10',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_11,$this->getParam($params,'shift11',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_12,$this->getParam($params,'shift12',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_13,$this->getParam($params,'shift13',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_14,$this->getParam($params,'shift14',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_15,$this->getParam($params,'shift15',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_16,$this->getParam($params,'shift16',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_17,$this->getParam($params,'shift17',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_18,$this->getParam($params,'shift18',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_19,$this->getParam($params,'shift19',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_20,$this->getParam($params,'shift20',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_21,$this->getParam($params,'shift21',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_22,$this->getParam($params,'shift22',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_23,$this->getParam($params,'shift23',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_24,$this->getParam($params,'shift24',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_25,$this->getParam($params,'shift25',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_26,$this->getParam($params,'shift26',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_27,$this->getParam($params,'shift27',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_28,$this->getParam($params,'shift28',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_29,$this->getParam($params,'shift29',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_30,$this->getParam($params,'shift30',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_31,$this->getParam($params,'shift31',"")) ;
		}else if($this->getParam($params,'shifttype',"")==ShiftType::Weekly){
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_01,$this->getParam($params,'shiftMon',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_02,$this->getParam($params,'shiftTue',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_03,$this->getParam($params,'shiftWed',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_04,$this->getParam($params,'shiftThu',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_05,$this->getParam($params,'shiftFri',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_06,$this->getParam($params,'shiftSat',"")) ;
			$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_07,$this->getParam($params,'shiftSun',"")) ;
		}
		$datas[] = $this->db->fieldValue(ShiftDetailTable::C_WS_ID,$ws) ;
		$datas[] = $this->db->fieldValue(ShiftDetailTable::C_MODIFY_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ShiftDetailTable::C_CREATE_BY,$modifyby) ;
		$datas[] = $this->db->fieldValue(ShiftDetailTable::C_MODIFY_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ShiftDetailTable::C_CREATE_DATE,$modifydate) ;
		$datas[] = $this->db->fieldValue(ShiftDetailTable::C_ORG_ID,$orgid) ;
		
		try {
			$id = $cls->addRecord($datas) ;
			if ($id > 0) {
				$this->sendJsonResponse(Status::Ok,"Shift detail successfully added to the system.",$id,$this->type);
			} else {
				$this->sendJsonResponse(Status::Error,"Sorry, there is an error in adding new shift detail to the system.",$id, $this->type) ;
			}
		} catch (Exception $e) {
			Log::write('[ShiftDetail]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is an error in database operation.","",$this->type) ;
		}
		unset($cls) ;
	}
	private function updateRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ShiftDetailClass($this->db) ;
			try {
				$datas = array() ;
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;

				
				$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_TYPE,$this->getParam($params,'shifttype',"")) ;
				$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_GROUP_ID,$this->getParam($params,'groupid',"")) ;
				
				if($this->getParam($params,'shifttype',"")==ShiftType::Daily){
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_01,$this->getParam($params,'shift01',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_02,$this->getParam($params,'shift02',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_03,$this->getParam($params,'shift03',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_04,$this->getParam($params,'shift04',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_05,$this->getParam($params,'shift05',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_06,$this->getParam($params,'shift06',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_07,$this->getParam($params,'shift07',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_08,$this->getParam($params,'shift08',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_09,$this->getParam($params,'shift09',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_10,$this->getParam($params,'shift10',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_11,$this->getParam($params,'shift11',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_12,$this->getParam($params,'shift12',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_13,$this->getParam($params,'shift13',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_14,$this->getParam($params,'shift14',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_15,$this->getParam($params,'shift15',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_16,$this->getParam($params,'shift16',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_17,$this->getParam($params,'shift17',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_18,$this->getParam($params,'shift18',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_19,$this->getParam($params,'shift19',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_20,$this->getParam($params,'shift20',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_21,$this->getParam($params,'shift21',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_22,$this->getParam($params,'shift22',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_23,$this->getParam($params,'shift23',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_24,$this->getParam($params,'shift24',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_25,$this->getParam($params,'shift25',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_26,$this->getParam($params,'shift26',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_27,$this->getParam($params,'shift27',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_28,$this->getParam($params,'shift28',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_29,$this->getParam($params,'shift29',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_30,$this->getParam($params,'shift30',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_31,$this->getParam($params,'shift31',"")) ;
				}else if($this->getParam($params,'shifttype',"")==ShiftType::Daily){
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_01,$this->getParam($params,'shiftMon',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_02,$this->getParam($params,'shiftTue',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_03,$this->getParam($params,'shiftWed',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_04,$this->getParam($params,'shiftThu',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_05,$this->getParam($params,'shiftFri',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_06,$this->getParam($params,'shiftSat',"")) ;
					$datas[] = $this->db->fieldValue(ShiftDetailTable::C_SHIFT_07,$this->getParam($params,'shiftSun',"")) ;
				}
				
				$datas[] = $this->db->fieldValue(ShiftDetailTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(ShiftDetailTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(ShiftDetailTable::C_MODIFY_DATE,$modifydate) ;
				$cls->updateRecord($id,$datas) ;
				$this->sendJsonResponse(Status::Ok,"Shift detail successfully updated to the system.",$id,$this->type) ;
			} catch (Exception $e) {
				Log::write('[ShiftDetail]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating shift detail to the system.","",$this->type) ;
			}
			unset($cls) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the shift detail id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ShiftDetailClass($this->db) ;
			try {
				$cls->deleteRecord($id) ; 
				$this->sendJsonResponse(Status::Ok,"Shift detail successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				Log::write('[ShiftDetail]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting shift detail record from the system.","",$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the shift detail id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new ShiftDetailClass($this->db) ;
		$sfg = new ShiftGroupClass($this->db) ;
		$filter = $this->db->fieldParam(ShiftDetailTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ShiftDetailTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ShiftDetailTable::C_ID,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			if($row[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Daily)
				$type='Daily';
			elseif($row[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Weekly)
				$type='Weekly';
			$id = $row[ShiftDetailTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $type . "</td>" ;
			$list .= "<td>" . $sfg->getDescription($row[ShiftDetailTable::C_SHIFT_GROUP_ID]) . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editShiftDetail(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteShiftDetail(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new ShiftDetailClass($this->db) ;
			$row = $cls->getRecord($id) ;
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid shift detail id. Please try again.",$id,$this->type);
			} else {
				$datas = array() ;
				
				$datas['id'] = $id ;
				$datas['shifttype']= $row[ShiftDetailTable::C_SHIFT_TYPE] ;
				$datas['groupid']= $row[ShiftDetailTable::C_SHIFT_GROUP_ID] ;
				
				if($datas['shifttype']==ShiftType::Daily){
					$datas['shift01'] = $row[ShiftDetailTable::C_SHIFT_01];
					$datas['shift02'] = $row[ShiftDetailTable::C_SHIFT_02];
					$datas['shift03'] = $row[ShiftDetailTable::C_SHIFT_03];
					$datas['shift04'] = $row[ShiftDetailTable::C_SHIFT_04];
					$datas['shift05'] = $row[ShiftDetailTable::C_SHIFT_05];
					$datas['shift06'] = $row[ShiftDetailTable::C_SHIFT_06];
					$datas['shift07'] = $row[ShiftDetailTable::C_SHIFT_07];
					$datas['shift08'] = $row[ShiftDetailTable::C_SHIFT_08];
					$datas['shift09'] = $row[ShiftDetailTable::C_SHIFT_09];
					$datas['shift10'] = $row[ShiftDetailTable::C_SHIFT_10];
					$datas['shift11'] = $row[ShiftDetailTable::C_SHIFT_11];
					$datas['shift12'] = $row[ShiftDetailTable::C_SHIFT_12];
					$datas['shift13'] = $row[ShiftDetailTable::C_SHIFT_13];
					$datas['shift14'] = $row[ShiftDetailTable::C_SHIFT_14];
					$datas['shift15'] = $row[ShiftDetailTable::C_SHIFT_15];
					$datas['shift16'] = $row[ShiftDetailTable::C_SHIFT_16];
					$datas['shift17'] = $row[ShiftDetailTable::C_SHIFT_17];
					$datas['shift18'] = $row[ShiftDetailTable::C_SHIFT_18];
					$datas['shift19'] = $row[ShiftDetailTable::C_SHIFT_19];
					$datas['shift20'] = $row[ShiftDetailTable::C_SHIFT_20];
					$datas['shift21'] = $row[ShiftDetailTable::C_SHIFT_21];
					$datas['shift22'] = $row[ShiftDetailTable::C_SHIFT_22];
					$datas['shift23'] = $row[ShiftDetailTable::C_SHIFT_23];
					$datas['shift24'] = $row[ShiftDetailTable::C_SHIFT_24];
					$datas['shift25'] = $row[ShiftDetailTable::C_SHIFT_25];
					$datas['shift26'] = $row[ShiftDetailTable::C_SHIFT_26];
					$datas['shift27'] = $row[ShiftDetailTable::C_SHIFT_27];
					$datas['shift28'] = $row[ShiftDetailTable::C_SHIFT_28];
					$datas['shift29'] = $row[ShiftDetailTable::C_SHIFT_29];
					$datas['shift30'] = $row[ShiftDetailTable::C_SHIFT_30];
					$datas['shift31'] = $row[ShiftDetailTable::C_SHIFT_31];
				}else if($datas['shifttype']==ShiftType::Weekly){
					$datas['shiftMon'] = $row[ShiftDetailTable::C_SHIFT_01];
					$datas['shiftTue'] = $row[ShiftDetailTable::C_SHIFT_02];
					$datas['shiftWed'] = $row[ShiftDetailTable::C_SHIFT_03];
					$datas['shiftThu'] = $row[ShiftDetailTable::C_SHIFT_04];
					$datas['shiftFri'] = $row[ShiftDetailTable::C_SHIFT_05];
					$datas['shiftSat'] = $row[ShiftDetailTable::C_SHIFT_06];
					$datas['shiftSun'] = $row[ShiftDetailTable::C_SHIFT_07];					
				}
				
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing shift detail id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/ShiftDetailView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	
	private function getShiftGroup() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(ShiftGroupTable::C_TABLE, ShiftGroupTable::C_ID, ShiftGroupTable::C_DESC,array('code'=>'','desc'=>'--- Select a Shift Group ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getTimeCard() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(TimeCardTable::C_TABLE, TimeCardTable::C_ID, TimeCardTable::C_DESC,array('code'=>'','desc'=>''),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getHour($type, $group, $day) {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$filter[] = array('field'=>$this->fldtype,'value'=>$type) ;
		$filter[] = array('field'=>$this->fldgroup,'value'=>$group) ;
		$vls = $this->getValueList(TimeCardTable::C_TABLE, TimeCardTable::C_ID, TimeCardTable::C_DESC,"",$filter) ;
		return Util::createOptionValue($vls) ;
	}
	
	
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new ShiftDetailClass($this->db) ;
		$sfg = new ShiftGroupClass($this->db) ;
		$filter = $this->db->fieldParam(ShiftDetailTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(ShiftDetailTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,ShiftDetailTable::C_ID,$params) ;

		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			if($row[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Daily)
				$type='Daily';
			else if($row[ShiftDetailTable::C_SHIFT_TYPE]==ShiftType::Weekly)
				$type='Weekly';
					
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[ShiftDetailTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($type,50) ;
			$items[$i][] = $this->createPdfItem($sfg->getDescription($row[ShiftDetailTable::C_SHIFT_GROUP_ID]),200) ;
			$items[$nr] = "1";
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Type",50,0,"C","B") ;
		$cols[] = $this->createPdfItem("Shift Group",200,0,"C","B") ;
		$pdf = new ListPdf('P');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("ShiftDetail Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('shiftdetail.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
}
?>