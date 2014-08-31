<?php
require_once (PATH_TABLES . "attendance/ShiftDetailTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class ShiftDetailClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ShiftDetailTable::C_TABLE ;
		$this->fldid = ShiftDetailTable::C_ID ;
		$this->fldtype = ShiftDetailTable::C_SHIFT_TYPE ;
		$this->fldgroup = ShiftDetailTable::C_SHIFT_GROUP_ID ;
		$this->fldorg = ShiftDetailTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function getTimeCardID($id) {
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam($this->fldtype) ." AND "    ;
		$params = array() ;
		$params[] = $this->db->valueParam($this->fldid,$id) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (is_null($rows) || count($rows) == 0)
			return null ;
		else 
			return $rows[0] ;
	}
}
?>