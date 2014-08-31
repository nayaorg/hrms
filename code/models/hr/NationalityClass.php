<?php
require_once (PATH_TABLES . "hr/NationalityTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class NationalityClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = NationalityTable::C_TABLE ;
		$this->fldid = NationalityTable::C_ID ;
		$this->flddesc = NationalityTable::C_DESC ;
		$this->fldorg = NationalityTable::C_ORG_ID ;
	}
	function __destruct() {
	}
}
?>