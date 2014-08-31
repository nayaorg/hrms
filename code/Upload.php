<?php
date_default_timezone_set('Asia/Singapore');
require_once('Constants.php') ;
require_once(PATH_LIB . 'Util.php') ;
require_once(PATH_LIB . 'Log.php') ;
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
if (isset($_REQUEST['t']))
	$type = $_REQUEST['t'] ;
else
	$type = "";

if ($type=="") {
	echo Status::Error .  "|Missing upload file type."  ;
} else {
	if ($type=="i" || $type=="l" || $type=="p")
		include(PATH_LIB . "ImageUpload.php") ;
	else if ($type=="c")
		include(PATH_LIB . "ClaimUpload.php") ;
	else
		echo Status::Error . "|Unsupported file type.";
}

?>