var login_temp = "" ;
$(document).ready(function() 
{ 
	$('#btnLogin').button().bind('click',this,onLogin) ;
	$('#txtName').focus() ;
}) ;

function onLogin(obj) {
	$('#err_mesg').text('') ;
	
	if ($('#txtName').blank())
	{
		$('#txtName').focus() ;
		$('#err_mesg').text("user name can not be blank.") ;
		return false ;
	}
	else {
		
	}
	
	var data = { "type": "q","id": $('#txtName').val()} ;
	var url = "request.pzx?c=" + login_url + "&d=" + new Date().getTime() ;	
	callServer(url,"json",data,onLoginResponse,obj) ;
}
	
function onLoginResponse(obj,data) {

	if (data == null)  {
		alert('invalid response : ' + JSON.stringify(data)) ;
	} else {
		if (data.status == '0') {
			openSite(data.data + '&d=' + new Date().getTime());
		} else if (data.status == '8') {
			if (checkPwd()) {
				login_temp = data.data ;
				var data = { "type": "i","id": $('#txtName').val(), "pwd": $('#txtPwd').val() } ;
				var url = "request.pzx?c=" + login_url + "&d=" + new Date().getTime() ;	
				data.data = "" ;
				login_temp = "" ;
				callServer(url,"json",data,onLoginResponse,obj) ;
			}
		}
		else {
			$('#err_mesg').text(data.mesg) ;
		}
	}
}

function checkPwd() {
	if ($('#txtPwd').blank()) {
		
		$('#txtPwd').focus() ;
		$('#err_mesg').text("password can not be blank.") ;
		return false ;
	} else {
		
	}
	return true ;
}
function encryptPwd(pwd) {
	return strToHex(xxtea_encrypt(pwd,login_temp)) ;
}