<?php

class excelWorksheet {
	private $SheetName ;
    private $Rows ;
	
	function __construct() {
		$this->SheetName = "" ;
		$this->Rows = array() ;
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
	public function addRow($row) {
		$this->Rows[] = $row ;
	}
	public function getXml() {
        $s = "" ;
		$spc = "   " ;
        $s .= $spc . '<Worksheet ss:Name="' . $this->SheetName . '" ss:Protected="0">' . "\n";
        $s .= $spc . '   <x:WorksheetOptions>' . "\n";
        //$s .= $spc . '      <x:TopRowVisible>2</x:TopRowVisible>' . "\n";
        //$s .= $spc . '      <x:LeftColumnVisible>3</x:LeftColumnVisible>' . "\n";
        $s .= $spc . '      <x:Zoom>100</x:Zoom>' . "\n";
        //$s .= $spc . '      <x:Selected/>' . "\n";
        $s .= $spc . '   </x:WorksheetOptions>' . "\n";
        $s .= $spc . '   <ss:Table>' . "\n";
        $s .= $spc . '      <ss:Column ss:AutoFitWidth="1" />' . "\n";   //Width, Span, Hidden (0-false,1-ture)
        foreach ($this->Rows as $row) {
			$s .= $row->getXml($spc . "      ") . "\n" ;
        }
        $s .= $spc . '   </ss:Table>' . "\n";
        $s .= $spc . '</Worksheet>' . "\n";
        return $s ;
    }
}
?>