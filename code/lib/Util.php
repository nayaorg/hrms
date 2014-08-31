<?php
define("KEY_SALT_1","J3@LDkd%3!ldkwi6") ;
define("KEY_SALT_2","9KDI^ldk#lk29lQa")	;
define("KEY_IV","Kd$1f@4wxiu!dlj(8Od#1kdrxmfil0*d") ;

require_once(PATH_LIB . "xxtea.php") ;

class Util {
	public static function encryptString($text,$key,$iv) {
		return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
	}
	public static function decryptString($text,$key,$iv) {
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
	}
	public static function convertLink($link,$key="",$iv="") {
		//return $link ;
		if ($key == "")
			$key = $_SESSION[SE_ID] ;
		if ($iv == "")
			$iv = KEY_IV ;
		return bin2hex(Util::encryptString($link,$key,$iv));
	}
	public static function convertLink_old($link,$key = "",$iv = "") {
		if ($key == "")
			$key = $_SESSION[SE_ID] ;
		if ($iv == "")
			$iv = KEY_IV ;
		$enc = base64_encode(Util::encryptString($link,$key,$iv));
		$idx = 0 ;
		$cnt = 0 ;
		$len = strlen($enc) - 1 ;
		for ($i=$len; $i >= 0; $i--)
		{
			if ($enc[$i] == "=")
			{
				$cnt++ ;
				$idx = $i ;
			}
			else 
				break ;
		}
		$enc1 = $enc ;
		$enc = str_replace("+","-",$enc) ;
		$enc = str_replace("/","_",$enc) ;
		return trim(substr($enc,0,$idx)) . $cnt ;
	}
	public static function revertLink($text,$key = "",$iv = "") {
		//return $text ;
		if ($key == "")
			$key = $_SESSION[SE_ID] ;
		if ($iv == "")
			$iv = KEY_IV ;
		return trim(Util::decryptString(pack("H*" , $text),$key,$iv)) ;
	}
	public static function revertLink_old($text,$key = "",$iv = "") {
		if ($key == "")
			$key = $_SESSION[SE_ID] ;
		if ($iv == "")
			$iv = KEY_IV ;
		$last = substr(trim($text),strlen(trim($text))-1,1) ;
		$enc = substr(trim($text),0,strlen(trim($text)) -1) ;
		$enc = str_replace("-","+",$enc) ;
		$enc = str_replace("_","/",$enc) ;
		if ($last > 0)
		{
			for ($j=1;$j <= $last;$j++)
			{
				$enc .= "=" ;
			}
		}
		return trim(Util::decryptString(trim(base64_decode($enc)),$key,$iv)) ;
	}
	public static function createMD5($data,$key1="",$key2="",$hex=false) {
		if ($data == "") return "" ;
		if ($key1=="")
			$d = KEY_SALT_1 . $data ;
		else
			$d = $key . $data ;
		if ($key2 == "")
			$d .= KEY_SALT_2 ;
		else
			$d .= $key2 ;
		if ($hex)
			return md5($d) ;
		else 
			return base64_encode(md5($d,true));
	}
	public static function createSHA1($data,$key1="",$key2="",$hex=false) {
		if ($data == "") return "" ;
		if ($key1=="")
			$d = KEY_SALT_1 . $data ;
		else
			$d = $key1 . $data ;
		if ($key2=="")
			$d .= KEY_SALT_2 ;
		else
			$d .= $key2 ;
		if ($hex)
			return sha1($d) ;
		else
			return base64_encode(sha1($d,true)) ;
	}
	public static function createSHA256($data,$key1="",$key2="",$hex=false) {
		if ($data == "") return "" ;
		if ($key1=="")
			$d = KEY_SALT_1 . $data ;
		else
			$d = $key1 . $data ;
		if ($key2=="")
			$d .= KEY_SALT_2 ;
		else
			$d .= $key2 ;
		if ($hex)
			return sha256($d) ;
		else
			return base64_encode(sha256($d,true)) ;
	}
	public static function searchStringLink() {
		$result = Util::getListScript('A') ;
		$result .= Util::getListScript('B')  ;
		$result .= Util::getListScript('C') ;
		$result .= Util::getListScript('D') ;
		return $result ;
	}
	private static function getListScript($opt) {
		return "<a href =\"javascript:getList('" . $opt . "')\">" .$opt ."</a>&nbsp;&nbsp;" ;
	}
	public static function createOptionValue($valuelists,$default="") {
		$c = "" ;
		if (!is_null($valuelists) && count($valuelists) > 0) {
			foreach ($valuelists as $vl) {
				$c .= "<option " ;
				if ($default != "") {
					if ($default == $vl['code'])
						$c .= "selected=\"selected\" " ;
				}
				$c .= "value=\"" . $vl['code'] . "\">" . $vl['desc'] . "</option>" ;
			}
		}
		return $c ;
	}
	public static function getRemoteIP()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	public static function getFirstDate($date=null) {
		if (is_null($date))
			return new DateTime(date('Y-m-01')) ;
		else
			return DateTime($date->format('Y') . '-' . $date-format('m') . '-01');
	}
	public static function getLastDate($date=null) {
		if (is_null($date))
			return new DateTime(date('Y-m-t')) ;
		else 
			return new DateTime($date->format('Y') . '-' . $date->format('m') . '-' . $date->format('t'));
	}
	public static function getLastDay($year,$month) {
		$d = new DateTime($year . "-" . $month . "-01") ;
		return $d->format('t') ;
	}
	public static function calculateCpfAge($dob,$asatdate=null) {
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
	public static function getLogoFile($path,$orgid) {
		$files = Util::getFileList($path . $orgid,"logo*.*") ;
		if (count($files) > 0)
			return $files[0] ;
		else 
			return ;
	}
	public static function removeLogoFile($path,$orgid) {
		$files = Util::getFileList($path . $orgid,"logo*.*") ;
		foreach ($files as $f) {
			$fn = $path . $orgid. "\\" . $f ;
			if (file_exists($fn))
				unlink($fn) ;
		}
	}
	public static function validFileExt($ext,$validexts) {
		if ($ext != "") {
			foreach ($validexts as $e) {
				if (strtolower($ext) == strtolower($e))
					return true ;
			}
		}
		return false ;
	}
	public static function getFileList($path=".",$mask="*") {
		$dir = @ dir("$path"); 
		$files = array() ;
		if (!is_null($dir)) {
			while (($file = $dir->read()) !== false) 
			{ 
				if($file !="." && $file!=".." && fnmatch($mask, $file)) 
					$files[] = $file; 
			} 
			$dir->close(); 
		}
		return $files; 
	}
	public static function decryptData($data,$key) {
		return xxtea_decrypt(pack('H*',$data),$key) ;
	}
	public static function encryptData($data,$key) {
		return bin2hex(xxtea_encrypt($data, $key)) ;
	}
	public static function getMonthShortName($month) {
		if ($month == 1)
			return "Jan" ;
		elseif ($month == 2)
			return "Feb" ;
		elseif ($month == 3)
			return "Mar" ;
		elseif ($month == 4)
			return "Apr";
		elseif ($month == 5)
			return "May" ;
		elseif ($month == 6)
			return "Jun";
		elseif ($month == 7)
			return "Jul" ;
		elseif ($month == 8)
			return "Aug" ;
		elseif ($month == 9)
			return "Sep";
		elseif ($month == 10)
			return "Oct" ;
		elseif ($month == 11)
			return "Nov";
		elseif ($month == 12)
			return "Dec";
		else 
			return "";
	}
	public static function getMonthOption() {
		$vls = array() ;
		$vls[] = array('code'=>1,'desc'=>' Jan ') ;
		$vls[] = array('code'=>2,'desc'=>' Feb ') ;
		$vls[] = array('code'=>3,'desc'=>' Mar ') ;
		$vls[] = array('code'=>4,'desc'=>' Apr ') ;
		$vls[] = array('code'=>5,'desc'=>' May ') ;
		$vls[] = array('code'=>6,'desc'=>' Jun ') ;
		$vls[] = array('code'=>7,'desc'=>' Jul ') ;
		$vls[] = array('code'=>8,'desc'=>' Aug ') ;
		$vls[] = array('code'=>9,'desc'=>' Sep ') ;
		$vls[] = array('code'=>10,'desc'=>' Oct ') ;
		$vls[] = array('code'=>11,'desc'=>' Nov ') ;
		$vls[] = array('code'=>12,'desc'=>' Dec ') ;
		return Util::createOptionValue($vls) ;
	}
	public static function getYearOption() {
		$vls = array() ;
		$year = date('Y') - 5 ;
		for ($i = 1 ;$i < 7; $i++) {
			$vls[] = array('code'=>$year + $i,'desc'=>$year + $i) ;
		}
		return Util::createOptionValue($vls) ;
	}
	public static function minifyHtml($html) {
		$h = str_replace("\r","",$html) ;
		$h = str_replace("\n","",$h) ;
		$h = str_replace("\t","",$h) ;
		return $h ;
	}
	public static function roundOff($amount) {
		if ($amount < 0)
			return ceil($amount) ;
		else
			return floor($amount) ;
	}
}
?>