<?php
class License {
	const C_MODULE_ADMIN = "0" ;
    const C_MODULE_PAYROLL = "10" ;
    const C_MODULE_CLAIM = "11" ;
    const C_MODULE_COMM = "12" ;
    const C_MODULE_BONUS = "13";
    const C_MODULE_TIMESHEET = "14";
    const C_MODULE_RECRUIT = "15" ;
    const C_MODULE_BENEFIT = "16" ;
    const C_MODULE_HANDBOOK = "17" ;
    const C_MODULE_PORTAL = "18" ;
    const C_MODULE_PAYSLIP = "19";
    const C_MODULE_LEAVE = "20" ;
    const C_MODULE_BOOKING = "21" ;
    const C_MODULE_ATT = "22" ;
    const C_MODULE_PERFORMANCE = "23" ;
	const C_MODULE_LEARNING = "24";
	
	private $type ;
	private $ver ;
	private $product ;
	private $company ;
	private $key ;
	private $date ;
	private $expiry ;
	private $users ;
	private $sno ;
	private $modules ;
	
	function __construct() {
		$this->init() ;
	}
	function __destruct() {
	}
	function getType() {
		return $this->type ;
	}
	function setType($value) {
		$this->type = $value ;
	}
	function getVersion() {
		return $this->ver ;
	}
	function setVersion($value) {
		$this->type = $value ;
	}
	function getProductName() {
		return $this->product ;
	}
	function setProductName($value) {
		$this->product = $value ;
	}
	function getCompany() {
		return $this->company ;
	}
	function setCompany($value) {
		$this->company = $value ;
	}
	function getProductKey() {
		return $this->key ;
	}
	function setProductKey($value) {
		$this->key = $value ;
	}
	function getFileDate() {
		return $this->date ;
	}
	function setFileDate($value) {
		$this->date = $value ;
	}
	function getExpiryDate() {
		return $this->expiry ;
	}
	function setExpiryDate($value) {
		$this->expiry = $value ;
	}
	function getNoOfUsers() {
		return $this->users ;
	}
	function setNoOfUsers($value) {
		$this->users = $value ;
	}
	function getSerialNo() {
		return $this->sno ;
	}
	function setSerialNo($value) {
		$this->sno = $value ;
	}
	function getModules() {
		return $this->modules ;
	}
	function setModules($value) {
		$this->modules = $value ;
	}
	function read($file) {
		if (file_exists($file)) {
			try {
				$fh = fopen($file,'r') ;
				$enc = fread($fh,filesize($file)) ;
				fclose($fh) ;
				//$d = trim(Util::decryptString(trim(base64_decode($enc)),$key,$iv)) ;
				$d = $this->decryptLicense($enc) ;
				if (strlen($d) != 256) {
					throw new Exception('Invalid License data size - ' . strlen($d)) ;
				}
				$data = substr($d,0,144) ;
				$module = substr($d,144,80) ;
				$hash = substr($d,224,32) ;
				$cs = md5(substr($d,0,224)) ;
				if ($cs != $hash)
					throw new Exception('Invalid data checksum - ' .$cs . ':' .$hash) ;
				$this->init() ;
				$this->type = substr($data,0,2) ;
				$this->product = substr($data,2,30) ;
				$this->company = substr($data,32,60) ;
				$this->ver = substr($data,92,2) ;
				$this->key = substr($data,94,20) ;
				$dte = trim(substr($data,114,8)) ;
				if ($this->validateDate($dte)) {
					$this->date = substr($dte,0,4) . "-" . substr($dte,4,2) . "-" . substr($dte,6,2) ;
					if (date_create($this->date) > date_create('now'))
						throw new Exception('Invalid License/System Date - ' . $dte . ',' . date('Y-m-d')) ;
				} else {
					throw new Exception('Invalid License Date - ' . $dte) ;
				}
				$dte = trim(substr($data,122,8)) ;
				if ($dte == "") {
					$this->expiry = "" ;
				} else {
					if ($this->validateDate($dte)) {
						$this->expiry = substr($dte,0,4) . "-" . substr($dte,4,2) . "-" . substr($dte,6,2) ;
					} else {
						throw new Exception('Invalid Expiry Date - ' . $dte) ;
					}
				}
				$tmp = substr($data,130,4);
				if (is_numeric($tmp))
					$this->users = intval($tmp) ;
				else
					throw new Exception('Invalid Users Count - ' . $tmp) ;
				
				$this->sno = $this->extractSerialNo($this->key) ;
				$blank = substr($data,130,10) ;

				$this->setModule($module) ;
				
			} catch (Exception $e) {
				throw new Exception('Error in reading License File. ' . $e->getMessage()) ;
			}
		} else {
			throw new Exception('License file not found.');
		}
	}
	function save($file) {
		if (file_exists($file)) {
			unlink($file) ;
		}
	}
	private function init() {
		$type = "" ;
		$ver = "" ;
		$product = "";
		$company = "" ;
		$key = "" ;
		$date = "" ;
		$expiry = "" ;
		$users = 0 ;
		$this->initModule() ;
	}
	private function validateDate($date) {
		if ($date == "")
			return false ;
		$y = substr($date,0,4) ;
		$m = substr($date,4,2) ;
		$d = substr($date,6,2) ;
		return checkdate($m,$d,$y) ;
	}
	private function setModule($m) {
		if ($m == "")
			return ;
		$len = strlen($m) ;
		for ($i=0;$i < $len;$i++) {
			if ($m[$i] == "1")
				$this->modules[$i] = true ;
			else
				$this->modules[$i] = false ;
		}
	}
	private function initModule() {
		$this->moduels = array() ;
		for ($i=0;$i<80;$i++) {
			$this->modules[$i] = false ;
		}
	}
	private function encryptLicense($data) {
		$extra = 8 - (strlen($data) % 8);
		// add the zero padding
		if($extra > 0) {
			for($i = 0; $i < $extra; $i++) {
				$data .= "\0";
			}
		}
		// very simple ASCII key and IV
		$key = $this->getKey() ;
		$iv = $this->getIV() ;
		// hex encode the return value
		return base64_encode(mcrypt_cbc(MCRYPT_3DES, $key, $data, MCRYPT_ENCRYPT, $iv));
	}
	private function decryptLicense($encdata) {
		$data = base64_decode($encdata);
		$key = $this->getKey() ;
		$iv = $this->getIV() ;
		$enc = mcrypt_decrypt(MCRYPT_3DES, $key, $data, MCRYPT_MODE_CBC, $iv) ;
		return rtrim($enc, "\0") ;
	}
	private function getKey() {
		//return "cK7uR!pdz8ir%df93Lkdfyo4rsiu)oiE" ;
		return "cK7uR!pdz8ir%df93Lkdfyo4";
	}
	private function getIV() {
		return "K7do#l!9";
	}
	private function extractSerialNo($key) {
        $temp = $this->parse_Key($key);
        return $temp[11] . $temp[17] . $temp[14] . $temp[3] . $temp[5] . $temp[19] . $temp[0] . $temp[6];
	}
	private function parse_Key($key){
		$k = "";
		for ($j = 0; $j < strlen($key); $j++) {
			$k .= $this->reverse_Key($key[$j], $j + 1);
        }
		return $k;
	}
	private function reverse_Key($k, $p) {
		$char1 = array("C", "F", "4", "P", "2", "A", "Z", "6", "E", "B", "T", "W", "M", "X", "Q", "5" );
        $char2 = array("T", "M", "N", "4", "K", "W", "Y", "P", "U", "3", "1", "S", "B", "E", "D", "Z" );
        $char3 = array("X", "8", "J", "K", "Q", "9", "5", "S", "C", "H", "T", "A", "R", "4", "F", "G" );
        $char4 = array("Z", "D", "1", "G", "3", "K", "7", "V", "R", "E", "C", "Y", "U", "N", "6", "P" );

        switch ($p){
			case 3:
            case 8:
            case 11:
            case 16:
			case 20:
				return $this->reverse_KeyValue($char1, $k);
				break ;
            case 5:
			case 10:
            case 12:
            case 14:
            case 17:
				return $this->reverse_KeyValue($char2, $k);
				break ;
			case 2:
            case 7:
            case 9:
            case 15:
			case 19:
				return $this->reverse_KeyValue($char3, $k);
				break ;
			case 1:
			case 4:
            case 6:
            case 13:
            case 18:
				return $this->reverse_KeyValue($char4, $k);
				break ;
            default:
				return $k;
				break ;
        }
    }
	private function reverse_KeyValue($chars, $v) {
		$k = -1;
        $len = count($chars);
		for ($i = 0; $i < $len; $i++) {
            if (strtolower($chars[$i]) == strtolower($v)) {
                $k = $i;
                break;
            }
        }
        if ($k > -1)
            return dechex($k);
        else
            return $v;
    }
}
?>
