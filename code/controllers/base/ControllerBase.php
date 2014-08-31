<?php
abstract class ControllerBase {
	protected $db ;
	protected $orgid = 0 ;
	protected $fldorg = "" ;
	
	abstract function ProcessRequest($params) ;
	protected function sendJsonResponse($status="",$mesg="",$data="",$type="") {
		header('Content-type: application/json');
		$arr = array(FIELD_STATUS => $status, FIELD_MESG => $mesg, FIELD_DATA => $data, FIELD_TYPE => $type);
		echo json_encode($arr) ;
	}
	protected function sendTextResponse($status="",$mesg="",$data="",$type="") {
		header('Content-type: text/plain');
		echo $status . "|" . $mesg . "|" . $data . "|" . $type ;
	}
	protected function sendImageResponse($file) {
		header('Content-type: image/jpg');
		echo $file;
	}
	protected function sendHtmlResponse($data="") {
		header('Content-type: text/html');
		echo $data ;
	}
	protected function sendPdfResponse($data=null) {
		header('Content-type: application/pdf') ;
		echo $data ;
	}
	protected function getParam($params,$key,$default) {
		if (!isset($params[$key]) || empty($params[$key])) {
			return $default ;
		} else {
			return $params[$key] ;
		}
	}
	protected function getParamDate($params,$key,$default) {
		$result = $default ;
		if (isset($params[$key]) && !empty($params[$key])) {
			$dte = explode("/", $params[$key]);
			if (count($dte) == 3) {
				$d = $dte[0] ;
				$m = $dte[1] ;
				$y = $dte[2] ;
				if (checkdate($m,$d,$y))
					$result = $y . "-" . $m . "-" . $d ;
			}
		}
		return $result ;
	}
	protected function getParamInt($params,$key,$default) {
		$result = $default ;
		if (isset($params[$key]) && $params[$key] != "") {
			if (is_numeric($params[$key]))
				$result = $params[$key] ;
		}
		return $result ;
	}
	protected function getParamNumeric($params,$key,$default) {
		$result = $default ;
		if (isset($params[$key]) && $params[$key] != "") {
			if (is_numeric($params[$key]))
				$result = $params[$key] ;
		}
		return $result ;
	}
	protected function getValueList($table,$code,$desc,$default=null,$filters=null) {
		$sql = "select " . $code ."," . $desc . " from " . $table ;
		$params = array() ;
		if (is_array($filters) && count($filters) > 0) {
			$cond = " where " ;
			foreach ($filters as $data) {
				$sql .= $cond . $this->db->fieldParam($data['field']) ;
				$cond = " and " ;
				$params[] = $this->db->valueParam($data['field'],$data['value']) ;
			}
		}
		
		$vls = array() ;
		if (!is_null($default) && count($default) > 0)
			$vls[] = array ('code'=>$default['code'],'desc'=>$default['desc']) ;
		$rows = $this->db->getTable($sql,$params) ;
		if (!is_null($rows) && count($rows) > 0) {
			foreach ($rows as $row) {
				$vls[] = array ('code'=>$row[$code],'desc'=>$row[$desc]) ;
			}
		}
		return $vls ;
	}
	protected function getDescription($table,$code,$desc,$id) {
		$result = "" ;
		$sql = "select " . $desc . " from " . $table . 
			" where " . $this->db->fieldParam($code) ;
		$params = array() ;
		$params[] = $this->db->valueParam($code,$id) ;
		$rows = $this->db->getRow($sql,$params) ;
		if (!is_null($rows) && count($rows) > 0) {
			$result = $rows[0][$desc] ;
		}
		return $result ;
	}
	protected function createPdfItem($t="",$w=0,$h=0,$a="L",$b="0",$nr="0") {
		return array('width'=>$w,'height'=>$h,'text'=>$t,'border'=>$b,'align'=>$a,'newrow'=>$nr) ;
	}
} 