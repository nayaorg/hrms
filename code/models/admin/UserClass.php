<?php
require_once (PATH_TABLES . "admin/UserTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class UserClass extends MasterBase {
	private $fldname ;
	private $fldpwd1 ;
	private $fldpwd2 ;
	private $fldchngpwd ;
	private $fldcount ;
	private $fldlogin ;
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = UserTable::C_TABLE ;
		$this->fldid = UserTable::C_ID ;
		$this->fldorg = UserTable::C_ORG_ID ;
		$this->flddesc = UserTable::C_FULL_NAME ;
		$this->fldname = UserTable::C_NAME ;
		$this->fldchngpwd = UserTable::C_CHANGE_PWD ;
		$this->fldcount = UserTable::C_FAIL_COUNT ;
		$this->fldlogin = UserTable::C_LAST_LOGIN ;
		$this->fldpwd1 = UserTable::C_PWD1 ;
		$this->fldpwd2 = UserTable::C_PWD2 ;
	}
	function __destruct() {
	}
	function getRecordByName($orgid,$name) {
		$sql = "select * from " . $this->tbl . 
			" where " . $this->db->fieldParam($this->fldname)  .
			" and " . $this->db->fieldParam($this->fldorg) ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldname,$name) ;
		$params[] = $this->db->valueParam($this->fldorg,$orgid) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ; 
	}
	function getIdByName($orgid,$name) {
		$row = $this->getRecordByName($orgid,$name) ;
		if (is_null($row))
			return -1 ;
		else 
			return $row[UserTable::C_ID] ;
	}
	function isNameFound($orgid,$name) {
		$filter = $this->db->fieldParam($this->fldname) . " and " . $this->db->fieldParam($this->fldorg) ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldname,$name) ;
		$params[] = $this->db->valueParam($this->fldorg,$orgid) ;
		if ($this->db->getRowsCount($this->tbl,$filter,$params) > 0 )
			return true ;
		else 
			return false ;
	}
	function changePassword($id,$pwd) {
		$sql = " update " . $this->tbl . " set " . $this->db->fieldParam($this->fldpwd1) . "," .
			$this->db->fieldParam($this->fldpwd2) . "," .
			$this->db->fieldParam($this->fldchngpwd) . 
			" where " . $this->db->fieldParam($this->fldid) ;
		$params = array();
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		$params[] = $this->db->valueParam($this->fldpwd1,$this->encryptPwd1($pwd)) ;
		$params[] = $this->db->valueParam($this->fldpwd2,$this->encryptPwd2($pwd)) ;
		$params[] = $this->db->valueParam($this->fldchngpwd,false) ;
		return $this->db->updateRow($sql,$params) ;
	}
	function resetPassword($id) {
		$sql = " update " . $this->tbl . " set " . 
			$this->db->fieldParam($this->fldchngpwd) . 
			" where " . $this->db->fieldParam($this->fldid) ;
		$params = array();
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		$params[] = $this->db->valueParam($this->fldchngpwd,true) ;
		return $this->db->updateRow($sql,$params) ;
	}
	function updateLogin($id) {
		$dte = date('Y-m-d H:i:s') ;
		$sql = " update " . $this->tbl . " set " . 
			$this->db->fieldParam($this->fldcount) . "," .
			$this->db->fieldParam($this->fldlogin) .
			" where " . $this->db->fieldParam($this->fldid) ;
		$params = array();
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		$params[] = $this->db->valueParam($this->fldlogin,$dte) ;
		$params[] = $this->db->valueParam($this->fldcount,0) ;
		return $this->db->updateRow($sql,$params) ;
	}
	function updateFailCount($id,$count) {
		$sql = " update " . $this->tbl . " set " . $this->db->fieldParam($this->fldcount) . 
			" where " . $this->db->fieldParam($this->fldid) ;
		$params = array();
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		$params[] = $this->db->valueParam($this->fldcount,$count) ;
		return $this->db->updateRow($sql,$params) ;
	}
	function encryptPwd1($pwd) {
		if ($pwd == "") {
			return $pwd ;
		} else  {
			return Util::createMD5($pwd,"","",true) ;
		}
	}
	function encryptPwd2($pwd) {
		if ($pwd == "") {
			return $pwd ;
		} else  {
			return Util::createSHA1($pwd,"","",true) ;
		}
	}
}
?>