<?php
require_once (PATH_CODE . "excel/PHPExcel.php") ;

class ExcelExport {
	private $company ;
    private $title ;
    private $cells ;
    private $headers;
    private $rowheaders ;
	private $excel ;
	
	function __construct() {
		$this->company = "" ;
		$this->title = "";
		$this->cells = array() ;
		$this->headers = array() ;
		$this->rowheaders = array() ;
		$this->excel = new PHPExcel() ;
	}
	function __destruct() {
		unset($this->excel) ;
	}
	public function Export($fname="") {
		if ($fname == "")
			$fname = "data.xlsx" ;
		
		
	}
}
?>