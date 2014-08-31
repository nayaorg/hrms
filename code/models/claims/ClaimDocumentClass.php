<?php
require_once (PATH_TABLES . "claims/ClaimDocumentTable.php") ;
require_once (PATH_MODELS . "base/ClaimBase.php") ;

class ClaimDocumentClass extends ClaimBase {

	function __construct($db) {
		$this->db = $db ;
		$this->tbl = ClaimDocumentTable::C_TABLE ;
		$this->fldid = ClaimDocumentTable::C_ID ;
		$this->fldid2 = ClaimDocumentTable::C_DOC ;
		$this->flddesc = ClaimDocumentTable::C_DESC ;
		$this->fldorg = ClaimDocumentTable::C_ORG_ID ;
	}

	function __destruct() {
	}

	function getDocs($id) {		
		$sql = "SELECT * FROM " .ClaimDocumentTable::C_TABLE . 
		" WHERE " . ClaimDocumentTable::C_ID . " = " . $id;
		
		$rows = $this->db->getTable($sql) ;
		
		if(is_null($rows) || count($rows) == 0){
			return null;
		}else {
			return $rows;
		}
	}

	function getFilePath($id, $doc_id) {		
		$sql = "SELECT * FROM " .ClaimDocumentTable::C_TABLE . 
		" WHERE " . ClaimDocumentTable::C_ID . " = " . $id . " AND " . ClaimDocumentTable::C_DOC . " = " . $doc_id;
		
		$rows = $this->db->getTable($sql) ;
		
		if(is_null($rows) || count($rows) == 0){
			return null;
		}else {
			return $rows[0];
		}
	}

	function deleteDoc($id, $doc_id) {		
		$sql = "delete from " . $this->tbl 
			. " where " . $this->db->fieldParam(ClaimDocumentTable::C_ID) 
			. " and " . $this->db->fieldParam(ClaimDocumentTable::C_DOC);

		$params = array() ;
		$params[] = $this->db->valueParam(ClaimDocumentTable::C_ID,$id) ;
		$params[] = $this->db->valueParam(ClaimDocumentTable::C_DOC,$doc_id) ;
		
		return $this->db->deleteRows($sql,$params) ;
	}

	function deleteDocs($id) {		
		$sql = "delete from " . $this->tbl 
			. " where " . $this->db->fieldParam(ClaimDocumentTable::C_ID) ;

		$params = array() ;
		$params[] = $this->db->valueParam(ClaimDocumentTable::C_ID,$id) ;
		
		return $this->db->deleteRows($sql,$params) ;
	}
}
?>