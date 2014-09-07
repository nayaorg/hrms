<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/PayFundLevyClass.php") ;
require_once (PATH_MODELS . "payroll/PayCpfClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;

class CpfEntry extends ControllerBase {
	
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = PayCpfTable::C_ORG_ID ;
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
					echo $this->getList($params) ;
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
			$empid = $params['id'] ;
			$month = $this->getParamInt($params,'month',0) ;
			$year = $this->getParamInt($params,'year',0) ;
			if ($month==0 || $year == 0) {
				$this->sendJsonResponse(Status::Error,"Invalid cpf period. Please try again.","",$this->type);
				return ;
			}
			$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
			$clsfund = new PayFundLevyClass($this->db) ;
			$clscpf = new PayCpfClass($this->db) ;
			$clsemp = new EmployeeClass($this->db) ;
			try {
				$emprow = $clsemp->getRecord($empid) ;
				if (is_null($emprow))
					$coyid = 0 ;
				else
					$coyid = $emprow[EmployeeTable::C_COY_ID] ;
				$cpfemp = $this->getParamNumeric($params,'cpfemp',0) ;
				$cpfcoy = $this->getParamNumeric($params,'cpfcoy',0) ;
				$sdl = $this->getParamNumeric($params,'sdl',0) ;
				$mbmf = $this->getParamNumeric($params,'mbmf',0) ;
				$sinda = $this->getParamNumeric($params,'sinda',0) ;
				$cdac = $this->getParamNumeric($params,'cdac',0) ;
				$ecf = $this->getParamNumeric($params,'ecf',0) ;
				$this->db->beginTran() ;
				$clsfund->deleteRecord($empid,$date) ;
				
				$datas = array() ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_CPF_EMP,$cpfemp) ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_CPF_COY,$cpfcoy);
				$clscpf->updateRecord($empid,$date,$datas) ;
				if ($sdl > 0)
					$clsfund->updateAmount(LEVY_SDL,$empid,$date,$sdl,$this->orgid,$coyid) ;
				if ($mbmf > 0)
					$clsfund->updateAmount(FUND_MBMF,$empid,$date,$mbmf,$this->orgid,$coyid) ;
				if ($sinda > 0)
					$clsfund->updateAmount(FUND_SINDA,$empid,$date,$sinda,$this->orgid,$coyid) ;
				if ($cdac > 0)
					$clsfund->updateAmount(FUND_CDAC,$empid,$date,$cdac,$this->orgid,$coyid) ;
				if ($ecf > 0)
					$clsfund->updateAmount(FUND_ECF,$empid,$date,$ecf,$this->orgid,$coyid) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Empooyee cpf detail successfully updated to the system.",$empid,$this->type) ;
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[CpfEntry]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating cpf detail to the system.","",$this->type) ;
			}
			unset($clsfund) ;
			unset($clscpf) ;
			unset($clsemp) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id you wish to update. Please try again.","",$this->type);
		}
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$empid = $params['id'] ;
			$month = $this->getParamInt($params,'month',0) ;
			$year = $this->getParamInt($params,'year',0) ;
			if ($month==0 || $year == 0) {
				$this->sendJsonResponse(Status::Error,"Invalid cpf perios. Please try again.","",$this->type);
				return ;
			}
			$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
			$clsemp = new EmployeeClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clscpf = new PayCpfClass($this->db) ;
			$clsfund = new PayFundLevyClass($this->db) ;
			
			$cpfrow = $clscpf->getRecord($empid,$date) ;
			$coydesc = "" ;
			$deptdesc = "" ;
			if (!is_null($cpfrow) && count($cpfrow) > 0) {
				$emprow = $clsemp->getRecord($empid) ;
				if (is_null($emprow) || count($emprow) == 0) {
					$name = $empid ;
					$deptdesc = "" ;
					$coyid = 0 ;
				} else {
					$name = $emprow[EmployeeTable::C_NAME] ;
					$deptdesc = $clsdept->getDescription($emprow[EmployeeTable::C_DEPT]) ;
					$coyid = $emprow[EmployeeTable::C_COY_ID] ;
				}
				$cpfcoy = number_format($cpfrow[0][PayCpfTable::C_CPF_COY], 2, '.', '');
				$cpfemp = number_format($cpfrow[0][PayCpfTable::C_CPF_EMP],2,'.','') ;
				$aw = number_format($cpfrow[0][PayCpfTable::C_AW_PAY], 2, '.', ',');
				$ow = number_format($cpfrow[0][PayCpfTable::C_OW_PAY],2,'.',',') ;
				$coydesc = $clscoy->getDescription($coyid) ;
				$sdl = "" ;
				$mbmf = "" ;
				$sinda = "" ;
				$cdac = "" ;
				$ecf = "" ;
				$fwl = "" ;
				$frows = $clsfund->getRecord($empid,$date) ;
				foreach ($frows as $fr) {
					$t = $fr[PayFundLevyTable::C_TYPE] ;
					$a = $fr[PayFundLevyTable::C_AMOUNT] ;
					if ($t == FUND_MBMF) {
						$mbmf = number_format($a, 2, '.', ','); 
					} elseif ($t == FUND_SINDA) {
						$sinda = number_format($a, 2, '.', ','); 
					} elseif ($t == FUND_CDAC) {
						$cdac = number_format($a, 2, '.', ','); 
					} elseif ($t == FUND_ECF) {
						$ecf = number_format($a, 2, '.', ','); 
					} elseif ($t == LEVY_FWL) {
						$fwl = number_format($a, 2, '.', ','); 
					} elseif ($t == LEVY_SDL) {
						$sdl = number_format($a, 2, '.', ','); 
					}
				}
				$datas = array() ;
				$datas['id'] = $empid ;
				$datas['name'] = $name ;
				$datas['cpfemp'] = $cpfemp;
				$datas['cpfcoy'] = $cpfcoy;
				$datas['ow'] = $ow ;
				$datas['aw'] = $aw ;
				$datas['coy'] = $coydesc ;
				$datas['dept'] = $deptdesc ;
				$datas['sdl'] = $sdl ;
				$datas['mbmf'] = $mbmf ;
				$datas['sinda'] = $sinda ;
				$datas['cdac'] = $cdac ;
				$datas['ecf'] = $ecf ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
				unset($clsemp) ;
				unset($clscpf) ;
				unset($clsfund) ;
				unset($clsdept) ;
				unset($clscoy) ;
				unset($cpfrows) ;
				unset($frows) ;
			} else {
				$this->sendJsonResponse(Status::Error,"Employee id not found in the Pay slip list. Please try again.","",$this->type) ;
			}
			unset($emp) ;
			unset($emprow) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing Employee id. Please try again.","",$this->type);
		}
	}
	private function getList($datas=null) {
		$month = $this->getParamInt($datas,'month',0) ;
		$year = $this->getParamInt($datas,'year',0) ;
		if ($month==0 || $year == 0) {
			echo "<tr><td colspan='9'>Invalid cpf period.</td></tr>" ;
			return ;
		}
		$clsemp = new EmployeeClass($this->db) ;
		$clscoy = new CompanyClass($this->db) ;
		$clsdept = new DepartmentClass($this->db) ;
		$clscpf = new PayCpfClass($this->db) ;
		$clsfund = new PayFundLevyClass($this->db) ;
		$filter = "" ;
		$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ; 
		$filter .= $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.") ;
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		
		if (!is_null($datas) && count($datas) > 0) {
			if (isset($datas['coy']) && $datas['coy'] != "") {
				$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_COY_ID,"=","e.") ;
				$params[] = $this->db->valueParam(EmployeeTable::C_COY_ID,$datas['coy']) ;
			}
			if (isset($datas['dept']) && $datas['dept'] != "") {
				$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_DEPT) ;
				$params[] = $this->db->valueParam(EmployeeTable::C_DEPT,$datas['dept']) ;
			}
			
			$filter .= " and " . $this->db->fieldParam(PayCpfTable::C_DATE) ;
			$params[] = $this->db->valueParam(PayCpfTable::C_DATE,$date) ;
		}
		$sql = " select h.*,e." . EmployeeTable::C_NAME . ",e." . EmployeeTable::C_DEPT
			. " from " . PayCpfTable::C_TABLE . "  as h "
			. " left join " . EmployeeTable::C_TABLE . " as e "
			. " on h." . PayCpfTable::C_EMP_ID . " = e." . EmployeeTable::C_ID
			. " where " . $filter 
			. " order by e." . EmployeeTable::C_NAME ;

		$rows = $this->db->getTable($sql,$params) ;
		$list = "" ;
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$ow = $row[PayCpfTable::C_OW_PAY] ;
				$aw = $row[PayCpfTable::C_AW_PAY] ;
				$cpfemp = $row[PayCpfTable::C_CPF_EMP];
				$cpfcoy = $row[PayCpfTable::C_CPF_COY] ;
				$id = $row[PayCpfTable::C_EMP_ID] ;
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;
				$list .= "<tr>" ;
				$list .= "<td>" . $id . "</td>" ;
				$list .= "<td>" . $empname . "</td>" ;
				$list .= "<td>" . $clscoy->getDescription($row[EmployeeTable::C_COY_ID]) . "</td>" ;
				$list .= "<td>" . $clsdept->getDescription($deptid) . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($ow, 2, '.', ',') . "</td>";
				$list .= "<td style='text-align:right'>" . number_format($aw, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($cpfemp, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($cpfcoy, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:center'><a href='javascript:' onclick='editCpfEntry(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
				$list .= "</tr>" ;
			}
		} else {
			$list .= "<tr><td colspan='9'>No Employee Found.</td></tr>" ;
		}
		unset($rows) ;
		unset($clsfund) ;
		unset($clscoy) ;
		unset($clsdept) ;
		unset($clsemp) ;
		unset($clscpf) ;
		return $list ;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'All Department'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getCompany() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CompanyTable::C_TABLE, CompanyTable::C_COY_ID, CompanyTable::C_DESC,array('code'=>'','desc'=>'All Company'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/CpfEntryView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
}
?>