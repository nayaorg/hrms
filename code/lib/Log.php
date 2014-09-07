<?php
Class Log { 
    public static function write($msg,$data="") 
    { 
		//return ;
		$date = date('Y-m-d H:i:s'); 
		$file = "Err_" . date('Ymd') . ".log" ;
		if ($data != "") {
			error_log("[" . $date . "] " . $data . "\n", 3, PATH_LOG . $file) ;
		}
		error_log("[" . $date . "] " . $msg . "\n", 3, PATH_LOG . $file); 
    } 
} 

?>