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

function myException($e)
{
	Log::write('[Service]' . $e->getMessage(),"") ;
	//die("Sorry, there is an error and the application can not continue and will be terminated.") ;
	die("") ;
}
set_exception_handler('myException');
register_shutdown_function('fatalErrorShutdownHandler');

function fatalErrorShutdownHandler()
{
	$last_error = error_get_last();
	if ($last_error['type'] > 0)
		Log::write('[Service]' . $last_error['message'] . " - " . $last_error['file'] . " on line " . $last_error['line']);
}
session_start() ;

//if (isset($_SERVER['QUERY_STRING']))
	//$query = $_SERVER['QUERY_STRING'] ;
//else 
	//$query = "";

if (isset($_GET['c']))
	$class = $_GET['c'];
else
	$class = "" ;
try {	
	$page = "Login";
	$data = array() ;
	$called = false ;
	
	if ($class !=""){
		if (isset($_SESSION[SE_USERID]) && $_SESSION[SE_USERID] != "" ){
			$page = Util::revertLink($class) ;
		} 
		if ($_POST) {
			foreach ($_POST as $key => $value) {
				$data[$key] = $value;
			}
		}	
		if (class_exists($page)) {
			$called = call_user_func_array(array(new $page,"processRequest"),array($data)) ;
		} 
	}
} catch (Exception $e) {
	Log::write('[Service]' . $e->getMessage(),$page) ;
}
if (!$called) {
	header('Content-type: application/json');
	$arr = array(FIELD_STATUS =>Status::Invalid,FIELD_MESG =>"Invalid Request.",FIELD_DATA => "index.pzx");
	echo json_encode($arr) ;
}
?>