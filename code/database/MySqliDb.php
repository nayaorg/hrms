<?php
require_once (PATH_CODE . "database/DbAdapter.php") ;
class MySqliDb implements iDatabase { 
	private $conn ;
	private $host ;
	private $dbuser ;
	private $dbpwd ;
	private $dbport ;
	private $dbname ;
	
    public function __construct($host,$db,$user,$pwd,$port="") { 
		$this->dbname = $db ;
		$this->dbuser = $user ;
		$this->dbpwd = $pwd ;
		$this->dbport = $port ;
		$this->host = $host ;
    } 
 
    public function __destruct() { 
        if (!is_null($this->conn))
			$this->conn->close() ;
    } 
	public function executeSql($sql) {
		try {
			return $this->conn->exec($sql) ;
		} catch (Exception $e) {
			Log:write('[MySqliDb][executeSql]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ; 
		}
	}
	public function querySql($sql) {
		try {
			return $this->conn->query($sql) ;
		} catch (Exception $e) {
			Log::write('[MySqliDb][querySql]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_QUERY) ; 
		}
	}
	
	public function open() {
		if (!is_null($this->conn)) {
			//$this->conn->close() ;
			$this->conn = null ;
		}
		try {
			if ($this->dbport == "")
				$this->conn = new mysqli($this->host,$this->dbuser, $this->dbpwd,$this->dbname); 
			else 
				$this->conn = new mysqli($this->host,$this->dbuser, $this->dbpwd,$this->dbname,$this->dbport); 
				
			if ($this->conn->connect_errno) {
				die('Database Connection Error : ' . $this->conn->connect_errno . "-" . $this->conn->connect_error) ;
			} else {
				return true ;
			}
		} catch(Exception $e) { 
			Log::write('[MySqliDb][open]' . $e->getMessage(),$this->host . ':' . $this->dbname) ;
			die("<h2>".Message::DB_ERR_CONN."</h2>"); 
		}
	}
	public function close() {
		if (!is_null($this->conn)) {
			$this->conn->close() ;
			$this->conn = null ;
		}
	}
	public function beginTran() {
		if (!is_null($this->conn))
			$this->conn->autocommit(false) ;
	}
	public function commitTran() {
		if (!is_null($this->conn))
			$this->conn->commit() ;
	}
	public function rollbackTran() {
		if (!is_null($this->conn))
			$this->conn->rollback() ;
	}
	
	public function initTable($table,$condition) {
		$sql = "delete from " . $table ;
		if (!empty($condition))
			$sql .= $condition ;
		try {
			$this->execStmt($sql) ;
			return true ;
		} catch (Exception $e) {
			Log::write('[MySqliDb][initTable]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC);
 		}
	}
	public function getNoOfRows($table) {
		return $this->getRowsCount($table) ;
	}
	public function getRowsCount($table,$filters="",$params=null) {
		$sql = "select count(*) from " . $table . " as cnt ";
		$cnt = 0 ;
		if (!empty($filters))
			$sql .= " where " . $filters ;
		try {
			$stmt = $this->conn->prepare($sql) ;
			$p = $this->prepareParam($params) ;
			call_user_func_array(array($stmt, 'bind_param'), $this->refValues($p));    
			$stmt->execute();
			$stmt->bind_result($cnt);
			$stmt->fetch() ;
			$stmt->close() ;
			return $cnt ;
		} catch (Exception $e) {
			Log::write('[MySqliDb][getRowsCount]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_QUERY) ;
		}
	}
	public function getRow($query,$params=null) {
		return $this->execGetStmt($query,$params) ;
	}
	public function deleteRows($sql,$params=null) {
		try {
			if ($stmt = $this->conn->prepare($sql)) {
				$p = $this->prepareParam($params) ;
				call_user_func_array(array($stmt, 'bind_param'), $this->refValues($p));
            
				$stmt->execute();
				return true ;
			} else {
				return false ;
			}
		} catch (Exception $e) {
			Log::write('[MySqliDb][deleteRows]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ;
		}
	}
	public function updateRow($sql,$params=null) {
		try {
			if ($stmt = $this->conn->prepare($sql)) {
				$p = $this->prepareParam($params) ;
				call_user_func_array(array($stmt, 'bind_param'), $this->refValues($p));
				$stmt->execute();
				return true ;
			} else {
				return false ;
			}
		} catch (Exception $e) {
			Log::write('[MySqliDb][updateRow]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ;
		}
	}
	public function insertRow($sql,$params=null) {
		try {
			if ($stmt = $this->conn->prepare($sql)) {
				$p = $this->prepareParam($params) ;
				call_user_func_array(array($stmt, 'bind_param'), $this->refValues($p));
            
				$stmt->execute();
				return true ;
			} else {
				return false ;
			}
		} catch (Exception $e) {
			Log::write('[MySqliDb][insertRow]' . $e->getMessage(),$sql) ;
			throw new Exception(Message::DB_ERR_EXEC) ;
		}
	}
	public function fieldParam($field,$logicalOperator='=',$prefix="") {
		return $prefix.$field . " " . $logicalOperator . "?" ;
	}
	public function valueParam($field,$value) {
		return array('field'=>'?','value'=>$value) ;
	}
	public function fieldValue($field,$value) {
		return array('field'=>$field,'value'=>$value) ;
	}
	public function formatValueParam($field) {
		return "?" ;
	}
	//public property
	public function getConnection() {
		return $this->conn ;
	}
	
	public function getTable($query,$params=null,$limit=0) {
		$sql = $query ;
		
		if ($limit > 0)
			$sql = $query . " limit " . $limit ;
		else 
			$sql = $query ;
		
		try {
			return $this->execGetStmt($sql,$params);
		} catch (Exception $e) {
			throw new Exception("Sorry, we are having problem in processing your request.") ;
		}
	}
	public function insertRowGetId($sql,$params=null) {
		try {
			if ($stmt = $this->conn->prepare($sql)) {
				$p = $this->prepareParam($params) ;
				call_user_func_array(array($stmt, 'bind_param'), $this->refValues($p));
            
				$stmt->execute();
				return  $stmt->insert_id ;
			} else {
				throw new Exception("Error in prepare statement. " . $sql . " Error : " . $this->conn->connect_error) ;
			}
		} catch (Exception $e) {
			Log::write('[MySqliDb][insertRowGetId]' . $e->getMessage(),$sql) ;
			throw new Exception("Sorry, we are having problem in processing your request.") ;
		}
	}
	
	public function getDbType() {
		return 'mysqli' ;
	}
	public function isTableExist($table) {
		return true ;
	}
	private function execGetStmt($sql, $params=null){
		$stmt = $this->conn->prepare($sql) ;
		$p = $this->prepareParam($params) ;
		call_user_func_array(array($stmt, 'bind_param'), $this->refValues($p));    
		$stmt->execute();
		$results = $this->fetchResult($stmt) ;
		//$stmt->store_result();
        //$meta = $stmt->result_metadata();
        //$cols = array() ; 
		//$col = array() ;
        //while ( $field = $meta->fetch_field() ) {
			//$cols[] = &$col[$field->name];
		//}
		//$meta->close() ;
		//$results = array() ;
		//call_user_func_array(array($stmt, 'bind_result'), $this->refValues($cols));
		
        //while ( $stmt->fetch() ) {  
			//$x = array();  
			//foreach( $row as $key => $val ) {  
				//$x[$key] = $val;  
			//}  
			//$results[] = $x;  
		//}
		//$stmt->free_result() ;
        $stmt->close();
		return  $results;
    }
	private function execStmt($sql, $params=null){
		if ($stmt = $this->conn->prepare($sql)) {
			$p = $this->prepareParam($params) ;
			call_user_func_array(array($stmt, 'bind_param'), $this->refValues($p));
            
			$stmt->execute();
			$result = $this->conn->affected_rows;
			return  $result;
		} else {
			return 0 ;
		}
    }
	private function refValues($arr){
        if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
        {
			$refs = array();
            foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
            return $refs;
        }
		return $arr;
	}
	private function fetchResult($result) {    
		$array = array();
     
		if ($result instanceof mysqli_stmt) {
			$result->store_result();
         
			$variables = array();
			$data = array();
			$meta = $result->result_metadata();
         
			while($field = $meta->fetch_field())
				$variables[] = &$data[$field->name]; // pass by reference
         
			call_user_func_array(array($result, 'bind_result'), $variables);
         
			$i=0;
			while($result->fetch()) {
				$array[$i] = array();
				foreach($data as $k=>$v) {
					$array[$i][$k] = $v;
				}
				$i++;
             
				// don't know why, but when I tried $array[] = $data, I got the same one result in all rows
			}
		} elseif ($result instanceof mysqli_result) {
			while($row = $result->fetch_assoc())
				$array[] = $row;
		}
     
		return $array;
	}
 
	private function prepareParam($params=null) {
		$arr = array() ;
		$type = "";
		if (is_array($params) && count($params) > 0) {
			$values = array() ;
			foreach ($params as $param) {
				if (is_int($param['value']))
					$type .= 'i' ;
				else if (is_float($param['value']))
					$type .= 'd' ;
				else
					$type .= 's' ;
				$values[] = $param['value'] ;
			}
			$arr = array_merge(array($type),$values) ;
		} 
		return $arr ;
	}
	public static function getTableList($dbhost,$db,$user,$pwd) {
		return null ;
	}
	public static function getDBList($host,$user,$pwd){
		return null ;
	}
	public static function testConnection($host,$dbname,$dbuser,$dbpwd,$dbport="") {
		if ($dbport=="")
			$conn = new mysqli($host,$dbuser, $dbpwd,$dbname) ;
		else
			$conn = new mysqli($host,$dbuser,$dbpwd,$dbname,$dbport) ;
		if ($conn->connect_errno) {
			throw new Exception("Connection Error : " . $conn->connect_errno . "," . $conn->connect_error) ;
		} else {
			$conn->close() ;
		}
	}
} 

?>