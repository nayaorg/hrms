<?php
date_default_timezone_set('Asia/Singapore');

require_once('Constants.php') ;
require_once(PATH_LIB . 'AutoLoader.php') ;
require_once(PATH_LIB . 'Util.php') ;
require_once(PATH_LIB . 'Log.php') ;
require_once(PATH_MESSAGE . 'Message.php') ;
require_once(PATH_CODE . "database/MsSqlDb.php") ;
require_once(PATH_CODE . "database/MySqlDb.php") ;
require_once(PATH_CODE . "database/MySqliDb.php") ;
require_once('ConfigDb.php') ;

function myException($e)
{
	Log::write('[Admin]' . $e->getMessage(),"") ;
	die("") ;
}
set_exception_handler('myException');
register_shutdown_function('fatalErrorShutdownHandler');

function fatalErrorShutdownHandler()
{
	$last_error = error_get_last();
	if ($last_error['type'] > 0)
		Log::write('[Admin]' . $last_error['message'] . " - " . $last_error['file'] . " on line " . $last_error['line']);
}
	
$page = "Config" ;

session_start(); 
	
if (class_exists($page)) {
	$data = array() ;
	if ($_POST) {
		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}
	}	
	$called = call_user_func_array(array(new $page,"processRequest"),array($data)) ;
	if ($called == FALSE) {
		echo "<h2>invalid request - call fail</h2>" ;
		Log::write("[Admin]Calling class failed : " . $class . " failed. Page : " . $page,$page) ;
	}
} else{
	echo "<h2>invalid request - class not found. </h2> ";
	Log::write("[Admin]class not found : " . $class . ", page : " . $page) ;
}
?>