<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "hr/EmployeeClass.php") ;
require_once (PATH_MODELS . "hr/EmployeePasswordClass.php") ;
require_once (PATH_MODELS . "general/ContactInfoClass.php") ;
require_once (PATH_MODELS . "hr/DepartmentClass.php") ;
require_once (PATH_TABLES . "hr/EmployeeTypeTable.php") ;
require_once (PATH_TABLES . "hr/NationalityTable.php") ;
require_once (PATH_TABLES . "hr/RaceTable.php") ;
require_once (PATH_TABLES . "hr/WorkPermitTable.php") ;
require_once (PATH_TABLES . "hr/JobTitleTable.php") ;
require_once (PATH_MODELS . "admin/CompanyClass.php") ;

class Employee extends ControllerBase {
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
		$cls = new EmployeeClass($this->db) ;
		$cls_p = new EmployeePasswordClass($this->db);
		
		if($cls->checkCode($params['code'], -1)){
			$datas = array() ;
			$modifyby = $_SESSION[SE_USERID] ;
			$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
			$dob = date_create('now')->format('Y-m-d') ;
			$join = $dob ;
			
			$ws = $_SESSION[SE_REMOTE_IP];
			$coyid = $this->getParamInt($params,'coy',0) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_CODE,$this->getParam($params,'code',"")) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_NAME,$this->getParam($params,'name',"")) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_ID_NO,$this->getParam($params,'nric',"")) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_ID_TYPE,$this->getParam($params,'idtype',"")) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_DEPT,$this->getParamInt($params,'dept',0));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_RACE,$this->getParamInt($params,'race',0));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_NATIONALITY,$this->getParamInt($params,'nat',0));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_JOB,$this->getParamInt($params,'job',0));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_GENDER,$this->getParam($params,'gender',"M"));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_MARITAL,$this->getParamInt($params,'marital',0));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_TYPE,$this->getParamInt($params,'emptype',0));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_REF,$this->getParam($params,'refno',""));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_PERMIT, $this->getParamInt($params,'permit',0)) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_COMMENTS,$this->getParam($params,'rmks',""));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_PHOTO,"");
			$datas[] = $this->db->fieldValue(EmployeeTable::C_JOIN,$this->getParamDate($params,'join',$join) . " 00:00:00") ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_RESIGN,$this->getParamDate($params,'resign',MAX_DATE)) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_DOB,$this->getParamDate($params,'dob',$dob). " 00:00:00");
			$datas[] = $this->db->fieldValue(EmployeeTable::C_BLOCK,$this->getParamInt($params,'block',0));
			$datas[] = $this->db->fieldValue(EmployeeTable::C_COY_ID,$coyid);
			$datas[] = $this->db->fieldValue(EmployeeTable::C_WS_ID,$ws) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_MODIFY_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_CREATE_BY,$modifyby) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_MODIFY_DATE,$modifydate) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_CREATE_DATE,$modifydate) ;
			$datas[] = $this->db->fieldValue(EmployeeTable::C_ORG_ID,$this->orgid) ;
			
			try {
				$this->db->beginTran() ;
				$id = $cls->addRecord($datas) ;

				if ($id > 0) {
					$this->addContact($id,$coyid,$params) ;
					$cls_p->updateTable();
					$this->db->commitTran() ;
					$this->sendJsonResponse(Status::Ok,"Employee successfully added to the system.",$id,$this->type);
				} else {
					$this->db->rollbackTran() ;
					$this->sendJsonResponse(Status::Error,"Sorry, there is a error in adding new Employee to the system.",$id, $this->type) ;
				}
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[Employee]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a error in database operation.","",$this->type) ;
			}
		} else {
			$this->sendJsonResponse(Status::Error,"Duplicate code", $this->type);
		}
		unset($cls) ;
		unset($cls_p);
	}
	private function updateRecord($params) {
		if (isset($params['id']) && isset($params['code'])) {
			$id = $params['id'] ;
			$cls = new EmployeeClass($this->db) ;
			$cls_p = new EmployeePasswordClass($this->db) ;
			$ci = new ContactInfoClass($this->db) ;
			if($cls->checkCode($params['code'], $params['id'])){
				try {
					$datas = array() ;
					$modifyby = $_SESSION[SE_USERID] ;
					$modifydate = date_create('now')->format('Y-m-d H:i:s') ;
					$ws = $_SESSION[SE_REMOTE_IP];
					$dob = date_create('now')->format('Y-m-d') ;
					$join = $dob ;
					$coyid = $this->getParamInt($params,'coy',0) ;
					
					$this->db->beginTran() ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_NAME,$this->getParam($params,'name',"")) ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_CODE,$this->getParam($params,'code',"")) ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_ID_NO,$this->getParam($params,'nric',"")) ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_ID_TYPE,$this->getParam($params,'idtype',""));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_DEPT,$this->getParamInt($params,'dept',0));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_RACE,$this->getParamInt($params,'race',0));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_NATIONALITY,$this->getParamInt($params,'nat',0));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_JOB,$this->getParamInt($params,'job',0));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_GENDER,$this->getParam($params,'gender',"M"));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_MARITAL,$this->getParamInt($params,'marital',0));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_TYPE,$this->getParamInt($params,'emptype',0));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_REF,$this->getParam($params,'refno',""));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_PERMIT, $this->getParamInt($params,'permit',0)) ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_COMMENTS,$this->getParam($params,'rmks',""));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_PHOTO,"");
					$datas[] = $this->db->fieldValue(EmployeeTable::C_JOIN,$this->getParamDate($params,'join',$join) . " 00:00:00") ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_RESIGN,$this->getParamDate($params,'resign',MAX_DATE)) ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_DOB,$this->getParamDate($params,'dob',$dob). " 00:00:00");
					$datas[] = $this->db->fieldValue(EmployeeTable::C_BLOCK,$this->getParamInt($params,'block',0));
					$datas[] = $this->db->fieldValue(EmployeeTable::C_COY_ID,$coyid);
					$datas[] = $this->db->fieldValue(EmployeeTable::C_WS_ID,$ws) ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_MODIFY_BY,$modifyby) ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_MODIFY_DATE,$modifydate) ;
					$datas[] = $this->db->fieldValue(EmployeeTable::C_ORG_ID,$this->orgid) ;
					
					$cls->updateRecord($id,$datas) ;
					$ci->deleteRecord(EmployeeTable::C_TABLE,$id) ;
					
					$cls_p->updateTable();
					$this->addContact($id,$coyid,$params) ;
					$this->db->commitTran() ;
					$this->sendJsonResponse(Status::Ok,"Employee detail successfully updated to the system.",$id,$this->type) ;
				} catch (Exception $e) {
					$this->db->rollbackTran() ;
					Log::write('[Employee]' . $e->getMessage());
					$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating employee detail to the system.","",$this->type) ;
				}
			} else {
				$this->sendJsonResponse(Status::Error,"Duplicate employee code","",$this->type);
			}
			unset($cls) ;
			unset($cls_p);
			unset($ci) ;
		}else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id you wish to update. Please try again.","",$this->type);
		}
	}
	private function deleteRecord($params) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeClass($this->db) ;
			$cls_p = new EmployeePasswordClass($this->db) ;
			$ci = new ContactInfoClass($this->db) ;
			try {
				$this->db->beginTran() ;
				$cls->deleteRecord($id) ; 
				$ci->deleteRecord(EmployeeTable::C_TABLE,$id) ;
				$cls_p->updateTable();
				$this->db->commitTran() ;
				$this->sendJsonResponse(Status::Ok,"Employee successfully deleted from the system.","",$this->type);
			} catch (Exception $e) {
				$this->db->rollbackTran() ;
				Log::write('[Employee]' . $e->getMessage());
				$this->sendJsonResponse(Status::Error,"Sorry, there is a problem in deleting employee record from the system.","",$this->type) ;
			}
			unset($cls) ;
			unset($cls_p) ;
			unset($ci) ;
		} else {
			$this->sendJsonResponse(Status::Error,"You must supply the employee id you wish to delete. Please try again.","",$this->type);
		}
	}
	private function getList($conditions=null) {
		$cls = new EmployeeClass($this->db) ;
		$coy = new CompanyClass($this->db) ;
		$dept = new DepartmentClass($this->db) ;
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID) ;
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;
		$rows = $cls->getTable($filter,EmployeeTable::C_NAME,$params) ;
		$list = "" ;
		foreach ($rows as $row) {
			$id = $row[EmployeeTable::C_ID] ;
			$list .= "<tr>" ;
			$list .= "<td>" . $id . "</td>" ;
			$list .= "<td>" . $row[EmployeeTable::C_CODE] . "</td>" ;
			$list .= "<td>" . $row[EmployeeTable::C_NAME] . "</td>" ;
			$list .= "<td>" . $coy->getDescription($row[EmployeeTable::C_COY_ID]) . "</td>" ;
			$list .= "<td>" . $dept->getDescription($row[EmployeeTable::C_DEPT]) . "</td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='editEmp(" . $id . ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" ;
			$list .= "<td style='text-align:center'><a href='javascript:' onclick='deleteEmp(" . $id . ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" ;
			$list .= "</tr>" ;
		}
		unset($rows) ;
		unset($cls) ;
		unset($dept) ;
		unset($coy) ;
		return $list ;
	}
	private function getRecord($params=null) {
		if (isset($params['id'])) {
			$id = $params['id'] ;
			$cls = new EmployeeClass($this->db) ;
			$ci = new ContactInfoClass($this->db) ;
			$row = $cls->getRecord($id) ;
			
			if (is_null($row)) {
				$this->sendJsonResponse(Status::Error,"Invalid employee id. Please try again.",$id,$this->type);
			} else {
				
				$datas = array() ;
				$datas['id'] = $id ;
				$datas['name'] = $row[EmployeeTable::C_NAME];
				$datas['code'] = $row[EmployeeTable::C_CODE];
				$datas['nric'] = $row[EmployeeTable::C_ID_NO] ;
				$datas['idtype'] = $row[EmployeeTable::C_ID_TYPE];
				$datas['refno'] = $row[EmployeeTable::C_REF] ;
				if ($row[EmployeeTable::C_PERMIT] == 0)
					$datas['permit'] = "" ;
				else 
					$datas['permit'] = $row[EmployeeTable::C_PERMIT] ;

				if ($row[EmployeeTable::C_DEPT] == 0)
					$datas['dept'] = "";
				else
					$datas['dept'] = $row[EmployeeTable::C_DEPT] ;
				$datas['block'] = $row[EmployeeTable::C_BLOCK];
				if (is_null($row[EmployeeTable::C_COMMENTS]))
					$datas['rmks'] = "" ;
				else 
					$datas['rmks'] = $row[EmployeeTable::C_COMMENTS];
				$dte = date_create($row[EmployeeTable::C_JOIN]);
				$datas['join'] = date_format($dte, 'd/m/Y'); 
				$dte = date_create($row[EmployeeTable::C_RESIGN]) ;
				if ($dte == date_create(MAX_DATE))
					$datas['resign'] = "" ;
				else
					$datas['resign'] = date_format($dte,'d/m/Y') ;
				
				$dte = date_create($row[EmployeeTable::C_DOB]) ;
				$datas['dob'] = date_format($dte,'d/m/Y') ;
				if ($row[EmployeeTable::C_RACE] == 0)
					$datas['race'] = "" ;
				else 
					$datas['race'] = $row[EmployeeTable::C_RACE] ;
				
				$datas['gender'] = $row[EmployeeTable::C_GENDER] ;
				$datas['marital'] = $row[EmployeeTable::C_MARITAL] ;
				
				if ($row[EmployeeTable::C_NATIONALITY] == 0)
					$datas['nat'] = "" ;
				else 
					$datas['nat'] = $row[EmployeeTable::C_NATIONALITY] ;
				
				if ($row[EmployeeTable::C_TYPE] == 0)
					$datas['emptype'] = "" ;
				else 
					$datas['emptype'] = $row[EmployeeTable::C_TYPE] ;
				
				if ($row[EmployeeTable::C_JOB] == 0)
					$datas['job'] = "" ;
				else
					$datas['job'] = $row[EmployeeTable::C_JOB] ;
				
				if ($row[EmployeeTable::C_COY_ID] == 0)
					$datas['coy'] = "" ;
				else 
					$datas['coy'] = $row[EmployeeTable::C_COY_ID] ;
					
				$rows = $ci->getRecord(EmployeeTable::C_TABLE,$id) ;
				if (!is_null($rows) && count($rows) > 0) {
					$crow = $rows[0] ;
					$datas['house'] = $crow[ContactInfoTable::C_ADDR1] ;
					$datas['street'] = $crow[ContactInfoTable::C_ADDR2] ;
					$datas['level'] = $crow[ContactInfoTable::C_ADDR4] ;
					$datas['unitno'] = $crow[ContactInfoTable::C_ADDR5] ;
					$datas['postal'] = $crow[ContactInfoTable::C_POSTAL] ;
					$datas['email'] = $crow[ContactInfoTable::C_EMAIL] ;
					$datas['tel'] = $crow[ContactInfoTable::C_TEL] ;
					$datas['mobile'] = $crow[ContactInfoTable::C_MOBILE] ;
				} else {
					$datas['house'] = "" ;
					$datas['street'] = "" ;
					$datas['level'] = "" ;
					$datas['unitno'] = "" ;
					$datas['postal'] = "" ;
					$datas['email'] = "" ;
					$datas['tel'] = "" ;
					$datas['mobile'] = "" ;
				}
				$this->sendJsonResponse(Status::Ok,"",$datas,$this->type) ;
			}
			unset($cls) ;
			unset($ci) ;
		} else {
			$this->sendJsonResponse(Status::Error,"Missing employee id. Please try again.","",$this->type);
		}
	}
	private function getView() {
		ob_start() ;
		include (PATH_VIEWS . "hr/EmployeeView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getDepartment() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(DepartmentTable::C_TABLE, DepartmentTable::C_ID, DepartmentTable::C_DESC,array('code'=>'','desc'=>'--- Select a Department ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getRace() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(RaceTable::C_TABLE, RaceTable::C_ID, RaceTable::C_DESC,array('code'=>'','desc'=>'--- Select a Race ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getNationality() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(NationalityTable::C_TABLE, NationalityTable::C_ID, NationalityTable::C_DESC,array('code'=>'','desc'=>'--- Select a Nationality ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getCompany() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(CompanyTable::C_TABLE, CompanyTable::C_COY_ID, CompanyTable::C_DESC,array('code'=>'','desc'=>'--- Select a Company ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getJobTitle() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(JobTitleTable::C_TABLE, JobTitleTable::C_ID, JobTitleTable::C_DESC,array('code'=>'','desc'=>'--- Select a Job Title ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getEmployeeType() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(EmployeeTypeTable::C_TABLE, EmployeeTypeTable::C_ID, EmployeeTypeTable::C_DESC,array('code'=>'','desc'=>'--- Select a Employee Type ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getWorkPermit() {
		$filter = array();
		$filter[] = array('field'=>$this->fldorg,'value'=>$this->orgid) ;
		$vls = $this->getValueList(WorkPermitTable::C_TABLE, WorkPermitTable::C_ID, WorkPermitTable::C_DESC,array('code'=>'','desc'=>'--- Select a Work Permit ---'),$filter) ;
		return Util::createOptionValue($vls) ;
	}
	private function getMarital() {
		$arr = array() ;
		$arr[] = array ('code'=>'0','desc'=>'Single' );
		$arr[] = array ('code'=>'1','desc'=>'Married' ) ;
		$arr[] = array ('code'=>'2','desc'=>'Widowed' ) ;
		$arr[] = array ('code'=>'3','desc'=>'Divorced' ) ;
		$arr[] = array ('code'=>'4','desc'=>'Separated') ;
		return Util::createOptionValue($arr) ;
	}
	private function getGender() {
		$arr = array() ;
		$arr[] = array ('code'=>'M','desc'=>'Male' );
		$arr[] = array ('code'=>'F','desc'=>'Female' ) ;
		return Util::createOptionValue($arr) ;
	}
	private function getIdType() {
		$arr = array() ;
		$arr[] = array ('code'=>'1','desc'=>'NRIC No' );
		$arr[] = array ('code'=>'2','desc'=>'FIN No' ) ;
		$arr[] = array ('code'=>'3','desc'=>'Immigration File Ref No.' );
		$arr[] = array ('code'=>'4','desc'=>'Work Permit No' ) ;
		$arr[] = array ('code'=>'5','desc'=>'Malaysian I/C' );
		$arr[] = array ('code'=>'6','desc'=>'Passport No' ) ;
		return Util::createOptionValue($arr) ;
	}
	private function getReport($params=null) {
		require_once(PATH_LIB . 'ListPdf.php');
		
		$cls = new EmployeeClass($this->db) ;
		$filter = $this->db->fieldParam(EmployeeTable::C_ORG_ID,"=","e.") ;
		$params = array() ;
		$params[] = $this->db->valueParam(EmployeeTable::C_ORG_ID,$_SESSION[SE_ORGID]) ;

		$sql = " select e.*,d." . DepartmentTable::C_DESC . ",c." . CompanyTable::C_DESC . ",j." . JobTitleTable::C_DESC
			. " from " . EmployeeTable::C_TABLE . "  as e "
			. " left join " . DepartmentTable::C_TABLE . " as d "
			. " on e." . EmployeeTable::C_DEPT . " = d." . DepartmentTable::C_ID
			. " left join " . CompanyTable::C_TABLE . "  as c "
			. " on e." . EmployeeTable::C_COY_ID . " = c." . CompanyTable::C_COY_ID
			. " left join " . JobTitleTable::C_TABLE . " as j "
			. " on e." . EmployeeTable::C_JOB . " = j." . JobTitleTable::C_ID
			. " where " . $filter 
			. " order by c." . CompanyTable::C_DESC . ",d." . DepartmentTable::C_DESC . ",e." . EmployeeTable::C_NAME ;

		$rows = $this->db->getTable($sql,$params) ;

		$deptdesc = "" ;
		$coydesc = "" ;
		$jobdesc = "" ;
		$w = 'width' ;
		$h = 'height' ;
		$a = 'align' ;
		$t = 'text' ;
		$i = 'items';
		$nr = 'newrow';
		$datas = array() ;
		foreach ($rows as $row) {
			if (is_null($row[DepartmentTable::C_DESC]))
				$deptdesc = "" ;
			else 
				$deptdesc = $row[DepartmentTable::C_DESC] ;
			if (is_null($row[CompanyTable::C_DESC]))
				$coydesc = "" ;
			else
				$coydesc = $row[CompanyTable::C_DESC] ;
			if (is_null($row[JobTitleTable::C_DESC]))
				$jobdesc = "" ;
			else 
				$jobdesc = $row[JobTitleTable::C_DESC] ;
			$items = array() ;
			$items[$i][] = $this->createPdfItem($row[EmployeeTable::C_ID],30) ;
			$items[$i][] = $this->createPdfItem($row[EmployeeTable::C_NAME],120) ;
			$items[$i][] = $this->createPdfItem($row[EmployeeTable::C_ID_NO],100) ;
			$items[$i][] = $this->createPdfItem($coydesc,200) ;
			$items[$i][] = $this->createPdfItem($deptdesc,150) ;
			$items[$i][] = $this->createPdfItem($jobdesc,100) ;
			$items[$nr] = "1" ;
			$datas[] = $items ;
		}
		$cols = array() ;
		$cols[] = $this->createPdfItem("ID",30,0,"C","B");
		$cols[] = $this->createPdfItem("Name",120,0,"C","B") ;
		$cols[] = $this->createPdfItem("NRIC/ID No",100,0,"C","B") ;
		$cols[] = $this->createPdfItem("Company",200,0,"C","B") ;
		$cols[] = $this->createPdfItem("Department",150,0,"C","B") ;
		$cols[] = $this->createPdfItem("Job Title",100,0,"C","B") ;
		$pdf = new ListPdf('L');
		$pdf->setCompanyName($_SESSION[SE_ORGNAME]) ;
		$pdf->setReportTitle("Employee Listing") ;
		$pdf->setColumnsHeader($cols) ;
		$pdf->render($datas) ;
		$pdf->Output('employee.pdf', 'I');
		unset($rows) ;
		unset($cls) ;
		unset($datas) ;
		unset($params) ;
		unset($items) ;
		unset($cols) ;
	}
	private function addContact($id,$coyid,$params) {
		$ci = new ContactInfoClass($this->db) ;
		$datas = array() ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_ADDR1,$this->getParam($params,'house',"")) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_ADDR2,$this->getParam($params,'street',"")) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_ADDR3,"") ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_ADDR4,$this->getParam($params,'level',"")) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_ADDR5,$this->getParam($params,'unitno',"")) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_TEL,$this->getParam($params,'tel',"")) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_FAX,"") ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_WEB,"") ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_EMAIL,$this->getParam($params,'email',"")) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_TYPE,AddressType::Local) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_POSTAL,$this->getParam($params,'postal',"")) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_MOBILE,$this->getParam($params,'mobile',"")) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_COUNTRY,0) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_COY_ID,$coyid) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_ORG_ID,$this->orgid) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_CODE,EmployeeTable::C_TABLE) ;
		$datas[] = $this->db->fieldValue(ContactInfoTable::C_ID,$id);
		$ci->addRecord($datas) ;
		unset($ci) ;
	}
}
?>