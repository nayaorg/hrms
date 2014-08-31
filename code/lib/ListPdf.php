<?php
require_once(PATH_CODE . 'tcpdf/tcpdf.php');

define("PDF_FONT_NAME","times") ;
define("PDF_H1_FONT_SIZE",16) ;
define("PDF_H2_FONT_SIZE",14) ;
define("PDF_P_FONT_SIZE",10) ;
define("PDF_CH_FONT_SIZE",12) ;

class ListPdf extends TCPDF {
	private $datas = null;
	private $rpttitle = "Listing" ;
	private $company = "" ;
	private $colheaders = null ;
	private $colpadding = 5;
	private $rowpadding = 5 ;
	private $headers = null ;
	private $printheaders = array() ;
	
	function __construct($orientation) {
		parent::__construct( $orientation, "pt","A4", true, 'UTF-8', false );

		$this->SetMargins( 36, 110, 36, true ); //left,top,right,keptmargin
		$this->SetAutoPageBreak(true, 50);
		$this->setHeaderMargin(36) ;
		$this->setFooterMargin(36) ;
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
	public function setHeaders($headers) {
		$this->headers = $headers ;
	}
	public function setColumnPadding($padding) {
		$this->colpadding = $padding ;
	}
	public function setHeaderHeight($h) {
		$this->setMargins(36,$h,36,true) ;
	}
	# Page header and footer code.
	public function Header() {
		$this->SetFont(PDF_FONT_NAME, 'b',PDF_H1_FONT_SIZE );
		$this->Cell(0, 0,$this->company , 0, 1,'C' );
		$this->SetFont('','b',PDF_H2_FONT_SIZE );
		$this->Cell(0, 0, $this->title, 0, 1,'C');
		$this->Ln();
		$coly = 90 ;
		if (!is_null($this->printheaders) && count($this->printheaders) > 0) {
			foreach ($this->printheaders as $h) {
				$this->Cell(0,0,$h) ;
				$this->Ln() ;
				$coly += 25 ;
			}
		}
		if (!is_null($this->colheaders) && count($this->colheaders) > 0) {
			$idx = 1 ;
			$this->setY($coly,true) ;
			$this->setFont('','',PDF_CH_FONT_SIZE) ;
			foreach ($this->colheaders as $col) {
				if (isset($col['newrow']) && $col['newrow'] == "1") {
					$idx = 1;
					$this->Ln() ;
				}
				$this->Cell($col['width'],$col['height'],$col['text'],$col['border'],0,$col['align']) ;
				if ($idx < count($this->colheaders)) {
					$this->Cell($this->colpadding) ;
				}
				$idx++ ;
			}
		} 
	}

	public function Footer() {
		//$this->SetLineStyle( array( 'width' => 2, 'color' => array( $webcolor['black'] ) ) );
		$this->Line(36, $this->getPageHeight() - 1.5 * 30 - 2, $this->getPageWidth() - 36, $this->getPageHeight() - 1.5 * 30 - 2 );
		$this->SetFont( 'times', '', 8 );
		$this->SetY( -1.5 * 30, true );
		$this->SetX(36,true) ;
		$this->Cell(0,0,date('Y-m-d H:i:s')) ;
		$this->SetX($this->getPageWidth(),true) ;
		$this->Cell(1, 0, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(),0,0,'R');
	}
	
	public function render($datas) {
		if (is_null($datas) || count($datas) == 0)
			return ;
			
		$this->SetFont(PDF_FONT_NAME, '',PDF_P_FONT_SIZE);
		//$this->SetY($this->headerheight,true);
		$this->SetFillColor(228,228,228);
		$fill = 0 ;
		$firstpg = true;
		foreach( $datas as $row ) {
			if (isset($row['newpage']) && $row['newpage'] == "1") {
				if (isset($row['pageheader']))
					$this->setHeaderText(array($row['pageheader'])) ;
				$this->AddPage();
				$firstpg = false ;
			} else {
				if ($firstpg) {
					$this->AddPage() ;
					$firstpg = false ;
				}
			}
			$idx = 1 ;
			foreach ($row['items'] as $item) {
				if (isset($item['newrow']) && $item['newrow']) {
					$this->Ln() ;
					$idx = 1 ;
				}
				$this->Cell($item['width'],$item['height'],$item['text'],$item['border'],0,$item['align'],$fill);
				if ($idx < count($row['items']))
					$this->Cell($this->colpadding,0,'',0,0,'',$fill) ;
				$idx++ ;
			}
			$this->Ln() ;
			if (!isset($row['newrow']) || ($row['newrow'] == "1"))
				$fill = !$fill ;
		}

		$this->Ln() ;
		$this->setFont('','B','') ;
		$this->Cell(0,0,'* * * * * End of Listing * * * * *', 0, 0, 'C' );
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
	private function setHeaderText($tagtext) {
		$this->printheaders = array() ;
		if (is_null($tagtext) || count($tagtext) == 0)
			return ;
		if (is_null($this->headers) || count($this->headers) == 0)
			return ;
		$cnt = count($this->headers) ;
		foreach ($tagtext as $t) {
			for ($i=0 ;$i<$cnt;$i++) {
				$this->printheaders[] = str_replace($t['tag'],$t['text'],$this->headers[$i]) ;
			}
		}		
	}
}
?>
