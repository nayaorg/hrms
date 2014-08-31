<?php
require_once (PATH_CONTROLLERS . "Common.php") ;

class Config {
	private $type = "" ;
	private $datas = array() ;
	
	function __construct() {
	}
	function __destruct() {
		unset($this->datas) ;
	}
	public function processRequest($params) {
		$this->type = REQ_VIEW ;
		try {
			if (isset($params) && count($params) > 0) {
				if (isset($params['type']))
					$this->type = $params['type'] ;
			}
			switch ($this->type) {
				case REQ_UPDATE:
					$this->saveConfig($params) ;
					break ;
				case REQ_GET:
					$this->getUserKey() ;
					break ;
				case REQ_QUERY:
					$this->testConnection($params) ;
					break ;
				case REQ_VIEW:
					$this->getView() ;
					break ;
				default:
					$this->sendJsonResponse(Status::Error,"invalid request.","",$this->type) ;
					break ;
			}
			return true ;
		} catch (Exception $e) {
			die ($e->getMessage()) ;
		}
	}
	private function saveConfig($params) {
		try {
			
			$pwd = Util::decryptData($this->getParam($params,'dbpwd',""),$_SESSION['key']) ;
			$config = new ConfigDb(PATH_CODE . 'db.xml') ;
			$config->loadConfig() ;
			$config->setHost($this->getParam($params,'server',""));
			$config->setDbName($this->getParam($params,'dbname',"")) ;
			$config->setUserName($this->getParam($params,'dbuser',"")) ;
			$config->setPassword($pwd);
			$port = $this->getParamInt($params,'dbport',0) ;
			if ($port > 0)
				$config->setPortNo($port) ;
			else
				$config->setPortNo("") ;
			$config->setDbType($this->getParam($params,'dbtype',DbType::MsSql)) ;
			$config->saveConfig() ;
			$this->sendJsonResponse(Status::Ok,"System Configuration successfully saved.","",$this->type) ;
		} catch (Exception $e) {
			Log::write('[Admin]' . $e->getMessage());
			$this->sendJsonResponse(Status::Error,"Sorry, there is a error in updating system configuration.","",$this->type) ;
		}
		unset($coy) ;
	}
	private function testConnection($params) {
		$status = "" ;
		$mesg = "" ;
		$pwd = Util::decryptData($this->getParam($params,'dbpwd',""),$_SESSION['key']) ;
		$host = $this->getParam($params,'server',"") ;
		$name = $this->getParam($params,'dbname',"") ;
		$user = $this->getParam($params,'dbuser',"") ;
		$port = $this->getParam($params,'dbport',"") ;
		$type = $this->getParam($params,'dbtype',"") ;
		if ($type == "") {
			$status = Status::Error ;
			$mesg = "Missing database type info." ;
		}
		if ($status == "") {
			if ($host == "" || $name == "") {
				$status = Status::Error ;
				$mesg = "Missing Server/Database info." ;
			}
		}
		if ($status == "") {
			if ($user == "" || $pwd == "") {
				$status = Status::Error ;
				$mesg = "Missing Database user name/password info." ;
			}
		}
		if ($status == "") {
			try {
				if ($type == DbType::MySql) {
					MySqlDb::testConnection($host,$name,$user,$pwd,$port) ;
				} else if ($type == DbType::MySqli) {
					MySqliDb::testConnection($host,$name,$user,$pwd,$port) ;
				} else {
					MsSqlDb::testConnection($host,$name,$user,$pwd,$port) ;
				}
				$mesg = "Database Connection Testing - Ok."  ;
			} catch (Exception $e) {
				$status = Status::Error ;
				$mesg = $e->getMessage() ;
			}
		}
		echo $mesg ;
	}
	private function getUserKey() {
		$key = Util::createMD5(time().'planetz',"","",true) ;
		$_SESSION['key'] = $key ;
		$this->sendJsonResponse(Status::Ok,"",$key,$this->type) ;
	}
	private function loadConfig() {
		$config = new ConfigDb(PATH_CODE . 'db.xml') ;
		$config->loadConfig() ;
		$this->datas['server'] = $config->getHost();
		$this->datas['dbname'] = $config->getDbName();
		$this->datas['dbuser'] = $config->getUserName() ;
		$this->datas['dbpwd'] = $config->getPassword();
		$this->datas['dbtype'] = $config->getDbType() ;
		$port = (int)$config->getPortNo() ;
		if ($port > 0)
			$this->datas['dbport'] = $port ;
		else
			$this->datas['dbport'] = "" ;
		unset($config) ;
	}
	private function getDbName() {
		return $this->datas['dbname'] ;
	}
	private function getServer() {
		return $this->datas['server'] ;
	}
	private function getDbUser() {
		return $this->datas['dbuser'] ;
	}
	private function getDbPwd() {
		return $this->datas['dbpwd'] ;
	}
	private function getDbPort() {
		return $this->datas['dbport'] ;
	}
	private function getDbType() {
		return $this->datas['dbtype'] ;
	}
	private function getView() {
		$this->loadConfig();
		ob_start() ;
		include (PATH_VIEWS . "admin/ConfigView.php") ;
		echo Util::minifyHtml(ob_get_clean()) ;
	}
	private function getTypeList() {
		$arr = array() ;
		$arr[] = array ('code'=>DbType::MsSql,'desc'=>'Microsoft SQL Server' );
		$arr[] = array ('code'=>DbType::MySql,'desc'=>'MySql' ) ;
		$arr[] = array ('code'=>DbType::PostgreSql,'desc'=>'Postgres Sql' ) ;
		$arr[] = array ('code'=>DbType::MySqli,'desc'=>'MySqli' ) ;
		return Util::createOptionValue($arr,$this->getDbType()) ;
	}
	private function sendJsonResponse($status="",$mesg="",$data="",$type="") {
		header('Content-type: application/json');
		$arr = array(FIELD_STATUS => $status, FIELD_MESG => $mesg, FIELD_DATA => $data, FIELD_TYPE => $type);
		echo json_encode($arr) ;
	}
	private function getParam($params,$key,$default) {
		if (!isset($params[$key]) || empty($params[$key])) {
			return $default ;
		} else {
			return $params[$key] ;
		}
	}
	private function getParamInt($params,$key,$default) {
		$result = $default ;
		if (isset($params[$key]) && $params[$key] != "") {
			if (is_numeric($params[$key]))
				$result = $params[$key] ;
		}
		return $result ;
	}
}
?>