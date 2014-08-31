<?php

class excelWorkbook {
	private $Worksheets ;
    private $Styles ;

	function __construct() {
		$this->Worksheets = array() ;
		$this->Styles = array() ;
	}
	function __destruct() {
	}
	function __set($property, $value) {
		$this->$property = $value;
	}
	function __get($property) {
		if (isset($this->$property)) {
			return $this->$property;
		}
	}
	public function addWorksheet($ws) {
		$this->Worksheets[] = $ws ;
	}
	public function getXml() {
		$count = 0 ;
		$s = "" ;
		$s .= $this->createWorkbook() ;
		$s .= $this->createStyle() ;
		foreach ($this->Worksheets as $ws) {
			if ($ws->SheetName == "") {
				$ws->SheetName = 'Sheet' . $count;
                $count++;
            }
            $s .= $ws->getXml() ;
        }
		$s .= $this->closeWorkbook() ;
		return $s ;
	}
	public function downloadFile($filename) {          
		$xml = $this->getXml() ;
		header("Cache-Control: public, must-revalidate");         
		header("Pragma: no-cache");         
		header("Content-Length: " . strlen($xml) );         
		header("Content-Type: application/vnd.ms-excel");         
		header('Content-Disposition: attachment; filename="'.$filename.'"');         
		header("Content-Transfer-Encoding: binary");          
		echo $xml;         
	}      
	private function createWorkbook() {         
		$s = "" ;
        $s .= '<?xml version="1.0"?>' . "\n" ;
        $s .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $s .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n";
        $s .= '   xmlns:o="urn:schemas-microsoft-com:office:office" ' . "\n";
        $s .= '   xmlns:x="urn:schemas-microsoft-com:office:excel" ' . "\n";
        $s .= '   xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n";
        $s .= '   xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n" ;  //to allow html tag in xml
        $s .= '   <DocumentProperties>' . "\n";
        $s .= '      <o:Author></o:Author>' . "\n";
        $s .= '   </DocumentProperties>' . "\n";
        $s .= '   <x:ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">' . "\n";
            //sb.Append("      <x:WindowHeight>9120</x:WindowHeight>" + Environment.NewLine);
            //sb.Append("      <x:WindowWidth>10005</x:WindowWidth>" + Environment.NewLine);
            //sb.Append("      <x:WindowTopX>120</x:WindowTopX>" + Environment.NewLine);
            //sb.Append("      <x:WindowTopY>135</x:WindowTopY>" + Environment.NewLine);
            //sb.Append("      <x:ActiveSheet>0</x:ActiveSheet>") ;     //first visible sheet when workbook opened.
            //sb.Append("      <x:SelectedSheets>1</x:SelectedSheets>");      //how many sheet can be selected at one time.
        $s .= '      <x:ProtectStructure>False</x:ProtectStructure>' . "\n";
        $s .= '      <x:ProtectWindows>False</x:ProtectWindows>' . "\n";
        $s .= '   </x:ExcelWorkbook>' . "\n";
        return $s ;
	} 
	private function closeWorkbook() {         
		return '</Workbook>';     
	}  
	private function CreateStyle() {
        $s = "" ;
        $spc = "   ";
        $s .= $spc . '<Styles>' . "\n";
        $s .= excelStyle::getDefaultStyle($spc . "   ");
		//if (!is_null($this->Styles) && count($this->Styles) > 0) {
			//foreach ($this->Styles as $style) {
				//$s .= excelStyle::getStyleXml($style,$spc . "   ");
			//}
		//}
        $s .= $spc . '</Styles>' . "\n";

        return $s ;
    }
}
?>