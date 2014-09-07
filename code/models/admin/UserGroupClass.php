<?php
require_once (PATH_TABLES . "admin/UserGroupTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class UserGroupClass extends MasterBase {
	
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = UserGroupTable::C_TABLE ;
		$this->fldid = UserGroupTable::C_ID ;
		$this->flddesc = UserGroupTable::C_DESC ;
		$this->fldorg = UserGroupTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>