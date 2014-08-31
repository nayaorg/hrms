<?php
class excelCell {

	private $Type;
    private $Data;
    private $Index;
    private $ColSpan;
    private $StyleId ;
    private $Formula ;
	
	function __construct() {
		$this->Type = "" ;	// String, DateTime,Integer, Decimal,Boolean, Links
		$this->Data = "" ;
		$this->Index = 0;
		$this->ColSpan = 0 ;
		$this->StyleId = "sString" ;	//sString, sDecimal, sInteger, sDateTime, sBoolean, sLinks, sHeader
		$this->Formula = "" ;
	}
	function __destruct() {
	}
	function __set($property, $value) {
		$this->$property = $value;
	}
	function __get($property) {
		//if (isset($this->$property)) {
			return $this->$property;
		//}
	}
	public function getXml() {
        $s = "";
        $s .= '<Cell';
        if ($this->Index > 0)
            $s .= ' ss:Index="' . $this->Index . '" ' ;

        if ($this->ColSpan > 0)
            $s .= ' ss:MergeAcross="' . $this->ColSpan . '" ';
                        
        $s .= ' ss:StyleID="' ;
        if ($this->StyleId == "")
            $s .= 'Default' ;
        else
            $s .= $this->StyleId ;

        $s .= '"' ;

        if ($this->Formula!= "") {
            if ($this->Type == "Links")
                $s .= ' ss:HRef="';
            else
				$s .= ' ss:Formula="';

            $s .= $this->Formula . '"';
        }

        $s .= '>';

        $s .= '<Data ss:Type="' . $this->getTypeString() . '">' ;
        $s .= $this->getDataString();
        $s .= '</Data>';
        $s .= '</Cell>' . "\n";
        return $s;
    }
	private function getTypeString() {
		if ($this->Type == "Integer") {
			return "Number" ;
		} else if ($this->Type == "Decimal") {
			return "Number" ;
		} else {
			return $this->Type ;
		}
	}
	private function getDataString() {
		if ($this->Type == "DateTime") {
			$date = new DateTime($this->Data);
			return $date->format('Y-m-d\TH:i:s');
        } else if ($this->Type == "Boolean") {
			return $this->Data ;
		} else if ($this->Type == 'Decimal') {
			return $this->Data ;
		} else if ($this->Type == "Integer") {
            return $this->Data ;
		} else {
            return '<![CDATA[' . $this->Data . ']]>';
        }
	}
}
?>