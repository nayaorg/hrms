<?php
class CompanyOptions {
	const C_CONTACT = "contact";
	const C_CONT_NAME2 = "name2";
	const C_CONT_ADDR1 = "address1";
	const C_CONT_ADDR2 = "address2";
	const C_CONT_ADDR3 = "address3";
	const C_CONT_ADDR4 = "address4" ;
	const C_CONT_ADDR5 = "address5" ;
	const C_CONT_TEL = "telno" ;
	const C_CONT_FAX = "faxno" ;
	const C_CONT_EMAIL = "email";
	const C_CONT_WEB = "website";
	
	const C_SETTING = "setting" ;
	const C_SET_CPF_NO = "cpfno";
	const C_SET_CPF_REF = "cpfref";
	const C_SET_REF_NO = "refno";
	const C_SET_BANK = "bank";
	const C_SET_BANK_ACCT = "bankacctno" ;
	
	const C_IRAS = "iras";
	const C_IRAS_NAME = "name" ;
	const C_IRAS_DEST = "designation" ;
	const C_IRAS_CONTACT = "contactno" ;
	const C_IRAS_EGM = "egmdate" ;
	const C_IRAS_BONUS = "bonusdate" ;
	const C_IRAS_TAX_ID = "taxid" ;
	const C_IRAS_ID_TYPE = "taxidtype" ;
	
	private $opt = array() ;
	
	function __construct() {
		$this->initOptions() ;
	}
	function __destruct() {
	}
	function getOption() {
		return $this->opt ;
	}
	function setOption($value) {
		$this->opt = $value ;
	}
	function loadXml($xml) {

		$xr = simplexml_load_string($xml);
		if ($xr) {
			$op = array() ;
			$op[self::C_CONT_NAME2] = (string) $xr->contact->{self::C_CONT_NAME2} ;
			$op[self::C_CONT_ADDR1] = (string) $xr->contact->{self::C_CONT_ADDR1} ;
			$op[self::C_CONT_ADDR2] = (string) $xr->contact->{self::C_CONT_ADDR2} ;
			$op[self::C_CONT_ADDR3] = (string) $xr->contact->{self::C_CONT_ADDR3} ;
			$op[self::C_CONT_ADDR4] = (string) $xr->contact->{self::C_CONT_ADDR4} ;
			$op[self::C_CONT_ADDR5] = (string) $xr->contact->{self::C_CONT_ADDR5} ;
			$op[self::C_CONT_TEL] = (string) $xr->contact->{self::C_CONT_TEL} ;
			$op[self::C_CONT_FAX] = (string) $xr->contact->{self::C_CONT_FAX} ;
			$op[self::C_CONT_EMAIL] = (string) $xr->contact->{self::C_CONT_EMAIL} ;
			$op[self::C_CONT_WEB] = (string) $xr->contact->{self::C_CONT_WEB} ;
			$this->opt[self::C_CONTACT] = $op;
			
			$op = array();
			$op[self::C_SET_CPF_NO] = (string) $xr->setting->{self::C_SET_CPF_NO} ;
			$op[self::C_SET_REF_NO] = (string) $xr->setting->{self::C_SET_REF_NO} ;
			$op[self::C_SET_CPF_REF] = (string) $xr->setting->{self::C_SET_CPF_REF} ;
			$op[self::C_SET_BANK] = (string) $xr->setting->{self::C_SET_BANK} ;
			$op[self::C_SET_BANK_ACCT] = (string) $xr->setting->{self::C_SET_BANK_ACCT} ;
			$this->opt[self::C_SETTING] = $op ;
			
			$op = array();
			$op[self::C_IRAS_NAME] = (string) $xr->iras->{self::C_IRAS_NAME} ;
			$op[self::C_IRAS_DEST] = (string) $xr->iras->{self::C_IRAS_DEST} ;
			$op[self::C_IRAS_EGM] = (string) $xr->iras->{self::C_IRAS_EGM} ;
			$op[self::C_IRAS_CONTACT] = (string) $xr->iras->{self::C_IRAS_CONTACT} ;
			$op[self::C_IRAS_BONUS] = (string) $xr->iras->{self::C_IRAS_BONUS} ;
			$op[self::C_IRAS_TAX_ID] = (string) $xr->iras->{self::C_IRAS_TAX_ID} ;
			$op[self::C_IRAS_ID_TYPE] = (string) $xr->iras->{self::C_IRAS_ID_TYPE} ;
			$this->opt[self::C_IRAS] = $op ;
			
		}
		unset($xr) ;
	}
	function getXml() {
		$xml = '<?xml version="1.0" encoding="utf-8"?>';
		$xml .= '<configuration>' ;
		
		$xml .= '<' . self::C_CONTACT . '>';
		$xml .= $this->createXmlElement(self::C_CONT_NAME2,$this->opt[self::C_CONTACT][self::C_CONT_NAME2]) ;
		$xml .= $this->createXmlElement(self::C_CONT_ADDR1,$this->opt[self::C_CONTACT][self::C_CONT_ADDR1]) ;
		$xml .= $this->createXmlElement(self::C_CONT_ADDR2,$this->opt[self::C_CONTACT][self::C_CONT_ADDR2]) ;
		$xml .= $this->createXmlElement(self::C_CONT_ADDR3,$this->opt[self::C_CONTACT][self::C_CONT_ADDR3]) ;
		$xml .= $this->createXmlElement(self::C_CONT_ADDR4,$this->opt[self::C_CONTACT][self::C_CONT_ADDR4]) ;
		$xml .= $this->createXmlElement(self::C_CONT_ADDR5,$this->opt[self::C_CONTACT][self::C_CONT_ADDR5]) ;
		$xml .= $this->createXmlElement(self::C_CONT_TEL,$this->opt[self::C_CONTACT][self::C_CONT_TEL]);
		$xml .= $this->createXmlElement(self::C_CONT_FAX,$this->opt[self::C_CONTACT][self::C_CONT_FAX]);
		$xml .= $this->createXmlElement(self::C_CONT_EMAIL,$this->opt[self::C_CONTACT][self::C_CONT_EMAIL]);
		$xml .= $this->createXmlElement(self::C_CONT_WEB,$this->opt[self::C_CONTACT][self::C_CONT_WEB]);
		$xml .= '</' . self::C_CONTACT . '>';
		
		$xml .= '<' . self::C_SETTING . '>';
		$xml .= $this->createXmlElement(self::C_SET_CPF_NO,$this->opt[self::C_SETTING][self::C_SET_CPF_NO]) ;
		$xml .= $this->createXmlElement(self::C_SET_REF_NO,$this->opt[self::C_SETTING][self::C_SET_REF_NO]) ;
		$xml .= $this->createXmlElement(self::C_SET_CPF_REF,$this->opt[self::C_SETTING][self::C_SET_CPF_REF]) ;
		$xml .= $this->createXmlElement(self::C_SET_BANK,$this->opt[self::C_SETTING][self::C_SET_BANK]) ;
		$xml .= $this->createXmlElement(self::C_SET_BANK_ACCT,$this->opt[self::C_SETTING][self::C_SET_BANK_ACCT]) ;
		$xml .= '</' . self::C_SETTING . '>';
		
		$xml .= '<' . self::C_IRAS . '>';
		$xml .= $this->createXmlElement(self::C_IRAS_NAME,$this->opt[self::C_IRAS][self::C_IRAS_NAME]) ;
		$xml .= $this->createXmlElement(self::C_IRAS_DEST,$this->opt[self::C_IRAS][self::C_IRAS_DEST]) ;
		$xml .= $this->createXmlElement(self::C_IRAS_CONTACT,$this->opt[self::C_IRAS][self::C_IRAS_CONTACT]) ;
		$xml .= $this->createXmlElement(self::C_IRAS_EGM,$this->opt[self::C_IRAS][self::C_IRAS_EGM]) ;
		$xml .= $this->createXmlElement(self::C_IRAS_BONUS,$this->opt[self::C_IRAS][self::C_IRAS_BONUS]) ;
		$xml .= $this->createXmlElement(self::C_IRAS_TAX_ID,$this->opt[self::C_IRAS][self::C_IRAS_TAX_ID]) ;
		$xml .= $this->createXmlElement(self::C_IRAS_ID_TYPE,$this->opt[self::C_IRAS][self::C_IRAS_ID_TYPE]) ;
		$xml .= '</' . self::C_IRAS . '>';
		
		$xml .= '</configuration>';
		return $xml ;
	}
	private function createXmlElement($tag,$value) {
		return '<' . $tag . '>' . $value . '</' . $tag . '>' ;
	}
	function initOptions() {
		$op = array() ;
		$op[self::C_CONT_NAME2] = "" ;
		$op[self::C_CONT_ADDR1] = "" ;
		$op[self::C_CONT_ADDR2] = "" ;
		$op[self::C_CONT_ADDR3] = "" ;
		$op[self::C_CONT_ADDR4] = "" ;
		$op[self::C_CONT_ADDR5] = "" ;
		$op[self::C_CONT_TEL] = "" ;
		$op[self::C_CONT_FAX] = "" ;
		$op[self::C_CONT_EMAIL] = "" ;
		$op[self::C_CONT_WEB] = "" ;
		$this->opt[self::C_CONTACT] = $op ; 
		
		$op = array();
		$op[self::C_SET_CPF_NO] = "" ;
		$op[self::C_SET_REF_NO] = "" ;
		$op[self::C_SET_CPF_REF] = "" ;
		$op[self::C_SET_BANK] = "" ;
		$op[self::C_SET_BANK_ACCT] = "" ;
		$this->opt[self::C_SETTING] = $op ;
		
		$op = array();
		$op[self::C_IRAS_NAME] = "" ;
		$op[self::C_IRAS_DEST] = "" ;
		$op[self::C_IRAS_CONTACT] = "" ;
		$op[self::C_IRAS_EGM] = "" ;
		$op[self::C_IRAS_BONUS] = "" ;
		$op[self::C_IRAS_TAX_ID] = "" ;
		$op[self::C_IRAS_ID_TYPE] = "" ;
		$this->opt[self::C_IRAS] = $op ;
	}
}
?>