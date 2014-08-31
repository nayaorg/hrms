<?php
require_once (PATH_TABLES . "payroll/CpfAgeTable.php") ;
require_once (PATH_MODELS . "base/MasterBase.php") ;

class CpfAgeClass extends MasterBase {
	function __construct($db) {
		$this->db = $db ;
		$this->tbl = CpfAgeTable::C_TABLE ;
		$this->fldid = CpfAgeTable::C_ID ;
		$this->fldorg = CpfAgeTable::C_ORG_ID ;
		$this->flddesc = CpfAgeTable::C_DESC ;
	}
	function __destruct() {
	}
	function getAgeId($age) {
		$sql = " select * from " . $this->tbl 
			. " where " . CpfAgeTable::C_FROM . " <= " . $age
			. " and " . CpfAgeTable::C_TO . " >= " . $age ;

			$row = $this->db->getRow($sql) ;
		if (!is_null($row) && count($row) > 0) {
			return $row[0][$this->fldid] ;
		} else {
			return 0 ;
		}
	}
	function calculateAge($dob,$asatdate=null) {
		$age = 0 ;
		if (is_null($asatdate))
			$asatdate = new DateTime('now');
		//$asatdate = pay date.
		//age shall be applied from the first day of the month after the month of birth date
		$date = new DateTime($asatdate->format('Y-m-01'));
		$date = $date->add(new DateInterval('P1M')); 
		$age = $date->format('Y') - $dob->format('Y') ;
		if ($date->format('md') < $dob->format('md')) { 
			$age-- ; 
		}
		return $age ;
	}
}
?>