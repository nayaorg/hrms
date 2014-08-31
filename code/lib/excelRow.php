<?php
class excelRow {
    private $Index;
    private $Cells ;
	
	function __construct() {
		$this->Index = 0;
		$this->Cells = array() ;
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
	public function addStringCell($value,$index) {
		$cell = new excelCell() ;
		$cell->StyleId = 'sString';
		$cell->Type = 'String' ;
		$cell->Data = $value ;
		$cell->Index = $index ;
		$this->Cells[] = $cell ;
	}
	public function addIntegerCell($value,$index) {
		$cell = new excelCell() ;
		$cell->StyleId = 'sInteger';
		$cell->Type = 'Integer' ;
		$cell->Data = $value ;
		$cell->Index = $index ;
		$this->Cells[] = $cell ;
	}
	public function addDecimalCell($value,$index) {
		$cell = new excelCell() ;
		$cell->StyleId = 'sDecimal' ;
		$cell->Type = 'Decimal' ;
		$cell->Data = $value ;
		$cell->Index = $index ;
		$this->Cells[] = $cell ;
	}
	public function getXml($leftmargin) {
		$s = "" ;
        $s .= $leftmargin . '<ss:Row' ;
        if ($this->Index > 0)
            $s .= ' ss:Index="' . $this->Index . '" ';
        $s .= '>' . "\n" ;
        foreach ($this->Cells as $cell) {
			$s .= $leftmargin . "   " . $cell->getXml() ;
        }
        $s .= $leftmargin . '</ss:Row>'  ;
        return $s ;
    }
}
?>