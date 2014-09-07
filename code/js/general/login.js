var login_temp = "" ;
$(document).ready(function() 
{ 
	$( "#sbg-pwd" ).dialog({
		autoOpen: false,
		height: 200,
		width: 300,
		modal: true,
		buttons: {
			"Ok": function() {
				if ($('#txtPwd1').val() == $('#txtPwd2').val()) {
					$( this ).dialog( "close" );
					var data = { "type": "n","id": $('#txtName').val(), "pwd": encryptPwd($('#txtPwd1').val()) } ;
					var url = "request.pzx?c=" + login_url + "&d=" + new Date().getTime() ;	
					login_temp = "" ;
					callServer(url,"json",data,onLoginResponse,null) ;
				} else {
					alert("Password does not match. Please try again.");
					$('#txtPwd2').focus()
				}
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		},
	});

	$('#btnLogin').button().bind('click',this,onLogin) ;
	$('#txtName').focus() ;
}) ;

function onLogin(obj) {
	$('#err_mesg').text('') ;
	$('#err_summary').text('') ;
	if ($('#txtName').blank())
	{
		$('#err_name').show() ;
		$('#txtName').focus() ;
		$('#err_mesg').text("user name can not be blank.") ;
		return false ;
	}
	else 
		$('#err_name').hide() ;
			
	
	var data = { "type": "q","id": $('#txtName').val()} ;
	var url = "request.pzx?c=" + login_url + "&d=" + new Date().getTime() ;	
	callServer(url,"json",data,onLoginResponse,obj) ;
}
	
function onLoginResponse(obj,data) {
	if (data == null) 
	{
		alert('invalid response : ' + JSON.stringify(data)) ;
	}
	else 
	{
		if (data.status == '0') {
			openSite(data.data + "&d=" + new Date().getTime());
		} else if (data.status == '6') {
			login_temp = data.data ;
			data.data = "" ;
			createPwd() ;
		} else if (data.status == '8') {
			if (checkPwd()) {
				login_temp = data.data ;
				var data = { "type": "i","id": $('#txtName').val(), "pwd": encryptPwd($('#txtPwd').val()) } ;
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
function createPwd() {
	$( "#sbg-pwd" ).dialog( "open" );
}
function checkPwd() {
	if ($('#txtPwd').blank()) {
		$('#err_pwd').show() ;
		$('#txtPwd').focus() ;
		$('#err_mesg').text("password can not be blank.") ;
		return false ;
	} else {
		$('#err_pwd').hide() ;
	}
	return true ;
}
function encryptPwd(pwd) {
	return strToHex(xxtea_encrypt(pwd,login_temp)) ;
}