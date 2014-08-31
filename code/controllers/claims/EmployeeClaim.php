<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_MODELS . "admin/CompanyOptions.php") ;
require_once (PATH_MODELS . "claims/ClaimHeaderClass.php") ;
require_once (PATH_MODELS . "claims/ClaimDetailClass.php") ;

class EmployeeClaim extends ControllerBase {
	
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
	
	private function getList($datas=null) {
		$month = $this->getParam($datas,'empIdBegin',"") ;
		$year = $this->getParam($datas,'empIdEnd',"") ;
		
		$filter = "" ;
		$dept_id = $this->getParam($datas,'dept',"") ;
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.");
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		if ($dept_id != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$dept_id) ;
		}
		
		$sql = "select * from " . EmployeeTable::C_TABLE . " e ";
		$sql .= "WHERE " . $filter;

		$rows_emp = $this->db->getTable($sql,$params) ;
		
		$list = "" ;
		if (count($rows_emp) > 0) {
			foreach ($rows_emp as $row_emp) {
				$rows = $this->getData($month, $year, $row_emp[EmployeeTable::C_ID]) ;
				$list .= "<tr>" ;
				$list .= "<td>" . $row_emp[EmployeeTable::C_ID] . "</td>" ;
				$list .= "<td>" . $row_emp[EmployeeTable::C_NAME] . "</td>" ;
				
				$list .= "<td style='text-align:right'>" . (is_null($rows[0]['APPROVED_AMOUNT']) ? '0.00' : number_format($rows[0]['APPROVED_AMOUNT'],2,'.','')). "</td>" ;
				$list .= "</tr>" ;
			}
		} else {
			$list .= "<tr><td colspan='10'>No Employee Found.</td></tr>" ;
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
		include (PATH_VIEWS . "claims/EmployeeClaimView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getMonth() {
		$arr = array() ;
		$arr[] = array ('code'=>'01','desc'=>'January' ) ;
		$arr[] = array ('code'=>'02','desc'=>'February' ) ;
		$arr[] = array ('code'=>'03','desc'=>'March' ) ;
		$arr[] = array ('code'=>'04','desc'=>'April') ;
		$arr[] = array ('code'=>'05','desc'=>'May') ;
		$arr[] = array ('code'=>'06','desc'=>'June') ;
		$arr[] = array ('code'=>'07','desc'=>'July') ;
		$arr[] = array ('code'=>'08','desc'=>'August') ;
		$arr[] = array ('code'=>'09','desc'=>'September') ;
		$arr[] = array ('code'=>'10','desc'=>'October') ;
		$arr[] = array ('code'=>'11','desc'=>'November') ;
		$arr[] = array ('code'=>'12','desc'=>'December') ;
		return Util::createOptionValue($arr) ;
	}
	private function getYear() {
		$date = date_create();
		$date = intval($date->format('Y'));
	
		$arr = array() ;
		for($i=1990;$i<=$date;$i++){
			$arr[] = array ('code'=>'' . $i,'desc'=>$i ) ;
		}
		return Util::createOptionValue($arr) ;
	}
	private function getData($month, $year, $emp_id) {
		$date_start = date_create($year . "-" . $month . "-01");
		$date_end = date_create($year . "-" . $month . "-01");
		$date_end->add(new DateInterval('P1M'));
		
		$sql = "SELECT SUM(D." . ClaimDetailTable::C_APPROVED_AMT . ") as APPROVED_AMOUNT ";
		$sql .= "FROM " . ClaimHeaderTable::C_TABLE . " C ";
		$sql .= "LEFT OUTER JOIN " . ClaimDetailTable::C_TABLE . " D on C." . ClaimHeaderTable::C_ID . " = D." . ClaimDetailTable::C_ID . " ";
		$sql .= "WHERE C." . ClaimHeaderTable::C_EMP . " = " . $emp_id . " ";
		$sql .= "AND C." . ClaimHeaderTable::C_ORG_ID . " = " . $_SESSION[SE_ORGID] . " ";
		$sql .= "AND C." . ClaimHeaderTable::C_DATE . " >= '" . $date_start->format('Y-m-d') . " 00:00:00.000' ";
		$sql .= "AND C." . ClaimHeaderTable::C_DATE . " < '" . $date_end->format('Y-m-d') . " 00:00:00.000' ";
		$sql .= "AND D." . ClaimDetailTable::C_STATUS . " = 2 ";
		$sql .= "AND C." . ClaimHeaderTable::C_STATUS . " = " . ClaimStatus::Approved;
		
		return $this->db->getTable($sql) ;
	}
	private function getExport($params=null) {
		//require_once(PATH_EXCEL . '01simple-download-xlsx.php');
		$month = $this->getParam($params,'date',"") ;
		$year = $this->getParam($params,'dateend',"") ;
		
		$filter = "" ;
		$dept_id = $this->getParam($params,'dept',"") ;
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.");
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		if ($dept_id != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$dept_id) ;
		}
		
		$sql = "select * from " . EmployeeTable::C_TABLE . " e ";
		$sql .= "WHERE " . $filter;

		$rows_emp = $this->db->getTable($sql,$params) ;
		
		if (count($rows_emp) > 0) {
			$datas = "" ;
			$expfile = "" ;
			
			$excel = new PHPExcel();
			

			$excel->getProperties()->setCreator("bttan")
								->setLastModifiedBy("bttan")
								->setTitle("Employee Claim Report")
								->setSubject("Employee Claim Report")
								->setDescription("Employee Claim Report")
								->setKeywords("Claim")
								->setCategory("");

			$excel->setActiveSheetIndex(0)
					->setCellValue('A1', "Employee Claim Report")
					->setCellValue('A2', $month . "/" . $year);
					
			$excel->setActiveSheetIndex(0)
					->setCellValue('A4', "ID")
					->setCellValue('B4', "Name")
					->setCellValue('C4', "Amount");
					
			
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$excel->getActiveSheet()->mergeCells('A1:C1');
			$excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);
			$excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$excel->getActiveSheet()->mergeCells('A2:C2');
			$excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			
			$excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$idx = 5;
			
			$excel->getActiveSheet()->setTitle('Simple');
			$excel->setActiveSheetIndex(0);
			
			foreach ($rows_emp as $row_emp) {
				$rows = $this->getData($month, $year, $row_emp[EmployeeTable::C_ID]) ;
				
				$excel->setActiveSheetIndex(0)
						->setCellValue('A' . $idx, $row_emp[EmployeeTable::C_ID])
						->setCellValue('B' . $idx, $row_emp[EmployeeTable::C_NAME])
						->setCellValue('C' . $idx, (is_null($rows[0]['APPROVED_AMOUNT']) ? '0.00' : number_format($rows[0]['APPROVED_AMOUNT'],2,'.','')));
					
				$excel->getActiveSheet()->getStyle('C' . $idx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$idx += 1;
			}
			$styleArray = array(
			  'borders' => array(
				'allborders' => array(
				  'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			  )
			);

			$excel->getActiveSheet()->getStyle('A4:C' . ($idx-1))->applyFromArray($styleArray);
			unset($styleArray);
			
			$excel->setActiveSheetIndex(0)
					->setCellValue('B' . $idx, 'Total :')
					->setCellValue('C' . $idx, "=SUM(C4:C" . ($idx-1) . ")");
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
			
			
			$expfile = "employeeclaim.xlsx" ;
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $expfile . '"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$objWriter->save('php://output');
		} else {
			echo "<tr><td colspan='10'>No Employee Found.</td></tr>" ;
			return;
		}
		unset($rows_emp) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		$month = $this->getParam($params,'date',"") ;
		$year = $this->getParam($params,'dateend',"") ;
		
		$filter = "" ;
		$dept_id = $this->getParam($params,'dept',"") ;
		
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.");
				
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		if ($dept_id != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
			$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$dept_id) ;
		}
		
		$sql = "select * from " . EmployeeTable::C_TABLE . " e ";
		$sql .= "WHERE " . $filter;

		$rows_emp = $this->db->getTable($sql,$params) ;
		
		if (count($rows_emp) > 0) {
			$datas = array() ;
			$nr = 'newrow';
			$np = 'newpage';
			$ph = "pageheader";
			$i = 'items';
			$coyid = -1 ;
			foreach ($rows_emp as $row_emp) {
				$rows = $this->getData($month, $year, $row_emp[EmployeeTable::C_ID]) ;
				
				$items = array() ;
				
				$items[$i][] = $this->createPdfItem($row_emp[EmployeeTable::C_ID],50,0,"C") ;
				$items[$i][] = $this->createPdfItem($row_emp[EmployeeTable::C_NAME],150) ;
				
				$items[$i][] = $this->createPdfItem((is_null($rows[0]['APPROVED_AMOUNT']) ? '0.00' : number_format($rows[0]['APPROVED_AMOUNT'],2,'.','')),200,0,"R") ;
				$items[$nr] = "1" ;
				
				$datas[] = $items ;
				
				unset($items) ;
			}
			$cols = array() ;
			$cols[] = $this->createPdfItem("ID",50,0,"C","B");
			$cols[] = $this->createPdfItem("Name",150,0,"C","B") ;
			$cols[] = $this->createPdfItem("Amount",200,0,"C","B") ;
			$headers = array() ;
			$headers[] = "Company : %=COMPANY=%" ;
			$pdf = new ListPdf('L');
			$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
			$pdf->setReportTitle("Employee Claim Report - " . $month . "/" . $year) ;
			$pdf->setColumnsHeader($cols) ;
			$pdf->setHeaders($headers) ;
			$pdf->setHeaderHeight(135) ;
			$pdf->render($datas) ;
			$pdf->Output('EmployeeClaim.pdf', 'I');
			unset($datas) ;
			unset($cols) ;
		} else {
			echo "<tr><td colspan='10'>No Record Found.</td></tr>" ;
			return;
		}
		unset($params) ;
		unset($rows_emp) ;
	}
}
?>