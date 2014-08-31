<?php 
require_once (PATH_CODE . "database/DbAdapter.php") ;
class MsSqlDb extends DbAdapter { 

    public function __construct($host,$db,$user,$pwd,$port="") { 
		$dsn = "sqlsrv:Server=" . $host ;
		if (!empty($port))
			$dsn .= "," . $port ;
		$dsn .= ";Database=" . $db;
		$opt = array() ;
		parent::__construct($dsn,$user,$pwd,$opt);
		//sqlsrv_configure("WarningsReturnAsErrors", 0);
		//sqlsrv_configure("LogSubsystems", SQLSRV_LOG_SYSTEM_ALL); 
		//SQLSRV_LOG_SYSTEM_ALL (-1) SQLSRV_LOG_SYSTEM_OFF (0) SQLSRV_LOG_SYSTEM_INIT (1) SQLSRV_LOG_SYSTEM_CONN (2) SQLSRV_LOG_SYSTEM_STMT (4) SQLSRV_LOG_SYSTEM_UTIL (8)
		//sqlsrv_configure("LogSeverity",SQLSRV_LOG_SEVERITY_ALL) ;
		//sqlsrv_configure("LogServrity",SQLSRV_LOG_SEVERITY_ERROR | SQLSRV_LOG_SEVERITY_WARNING) ; //SQLSRV_LOG_SEVERITY_NOTICE (4)
    } 
 
    public function __destruct() { 
        parent::__destruct() ;
    } 
	
	public function getTable($query,$params=null,$top=0) {
		if ($top > 0)
			$sql = str_replace("select ","select top " . $top . " ",$query) ;
		else 
			$sql = $query ;
		
		try {
			$stmt = $this->conn->prepare($sql , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			if (is_array($params) && count($params) > 0) {
				foreach ($params as $param) {
					$stmt->bindValue($param['field'],$param['value']) ;
				}
			}
			$stmt->execute();
			return  $stmt->fetchAll(PDO::FETCH_ASSOC); 
			$stme = null ;
		} catch (Exception $e) {
			Log::write($e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_QUERY) ;
		}
	}
	
	public function insertRowGetId($sql,$params=null) {
		$sql = $sql . "; SELECT SCOPE_IDENTITY()" ;
		$id = 0 ;
		try {
			$stmt = $this->conn->prepare($sql);
			if (is_array($params) && count($params) > 0) {
				foreach ($params as $param) {
					$stmt->bindValue($param['field'],$param['value']) ;
				}
			}
			$stmt->execute();
			$stmt->nextRowset() ;
			$id = $stmt->fetchColumn() ; 
			$stmt = null ;
			return $id ;
		} catch (Exception $e) {
			Log::write($e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ;
		}
	}
	public function isTableExist($table) {
		return true ;
	}
	public function getDBType() {
		return 'mssql' ;
	}
	public static function getTableList($dbhost,$db,$user,$pwd) {
		return null ;
	}
	public static function getDBList($host,$user,$pwd){
		return null ;
	}
	public static function testConnection($host,$dbname,$dbuser,$dbpwd,$dbport="") {
		$dsn = "sqlsrv:Server=" . $host ;
		if (!empty($dbport))
			$dsn .= "," . $dbport ;
		$dsn .= ";Database=" . $dbname;
		$opt = array() ;
		$conn = new PDO($dsn,$dbuser, $dbpwd,$opt); 
		$conn = null ;
	}
} 

?>