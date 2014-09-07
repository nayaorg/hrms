<?php
require_once (PATH_CODE . "database/DbAdapter.php") ;
class MySqlDb extends DbAdapter { 

    public function __construct($host,$db,$user,$pwd,$port="") { 
		$dbname = empty($db) ? '' : ";dbname=$db"; 
		if (empty($host)) $host = 'localhost'; 
    
		if ($host[0] === '/') { 
			$c = "unix_socket=$DBHost"; 
		} 
		else 
		{ 
			$c = "host=$host" ;
			if (!empty($port))
				$c .= ";port=$port"; 
		} 
    
        $dsn = "mysql:$c$dbname" ;
		$opt = array() ;
		parent::__construct($dsn,$user,$pwd,$opt);
    } 
 
    public function __destruct() { 
        parent::__destruct() ;
    } 
	
	
	public function getTable($query,$params=null,$limit=0) {
		$sql = $query ;
		
		if ($limit > 0)
			$sql .= " limit " . $limit ;
		
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
			throw new Exception("Sorry, we are having problem in processing your request.") ;
		}
	}
	public function insertRowGetId($sql,$params=null) {
		$sql = $sql .= ";SELECT LAST_INSERT_ID() as id";
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
			throw new Exception("Sorry, we are having problem in processing your request.") ;
		}
	}
	
	public function getDbType() {
		return 'mysql' ;
	}
	public function isTableExist($table) {
		return true ;
	}
	
	public static function getTableList($dbhost,$db,$user,$pwd) {
		return null ;
	}
	public static function getDBList($host,$user,$pwd){
		return null ;
	}
	public static function testConnection($host,$dbname,$dbuser,$dbpwd,$dbport="") {
		$dbname = empty($db) ? '' : ";dbname=$db"; 
		if (empty($host)) $host = 'localhost'; 
    
		if ($host[0] === '/') { 
			$c = "unix_socket=$DBHost"; 
		} 
		else 
		{ 
			$c = "host=$host" ;
			if (!empty($port))
				$c .= ";port=$port"; 
		} 
    
        $dsn = "mysql:$c$dbname" ;
		$opt = array() ;
		$conn = new PDO($dsn,$dbuser, $dbpwd,$opt); 
		$conn = null ;
	}
} 

?>