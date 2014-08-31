<?php
class ConfigDb {

	const TAG_TYPE = "type" ;
	const TAG_HOST = "host" ;
	const TAG_NAME = "dbname" ;
	const TAG_PORT = "port" ;
	const TAG_USER = "username" ;
	const TAG_PWD = "password" ;
	const TAG_PERSIT = "persistent" ;
	
	private $type ;
	private $dbname ;
	private $host ;
	private $username ;
	private $pwd ;
	private $portno ;
	private $persistent ;
	private $file ;
	
	function setDbType($value) {
		$this->type = $value ;
	}
	function getDbType() {
		return $this->type ;
	}
	function setDbName($value) {
		$this->dbname = $value ;
	}
	function getDbName() {
		return $this->dbname ;
	}
	function setHost($value) {
		$this->host = $value ;
	}
	function getHost() {
		return $this->host ;
	}
	function setUserName($value) {
		$this->username = $value ;
	}
	function getUserName() {
		return $this->username ;
	}
	function setPassword($value) {
		$this->pwd = $value ;
	}
	function getPassword() {
		return $this->pwd ;
	}
	function setPortNo($value) {
		$this->portno = $value ;
	}
	function getPortNo() {
		return $this->portno ;
	}
	function setPersistent($value) {
		$this->persistent = $value ;
	}
	function getPersistent() {
		return $this->persistent ;
	}
	function setFileName($value) {
		$this->file = $value ;
	}
	function getFileName() {
		return $this->file ;
	}
	
	function __construct($filename) {
		$this->initData() ;
		$this->file = $filename ;
		//$fn = $filename ;
		//touch($fn);		//for win server environment bug
		//$this->file = realpath($fn);	//for win server environment bug
	}
	function __destruct() {
	}
	function loadConfig() {
		$this->readSimpleXml() ;
	}
	function saveConfig() {
		$this->saveXml() ;
	}
	private function saveXml() {
		$v = "" ;
		$xw = new XMLWriter(); 
		$xw->openURI($this->file); 
		$xw->startDocument('1.0','utf-8'); 
		$xw->setIndent(4); 
		$xw->startElement("database"); 
        $xw->writeElement(self::TAG_TYPE, $this->type); 
        $xw->writeElement(self::TAG_NAME, $this->dbname); 
        $xw->writeElement(self::TAG_HOST, $this->host); 
        $xw->writeElement(self::TAG_PORT, $this->portno); 
        $xw->writeElement(self::TAG_USER, $this->username);
		$xw->writeElement(self::TAG_PWD, bin2hex(Util::encryptString($this->pwd,"",""))) ;
		$xw->writeElement(self::TAG_PERSIT,$this->persistent) ;
		$xw->endElement(); 
		$xw->endDocument(); 
		$xw->flush(); 
		unset($xw) ;
	}
	private function readXml_unuse() {
		$this->initData() ;
		$tag = "" ;
		$value = "" ;
		if (file_exists($this->file)) {
			$xr = new XMLReader();
			$xr->open($this->file);
			while($xr->read()) {
				if($xr->nodeType == XMLReader::ELEMENT) {
					$tag = $xr->localName ;
					switch ($tag) {
						case self::TAG_TYPE:
							$this->type = $xr->read() ;
							break ;
						case self::TAG_NAME:
							$this->dbname = $xr->read() ;
							break ;
						case self::TAG_HOST:
							$this->host = $xr->read() ;
							break ;
						case self::TAG_PORT:
							$this->portno = $xr->read() ;
							break ;
						case self::TAG_USER:
							$this->username = $xr->read() ;
							break ;
						case self::TAG_PWD:
							$p = $xr->read() ;
							if ($p == "")
								$this->pwd = $p ;
							else
								$this->pwd = trim(Util::decryptString(pack("H*",$p),"","")) ;
							break ;
						case self::TAG_PERSIT:
							$this->persistent = $xr->read() ;
							break ;
						default:
					}
				}
			}
			unset($reader) ;
		}
	}
	private function readSimpleXml() {
		//$xmlObject = simplexml_load_string($xml);
		if (file_exists($this->file)) {
			$xr = simplexml_load_file($this->file);
			if ($xr) {
				$this->type = (string) $xr->{self::TAG_TYPE} ;
				$this->dbname = (string) $xr->{self::TAG_NAME} ;
				$this->host = (string) $xr->{self::TAG_HOST} ;
				$this->portno = (string) $xr->{self::TAG_PORT} ;
				$this->username = (string) $xr->{self::TAG_USER} ;
				$this->persistent = (string) $xr->{self::TAG_PERSIT} ;
				
				$p = (string) $xr->{self::TAG_PWD}  ;
				if ($p == "")
					$this->pwd = $p ;
				else
					$this->pwd = trim(Util::decryptString(pack("H*",$p),"","")) ;
			}
			unset($xr) ;
		}
	}
	private function initData() {
		$this->type = DbType::MsSql ;
		$this->dbname = "" ;
		$this->host = "" ;
		$this->portno = "" ;
		$this->username = "" ;
		$this->pwd = "" ;
		$this->persistent = true ;
	}
}
?>