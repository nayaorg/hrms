<?php
require_once (PATH_TABLES . "attendance/HolidayTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class HolidayClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = HolidayTable::C_TABLE ;
		$this->fldid = HolidayTable::C_ID ;
		$this->flddate = HolidayTable::C_DATE ;	
		$this->flddesc = HolidayTable::C_DESC ;
		$this->fldorg = HolidayTable::C_ORG_ID ;
	}
	function __destruct() {
	}
	function isHoliday($date){
		$sql = "select * from " . $this->tbl
			. " where " . $this->db->fieldParam(HolidayTable::C_DATE) ;
			
		$params = array() ;
		$params[] = $this->db->valueParam(HolidayTable::C_DATE,$date) ;
			
		$rows = $this->db->getRow($sql,$params) ;
		return !(is_null($rows) || count($rows) == 0) ;
	}
}
?>