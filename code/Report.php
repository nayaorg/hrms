<?php
date_default_timezone_set('Asia/Singapore');

require_once('Constants.php') ;
require_once(PATH_LIB . 'AutoLoader.php') ;
require_once(PATH_LIB . 'Util.php') ;
require_once(PATH_LIB . 'Log.php') ;
require_once(PATH_EXCEL . 'PHPExcel.php') ;
require_once(PATH_MESSAGE . 'Message.php') ;
require_once(PATH_CODE . "database/MsSqlDb.php") ;
require_once(PATH_CODE . "database/MySqlDb.php") ;
require_once(PATH_CODE . "database/MySqliDb.php") ;
require_once('ConfigDb.php') ;

function myException($e)
{
	die("Sorry, there is an error and the application can not continue and will be terminated.") ;
	Log::write('[Report]' . $e->getMessage(),"") ;
}
set_exception_handler('myException');
register_shutdown_function('fatalErrorShutdownHandler');

function fatalErrorShutdownHandler()
{
	$last_error = error_get_last();
	if ($last_error['type'] > 0)
		Log::write('[Report]' . $last_error['message'] . " - " . $last_error['file'] . " on line " . $last_error['line']);
}
if (session_id() == "") 
	session_start(); 

if (!isset($_SESSION[SE_INIT])){
    session_regenerate_id();
    $_SESSION[SE_INIT] = true;
	$_SESSION[SE_USERID] = "" ;
	$_SESSION[SE_USERGROUP] = "" ;
	$_SESSION[SE_MENU] = "" ;
	$_SESSION[SE_ORGID] = -1 ;
	$_SESSION[SE_ORGNAME] = "" ;
	$_SESSION[SE_USERKEY] = "";
}
if (isset($_SESSION[SE_ID]))
	$oldseid = $_SESSION[SE_ID] ;
else
	$oldseid = "" ;
	
$_SESSION[SE_ID] = session_id() ;

if (!isset($_SESSION[SE_THEME])) {
	$_SESSION[SE_THEME] = "redmond" ;
}
//if (!isset($_SESSION[SE_DB])) {
	//$config = new ConfigDb(PATH_CODE . 'db.xml') ;
	//$config->loadConfig() ;
	//if ($config->getDbType() == DbType::MySql)
		//$_SESSION[SE_DB] = new MySqlDb($config->getHost(),$config->getDbName(),$config->getUserName(),$config->getPassword()) ;
	//else
		//$_SESSION[SE_DB] = new MsSqlDb($config->getHost(),$config->getDbName(),$config->getUserName(),$config->getPassword()) ;
	//unset($config) ;
//}

if (isset($_GET['c']))
	$class = $_GET['c'] ;
else
	$class = "" ;
$page = "" ;	
if ($class !=""){
	if (isset($_SESSION[SE_USERID]) && $_SESSION[SE_USERID] != "" ){
		$page = Util::revertLink($class,$_SESSION[SE_ID]) ;
	}
	if (class_exists($page)) {
		$params = array() ;
		$params['type'] = 'r' ;
		if (isset($_GET['m']))
			$params['month'] = $_GET['m'] ;
		if (isset($_GET['y']))
			$params['year'] = $_GET['y'] ;
		if (isset($_GET['co']))
			$params['coy'] = $_GET['co'] ;
		if (isset($_GET['dp']))
			$params['dept'] = $_GET['dp'] ;
		if (isset($_GET['dt']))
			$params['date'] = $_GET['dt'] ;
		if (isset($_GET['dtend']))
			$params['dateend'] = $_GET['dtend'] ;
		if (isset($_GET['t']))
			$params['type'] = $_GET['t'] ;
		if (isset($_GET['empIdBegin']))
			$params['empIdBegin'] = $_GET['empIdBegin'] ;
		if (isset($_GET['empIdEnd']))
			$params['empIdEnd'] = $_GET['empIdEnd'] ;
		if (isset($_GET['reporttype']))
			$params['reporttype'] = $_GET['reporttype'] ;
		$called = call_user_func_array(array(new $page,"processRequest"),array($params)) ;
		if ($called == FALSE) {
			echo "<h2>invalid request - call fail</h2>" ;
			Log::write("[Report]Calling class failed : " . $page . " failed.",$page) ;
		}
	} else{
		echo "<h2>invalid request - class/module not found. </h2> ";
		Log::write("[Report]Class not found - " . $class . ", page : " . $page) ;
	}
} else {
	echo "<h2>invalid request - missing class/module info.</hf>" ;
	Log::write("[Report]Missing class name.",$class) ;
}

?>