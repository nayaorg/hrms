<?php
class UserRight {
	const C_ENABLE = 0 ;
	const C_ADD = 1 ;
	const C_UPDATE = 2;
	const C_DELETE = 3;
	const C_VIEW = 4;
	const C_PRINT = 5 ;
	
	const C_ADMIN = "admin";
	const C_ADMIN_SETTING = 0;
	const C_ADMIN_COY = 1;
	const C_ADMIN_USER = 2;
	const C_ADMIN_GROUP = 3;
	const C_ADMIN_RESET = 4;
	
	const C_HR = "hr";
	const C_HR_EMP = 0;
	const C_HR_TYPE = 1 ;
	const C_HR_DEPT = 2;
	const C_HR_JOB = 3;
	const C_HR_NAT = 4 ;
	const C_HR_RACE = 5;
	const C_HR_PERMIT = 6;
	
	const C_PAYROLL = "payroll" ;
	const C_PAYROLL_BANK = 0;
	const C_PAYROLL_EMP = 1;
	const C_PAYROLL_TYPE = 2;
	const C_PAYROLL_CPF = 3;
	const C_PAYROLL_CREATE = 4;
	const C_PAYROLL_ENTRY = 5;
	const C_PAYROLL_PAYLIST = 6;
	const C_PAYROLL_PAYSLIP = 7;
	const C_PAYROLL_CPFLIST = 8;
	const C_PAYROLL_CPFENTRY = 9 ;
	const C_PAYROLL_INCOMEYEAR = 10;
	
	const C_LEAVE = "leave";
	const C_LEAVE_TYPE = 0 ;
	const C_LEAVE_GROUP = 1 ;
	const C_LEAVE_EMP = 2 ;
	const C_LEAVE_APPLY = 3 ;
	const C_LEAVE_APPROVE = 4 ;
	const C_LEAVE_SUMMARY = 5;
	const C_LEAVE_LIST = 6 ;
	const C_LEAVE_REPORT = 7 ;
	
	private $hr = array() ;
	private $admin = array() ;
	private $payroll = array() ;
	private $leave = array() ;
	
	function __construct() {
		$this->initRights() ;
	}
	function __destruct() {
	}
	function getAdminRight() {
		return $this->admin ;
	}
	function getHrRight() {
		return $this->hr ;
	}
	function getPayrollRight() {
		return $this->payroll ;
	}
	function getLeaveRigth() {
		return $this->leave ;
	}
	function setAdminRight($value) {
		$this->admin = $value ;
	}
	function setHrRight($value) {
		$this->hr = $value ;
	}
	function setPayrollRight($value) {
		$this->payroll = $value ;
	}
	function setLeaveRight($value) {
		$this->leave = $value ;
	}
	
	function toRights($data) {
		$this->initRights() ;
		if ($data == "")
			return ;
		
		$r = trim(Util::decryptString(pack("H*" , $data),"","")) ;
		//$r = $data ;
		if ($r == "")
			return ;
		$d = explode("|",$r) ;
		$cnt = count($d) ;
		if ($cnt > 0)
			$this->toAdminRight($d[0]) ;
		else 
			return ;
		if ($cnt > 1) 
			$this->toHrRight($d[1]) ;
		else
			return;
		if ($cnt > 2) 
			$this->toPayrollRight($d[2]) ;
		
		if ($cnt > 3)
			$this->toLeaveRight($d[3]) ;
	}
	function toString() {
		$s = "" ;
		for ($i=0;$i<5;$i++) {
			$s .= $this->access2Hex($this->admin[$i]) ;
		}
		$s .= "|" ;
		for ($i=0;$i<7;$i++) {
			$s .= $this->access2Hex($this->hr[$i]) ;
		}
		$s .= "|" ;
		for ($i=0;$i<11;$i++) {
			$s .= $this->access2Hex($this->payroll[$i]) ;
		}
		$s .= "|" ;
		for ($i=0;$i<8;$i++) {
			$s .= $this->access2Hex($this->leave[$i]) ;
		}
		return bin2hex(Util::encryptString($s,"","")) ;
		//return $s ;
	}
	function initRights() {
		$r  = '00';
		for ($i=0 ;$i < 5;$i++) {
			$this->admin[$i] = $this->hex2Access($r) ;
		}
		for ($i= 0 ;$i < 7;$i++) {
			$this->hr[$i] = $this->hex2Access($r) ;
		}
		for ($i=0;$i<11;$i++) {
			$this->payroll[$i] = $this->hex2Access($r) ;
		}
		for ($i=0;$i<8;$i++) {
			$this->leave[$i] = $this->hex2Access($r) ;
		}
	}
	private function toAdminRight($data) {
		if (trim($data) == "")
			return ;

		$len = strlen($data) ;
		if ($len < 2)
			return ;
			
		if ($len > 5)
			$len = 5 ;
		$idx = 0 ;
		for ($i=0;$i<$len;$i++) {
			$this->admin[$i] = $this->hex2Access(substr($data,$idx,2)) ;
			$idx += 2;
		}
	}
	private function toHrRight($data) {
		if (trim($data) == "")
			return ;

		$len = strlen($data) ;
		if ($len < 2) 
			return ;
			
		if ($len > 7)
			$len = 7 ;
		$idx = 0 ;
		for ($i=0;$i<$len;$i++) {
			$this->hr[$i] = $this->hex2Access(substr($data,$idx,2)) ;
			$idx += 2;
		}
	}
	private function toPayrollRight($data) {
		if (trim($data) == "")
			return ;
		
		$len = strlen($data) ;
		if ($len < 2)
			return ;
			
		if ($len > 11)
			$len = 11 ;
		$idx = 0 ;
		for ($i=0;$i<$len;$i++) {
			$this->payroll[$i] = $this->hex2Access(substr($data,$idx,2)) ;
			$idx += 2;
		}
	}
	private function toLeaveRight($data) {
		if (trim($data) == "")
			return ;
		
		$len = strlen($data) ;
		if ($len < 2)
			return ;
			
		if ($len > 8)
			$len = 8 ;
		$idx = 0 ;
		for ($i=0;$i<$len;$i++) {
			$this->leave[$i] = $this->hex2Access(substr($data,$idx,2)) ;
			$idx += 2;
		}
	}
	private function access2Hex($access) {
		$data = "00000000";
		if (!is_null($access) && count($access) > 0) {
			$cnt = count($access) ;
			if ($cnt > 8)
				$cnt = 8 ;
			for ($i=0;$i < $cnt ;$i++) {
				if ($access[$i])
					$data[$i] = "1";
			}
		} 
		return str_pad(dechex(bindec($data)),2,"0",STR_PAD_LEFT) ;
	}
	private function hex2Access($hex) {
		$access = array();
		for ($i=0;$i<8;$i++) {
			$access[$i] = false ;
		}
		$r = str_pad(decbin(hexdec($hex)),8,"0",STR_PAD_LEFT) ;
		for ($i=0;$i<8;$i++) {
			if ($r[$i] == "1")
				$access[$i] = true ;
		}
		return $access ;
	}
	function bin2Access($binary) {
		$access = array() ;
		for ($i=0;$i < 8;$i++) {
			$access[$i] = false ;
		}
		$len = strlen($binary) ;
		if ($len > 8)
			$len = 8 ;
		for ($i=0;$i < $len; $i++) {
			if ($binary[$i] == 1) {
				$access[$i] = true ;
			}
		}
		return $access ;
	}
}
?>