<?php
require_once (PATH_TABLES . "claims/TravelPlanTable.php") ;
require_once (PATH_TABLES . "claims/CountryTable.php") ;
require_once (PATH_MODELS . "base/ClaimBase.php") ;

class TravelPlanClass extends ClaimBase {

	function __construct($db) {
		$this->db = $db ;
		$this->tbl = TravelPlanTable::C_TABLE ;
		$this->fldid = TravelPlanTable::C_ID ;
		$this->flddesc = TravelPlanTable::C_DESC ;
		$this->fldorg = TravelPlanTable::C_ORG_ID ;
	}
	
	function __destruct() {
	}
	
	function getCountryTable($filter="",$orderby="",$params=null) {
		// $sql = "select * from " . $this->tbl ;
		// if (!empty($filter))
			// $sql .= " where " . $filter ;
		// if (!empty($orderby))
			// $sql .= " order by " . $orderby ;
			
		// $sql = "SELECT t.TRAVEL_TITLE, t.TRAVEL_DESC , c.COUNTRY_DESC FROM  TRAVEL_PLAN t " ;
		// $sql .= "LEFT JOIN COUNTRY c ON t.TRAVEL_COUNTRY = c.COUNTRY_ID" ;
		// $sql .= "WHERE t.TRAVEL_COUNTRY = c.COUNTRY_ID ;
		
		$sql = "SELECT t.TRAVEL_ID, t.TRAVEL_TITLE, t.TRAVEL_DESC , c.COUNTRY_DESC FROM " . $this->tbl . 
		" t , " . CountryTable::C_TABLE." c WHERE t.TRAVEL_COUNTRY = c.COUNTRY_ID";
		return $this->db->getTable($sql) ;
	}
}
?>