<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_MODELS . "admin/CompanyOptions.php") ;
require_once (PATH_MODELS . "attendance/AttendanceClass.php") ;
require_once (PATH_MODELS . "attendance/TimeOffClass.php") ;
require_once (PATH_MODELS . "attendance/OvertimeClass.php") ;

class Disciplinary extends ControllerBase {
	
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = EmployeeTable::C_ORG_ID ;
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
				case REQ_EXPORT:
					$this->getExport($params) ;
					break ;
				case REQ_REPORT:
					$this->getReport($params) ;
					break ;
				case REQ_LIST:
					echo $this->getList($params) ;
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
	private function getOvertimeRecord($id, $date){
		$sql = "SELECT * FROM OVERTIME ";
		$sql .= " WHERE " . $this->db->fieldParam(OvertimeTable::C_EMP_ID) ;
		$sql .= " AND " . $this->db->fieldParam(OvertimeTable::C_DATE) ;
		$params = array() ;
		$params[] = $this->db->valueParam(OvertimeTable::C_EMP_ID,$id) ;
		$params[] = $this->db->valueParam(OvertimeTable::C_DATE,$date) ;
		
		$rows = $this->db->getTable($sql,$params);
		
		return $rows;
	}
	
	private function getList($datas=null) {
			
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($datas,'dateReportBegin',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($datas,'dateReportEnd',$dte). " 23:59:59";
		
		$filter = "" ;
		$emp_id_begin = $this->getParam($datas,'empIdBegin',"") ;
		$emp_id_end = $this->getParam($datas,'empIdEnd',"") ;
		$dept_id = $this->getParam($datas,'dept',"") ;
		$reporttype = $this->getParam($datas,'reporttype',"") ;
		
		$rows = $this->getData($dateBegin, $dateEnd, $emp_id_begin, $emp_id_end, $reporttype, $dept_id) ;
		
		$list = "" ;
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$list .= "<tr>" ;
				$list .= "<td>" . $row[EmployeeTable::C_ID] . "</td>" ;
				$list .= "<td>" . $row[EmployeeTable::C_NAME] . "</td>" ;
				
				$list .= "<td style='text-align:center'>" . (is_null($row[AttendanceTable::C_TIME_START]) ? "--" : $row[AttendanceTable::C_TIME_START])  . "</td>" ;
				
				$list .= "<td style='text-align:center'>" . ($row[AttendanceTable::C_LATE_IN] != 0 ? $row[AttendanceTable::C_LATE_IN] : "--") . "</td>" ;
				
				$list .= "<td style='text-align:center'>" . (is_null($row[AttendanceTable::C_TIME_END]) ? "--" : $row[AttendanceTable::C_TIME_END])  . "</td>" ;
				
				$list .= "<td style='text-align:center'>" . ($row[AttendanceTable::C_EARLY_OUT] != 0 ? $row[AttendanceTable::C_EARLY_OUT] : "--") . "</td>" ;
				
				if($row[AttendanceTable::C_LATE_IN] != 0 && $row[AttendanceTable::C_EARLY_OUT] == 0){
					$list .= "<td>" . "Late In" . "</td>" ;
				} else if($row[AttendanceTable::C_LATE_IN] == 0 && $row[AttendanceTable::C_EARLY_OUT] != 0){
					$list .= "<td>" . "Early Out" . "</td>" ;
				} else if($row[AttendanceTable::C_LATE_IN] != 0 && $row[AttendanceTable::C_EARLY_OUT] != 0){
					$list .= "<td>" . "Both" . "</td>" ;
				} else {
					$list .= "<td>" . "None" . "</td>" ;
				}
				
				$list .= "</tr>" ;
			}
		} else {
			$list .= "<tr><td colspan='7'>No Employee Found.</td></tr>" ;
		}
		unset($rows) ;
		
		return $list ;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "attendance/DisciplinaryView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getData($dateBegin, $dateEnd, $empIdBegin, $empIdEnd, $reporttype, $dept) {
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.") . ' and '
			. ' a.' . AttendanceTable::C_DATE . " >= '" . $dateBegin . "' and a." . AttendanceTable::C_DATE . " <= '" . $dateEnd . "' ";
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		
		if ($empIdBegin != "-1") {
			$filter .= " and e." . EmployeeTable::C_ID . " >= " . $empIdBegin . " and e." . EmployeeTable::C_ID . " <= " . $empIdEnd;
		}
		if ($dept != "-1" && $dept != "") {
			$filter .= " and e." . EmployeeTable::C_DEPT . " = " . $dept ;
		}
		
		if ($reporttype == "1"){
			$filter .= " and a." . AttendanceTable::C_LATE_IN . " != 0 ";
		} else if ($reporttype == "2"){
			$filter .= " and a." . AttendanceTable::C_EARLY_OUT . " != 0 ";
		} else if ($reporttype == "3"){
			$filter .= " and (a." . AttendanceTable::C_EARLY_OUT . " != 0 or a." . AttendanceTable::C_LATE_IN . " != 0)";
		} else if ($reporttype == "4"){
			$filter .= " and a." . AttendanceTable::C_EARLY_OUT . " != 0 and a." . AttendanceTable::C_LATE_IN . " != 0 ";
		}
		
		$sql = " select e." . EmployeeTable::C_ID . ", e." . EmployeeTable::C_NAME . ", a." . AttendanceTable::C_DATE . ", "
				. " a." . AttendanceTable::C_TIME_START . ", a." . AttendanceTable::C_TIME_END . ", "
				. " a." . AttendanceTable::C_LATE_IN . ", a." . AttendanceTable::C_EARLY_OUT . " "
				. " from " . AttendanceTable::C_TABLE . "  a "
				. " left outer join " . EmployeeTable::C_TABLE . " e "
				. " on (a." . AttendanceTable::C_EMP_ID . " = e." . EmployeeTable::C_ID . ")"
				. " where " . $filter 
				. " order by e." . EmployeeTable::C_COY_ID . ",e." . EmployeeTable::C_DEPT . ",e." . EmployeeTable::C_NAME ;

		return $this->db->getTable($sql,$params) ;
	}
	private function getDisciplinaryType(){
		$arr = array() ;
		$arr[] = array ('code'=>'1','desc'=>'Late In' ) ;
		$arr[] = array ('code'=>'2','desc'=>'Early Out' ) ;
		$arr[] = array ('code'=>'3','desc'=>'Late In or Early Out' ) ;
		$arr[] = array ('code'=>'4','desc'=>'Late In and Early Out') ;
		return Util::createOptionValue($arr) ;
	}
	private function getExport($params=null) {
			
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($params,'date',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($params,'dateend',$dte). " 23:59:59";
		
		$filter = "" ;
		$emp_id_begin = $this->getParam($params,'empIdBegin',"") ;
		$emp_id_end = $this->getParam($params,'empIdEnd',"") ;
		$dept_id = $this->getParam($params,'dept',"") ;
		$reporttype = $this->getParam($params,'reporttype',"") ;
		
		$rows = $this->getData($dateBegin, $dateEnd, $emp_id_begin, $emp_id_end, $reporttype, $dept_id) ;
		
		if (count($rows) > 0) {
			$datas = "" ;
			$expfile = "";
			foreach ($rows as $row) {
				$datas .= $row[EmployeeTable::C_ID] . "," . $row[EmployeeTable::C_NAME];
				
				$datas .= "," . (is_null($row[AttendanceTable::C_TIME_START]) ? "--" : $row[AttendanceTable::C_TIME_START]);
				$datas .= "," . ($row[AttendanceTable::C_LATE_IN] != 0 ? $row[AttendanceTable::C_LATE_IN] : "--");
				$datas .= "," . (is_null($row[AttendanceTable::C_TIME_END]) ? "--" : $row[AttendanceTable::C_TIME_END]);
				$datas .= "," . ($row[AttendanceTable::C_EARLY_OUT] != 0 ? $row[AttendanceTable::C_EARLY_OUT] : "--");
				
				if($row[AttendanceTable::C_LATE_IN] != 0 && $row[AttendanceTable::C_EARLY_OUT] == 0){
					$datas .= "," . "Late In";
				} else if($row[AttendanceTable::C_LATE_IN] == 0 && $row[AttendanceTable::C_EARLY_OUT] != 0){
					$datas .= "," . "Early Out";
				} else if($row[AttendanceTable::C_LATE_IN] != 0 && $row[AttendanceTable::C_EARLY_OUT] != 0){
					$datas .= "," . "Both";
				} else {
					$datas .= "," . "None";
				}
				
				$datas .= "\r\n" ;
			}
			if ($expfile == "")
				$expfile = "disciplinary.txt" ;
			header('Content-disposition: attachment; filename='. $expfile);
			header('Content-type: text/plain');
			header("Content-Length: ".strlen($datas));
			echo $datas ;
		} else {
			echo "<tr><td colspan='7'>No Record Found.</td></tr>" ;
			return;
		}
		unset($rows) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$dte = date_create('now')->format('Y-m-d') ;
		$dateBegin = $this->getParamDate($params,'date',$dte). " 00:00:00";
		$dateEnd = $this->getParamDate($params,'dateend',$dte). " 23:59:59";
		
		$filter = "" ;
		$emp_id_begin = $this->getParam($params,'empIdBegin',"") ;
		$emp_id_end = $this->getParam($params,'empIdEnd',"") ;
		$dept_id = $this->getParam($params,'dept',"") ;
		$reporttype = $this->getParam($params,'reporttype',"") ;
		
		$rows = $this->getData($dateBegin, $dateEnd, $emp_id_begin, $emp_id_end, $reporttype, $dept_id) ;
		
		if (count($rows) > 0) {
			$datas = array() ;
			$nr = 'newrow';
			$np = 'newpage';
			$ph = "pageheader";
			$i = 'items';
			foreach ($rows as $row) {
				$items = array() ;
				
				$items[$i][] = $this->createPdfItem($row[EmployeeTable::C_ID],50,0,"C") ;
				$items[$i][] = $this->createPdfItem($row[EmployeeTable::C_NAME],150) ;
				
				$items[$i][] = $this->createPdfItem((is_null($row[AttendanceTable::C_TIME_START]) ? "--" : $row[AttendanceTable::C_TIME_START]),50,0,"C") ;
				$items[$i][] = $this->createPdfItem(($row[AttendanceTable::C_LATE_IN] != 0 ? $row[AttendanceTable::C_LATE_IN] : "--"),50,0,"C") ;
				$items[$i][] = $this->createPdfItem((is_null($row[AttendanceTable::C_TIME_END]) ? "--" : $row[AttendanceTable::C_TIME_END]),50,0,"C") ;
				$items[$i][] = $this->createPdfItem(($row[AttendanceTable::C_EARLY_OUT] != 0 ? $row[AttendanceTable::C_EARLY_OUT] : "--"),50,0,"C") ;
				
				if($row[AttendanceTable::C_LATE_IN] != 0 && $row[AttendanceTable::C_EARLY_OUT] == 0){
					$items[$i][] = $this->createPdfItem("Late In",50,0,"C") ;
				} else if($row[AttendanceTable::C_LATE_IN] == 0 && $row[AttendanceTable::C_EARLY_OUT] != 0){
					$items[$i][] = $this->createPdfItem("Early Out",50,0,"C") ;
				} else if($row[AttendanceTable::C_LATE_IN] != 0 && $row[AttendanceTable::C_EARLY_OUT] != 0){
					$items[$i][] = $this->createPdfItem("Both",50,0,"C") ;
				} else {
					$items[$i][] = $this->createPdfItem("None",50,0,"C") ;
				}
				
				$items[$nr] = "1" ;
				$datas[] = $items ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",50,0,"C","B");
			$cols[] = $this->createPdfItem("Name",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("In",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Late In",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Out",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Early Out",50,0,"C","B") ;
			$cols[] = $this->createPdfItem("Remarks",50,0,"C","B") ;
			$headers = array() ;
			$headers[] = "Company : %=COMPANY=%" ;
			$pdf = new ListPdf('L');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Disciplinary Report - " . $this->getParamDate($params,'date',$dte) . " - " . $this->getParamDate($params,'dateend',$dte)) ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->setHeaders($headers) ;
			$pdf->setHeaderHeight(135) ;
			$pdf->render($datas) ;
			$pdf->Output('disciplinary.pdf', 'I');
			unset($datas) ;
			unset($params) ;
			unset($items) ;
			unset($cols) ;
		} else {
			echo "<tr><td colspan='7'>No Record Found.</td></tr>" ;
			return;
		}
		unset($rows) ;
	}
}
?>