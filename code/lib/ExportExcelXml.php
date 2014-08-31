<?php 
/** 
* Class for generating xml with multiple spreadsheets 
* @author Marin 
* @person_who_improved_the_code Chun Lin 
* @version 0.9 
*/  
class excel_xml {      
	var $xml_data;     
	var $nl = "\n";     
	var $tab = "\t";     
	var $cols = array();     
	var $rows = array();     
	var $worksheets = array();     
	var $counters = array();     
	var $xml;      
	
	/**     
	* Constructor     
	*/     
	function excel_xml($column_width = 100){                 
		$this->column_width = $column_width;         
		$this->row_array    = array();     
	}      
	/**     
		* Create worksheet     
		* Uppon creating a worksheet, delete counters     
		* @param string $worksheet_name: name of the worksheet     
	*/     
	function create_worksheet($worksheet_name) {         
		$worksheet = '<Worksheet ss:Name="'.$worksheet_name.'">';         
		$worksheet .= $this->create_table();         
		$worksheet .= '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">         
		<ProtectObjects>False</ProtectObjects>         
		<ProtectScenarios>False</ProtectScenarios>         
		</WorksheetOptions>         
		</Worksheet>';          
		
		// Unset the counters and rows so you can generate another worksheet table         
		$this->counters     = array();         
		$this->row_array    = array();         
		$this->rows         = '';          
		
		// Add generated worksheet to the worksheets array         
		$this->worksheets[] = $worksheet;     
	}      
	
	/**     
	* Create table     
	* @returns string     
	*/     
	function create_table() {         
		// Create rows with the method that automaticaly sets counters for number of columns and rows         
		$rows = $this->create_rows();          
		// Table header         
		$table = '<Table ss:ExpandedColumnCount="'.$this->counters['cols'].'" ss:ExpandedRowCount="'.$this->counters['rows'].'" x:FullColumns="1" x:FullRows="1">'.$this->nl;          
		// Columns data (width mainly)         
		for($i = 1; $i <= $this->counters['cols']; $i++) {                         
			if (!is_array($this->column_width)) { 
				//All columns having the same width                                 
				$table .= '<Column ss:Index="'.$i.'" ss:Width="'.$this->column_width.'" />'.$this->nl;                         
			} else if (isset($this->column_width[$i - 1])) { 
				//Some columns having the specified width                                 
				$current_column_width = $this->column_width[$i - 1];                                 
				$table .= '<Column ss:Index="'.$i.'" ss:Width="'.$current_column_width.'" />'.$this->nl;                         
			} else { 
				//For columns which have no width indicated                                 
				$table .= '<Column ss:Index="'.$i.'" ss:Width="100" />'.$this->nl;                         
			}         
		}         
		// Insert all rows         
		$table .= join('', $rows);          
		// End table         
		$table .= '</Table>'.$this->nl;         
		return $table;     
	}      
	
	/**     
	* Add another row into the array     
	* @param mixed $array: array with row cells     
	* @param mixed $style: default null, if set, adds style to the array     
	*/     
	
	function add_row($array, $style = null) {         
		if(!is_array($array)) 
			return;                          
		if(!is_null($style)) {             
			$style_array = array('attach_style' => $style);             
			$array = array_merge($array, $style_array);         
		}          
		$this->row_array[] = $array;     
	}      
	
	/**     
	* Create rows     
	* @returns array     
	*/     
	function create_rows() {         
		if(!is_array($this->row_array)) 
			return;          
		$cnt = 0;         
		$row_cell = array();         
		foreach($this->row_array as $row_data) {             
			$cnt++;              
			// See if there are styles attached             
			$style = null;             
			if($row_data['attach_style']) {                 
				$style = $row_data['attach_style'];                 
				unset($row_data['attach_style']);             
			}              
			// Store the counter of rows             
			$this->counters['rows'] = $cnt;              
			$cells = '';             
			$cell_cnt = 0;             
			foreach($row_data as $key => $cell_data) {                 
				$cell_cnt++;                  
				$cells .= $this->nl.$this->prepare_cell($cell_data, $style);
			}              
			// Store the number of cells in row             
			$row_cell[$cnt][] = $cell_cnt;              
			$this->rows[] = '<Row>'.$cells.$this->nl.'</Row>'.$this->nl;         
		}          
		// Find out max cells in all rows         
		$max_cells = max($row_cell);         
		$this->counters['cols'] = $max_cells[0];          
		return $this->rows;     
	}      
	
	/**     
	* Prepare cell     
	* @param string $cell_data: string for a row cell     
	* @returns string     
	*/     
	function prepare_cell($cell_data, $style = null) {          
		$str = str_replace("\t", " ", $cell_data);          
		// replace tabs with spaces         
		$str = str_replace("\r\n", "\n", $str);             
		// replace windows-like new-lines with unix-like         
		$str = str_replace('"',  '""', $str);               
		// escape quotes so we support multiline cells now         
		preg_match('#\"\"#', $str) ? $str = '"'.$str.'"' : $str; 
		// If there are double doublequotes, encapsulate str in doublequotes          
		// Formating: bold         
		if(!is_null($style)) {             
			$style = ' ss:StyleID="'.$style.'"';         
		} elseif (preg_match('/^\*([^\*]+)\*$/', $str, $out)) {             
			$style  = ' ss:StyleID="bold"';             
			$str    = $out[1];         
		}          
		if (preg_match('/\|([\d]+)$/', $str, $out)) {             
			$merge  = ' ss:MergeAcross="'.$out[1].'"';             
			$str    = str_replace($out[0], '', $str);         
		}         
		// Get type         
		$type = preg_match('/^([\d]+)$/', $str) ? 'Number' : 'String';          
		return '<Cell'.$style.$merge.'><Data ss:Type="'.$type.'">'.$str.'</Data></Cell>';     
	}                  
	
	/**     
	* Define all the styles that will be used in the spreadsheet     
	* @param array $styles: array in which each of its element has style ID as the key and style parameters as values     
	*/         
	function define_styles($styles){                 
		foreach($styles as $style_id => $parameters) {                         
			$border = '';                         
			unset($interior, $font, $interiors, $fonts, $interior_xml, $font_xml);                         
			foreach($parameters as $param => $data) {                                 
				switch($param) {                                         
					case 'size':                                                 
						$font['ss:Size'] = $data;                                         
						break;                                                  
					case 'font':                                                 
						$font['ss:FontName'] = $data;                                         
						break;                                                  
					case 'color':                                                 
						$font['ss:Color'] = $data;                                         
						break;                                                  
					case 'bgcolor':                                                 
						$interior['ss:Color'] = $data;                                         
						break;                                                  
					case 'bold':                                                 
						$font['ss:Bold'] = $data;                                         
						break;                                                  
					case 'italic':                                                 
						$font['ss:Italic'] = $data;                                         
						break;                                                  
					case 'strike':                                                 
						$font['ss:StrikeThrough'] = $data;                                         
						break;                                                  
					case 'border':                                                 
						$border .= '<Borders>';                                                 
						foreach($data as $border_position => $border_weight) {                                                         
							$border .= '<Border ss:Position="'.$border_position.'"';                                                         
							$border .= ' ss:LineStyle="Continuous" ss:Weight="'.$border_weight.'" />';                                                 
						}                                                 
						$border .= '</Borders>';                                         
						break;                                 
				}                         
			}                         
			if(is_array($interior) && !empty($interior)) {                                 
				foreach($interior as $param => $value) {                                         
					$interiors .= ' '.$param.'="'.$value.'"';                                 
				}                                 
				$interior_xml = '<Interior ss:Pattern="Solid"'.$interiors.' />'.$this->nl;                         
			}                         
			if(is_array($font) && !empty($font)) {                                 
				foreach($font as $param => $value) {                                         
					$fonts .= ' '.$param.'="'.$value.'"';                                 
				}                                 
				$font_xml = '<Font'.$fonts.' />'.$this->nl;                         
			}                         
			$this->styles[] = '                         
			<Style ss:ID="'.$style_id.'">                         
			'.$border.$interior_xml.$font_xml.'                         
			</Style>';                 
		}         
	}      
	
	/**     
	* Create footer     
	* @returns string     
	*/     
	function create_footer() {         
		return '</Workbook>';     
	}      
	/**     
	* Output as download     
	*/     
	function download($filename) {          
		// Create header         
		$xml = $this->create_header().$this->nl;         
		// Put all worksheets         
		$xml .= join('', $this->worksheets).$this->nl;         
		// Finish with a footer         
		$xml .= $this->create_footer();                                  
		// Store it globally, in case we need it later         
		$this->xml = $xml;          
		header("Cache-Control: public, must-revalidate");         
		header("Pragma: no-cache");         
		header("Content-Length: " .strlen($xml) );         
		header("Content-Type: application/vnd.ms-excel");         
		header('Content-Disposition: attachment; filename="'.$filename.'"');         
		header("Content-Transfer-Encoding: binary");          
		print $xml;         
		exit;     
	}      
	/**     
	* Create header     
	* @returns string     
	*/     
	function create_header() {         
		if (is_array($this->styles)) {             
			$styles = join('', $this->styles);         
		}                          
		return <<<EOF <?xml version="1.0"?> 
		<?mso-application progid="Excel.Sheet"?> 
		<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" 
		xmlns:o="urn:schemas-microsoft-com:office:office" 
		xmlns:x="urn:schemas-microsoft-com:office:excel" 
		xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" 
		xmlns:html="http://www.w3.org/TR/REC-html40">     
		<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">         
		<DownloadComponents/>         
		<LocationOfComponents HRef="file:///\\"/>     
		</OfficeDocumentSettings>     
		<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">         
			<WindowHeight>12525</WindowHeight>         
			<WindowWidth>15195</WindowWidth>         
			<WindowTopX>480</WindowTopX>         
			<WindowTopY>120</WindowTopY>         
			<ActiveSheet>0</ActiveSheet>         
			<ProtectStructure>False</ProtectStructure>         
			<ProtectWindows>False</ProtectWindows>     
		</ExcelWorkbook>     
		<Styles>         
			<Style ss:ID="Default" ss:Name="Normal">             
				<Alignment ss:Vertical="Bottom"/>                 
				<Borders/>             
				<Font/>             
				<Interior/>             
				<NumberFormat/>             
				<Protection/>         
			</Style>         
			<Style ss:ID="bold">             
				<Font ss:Bold="1" />         
			</Style>         
			$styles     
		</Styles> EOF;     
	}  
} 
?> 