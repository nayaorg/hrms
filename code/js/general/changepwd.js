var changepwd_temp = "";
$(document).ready(function() 
{ 
	$('#btnChangePwd').button().bind('click',changePassword) ;
	$('#txtChangePwdOld').focus() ;
}) ;
function clearChangePwd() {
	$('#txtChangePwdOld').val("") ;
	$('#txtChangePwdNew').val("") ;
	$('#txtChangePwdConfirm').val("") ;
	$('#changepwd_err_mesg').text("") ;
	$('#txtChangePwdOld').focus() ;
}
function changePassword() {
	if (changepwd_temp == "") {
		var data = { "type": C_GET } ;
		var url = "request.pzx?c=" + changepwd_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onChangePwdResponse,null) ;
	} else {
		if (validateChangePwd()) {
			var data = { "type": C_CHANGE, "old": encryptPwd($('#txtChangePwdOld').val()), "new": encryptPwd($('#txtChangePwdNew').val()) } ;
			var url = "request.pzx?c=" + changepwd_url + "&d=" + new Date().getTime() ;
			changepwd_temp = "" ;
			callServer(url,"json",data,onChangePwdResponse,null) ;
		}
	}
}
function onChangePwdResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		if (resp.type == C_GET) {
			changepwd_temp = resp.data ;
			changePassword() ;
		} else  {
			showDialog("System Message",resp.mesg) ;
			clearChangePwd() ;
		}
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateChangePwd() {
	$('#changepwd_err_mesg').text('') ;
	if ($('#txtChangePwdOld').blank())
	{
		$('#txtChangePwdOld').focus() ;
		$('#changepwd_err_mesg').text("You must enter your current password.") ;
		return false ;
	}
	if ($('#txtChangePwdNew').blank()) {
		$('#txtChangePwdNew').focus() ;
		$('#changepwd_err_mesg').text("You must enter your new password.") ;
		return false ;
	}
	if ($('#txtChangePwdNew').val() != $('#txtChangePwdConfirm').val()) {
		$('#txtChangePwdConfirm').focus() ;
		$('#changepwd_err_mesg').text("Password does not match.") ;
		return false ;
	}
	return true ;
}
function encryptPwd(pwd) {
	return strToHex(xxtea_encrypt(pwd,changepwd_temp)) ;
}