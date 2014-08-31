<?php
date_default_timezone_set('Asia/Singapore');

require_once('Constants.php') ;
require_once(PATH_LIB . 'AutoLoader.php') ;
require_once(PATH_LIB . 'Util.php') ;
require_once(PATH_LIB . 'Log.php') ;
require_once(PATH_LIB . 'License.php') ;
require_once(PATH_MESSAGE . 'Message.php') ;
require_once(PATH_CODE . "database/MsSqlDb.php") ;
require_once(PATH_CODE . "database/MySqlDb.php") ;
require_once(PATH_CODE . "database/MySqliDb.php") ;
require_once('ConfigDb.php') ;

function myException($e)
{
	Log::write('[WebView]' . $e->getMessage(),"") ;
	//die("Sorry, there is an error and the application can not continue and will be terminated.") ;
	die("") ;
}
set_exception_handler('myException');
register_shutdown_function('fatalErrorShutdownHandler');

function fatalErrorShutdownHandler()
{
	$last_error = error_get_last();
	if ($last_error['type'] > 0)
		Log::write('[WebView]' . $last_error['message'] . " - " . $last_error['file'] . " on line " . $last_error['line']);
}

if (session_id() == "") 
	session_start(); 

if (!isset($_SESSION[SE_INIT]) || $_SESSION[SE_INIT] == false ){
    session_regenerate_id();
    $_SESSION[SE_INIT] = true;
	$_SESSION[SE_USERID] = "" ;
	$_SESSION[SE_USERGROUP] = "" ;
	$_SESSION[SE_MENU] = "" ;
	$_SESSION[SE_ORGID] = -1 ;
	$_SESSION[SE_USERKEY] = "" ;
	$_SESSION[SE_ORGNAME] = "" ;
	$_SESSION[SE_REMOTE_IP] = Util::getRemoteIP() ;
}
if (isset($_SESSION[SE_ID]))
	$oldseid = $_SESSION[SE_ID] ;
else
	$oldseid = "" ;
	
$_SESSION[SE_ID] = session_id() ;

if (!isset($_SESSION[SE_DB])) {
	$config = new ConfigDb(PATH_CODE . 'db.xml') ;
	$config->loadConfig() ;
	if ($config->getDbType() == DbType::MySql)
		$_SESSION[SE_DB] = new MySqlDb($config->getHost(),$config->getDbName(),$config->getUserName(),$config->getPassword()) ;
	else if ($config->getDbType() == DbType::MySqli)
		$_SESSION[SE_DB] = new MySqliDb($config->getHost(),$config->getDbName(),$config->getUserName(),$config->getPassword()) ;
	else
		$_SESSION[SE_DB] = new MsSqlDb($config->getHost(),$config->getDbName(),$config->getUserName(),$config->getPassword()) ;
	unset($config) ;
}

if (isset($_GET['c'])){
	$class = $_GET['c'];
} else {
	$class = "" ;
}

$page = "Signin";
if ($class !=""){
	if (isset($_SESSION[SE_USERID]) && $_SESSION[SE_USERID] != "" ){
		$page = Util::revertLink($class);
	} 
}
if (!class_exists($page)) {
	$page = "Signin" ;
	Log::write("[WebView]class not found : " . $class . ", page : " . $page) ;
} 
$params = array() ;
$params['type'] = 'v' ;
$called = call_user_func_array(array(new $page,"processRequest"),array($params)) ;
if ($called == FALSE) {
	echo "<h2>invalid request - call fail</h2>" ;
	Log::write("[WebView]Calling class failed : " . $class . " failed. Page : " . $page,$page) ;
}

?>