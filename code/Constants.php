<?php

define("SE_MENU", "menu");
define("SE_USERID","userid") ;
define("SE_USERNAME","username") ;
define("SE_USERGROUP","usergroup") ;
define("SE_FULLNAME","fullname") ;
define("SE_USERKEY","userkey") ;
define("SE_THEME","uitheme") ;
define("SE_PAGE","page") ;
define("SE_DB","db") ;
define("SE_ORGID","orgid") ;
define("SE_ID","sessionid") ;
define("SE_INIT","initiated") ;
define("SE_REMOTE_IP","remoteip") ;
define("SE_ORGNAME", "orgname") ;
define("SE_ORGCODE", "orgcode") ;


define("NUM_CLAIM_SHOW",5) ;

$path = $_SERVER['DOCUMENT_ROOT'] ;
if ($path != "/")
	$path = $path . "/" ;
define("PATH_ROOT",$path) ;
//define("PATH_PICTURE",$path . "picture/") ;

$path = __DIR__ ;
if ($path != "/") 
	$path = $path . "/" ;
define("PATH_CODE",$path) ;
define("PATH_TEMPLATES",PATH_CODE . "templates/");
define("PATH_VIEWS",PATH_CODE . "views/") ;
define("PATH_LIB",PATH_CODE . "lib/") ;
define("PATH_CONTROLLERS",PATH_CODE . "controllers/") ;
define("PATH_MODELS", PATH_CODE . "models/") ;
define("PATH_EXCEL", PATH_CODE . "excel/") ;
define("PATH_TABLES", PATH_CODE . "tables/") ;
define("PATH_MESSAGE", PATH_CODE . "message/") ;
define("PATH_HOST", $_SERVER['HTTP_HOST']) ;
define("PATH_SCRIPT", PATH_CODE . "js/") ;
define("PATH_LOG",PATH_CODE . "../log/") ;
define("PATH_PICTURE", PATH_CODE . "../web/picture/") ;
define("PATH_CLAIMS", PATH_CODE . "../web/claims/") ;

define("TAG_TITLE","{%=Title=%}");
define("TAG_CONTENT","{%=Content=%}") ;
define("TAG_HEADER","{%=Header=%}") ;
define("TAG_FOOTER","{%=Footer=%}") ;
define("TAG_BOTTOM","{%=Bottom=%}") ;
define("TAG_MENU_BAR","{%=MenuBar=%}") ;
define("TAG_THEME","{%=Theme=%}") ;
	
define("APP_NAME","SBG Human Resource Management") ;
define("APP_VER_MAJOR","3") ;
define("APP_VER_MINOR","0") ;
define("APP_VER_REV","0") ;
define("PRODUCT_CODE","11") ;	//
define("PRODUCT_TYPE","02") ;	//01-desktop 02-web 03-mobile

define("FIELD_STATUS","status") ;
define("FIELD_MESG","mesg") ;
define("FIELD_DATA","data") ;
define("FIELD_TYPE","type") ;

define("REQ_ADD", "a") ;
define("REQ_CHANGE","c") ;
define("REQ_DELETE","d") ;
define("REQ_EXPORT","e") ;
define("REQ_GET","g") ;
define("REQ_SIGNIN","i")  ;
define("REQ_LIST","l") ;
define("REQ_NEW","n") ;
define("REQ_SIGNOUT","o") ;
define("REQ_QUERY","q") ;
define("REQ_REPORT","r") ;
define("REQ_UPDATE","u") ;
define("REQ_VIEW","v") ;
define("REQ_CLAIM_FILTER","c_f") ;


define("PORTAL_HOME","p_h") ;
define("PORTAL_CLAIM","p_e") ;
define("PORTAL_LEAVES","p_l") ;
define("PORTAL_CALENDAR","p_c") ;
define("PORTAL_CLAIM_ADD_VIEW","p_c_a_v") ;
define("PORTAL_CLAIM_UPDATE_VIEW","p_c_u_v") ;
define("PORTAL_CLAIM_UPLOAD_VIEW","p_c_up") ;
define("PORTAL_CLAIM_ADD_ITEM_VIEW","p_c_a_i") ;


define("ATTENDANCE_STATUS_O", "Off Duty");
define("ATTENDANCE_STATUS_T", "Time Off");
define("ATTENDANCE_STATUS_N", "No Excuse");
define("ATTENDANCE_STATUS_P", "Present");

define("MAX_DATE","2999-12-31 23:59:59") ;
define("NULL_DATE","1969-12-31 23:59:59") ;

class ShiftType {
	const Daily = 0;
	const Weekly = 1;
}

class RateType {
	const Hourly = 0;
	const Daily = 1;
}

class Status {
	const Ok = "0" ;
	const Info = "6" ;
	const Invalid = "7";
	const Confirm = "8" ;
	const Error = "9" ;
}
class DbType {
	const MySql = "mysql" ;
	const MsSql = "mssql" ;
	const PostgreSql = "postgre";
	const MySqli = "mysqli" ;
}
class AddressType {
	const Local = "0" ;
	const Oversea = "1";
}
class TaxType {
	const NoTax = "0" ;
	const Salary = "1" ;
	const Bonus = "2" ;
	const Director = "3";
	const Commission = "4" ;
	const TpAllowance = "5";
	const EntAllowance = "6" ;
	const OtherAllowance = "7" ;
	const Retirement = "8" ;
	const Compensation = "9" ;
}
class ClaimStatus {
	const Open = 0 ;
	const Pending = 1 ;
	const Checking = 2 ;
	const Verification = 3 ;
	const Approved = 4 ;
	const Rejected = 5 ;
	const Cancelled = 6 ;
	const Closed = 7 ;
	const Unknow = 9 ;
}
class ClaimLimitType {
	const Claim = 0;
	const Daily = 1;
	const Weekly = 2 ;
	const Monthly = 3 ;
	const Yearly = 4 ;
	const Project = 5;
}
class ExpenseItemType {
	const Personal = 0 ;
	const Business = 1 ;
	const Project = 2 ;
	const Other = 3 ;
}

class MenuName {
	const HomeMenu = "Home";
	const ClaimMenu= "Claim";
	const LeaveMenu= "Leave";
	const CalendarMenu = "Calendar";
}


?>