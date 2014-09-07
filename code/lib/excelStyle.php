<?php

class excelStyle {
	private $id ;
	private $name ;
	private $fname ;
	private $fsize;
	private $bold ;
	private $italic ;
	private $underline ;
	private $bgcolor ;
	private $fcolor ;
	private $format ;
	private $horizontal ;
	private $vertical ;
	
	function __construct() {
		$this->id = "" ;
		$this->name = "" ;
		$this->fname = "";
		$this->fize = 9 ;
		$this->bold = false ;
		$this->italic = false ;
		$this->underline = false ;
		$this->bgcolor = "" ;
		$this->fcolor = "" ;
		$this->format = "" ;
		$this->horizontal = "Left" ;
		$this->vertical = "Top" ;
	}
	function __destruct() {
	}
	public function setStyleId($value) {
		$this->id = $value ;
	}
	public function getStyleId() {
		return $this->id ;
	}
	public function setStyleName($value) {
		$this->name = $value ;
	}
	public function getStyleName() {
		return $this->name ;
	}
	public function setFontName($value) {
		$this->fname = $value ;
	}
	public function getFontName() {
		return $this->fname ;
	}
	public function setFontSize($value) {
		$this->fsize = $value ;
	}
	public function getFontSize() {
		return $this->fsize ; 
	}
	public function setBold($value) {
		$this->bold = $value ;
	}
	public function getBold() {
		return $this->bold ;
	}
	public function setItalic($value) {
		$this->italic = $value ; 
	}
	public function getItalic() {
		return $this->italic ;
	}
	public function setUnderline($value) {
		$this->underline = $value ;
	}
	public function getUnderline() {
		return $this->underline ;
	}
	public function setBgColor($value) {
		$this->bgcolor = $value ;
	}
	public function getBgColor() {
		return $this->bgcolor ;
	}
	public function setForeColor($value) {
		$this->fcolor = $value ;
	}
	public function getForeColor() {
		return $this->fcolor ;
	}
	public function setNumberFormat($value) {
		$this->format = $value ;
	}
	public function getNumberFormat() {
		return $this->format ;
	}
	public function setHorizontal($value) {
		$this->horizontal = $value ;
	}
	public function getHorizontal() {
		return $this->horizontal ;
	}
	public function setVertical($value) {
		$this->vertical = $value ;
	}
	public function getVertical() {
		return $this->vertical ;
	}
	public function getXml($leftmargin="   ") {
		return excelStyle::getStyleXml($this,$leftmargin) ;
	}
	
	public static function getDefaultStyle($leftmargin="   ") {
        $s = "";
        $s .= $leftmargin . '<Style ss:ID="Default" ss:Name="Normal">' . "\n";
        $s .= $leftmargin . '    <Alignment ss:Vertical="Bottom"/>' . "\n";
        $s .= $leftmargin . '    <Borders/>' . "\n";
        $s .= $leftmargin . '    <Font/>' . "\n";
        $s .= $leftmargin . '    <Interior/>' . "\n";
        $s .= $leftmargin . '    <NumberFormat/>' . "\n";
        $s .= $leftmargin . '    <Protection/>' . "\n";
        $s .= $leftmargin . '</Style>' . "\n";

        $s .= excelStyle::getDefaultHeaderStyle($leftmargin) ;
        $s .= excelStyle::getDefaultDateTimeStyle($leftmargin) ;
        $s .= excelStyle::getDefaultBooleanStyle($leftmargin);
        $s .= excelStyle::getDefaultDecimalStyle($leftmargin);
        $s .= excelStyle::getDefaultIntegerStyle($leftmargin);
        $s .= excelStyle::getDefaultStringStyle($leftmargin);
            
		return $s ;
    }
	private static function getDefaultStringStyle($leftmargin){
		$style = new excelStyle();
		$style->setStyleId("sString") ;
		$style->setFontName("Roman") ;
		$style->setHorizontal("Left") ;
		$style->setVertical("Top") ;
		return excelStyle::getStyleXml($style,$leftmargin);
    }
    private static function getDefaultIntegerStyle($leftmargin) {
		$style = new excelStyle();
		$style->setStyleId("sInteger");
        $style->setFontName("Roman");
        $style->setNumberFormat("#,###,###,###,##0;[Red]-#,###,###,###,##0");
        $style->setHorizontal("Right");
        $style->setVertical("Top");
        return excelStyle::getStyleXml($style,$leftmargin);
    }
    private static function getDefaultDecimalStyle($leftmargin) {
		$style = new excelStyle();
		$style->setStyleId("sDecimal") ;
        $style->setFontName("Roman");
        $style->setNumberFormat("#,###,###,###,##0.00;[Red]-#,###,###,###,##0.00");
        $style->setHorizontal("Right");
        $style->setVertical("Top");
        return excelStyle::getStyleXml($style,$leftmargin);
    }
    private static function getDefaultBooleanStyle($leftmargin)  {
        $style = new excelStyle();
		$style->setStyleId("sBoolean");
        $style->setFontName("Roman");
        $style->setHorizontal("Left");
        $style->setVertical("Top");
        return excelStyle::getStyleXml($style,$leftmargin);
    }
    private static function GetDefaultDateTimeStyle($leftmargin){
        $style = new excelStyle();
		$style->setStyleId("sDateTime");
        $style->setFontName("Roman");
        $style->setNumberFormat("dd-MMM-yyyy");
        $style->setHorizontal("Left");
        $style->setVertical("Top");
        return excelStyle::getStyleXml($style,$leftmargin);
    }
    private static function GetDefaultHeaderStyle($leftmargin) {
        $style = new excelStyle();
		$style->setStyleId("sHeader");
        $style->setFontName("Roman");
        $style->setFontSize(12);
        $style->setBold(true);
        $style->setHorizontal("Center");
        $style->setVertical("Center");
        return excelStyle::getStyleXml($style,$leftmargin);
    }
	public static function getStyleXml($style,$leftmargin="   ") {
		if (is_null($style))
			return "ddd" ;
			
		$s = "" ;
		$fontxml = "" ;

        $s .= $leftmargin . '<Style';
        if ($style->getStyleId() != "")
			$s .= ' ss:ID="' . $style->getStyleId() . '" ';
            
        if ($style->getStyleName() != "")
            $s .= ' ss:Name="' . $style->getStyleName() . '" ';

        $s .= '>' . "\n";

        if ($style->getForeColor() != "")
            $fontxml .= ' ss:Color="' . $style->getForeColor() . '" ';
            
        if ($style->getFontName() != "")
            $fontxml .= ' x:Family="' . $style->getFontName() . '" ' ;

        if ($style->getFontSize() > 0)
            $fontxml .= ' ss:Size="' . $style->getFontSize() . '" ' ;

        if ($style->getBold())
            $fontxml .= ' ss:Bold="1"';

        if ($style->getItalic())
            $fontxml .= ' ss:Italic="1"' ;

        if ($style->getUnderline())
            $fontxml .= ' ss:Underline="Single"';

        if ($fontxml != "")
            $s .= $leftmargin . '   <Font' . $fontxml . ' />' . "\n";

        if ($style->getBgColor() != "")
            $s .= $leftmargin . '   <Interior ss:Color=' . $style->getBgColor() . ' ss:Pattern="Solid" />' . "\n";

        if ($style->getNumberFormat() != "")
			$s .= $leftmargin . '   <ss:NumberFormat ss:Format="' . $style->getNumberFormat() . '" />' . "\n";

        if ($style->getHorizontal() != "" || $style->getVertical() != "") {
            $s .= $leftmargin . '   <ss:Alignment';
            if ($style->getHorizontal() != "")
				$s .= ' ss:Horizontal="' .   $style->getHorizontal() . '" ';	//Center, Left, Right

            if ($style->getVertical() != "")
                $s .= ' ss:Vertical="' .  $style->getVertical() . '" '; //Bottom, Center, Top

            $s .= ' />' . "\n";
        }

        $s .= $leftmargin . '</Style>' . "\n" ;

        return $s ;
        
	}
}
?>