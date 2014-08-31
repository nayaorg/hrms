<?php
require_once(PATH_CODE . 'tcpdf/tcpdf.php');

define("PDF_FONT_NAME","times") ;
define("PDF_H1_FONT_SIZE",16) ;
define("PDF_H2_FONT_SIZE",14) ;
define("PDF_P_FONT_SIZE",10) ;
define("PDF_CH_FONT_SIZE",12) ;

class PagePdf extends TCPDF {
	private $datas = null;
	private $rpttitle = "Report" ;
	private $company = "" ;
	private $colheaders = null ;
	private $headerheight = 72;
	private $colpadding = 5;
	function __construct($orientation) {
		parent::__construct( $orientation, "pt","A6", true, 'UTF-8', false );

		# Set the page margins: 72pt on each side, 36pt on top/bottom.
		$this->SetMargins( 30, 60, 36, true );
		$this->SetAutoPageBreak( true, 50);
		$this->setHeaderMargin(20) ;
		$this->setFooterMargin(20) ;
		# Set document meta-information
		$this->SetCreator('tcpdf 1.7');
		$this->SetAuthor( 'SBG' );
		$this->SetTitle($this->rpttitle);
		$this->SetSubject("Listing");
		$this->SetKeywords('');

		//set image scale factor
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO); 
	}
	public function setReportTitle($title) {
		$this->rpttitle = $title ;
		$this->setTitle($title) ;
	}
	public function setCompanyName($name) {
		$this->company = $name ;
	}
	public function setColumnsHeader($cols) {
		$this->colheaders = $cols ;
	}
	public function setColumnPadding($padding) {
		$this->colpadding = $padding ;
	}
	# Page header and footer code.
	public function Header() {
		$fn = Util::getLogoFile(PATH_PICTURE,$_SESSION[SE_ORGID]);
		if ($fn != "") {
			$fn = PATH_PICTURE . $_SESSION[SE_ORGID] . "\\" . $fn ;
			$size = getimagesize($fn);
			$this->ImagePngAlpha($fn, 0, 5, $size[0],$size[1], 100,50, '', null, 'T', false, 72, 'L' );
			$this->SetFont(PDF_FONT_NAME, 'b',PDF_H1_FONT_SIZE );
			$this->SetY(10,true) ;
			$this->SetX(130,true) ;
			$this->Cell(0, 0,$this->company , 0, 1,'L' );
			$this->SetFont('','b',PDF_H2_FONT_SIZE );
			$this->setX(130,true) ;
			$this->Cell(0, 0, $this->title, 0, 1,'L');
		}
		else {
			$this->SetFont(PDF_FONT_NAME, 'b',PDF_H1_FONT_SIZE );
			$this->Cell(0, 0,$this->company , 0, 1,'L' );
			$this->SetFont('','b',PDF_H2_FONT_SIZE );
			$this->Cell(0, 0, $this->title, 0, 1,'L');
		}
	}

	public function Footer() {
		//$this->SetLineStyle( array( 'width' => 2, 'color' => array( $webcolor['black'] ) ) );
		//$this->Line(36, $this->getPageHeight() - 1.5 * 30 - 2, $this->getPageWidth() - 36, $this->getPageHeight() - 1.5 * 30 - 2 );
		//$this->SetFont( 'times', '', 8 );
		//$this->SetY( -1.5 * 30, true );
		//$this->SetX(36,true) ;
		//$this->Cell(0,0,date('Y-m-d H:i:s')) ;
		//$this->SetX($this->getPageWidth(),true) ;
		//$this->Cell(1, 0, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(),0,0,'R');
	}
	
	public function render($datas) {
		$this->SetFont(PDF_FONT_NAME, '',PDF_P_FONT_SIZE);
		//$this->SetY($this->headerheight,true);
		//$this->SetFillColor(228,228,228);
		foreach( $datas as $row ) {
			if(isset($row['newpage']) && $row['newpage'] == 1)
				$this->AddPage() ;
			foreach ($row['items'] as $item) {
				$this->Cell($item['width'],$item['height'],$item['text'],0,0,$item['align']);
				if(isset($item['newrow']) && $item['newrow'] == "1")
					$this->Ln();
			}
		}

		//Cell (float $w : 0=full width, 
			//float $h, 
			//[string $txt = ''], 
			//mixed $border : 0-no border 1-frame L-left T-top R-right B-bottom, 
			//int $ln : 0-right 1-beginning of next line or Ln 2-below, 
			//[string $align = ''] : L-left R-right C-center J-justify, 
			//boolean $fill : true-background color false-transparent color, 
			//[mixed $link = ''] : url link, 
			//int $stretch : 0-disabled 1-scale horizontal 2-fit cell width horiz 3- 4-
			//ignore_min_height,
			//calign : T-top C-center B-bottom A-font top L-font baseline D-font bottom
			//valign : T-top C-center B-bottom
		//$w,$h = 0,$txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' 
	}
}
?>
