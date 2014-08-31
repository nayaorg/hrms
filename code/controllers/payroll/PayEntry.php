<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/PayHeaderClass.php") ;
require_once (PATH_MODELS . "payroll/PayDetailClass.php") ;
require_once (PATH_MODELS . "payroll/PayFundLevyClass.php") ;
require_once (PATH_MODELS . "payroll/PayCpfClass.php") ;
require_once (PATH_MODELS . "payroll/EmployeePayClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;
require_once (PATH_TABLES . "payroll/PayTypeTable.php") ;

class PayEntry extends ControllerBase {
	
	private $type = "" ;
	
	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->orgid = $_SESSION[SE_ORGID] ;
		$this->fldorg = PayHeaderTable::C_ORG_ID ;
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
				$this->sendJsonResponse(Status::Error,"Invalid pay date. Please try again.","",$this->type);
				return ;
			}
			$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
			$clsheader = new PayHeaderClass($this->db) ;
			$clsdetail = new PayDetailClass($this->db) ;
			$clsfund = new PayFundLevyClass($this->db) ;
			$clspay = new EmployeePayClass($this->db) ;
			$clstype = new PayTypeClass($this->db) ;
			$clscpf = new PayCpfClass($this->db) ;
			$clsemp = new EmployeeClass($this->db) ;
			
			$cpfamt = array() ;
			$incomeamt = array() ;
			$adds = 0 ;
			$deducts = 0 ;
			$prow = $clspay->getRecord($empid) ;
			if (is_null($prow)) {
				$fund = "" ;
				$cpfid = 0 ;
			} else {
				$fund = $prow[EmployeePayTable::C_FUND] ;
				$cpfid = $prow[EmployeePayTable::C_CPF_TYPE] ;
			}
			$emprow = $clsemp->getRecord($empid) ;
			if (is_null($emprow)) {
				$coyid = 0 ;
				$orgid = 0 ; 
			} else {
				$coyid = $emprow[EmployeeTable::C_COY_ID] ;
				$orgid = $emprow[EmployeeTable::C_ORG_ID] ;
			}
			try {
				$modifyby = $_SESSION[SE_USERID] ;
				$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
				$ws = $_SESSION[SE_REMOTE_IP] ;
				
				$qty = $this->getParamNumeric($params,'qty',1) ;
				$value = $this->getParamNumeric($params,'value',0) ;
				
				$this->db->beginTran() ;
				$clsdetail->deleteRecord($empid,$date) ;
				$clsfund->deleteRecord($empid,$date) ;
				
				$basic = $qty * $value ;
				$incomeamt = $clsdetail->updateDetail($empid,$date,$orgid,$coyid,$this->getParam($params,'income',""),$this->getParam($params,'deduct',"")) ;
				$totpay = $basic + $incomeamt['income'] - $incomeamt['deduct'] ;

				$cpfamt = $clscpf->calculateCpf($empid,$cpfid,$date,$incomeamt['ow']+$basic,$incomeamt['aw']) ;
				
				$datas = array() ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_BASIC,$basic) ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_INCOME,$incomeamt['income']) ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_DEDUCT,$incomeamt['deduct']) ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_REF,"") ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_COY_ID,$coyid) ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_WS_ID,$ws) ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_MODIFY_BY,$modifyby) ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_MODIFY_DATE,$modifydate) ;
				$datas[] = $this->db->fieldValue(PayHeaderTable::C_ORG_ID,$orgid) ;
				$clsheader->updateRecord($empid,$date,$datas) ;
				$datas = array() ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_OW_PAY,$incomeamt['ow']+$basic) ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_AW_PAY,$incomeamt['aw']) ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_OW,$cpfamt['ow']) ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_AW,$cpfamt['aw']) ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_CPF_EMP,$cpfamt['emp']) ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_CPF_COY,$cpfamt['coy']);
				$datas[] = $this->db->fieldValue(PayCpfTable::C_REF,"") ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_COY_ID,$coyid) ;
				$datas[] = $this->db->fieldValue(PayCpfTable::C_ORG_ID,$orgid) ;
				$clscpf->updateRecord($empid,$date,$datas) ;
				$datas = array() ;
				$datas[] = $this->db->fieldValue(PayDetailTable::C_EMP_ID,$empid);
				$datas[] = $this->db->fieldValue(PayDetailTable::C_DATE,$date) ;
				$datas[] = $this->db->fieldValue(PayDetailTable::C_WAGE_TYPE,1) ;	//1-ow
				$datas[] = $this->db->fieldValue(PayDetailTable::C_TAX_TYPE,1) ;	//1-salary
				$datas[] = $this->db->fieldValue(PayDetailTable::C_VALUE,$value) ;
				$datas[] = $this->db->fieldValue(PayDetailTable::C_TYPE,0) ;		//pay type = 0 for basic pay.
				$datas[] = $this->db->fieldValue(PayDetailTable::C_QTY,$qty) ;
				$datas[] = $this->db->fieldValue(PayDetailTable::C_INCOME_TYPE,0) ;	//0-income 1-deductions
				$datas[] = $this->db->fieldValue(PayDetailTable::C_ORG_ID,$orgid) ;
				$datas[] = $this->db->fieldValue(PayDetailTable::C_COY_ID,$coyid) ; 
				$clsdetail->addRecord($datas) ;
				if ($fund != "")
					$clsfund->updateFundLevy($fund,$empid,$date,$totpay,$orgid,$coyid) ;
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Empooyee pay detail successfully updated to the system.",$empid,$this->type) ;
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[PayEntry]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating employee pay detail to the system.","",$this->type) ;
			}
			unset($clsheader) ;
			unset($clsdetail) ;
			unset($clsfund) ;
			unset($clspay) ;
			unset($clstype) ;
			unset($clscpf) ;
			unset($clsemp) ;
			unset($cpfamt) ;
			unset($incomeamt) ;
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
				$this->sendJsonResponse(Status::Error,"Invalid pay date. Please try again.","",$this->type);
				return ;
			}
			$date = $year . '-' . $month . '-' . Util::getLastDay($year,$month) ;
			$clsemp = new EmployeeClass($this->db) ;
			$clscoy = new CompanyClass($this->db) ;
			$clsdept = new DepartmentClass($this->db) ;
			$clsheader = new PayHeaderClass($this->db) ;
			$clsdetail = new PayDetailClass($this->db) ;
			$clsfund = new PayFundLevyClass($this->db) ;
			
			$hrow = $clsheader->getRecord($empid,$date) ;
			$coydesc = "" ;
			$deptdesc = "" ;
			if (!is_null($hrow) && count($hrow) > 0) {
				$emprow = $clsemp->getRecord($empid) ;
				if (is_null($emprow) || count($emprow) == 0) {
					$name = $empid ;
					$deptdesc = "" ;
				} else {
					$name = $emprow[EmployeeTable::C_NAME] ;
					$deptdesc = $clsdept->getDescription($emprow[EmployeeTable::C_DEPT]) ;
				}
				$coydesc = $clscoy->getDescription($hrow[0][PayHeaderTable::C_COY_ID]) ;
				$drows = $clsdetail->getRecord($empid,$date) ;
				$paytype = "" ;
				$qty = "" ;
				$value = "" ;
				$income = "" ;
				$deduct = "" ;
				if (!is_null($drows) || count($drows) > 0) {
					foreach ($drows as $drow) {
						$dtype = $drow[PayDetailTable::C_TYPE] ;
						
						if ($dtype == 0) {
							$value = number_format($drow[PayDetailTable::C_VALUE], 2, '.', '');
							$qty = number_format($drow[PayDetailTable::C_QTY],2,'.','') ;
						} else {
							if ($drow[PayDetailTable::C_INCOME_TYPE] == 0) {
								if (strlen($income) > 0)
									$income .= "|" ;
								$income .= $dtype . ":" . number_format($drow[PayDetailTable::C_VALUE],2,'.','') ;
							} else {
								if (strlen($deduct) > 0)
									$deduct .= "|" ;
								$deduct .= $dtype . ":" . number_format($drow[PayDetailTable::C_VALUE],2,'.','') ;
							}							
						}
					}
				}

				$sdl = "" ;
				$mbmf = "" ;
				$cdac = "";
				$sinda = "" ;
				$ecf = "" ;
				$frows = $clsfund->getRecord($empid,$date) ;
				if (!is_null($frows) && count($frows) > 0) {
					foreach ($frows as $frow) {
						$ftype = $frow[PayFundLevyTable::C_TYPE];
						$famt = number_format($frow[PayFundLevyTable::C_AMOUNT],2,'.',',');
						if ($ftype == LEVY_SDL)
							$sdl = $famt ;
						else if ($ftype == FUND_MBMF)
							$mbmf = $famt ;
						else if ($ftype == FUND_SINDA)
							$sinda = $famt ;
						else if ($ftype == FUND_CDAC)
							$cdac = $famt ;
						else if ($ftype == FUND_ECF)
							$ecf = $famt ;
					}
				}
				$datas = array() ;
				$datas['id'] = $empid ;
				$datas['name'] = $name ;
				$datas['paytype'] = $paytype ;
				$datas['qty'] = $qty;
				$datas['value'] = $value;
				$datas['coy'] = $coydesc ;
				$datas['dept'] = $deptdesc ;
				$datas['sdl'] = $sdl ;
				$datas['mbmf'] = $mbmf;
				$datas['sinda'] = $sinda ;
				$datas['cdac'] = $cdac ;
				$datas['ecf'] = $ecf ;
				$datas['income'] = $income ;
				$datas['deduct'] = $deduct ;
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
				unset($clsemp) ;
				unset($clsheader) ;
				unset($clsdetail) ;
				unset($clsfund) ;
				unset($clsdept) ;
				unset($clscoy) ;
				unset($hrows) ;
				unset($drows);
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
			echo "<tr><td colspan='9'>Invalid pay date.</td></tr>" ;
			return ;
		}
		$clsemp = new EmployeeClass($this->db) ;
		$clscoy = new CompanyClass($this->db) ;
		$clsdept = new DepartmentClass($this->db) ;
		$clsheader = new PayHeaderClass($this->db) ;
		$clsdetail = new PayDetailClass($this->db) ;
		$clsfund = new PayFundLevyClass($this->db) ;
		$filter = "" ;
		$basic = 0;
		$income = 0;
		$deduct = 0;
		$netpay = 0;
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
			
			$filter .= " and " . $this->db->fieldParam(PayHeaderTable::C_END) ;
			$params[] = $this->db->valueParam(PayHeaderTable::C_END,$date) ;
		}
		$sql = " select h.*,e." . EmployeeTable::C_NAME . ",e." . EmployeeTable::C_DEPT
			. " from " . PayHeaderTable::C_TABLE . "  as h "
			. " left join " . EmployeeTable::C_TABLE . " as e "
			. " on h." . PayHeaderTable::C_EMP_ID . " = e." . EmployeeTable::C_ID
			. " where " . $filter 
			. " order by e." . EmployeeTable::C_NAME ;

		$rows = $this->db->getTable($sql,$params) ;
		$list = "" ;
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$basic = $row[PayHeaderTable::C_BASIC] ;
				$income = $row[PayHeaderTable::C_INCOME] ;
				$deduct = $row[PayHeaderTable::C_DEDUCT];
				$netpay = $basic + $income - $deduct ;
				$id = $row[PayHeaderTable::C_EMP_ID] ;
				$deptid = $row[EmployeeTable::C_DEPT] ;
				$empname = $row[EmployeeTable::C_NAME] ;
				$list .= "<tr>" ;
				$list .= "<td>" . $id . "</td>" ;
				$list .= "<td>" . $empname . "</td>" ;
				$list .= "<td>" . $clscoy->getDescription($row[PayHeaderTable::C_COY_ID]) . "</td>" ;
				$list .= "<td>" . $clsdept->getDescription($deptid) . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($basic, 2, '.', ',') . "</td>";
				$list .= "<td style='text-align:right'>" . number_format($income, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($deduct, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:right'>" . number_format($netpay, 2, '.', ',') . "</td>" ;
				$list .= "<td style='text-align:center'><a href='javascript:' onclick='editPayEntry(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
				$list .= "</tr>" ;
			}
		} else {
			$list .= "<tr><td colspan='9'>No Employee Found.</td></tr>" ;
		}
		unset($rows) ;
		unset($clsheader) ;
		unset($clsdetail) ;
		unset($clsfund) ;
		unset($clscoy) ;
		unset($clsdept) ;
		unset($clsemp) ;
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
	private function getPayType($type) {
		$filter = array() ;
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$filter[] = array('field'=>PayTypeTable::C_INCOME_TYPE,'value'=>$type) ;
		if ($type == 0)
			$desc = "Pay Type" ;
		else 
			$desc = "Pay Type" ;
		$vls = $this->getValueList(PayTypeTable::C_TABLE, PayTypeTable::C_ID, PayTypeTable::C_DESC,array('code'=>'','desc'=>'--- Select a ' . $desc . ' ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/PayEntryView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
}
?>