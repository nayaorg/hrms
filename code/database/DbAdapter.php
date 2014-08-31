<?php
interface iDatabase {
	public function getTable($query,$params=null,$limit=0) ;
	public function initTable($table,$condition) ;
	public function getRow($query,$params=null) ;
	public function getNoOfRows($table) ;
	public function getRowsCount($table,$filters="",$params=null) ;
	public function deleteRows($sql,$params=null) ;
	public function updateRow($sql,$params=null) ;
	public function insertRow($sql,$params=null) ;
	public function insertRowGetId($sql,$params=null) ;
	public function executeSql($sql) ;
	public function querySql($sql) ;
	public function open() ;
	public function close() ;
	public function beginTran() ;
	public function commitTran() ;
	public function rollbackTran() ;
	public function getConnection() ;
	public function getDbType() ;
	public function isTableExist($table) ;
	public function fieldParam($field,$logicalOperator) ;
	public function valueParam($field,$value) ;
	public function formatValueParam($field) ;
	public static function getTableList($host,$db,$user,$pwd) ;
	public static function getDBList($host,$user,$pwd) ;
}
abstract class DbAdapter implements iDatabase {
	//protected variable.
	protected $conn ;
	private $dsn ;
	private $dbuser ;
	private $dbpwd ;
	private $dbopt ;
	//constructor/destructor
	function __construct($dsn,$user,$pwd,$opt) { 
		$this->dsn = $dsn ;
		$this->dbuser = $user ;
		$this->dbpwd = $pwd ;
		$this->dbopt = $opt ;
    } 
	function __destruct() { 
		if (!is_null($this->conn))
			$this->conn = null ;
    } 
	
	//public function
	public function executeSql($sql) {
		try {
			return $this->conn->exec($sql) ;
		} catch (Exception $e) {
			Log:write('[DbADapter][executeSql]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ; 
		}
	}
	public function querySql($sql) {
		try {
			return $this->conn->query($sql) ;
		} catch (Exception $e) {
			Log::write('[DbAdapter][querySql]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_QUERY) ; 
		}
	}
	public function open() {
		if (!is_null($this->conn))
			$this->conn = null ;
		try {
			$this->conn = new PDO($this->dsn,$this->dbuser, $this->dbpwd,$this->dbopt); 
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 
			return true ;
		} catch(Exception $e) { 
			Log::write('[DbAdapter][open]' . $e->getMessage(),$this->dsn) ;
			die("<h2>".Message::DB_ERR_CONN."</h2>"); 
		}
	}
	public function close() {
		if (!is_null($this->conn))
			$this->conn = null ;
	}
	public function beginTran() {
		if (!is_null($this->conn))
			$this->conn->beginTransaction(); 
	}
	public function commitTran() {
		if (!is_null($this->conn))
			$this->conn->commit() ;
	}
	public function rollbackTran() {
		if (!is_null($this->conn))
			$this->conn->rollBack() ;
	}
	
	public function initTable($table,$condition) {
		$sql = "delete from " . $table ;
		if (!empty($condition))
			$sql .= $condition ;
		try {
			$this->conn->execute($sql) ;
			return true ;
		} catch (Exception $e) {
			Log::write('[DbAdapter][initTable]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC);
 		}
	}
	public function getNoOfRows($table) {
		return $this->getRowsCount($table) ;
	}
	public function getRowsCount($table,$filters="",$params=null) {
		$sql = "select count(*) from " . $table . " as cnt ";
		if (!empty($filters))
			$sql .= " where " . $filters ;
		try {
			$stmt = $this->conn->prepare($sql);  
			if (is_array($params) && count($params) > 0) {
				foreach ($params as $param) {
					$stmt->bindValue($param['field'],$param['value']) ;
				}
			}
			$stmt->execute();  
			$cnt = $stmt->fetchColumn(); 
			$stmt = null ;
			return $cnt ;
		} catch (Exception $e) {
			Log::write('[DbAdapter][getRowsCount]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_QUERY) ;
		}
	}
	public function getRow($query,$params=null) {
		$sql = $query ;
		try {
			$stmt = $this->conn->prepare($sql , array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			if (is_array($params) && count($params) > 0) {
				foreach ($params as $param) {
					$stmt->bindValue($param['field'],$param['value']) ;
				}
			}
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_BOTH); 
			$stmt = null ;
		} catch (Exception $e) {
			Log::write('[DbAdapter][getRow]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_QUERY) ;
		}
	}
	public function deleteRows($sql,$params=null) {
		try {
			$stmt = $this->conn->prepare($sql);
			if (is_array($params) && count($params) > 0) {
				foreach ($params as $param) {
					$stmt->bindValue($param['field'],$param['value']) ;
				}
			}
			$stmt->execute();
			return true ;
			$stmt = null ;
		} catch (Exception $e) {
			Log::write('[DbAdapter][deleteRows]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ;
		}
	}
	public function updateRow($sql,$params=null) {
		try {
			$stmt = $this->conn->prepare($sql);
			if (is_array($params) && count($params) > 0) {
				foreach ($params as $param) {
					$stmt->bindValue($param['field'],$param['value']) ;
				}
			}
			$stmt->execute();
			$stmt = null ;
			return true ;
		} catch (Exception $e) {
			Log::write('[DbAdapter][updateRow]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ;
		}
	}
	public function insertRow($sql,$params=null) {
		try {
			$stmt = $this->conn->prepare($sql);
			if (is_array($params) && count($params) > 0) {
				foreach ($params as $param) {
					$stmt->bindValue($param['field'],$param['value']) ;
				}
			}
			$stmt->execute();
			$stmt = null ;
			return true ;
		} catch (Exception $e) {
			Log::write('[DbAdapter][insertRow]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ;
		}
	}
	public function fieldParam($field,$logicalOperator='=',$prefix="") {
		return $prefix.$field . " " . $logicalOperator . " :" . $field ;
	}
	public function valueParam($field,$value) {
		return array('field'=>':' . $field,'value'=>$value) ;
	}
	public function fieldValue($field,$value) {
		return array('field'=>$field,'value'=>$value) ;
	}
	public function formatValueParam($field) {
		return ":" . $field ;
	}
	//public property
	public function getConnection() {
		return $this->conn ;
	}
	
	//local function.
	protected function getPDOConstantType( $var ){
		//PDO::PARAM_LOB (integer) Represents the SQL large object data type. 
		//PDO::PARAM_STMT (integer) Represents a recordset type. Not currently supported by any drivers. 
		//PDO::PARAM_INPUT_OUTPUT (integer) Specifies that the parameter is an INOUT parameter for a stored procedure. You must bitwise-OR this value with an explicit PDO::PARAM_* data type. 
		if( is_int( $var ) )
			return PDO::PARAM_INT;
		if( is_bool( $var ) )
			return PDO::PARAM_BOOL;
		if( is_null( $var ) )
			return PDO::PARAM_NULL;
		//Default  
		return PDO::PARAM_STR;
	}
}
?>