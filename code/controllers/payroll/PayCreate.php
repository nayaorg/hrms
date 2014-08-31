<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "payroll/PayHeaderClass.php") ;
require_once (PATH_MODELS . "payroll/PayDetailClass.php") ;
require_once (PATH_MODELS . "payroll/PayFundLevyClass.php") ;
require_once (PATH_MODELS . "payroll/PayCpfClass.php") ;
require_once (PATH_MODELS . "payroll/PayTypeClass.php") ;
require_once (PATH_MODELS . "payroll/EmployeePayClass.php") ;
require_once (PATH_MODELS . "payroll/EmployeePayTypeClass.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_TABLES . "admin/CompanyTable.php") ;

class PayCreate extends ControllerBase {
	
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
				case REQ_QUERY:
					$this->checkRecord($params) ;
					break ;
				case REQ_UPDATE:
					$this->createRecord($params) ;
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
	private function getList($datas=null) {
		return "" ;
	}
	private function getReport($datas=null) {
		return "" ;
	}
	private function checkRecord($datas=null) {
		$this->sendJsonResponse(Status::Ok,"","","") ;
	}
	private function createRecord($datas=null) {
		$month = $this->getParamInt($datas,'month',0) ;
		$year = $this->getParamInt($datas,'year',0) ;
		if ($month == 0 || $year == 0) {
			$this->sendJsonResponse(Status::Error,"Invalid Pay date. Please try again.","", "") ;
			return ;
		}
		$end = $year . "-" . $month . "-" . Util::getLastDay($year,$month) ; 
		$coyid = $this->getParam($datas,'coy',"") ;
		$count = 0 ;
		$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
		$clsheader = new PayHeaderClass($this->db) ;
		$clsdetail = new PayDetailClass($this->db) ;
		$clsfund = new PayFundLevyClass($this->db) ;
		$clsemppay = new EmployeePayClass($this->db) ;
		$clstype = new PayTypeClass($this->db) ;
		$clsincome = new EmployeePayTypeClass($this->db) ;
		$clscpf = new PayCpfClass($this->db) ;
		
		try {
			$rows = $this->getData($coyid,$end) ;
			$this->db->beginTran() ;
			if ($coyid == "") {
				$clsheader->deleteDate($end) ;
				$clsdetail->deleteDate($end) ;
				$clsfund->deleteDate($end) ;
				$clscpf->deleteDate($end) ;
			} else {
				$clsheader->deleteCompany($coyid,$end) ;
				$clsdetail->deleteCompany($coyid,$end) ;
				$clsfund->deleteCompany($coyid,$end);
				$clscpf->deleteCompany($coyid,$end) ;
			}
			$start = ($year . '-' . $month . '-01');
			
			if (count($rows) > 0) {
				foreach ($rows as $row) {
					$fund = $row[EmployeePayTable::C_FUND] ;
					$value = $row[EmployeePayTable::C_VALUE];
					$method = $row[EmployeePayTable::C_METHOD] ;
					$empid = $row[EmployeePayTable::C_ID] ;
					$coyid = $row[EmployeePayTable::C_COY_ID] ;
					$cpfid = $row[EmployeePayTable::C_CPF_TYPE] ;
					$count++ ;
					$datas = array() ;
					$datas[] = $this->db->fieldValue(PayDetailTable::C_EMP_ID,$empid);
					$datas[] = $this->db->fieldValue(PayDetailTable::C_DATE,$end) ;
					$datas[] = $this->db->fieldValue(PayDetailTable::C_WAGE_TYPE,1) ;		//0-none 1-ow 2-aw
					$datas[] = $this->db->fieldValue(PayDetailTable::C_TAX_TYPE,1) ;		//1-SALARY
					$datas[] = $this->db->fieldValue(PayDetailTable::C_VALUE,$value) ;
					$datas[] = $this->db->fieldValue(PayDetailTable::C_TYPE,0) ;		//pay type = 0 for basic pay.
					$datas[] = $this->db->fieldValue(PayDetailTable::C_QTY,1) ;
					$datas[] = $this->db->fieldValue(PayDetailTable::C_INCOME_TYPE,0) ;	//0-income 1-deduct
					$datas[] = $this->db->fieldValue(PayDetailTable::C_ORG_ID,$this->orgid) ;
					$datas[] = $this->db->fieldValue(PayDetailTable::C_COY_ID,$coyid) ; 
					$clsdetail->addRecord($datas) ;
					$income = 0 ;
					$deduct = 0;
					$aw = 0 ;
					$ow = $value ;
					$trows = $clsincome->getRecord($empid) ;
					if (!is_null($trows) && count($trows) > 0) {
						foreach ($trows as $trow) {
							$paytype = $trow[EmployeePayTypeTable::C_TYPE] ;
							$incvalue = $trow[EmployeePayTypeTable::C_VALUE] ;
							$wagetype = 1 ;	//0-none 1-ow 2-aw
							$taxtype = 1;	//0-false 1-true
							$inctype = 0 ;	//0-income 1-deduction
							$qty = 1 ;
							$prow = $clstype->getRecord($paytype) ;
							if (!is_null($prow) && count($prow) > 0 ) {
								$inctype = $prow[PayTypeTable::C_INCOME_TYPE] ;
								$wagetype = $prow[PayTypeTable::C_WAGE_TYPE] ;
								$taxtype = $prow[PayTypeTable::C_TAX_TYPE] ;
							}
							if ($inctype == 1)
								$qty = -1 ;
							$datas = array() ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_EMP_ID,$empid);
							$datas[] = $this->db->fieldValue(PayDetailTable::C_DATE,$end) ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_WAGE_TYPE,$wagetype) ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_TAX_TYPE,$taxtype) ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_VALUE,$incvalue) ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_TYPE,$paytype) ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_QTY,$qty) ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_INCOME_TYPE,$inctype) ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_ORG_ID,$this->orgid) ;
							$datas[] = $this->db->fieldValue(PayDetailTable::C_COY_ID,$coyid) ; 
							$clsdetail->addRecord($datas) ;
							if ($inctype == 0) {
								if ($wagetype == 1)
									$ow += $incvalue ;
								else if ($wagetype == 2)
									$aw += $incvalue ;
									
								$income += $incvalue ;
							} else {
								if ($wagetype == 1)
									$ow -= $incvalue ;
								else if ($wagetype == 2)
									$aw -= $incvalue ;
								$deduct += $incvalue ;
							}
						}
					}

					if ($fund != "")
						$clsfund->updateFundLevy($fund,$empid,$end,$income+$value,$this->orgid,$coyid) ;
					$cpfamt = $clscpf->calculateCpf($empid,$cpfid,$end,$ow,$aw) ;
					$datas = array() ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_EMP_ID,$empid) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_START,$start) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_END,$end) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_BASIC,$value) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_METHOD,$method) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_INCOME,$income) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_DEDUCT,$deduct) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_REF,"") ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_COY_ID,$coyid) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_ORG_ID,$this->orgid) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_WS_ID,$_SESSION[SE_REMOTE_IP]) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_MODIFY_BY,$_SESSION[SE_USERID]);
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_CREATE_BY,$_SESSION[SE_USERID]) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_MODIFY_DATE,$modifydate) ;
					$datas[] = $this->db->fieldValue(PayHeaderTable::C_CREATE_DATE,$modifydate) ;
					$clsheader->addRecord($datas) ;
					$datas = array() ;
					$datas[] = $this->db->fieldValue(PayCpfTable::C_EMP_ID,$empid) ;
					$datas[] = $this->db->fieldValue(PayCpfTable::C_DATE,$end) ;
					$datas[] = $this->db->fieldValue(PayCpfTable::C_CPF_EMP,$cpfamt['emp']) ;
					$datas[] = $this->db->fieldValue(PayCpfTable::C_CPF_COY,$cpfamt['coy']) ;
					$datas[] = $this->db->fieldValue(PayCpfTable::C_OW,$cpfamt['ow']) ;	//ow amount for cpf calculation
					$datas[] = $this->db->fieldValue(PayCpfTable::C_AW,$cpfamt['aw']) ; //aw amount for cpf calculation
					$datas[] = $this->db->fieldValue(PayCpfTable::C_AW_PAY,$aw) ;	//total aw amount
					$datas[] = $this->db->fieldValue(PayCpfTable::C_OW_PAY,$ow) ;	//total ow amount
					$datas[] = $this->db->fieldValue(PayCpfTable::C_REF,"") ;
					$datas[] = $this->db->fieldValue(PayCpfTable::C_COY_ID,$coyid) ;
					$datas[] = $this->db->fieldValue(PayCpfTable::C_ORG_ID,$this->orgid) ;
					$clscpf->addRecord($datas) ;
				}
			}
			$this->db->commitTran() ;
			unset($rows) ;
			unset($clsheader);
			unset($clsdetail);
			unset($clsfund) ;
			unset($clspay) ;
			unset($clstype) ;
			unset($clscpf) ;

			if ($count > 0)
				$this->sendJsonResponse(Status::Ok,"Employee Pay record successfully created.",$count,"") ;
			else 
				$this->sendJsonResponse(Status::Ok,"No Employee record foud.",$count,"") ;
		} catch (Exception $e) {
			$this->db->rollbackTran() ;
			Log:write('[PayCreate]' . $e->getMessage()) ;
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","","") ;
		}
	}
	private function getData($coyid,$date) {
		
		$params = array() ;
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.") .
			" and " . $this->db->fieldParam(EmployeePayTable::C_START,"<=") .
			" and " . $this->db->fieldParam(EmployeePayTable::C_END,">=") ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$this->orgid) ;
		$params[] = $this->db->valueParam(EmployeePayTable::C_START,$date) ;
		$params[] = $this->db->valueParam(EmployeePayTable::C_END,$date) ;
		
		if ($coyid != "") {
			$filter .= " and " . $this->db->fieldParam(EmployeeTable::C_COY_ID,"=","e.") ;
			$params[] = $this->db->valueParam(EmployeeTable::C_COY_ID,$coyid) ;
		}
		$sql = " select p.*,e." . EmployeeTable::C_COY_ID
			. " from " . EmployeePayTable::C_TABLE . "  as p "
			. " left join " . EmployeeTable::C_TABLE . " as e "
			. " on p." . EmployeePayTable::C_ID . " = e." . EmployeeTable::C_ID
			. " where " . $filter 
			. " order by p." . EmployeePayTable::C_ID ;

		return $this->db->getTable($sql,$params) ;
	}
	private function getCompany() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CompanyTable::C_TABLE, CompanyTable::C_COY_ID, CompanyTable::C_DESC,array('code'=>'','desc'=>'All Company'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "payroll/PayCreateView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
}
?>