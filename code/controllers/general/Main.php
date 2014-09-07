<?php
require_once (PATH_CONTROLLERS . "Common.php") ;
require_once (PATH_MODELS . "admin/UserGroupClass.php") ;
require_once (PATH_MODELS . "admin/UserRight.php") ;
require_once (PATH_MODELS . "admin/OrganizationOptions.php") ;
require_once (PATH_MODELS . "admin/OrganizationClass.php") ;

class Main extends ControllerBase {
	private $clsgroup ;
	private $clsright ;
	private $clsorg ;

	function __construct() {
		$this->db = $_SESSION[SE_DB] ;
		$this->clsgroup = new UserGroupClass($this->db) ;
		$this->clsright = new UserRight() ;
		$this->clsorg = new OrganizationClass($this->db) ;
	}
	function __destruct() {
		unset($this->db) ;
		unset($this->clsgroup) ;
		unset($this->clsright) ;
		unset($this->clsorg) ;
	}
	public function processRequest($params) {
		try {
			$this->db->open() ;
			$this->loadSetting() ;
			$this->processView() ;
			$this->db->close() ;
			return true ;
		} catch (Exception $e) {
			$this->db->close() ;
			die ($e->getMessage()) ;
		}
	}
	private function processView() {
		ob_start() ;
		$this->clsright->initRights() ;
		if ($_SESSION[SE_USERGROUP] != "")
		{
			$row = $this->clsgroup->getRecord($_SESSION[SE_USERGROUP]) ;
			if (!is_null($row)) {
				if (!is_null($row[UserGroupTable::C_RIGHTS]))
					$this->clsright->toRights($row[UserGroupTable::C_RIGHTS]); ;
			}
		}
		include (PATH_VIEWS . "general/MainView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getTheme() {
		if (isset($_SESSION[SE_THEME]) && $_SESSION[SE_THEME] != "")
			return $_SESSION[SE_THEME] ;
		else
			return "redmond" ;
	}
	private function getOrganizationName() {
		return $_SESSION[SE_ORGNAME] ;
	}
	private function getFullName() {
		return $_SESSION[SE_FULLNAME] ;
	}
	private function loadSetting() {
		$row = $this->clsorg->getRecord($_SESSION[SE_ORGID]) ;
		
		if (!is_null($row)) {
			$opt = new OrganizationOptions() ;
			$opt->loadXml($row[OrganizationTable::C_OPTIONS]) ;
			$op = $opt->getOption() ;
			$_SESSION[SE_ORGNAME] = $op[OrganizationOptions::C_CONTACT][OrganizationOptions::C_CONT_NAME1] ;
			$_SESSION[SE_ORGCODE] = $row[OrganizationTable::C_CODE] ;
			unset($opt) ;
		}
		else {
			$_SESSION[SE_ORGNAME] = "default company" ;
			$_SESSION[SE_ORGCODE] = "" ;
		}
		unset($row) ;
	}
	private function loadCompany_unuse() {
		$arr = array() ;
		$arr[] = array ('code'=>'','desc'=>'--- Select a Company ---' ) ;
		$arr[] = array ('code'=>'sbg','desc'=>'Strategic Business Group' );
		$arr[] = array ('code'=>'pzs','desc'=>'PlanetZ System' ) ;
		return Util::createOptionValue($arr) ;
	}
	private function loadNews() {
		$c = "<div>1 company news to be place at the right of the home page.</div>" ;
		$c .= "<div>2 company news to be place at the right of the home page.</div>" ;
		$c .= "<div>3 company news to be place at the right of the home page.</div>" ;
		
		return "" ;
	}
	private function loadProfile() {
		$c = "<div>Administrator</div>" ;
		$c .= "<div>Last Login</div>" ;
		$c .= "<div><a href=\"javascript:changePwd();\">Change Password</a></div>" ;
		$c .= "<div><a href=\"javascript:changeProfile();\">Update Profile</a></div>" ;
		return $c ;
	}
	private function loadMessage() {
		$c = "<div>No Message</div>" ;
		$c .= "<div>2 message</div>" ;
		return "" ;
	}
	private function loadFooter() {
		$c = "<div>copyright info</div>";
		return $c ;
	}
	private function getTopMenu() {
		$c = "" ;
		$c .= "<div>" . $this->createTopItem("employee_32.png", "Employee Profile",$this->createMenuFunc("Employee","Employee Profile")) . "</div>" ;
		$c .= "<div>" . $this->createTopItem("emppay_32.png", "Employee Pay Setup", $this->createMenuFunc("EmployeePay","Employee Pay Setup")) . "</div>" ;
		$c .= "<div>" . $this->createTopItem("paycreate_32.png","Create Pay Slip",$this->createMenuFunc("PayCreate","Create Pay Slip")) . "</div>";
		$c .= "<div>" . $this->createTopItem("payentry_32.png","Pay Entry",$this->createMenuFunc("PayEntry","Pay Entry")) . "</div>";
		$c .= "<div>" . $this->createTopItem("paylist_32.png","Pay Listing",$this->createMenuFunc("PayList","Pay Listing")) . "</div>" ;
		$c .= "<div>" . $this->createTopItem("payslip_32.png", "Print Pay Slip", $this->createMenuFunc("PaySlip","Print Pay Slip")) . "</div>" ;
		$c .= "<div>" . $this->createTopItem("cpfentry_32.png","CPF Entry",$this->createMenuFunc("CpfEntry","CPF Entry")) . "</div>" ;
		$c .= "<div>" . $this->createTopItem("cpflist_32.png","CPF Listing",$this->createMenuFunc("CpfList","CPF Listing")) . "</div>" ;
		$c .= "<div>" . $this->createTopItem("incomeyear_32.png","Yearly Income Report",$this->createMenuFunc("IncomeYear","Yearly Income Report")) . "</div>" ;
		return $c ;
	}
	private function getSideMenu() {
		$c = "" ;
		//$c .= "<div>" . $this->createTopItem("help_32.png","Help","javascript:showReport('help.html')") . "</div>" ;
		$c .= "<div>" . $this->createTopItem("signout_32.png","Sign Out",$this->createJsFunc("onLogout()")) . "</div>" ;
		$c .= "<div>" . $this->createTopItem("changepwd_32.png","Change Password",$this->createMenuFunc("ChangePwd","Change Password")) . "</div>" ;
		return $c ;
	}
	private function getMenuItem() {
		$c = "" ;
		$c .= $this->createMenuAdmin() ;
		$c .= $this->createMenuHr() ;
		$c .= $this->createMenuPayroll() ;
		$c .= $this->createMenuLeave() ;
		$c .= $this->createMenuAttendance() ;
		$c .= $this->createMenuClaim() ;
		return $c ;
	}
	private function createMenuAdmin() {
		$c = "" ;
		$c .= "<div><a href=\"#\">Admin</a></div>" ;
		$c .= "<div class=\"submenu\">" ;
		$right = $this->clsright->getAdminRight() ;
		if ($right[UserRight::C_ADMIN_COY][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("company_20.png", "Company Profile",$this->createMenuFunc("Company","Company Profile"),"Company Profile") . "</div>" ;
		if ($right[UserRight::C_ADMIN_SETTING][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("setting_20.png", "System Setting",$this->createMenuFunc("Setting","System Setting"),"System Setting") . "</div>" ;
		if ($right[UserRight::C_ADMIN_USER][UserRight::C_ENABLE]) 
			$c .= "<div>" . $this->createItem("user_20.png", "User Management",$this->createMenuFunc("Users","User Profile"),"User Management") . "</div>" ;
		if ($right[UserRight::C_ADMIN_GROUP][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("usergroup_20.png", "User Group",$this->createMenuFunc("UserGroup","User Group"),"User Group") . "</div>" ;
		if ($right[UserRight::C_ADMIN_RESET][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("resetpwd_20.png", "Reset Password",$this->createMenuFunc("ResetPwd","Reset Password"),"Reset Pssword") . "</div>" ;
		$c .= "</div>" ;
		return $c ;
	}
	private function createMenuHr() {
		$c = "" ;
		$c .= "<div><a href=\"#\">Human Resource</a></div>" ;
		$c .= "<div class=\"submenu\">" ;
		$right = $this->clsright->getHrRight() ;
		if ($right[UserRight::C_HR_EMP][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("employee_20.png","Employee Profile", $this->createMenuFunc("Employee","Employee Profile"),"Employee Profile") . "</div>" ;
		if ($right[UserRight::C_HR_DEPT][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("dept_20.png","Department", $this->createMenuFunc("Department","Department Master"),"Department") . "</div>" ;
		//$c .= "<div>" . $this->createItem("leavetype_20.png","Leave Type", $this->createMenuFunc("LeaveType","Leave Type"),"LeaveType") . "</div>" ;
		if ($right[UserRight::C_HR_JOB][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("position_20.png","Job Title",$this->createMenuFunc("JobTitle","Job Title Master"),"Job Title") . "</div>" ;
		if ($right[UserRight::C_HR_TYPE][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("emptype_20.png","Employee Type",$this->createMenuFunc("EmployeeType","Employee Type"),"Employee Type") . "</div>" ;
		if ($right[UserRight::C_HR_NAT][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("nationality_20.png","Nationality",$this->createMenuFunc("Nationality","Nationality Master"),"Nationality") . "</div>" ;
		//$c .= "<div>" . $this->createItem("education_20.png","Education Group",$this->createMenuFunc("Education","Education Master"), "Education Group") . "</div>" ;
		if ($right[UserRight::C_HR_RACE][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("race_20.png","Race",$this->createMenuFunc("Race","Race Master"),"Race") . "</div>" ;
		if ($right[UserRight::C_HR_PERMIT][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("workpermit_20.png","Work Permit",$this->createMenuFunc("WorkPermit","Work Permit"),"Work Permit") . "</div>" ;
		$c .= "</div>" ;
		return $c ;
	}
	private function createMenuPayroll() {
		$c = "" ;
		$c .= "<div><a href=\"#\">Payroll</a></div>" ;
		$c .= "<div class=\"submenu\">" ;
		$right = $this->clsright->getPayrollRight() ;
		if ($right[UserRight::C_PAYROLL_BANK][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("bank_20.png","Bank",$this->createMenuFunc("Bank","Bank"),"Bank"). "</div>" ;
		if ($right[UserRight::C_PAYROLL_CPF][UserRight::C_ENABLE])	
			$c .= "<div>" . $this->createItem("cpfgroup_20.png","CPF Type",$this->createMenuFunc("CpfType","CPF Type Master"),"CPF Type") . "</div>" ;
		if ($right[UserRight::C_PAYROLL_TYPE][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("salarytype_20.png","Pay Type",$this->createMenuFunc("PayType","Pay Type Master"), "Pay Type") . "</div>" ;
		if ($right[UserRight::C_PAYROLL_EMP][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("emppay_20.png","Employee Pay Setup",$this->createMenuFunc("EmployeePay","Employee Pay"),"Employee Pay Setup") . "</div>" ;
		if ($right[UserRight::C_PAYROLL_CREATE][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("paycreate_20.png","Create Pay Slip",$this->createMenuFunc("PayCreate","Create Pay Slip"),"Create Pay Slip") . "</div>" ;
		if ($right[UserRight::C_PAYROLL_ENTRY][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("payentry_20.png","Pay Entry",$this->createMenuFunc("PayEntry","Pay Entry"),"Pay Entry") . "</div>" ;
		if ($right[UserRight::C_PAYROLL_PAYLIST][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("paylist_20.png","Pay Listing",$this->createMenuFunc("PayList","Pay Listting"),"Pay Listing") . "</div>" ;
		if ($right[UserRight::C_PAYROLL_PAYSLIP][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("payslip_20.png","Print Pay Slip",$this->createMenuFunc("PaySlip","Print Pay Slip"),"Print Pay Slip"). "</div>" ;
		if ($right[UserRight::C_PAYROLL_CPFENTRY][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("cpfentry_20.png","CPF Entry",$this->createMenuFunc("CpfEntry","CPF Entry"),"CPF Entry"). "</div>" ;	
		if ($right[UserRight::C_PAYROLL_CPFLIST][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("cpflist_20.png","CPF Listing",$this->createMenuFunc("CpfList","CPF Listing"),"CPF Listing"). "</div>" ;
		if ($right[UserRight::C_PAYROLL_INCOMEYEAR][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("incomeyear_20.png","Yearly Income Report",$this->createMenuFunc("IncomeYear","Yearly Income Report"),"Yearly Income Report"). "</div>" ;
		$c .= "</div>" ;
		return $c ;
	}
	private function createMenuLeave() {
		$c = "" ;
		$c .= "<div><a href=\"#\">Leave</a></div>" ;
		$c .= "<div class=\"submenu\">" ;
		$right = $this->clsright->getPayrollRight() ;
		if ($right[UserRight::C_LEAVE_TYPE][UserRight::C_ENABLE])
			$c .= "<div>" . $this->createItem("leavetype_20.png","Leave Type",$this->createMenuFunc("LeaveType","Leave Type Master"),"Leave Type"). "</div>" ;
		if ($right[UserRight::C_LEAVE_EMP][UserRight::C_ENABLE])	
			$c .= "<div>" . $this->createItem("leaveentry_20.png","Employee Leave",$this->createMenuFunc("EmployeeLeave","Employee Leave Setup"),"Employee Leave") . "</div>" ;
		if ($right[UserRight::C_LEAVE_GROUP][UserRight::C_ENABLE])	
			$c .= "<div>" . $this->createItem("leaveentry_20.png","Leave Group",$this->createMenuFunc("LeaveGroup","Leave Group Master"),"Leave Group") . "</div>" ;

		$c .= "</div>" ;
		return $c ;
	}
	private function createMenuClaim() {
		$c = "" ;
		$c .= "<div><a href=\"#\">Claim</a></div>" ;
		$c .= "<div class=\"submenu\">" ;
		$c .= "<div>" . $this->createItem("nationality_20.png","Country",$this->createMenuFunc("Country","Country Master"),"Country Master"). "</div>" ;
		$c .= "<div>" . $this->createItem("currency_20.png","Currency",$this->createMenuFunc("Currency","Currency Master"),"Currency Master") . "</div>" ;
		$c .= "<div>" . $this->createItem("payslip_20.png","Travel Plan",$this->createMenuFunc("TravelPlan","Travel Plans"),"Travel Plans") . "</div>" ;
		$c .= "<div>" . $this->createItem("leaveentry_20.png","Claim Group",$this->createMenuFunc("ClaimGroup","Claim Group"),"Claim Group") . "</div>" ;
		$c .= "<div>" . $this->createItem("leaveentry_20.png","Expense Group",$this->createMenuFunc("ExpenseGroup","Expense Group"),"Expense Group") . "</div>" ;
		$c .= "<div>" . $this->createItem("leavetype_20.png","Expense Item",$this->createMenuFunc("ExpenseItem","Expense Item Master"),"Expense Item") . "</div>" ;
		$c .= "<div>" . $this->createItem("emppay_20.png","Claim",$this->createMenuFunc("Claim","Records of Claim"),"Claim"). "</div>" ;
		$c .= "<div>" . $this->createItem("leavetype_20.png","Claim Approval",$this->createMenuFunc("ClaimApproval","Claim Approval"),"Claim Approval") . "</div>" ;
		$c .= "<div>" . $this->createItem("leavetype_20.png","Claim Item Approval",$this->createMenuFunc("ClaimItemApproval","Claim Item Approval"),"Claim Items Approval") . "</div>" ;
		$c .= "<div>" . $this->createItem("leavetype_20.png","Claim Document Approval",$this->createMenuFunc("ClaimDocumentApproval","Claim ClaimDocumentApproval"),"Claim Document Verification") . "</div>" ;
		$c .= "<div>" . $this->createItem("report_user_20.png","Employee Claim",$this->createMenuFunc("EmployeeClaim","Employee Claim"),"Employee Claim"). "</div>" ;
		$c .= "</div>" ;
		return $c ;
	}
	private function createMenuAttendance() {
		$c = "" ;
		$c .= "<div><a href=\"#\">Attendance</a></div>" ;
		$c .= "<div class=\"submenu\">" ;
		$right = $this->clsright->getHrRight();
		
		//if ($right[UserRight::C_HR_DEPT][UserRight::C_ENABLE])
		$c .= "<div>" . $this->createItem("project_20.png","Project",$this->createMenuFunc("Project","Project Master"),"Project"). "</div>" ;
		$c .= "<div>" . $this->createItem("project_20.png","Activity",$this->createMenuFunc("Activity","Activity Master"),"Activity"). "</div>" ;
		$c .= "<div>" . $this->createItem("project_20.png","Timesheet",$this->createMenuFunc("Timesheet","Timesheet Master"),"Timesheet"). "</div>" ;
		$c .= "<div>" . $this->createItem("rate_group_20.png","Rate Group",$this->createMenuFunc("RateGroup","Rate Group"),"Rate Group"). "</div>" ;
		$c .= "<div>" . $this->createItem("employee_shift_20.png","Employee Shift",$this->createMenuFunc("EmployeeShift","Employee Shift Master"),"Employee Shift"). "</div>" ;
		$c .= "<div>" . $this->createItem("shift_group_20.png","Shift Group",$this->createMenuFunc("ShiftGroup","Shift Group Master"),"Shift Group"). "</div>" ;
		$c .= "<div>" . $this->createItem("shift_detail_20.png","Shift Detail",$this->createMenuFunc("ShiftDetail","Shift Detail Master"),"Shift Detail"). "</div>" ;
		$c .= "<div>" . $this->createItem("shift_group_20.png","Shift Update",$this->createMenuFunc("ShiftUpdate","Shift Update Report"),"Shift Update"). "</div>" ;
		$c .= "<div>" . $this->createItem("time_card_limit_20.png","Time Card",$this->createMenuFunc("TimeCard","Time Card Master"),"Time Card"). "</div>" ;
		$c .= "<div>" . $this->createItem("time_delete_20.png","Time Card Limit",$this->createMenuFunc("TimeCardLimit","Time Card Master"),"Time Card Limit"). "</div>" ;
		$c .= "<div>" . $this->createItem("holiday_20.png","Holiday",$this->createMenuFunc("Holiday","Holiday Master"),"Holiday"). "</div>" ;
		$c .= "<div>" . $this->createItem("attendance_20.png","Attendance",$this->createMenuFunc("Attendance","Attendance Entry"),"Attendance"). "</div>" ;
		$c .= "<div>" . $this->createItem("overtime_20.png","Over Time",$this->createMenuFunc("OverTime","Over Time Entry"),"Over Time"). "</div>" ;
		$c .= "<div>" . $this->createItem("timeoff_20.png","Time Off",$this->createMenuFunc("TimeOff","Time Off Entry"),"Time Off"). "</div>" ;
		$c .= "<div>" . $this->createItem("report_attendance_20.png","Daily Attendance",$this->createMenuFunc("DailyAttendance","Daily Attendance Report"),"Daily Attendance"). "</div>" ;
		$c .= "<div>" . $this->createItem("daily_absentee_20.png","Daily Absentee",$this->createMenuFunc("DailyAbsentee","Daily Absentee Report"),"Daily Absentee"). "</div>" ;
		$c .= "<div>" . $this->createItem("report_discipline_20.png","Disciplinary",$this->createMenuFunc("Disciplinary","Disciplinary Report"),"Disciplinary"). "</div>" ;
		$c .= "<div>" . $this->createItem("report_user_20.png","Staff Periodic Attendance",$this->createMenuFunc("StaffPeriodicAttendance","Staff Periodic Attendance Report"),"Staff Periodic Attendance"). "</div>" ;
		$c .= "<div>" . $this->createItem("report_user_20.png","Employee Project Report",$this->createMenuFunc("EmployeeProject","Employee Project Report"),"Employee Project Report"). "</div>" ;
		$c .= "</div>" ;
		return $c ;
	}
	private function createItem($imgfile,$imgtitle,$link="",$caption="") {
		$c = "" ;
		if ($imgfile != "") {
			$c .= "<img src=\"image/" . $imgfile . "\" alt=\"" . $imgtitle . "\"></img>" ;
		}
		if ($link != "") {
			$c .= "<a href=\"" . $link . "\">" . $caption . "</a>" ;
		}
		return $c ;
	}
	private function createTopItem($imgfile,$title,$link="",$caption="") {
		$c = "" ;
		$c .= "<a title=\"" . $title . " \" href=\"" . $link . "\">";
		if ($imgfile != "") {
			$c .= "<img src=\"image/" . $imgfile . "\"></img>" ;
		}
		$c .= "</a>" ;
		return $c ;
	}
	private function createMenuFunc($href,$desc) {
		return "javascript:showPage('" . Util::convertLink($href) . "','". $desc . "')" ;
	}
	private function createJsFunc($func) {
		return "javascript:" . $func ;
	}
}
?>