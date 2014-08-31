
var config_temp = "" ;
var config_type = "" ;
$(document).ready(function() 
{ 
	//if ($.browser.webkit) { 
		$('input').css('marginTop', 0); 
		$('input').css('marginBottom',0);
		$('select').css('marginTop',0) ;
		$('select').css('marginBottom',0) ;
	//} 
	$('#config-tabs').tabs() ;
	$('#txtConfigServer').focus() ;
	$('#btnConfigSave').button().bind('click',saveConfig) ;
	$('#btnConfigTest').button().bind('click',testConfig) ;
	$('#btnConfigCreate').button().bind('click',createConfig) ;
	$('#txtConfigDbPort').keypress(function() { numericInput(0,'',0,0) ; })
		
}) ;
function saveConfig() {
	config_type = "u" ;
	if (config_temp == "") {
		var data = { "type": "g" } ;
		var url = "admin.pzx?&d=" + new Date().getTime() ;
		callServer(url,"json",data,onConfigResponse,null) ;
	} else {
		if (validateConfig()) {
			var data = { "type": "u", "server": $('#txtConfigServer').val(),
				"dbname": $('#txtConfigDbName').val(), "dbuser": $('#txtConfigDbUser').val(), 
				"dbpwd": strToHex(xxtea_encrypt($('#txtConfigDbPwd').val(),config_temp)) ,
				"dbport": $('#txtConfigPort').val(), "dbtype": $('#cobConfigDbType').val() } ; 
			var url = "admin.pzx?d=" + new Date().getTime() ;
			
			clearConfig() ;
			config_temp = "" ;
			callServer(url,"json",data,onConfigResponse,null) ;
		}
	}
}
function testConfig() {
	config_type = "q" ;
	if (config_temp == "") {
		var data = { "type": "g" } ;
		var url = "admin.pzx?&d=" + new Date().getTime() ;
		callServer(url,"json",data,onConfigResponse,null) ;
	} else {
		if (validateConfig()) {
			var data = { "type": "q", "server": $('#txtConfigServer').val(),
				"dbname": $('#txtConfigDbName').val(), "dbuser": $('#txtConfigDbUser').val(), 
				"dbpwd": strToHex(xxtea_encrypt($('#txtConfigDbPwd').val(),config_temp)) ,
				"dbport": $('#txtConfigPort').val(), "dbtype": $('#cobConfigDbType').val() } ; 
			var url = "admin.pzx?d=" + new Date().getTime() ;

			clearConfig() ;
			config_temp = "" ;
			config_type = "" ;
			//alert("callserver") ;
			callServer(url,"text",data,onConfigTestResponse,null) ;
		}
	}
}
function createConfig() {
}
function clearConfig() {
	$('#config_err_mesg').text("") ;
}
function onConfigResponse(obj,resp) {
	//alert(JSON.stringify(resp)) ;
	if (resp.status == "0") {
		if (resp.type == "g") {
			config_temp = resp.data ;
			if (config_type == "q")
				testConfig() ;
			else
				saveConfig() ;
		} else  {
			alert(resp.mesg) ;
			clearConfig() ;
		}
	}
	else 
		alert(resp.mesg) ;
}
function onConfigTestResponse(obj,resp) {
	alert(resp) ;
	//$('#sbg-progress1').hide() ;
}
function validateConfig() {
	$('#config_err_mesg').text('') ;
	if ($('#txtConfigServer').blank())
	{
		$('#config_err_server').show() ;
		$('#txtConfigServer').focus() ;
		$('#config_err_mesg').text("Server name can not be blank.") ;
		return false ;
	}
	else 
		$('#config_err_server').hide() ;
	
	if ($('#txtConfigDbName').blank())
	{
		$('#config_err_name').show() ;
		$('#txtConfigDbName').focus() ;
		$('#config_err_mesg').text("Database name can not be blank.") ;
		return false ;
	}
	else 
		$('#config_err_name').hide() ;
	
	if (!$('#txtConfigDbPort').blank())
	{
		if (!isInt($('#txtConfigDbPort').val())) {
			$('#config_err_port').show() ;
			$('#txtConfigDbPort').focus() ;
			$('#config_err_mesg').text("Invalid Port no. Port no must be integer.") ;
			return false ;
		} else
			$('#config_err_port').hide() ;
	}
	else 
		$('#config_err_port').hide() ;
	
	if ($('#txtConfigDbUser').blank())
	{
		$('#config_err_user').show() ;
		$('#txtConfigDbUser').focus() ;
		$('#config_err_mesg').text("Database login user name can not be blank.") ;
		return false ;
	}
	else 
		$('#config_err_user').hide() ;
	
	if ($('#txtConfigDbPwd').blank())
	{
		$('#config_err_pwd').show() ;
		$('#txtConfigDbPwd').focus() ;
		$('#config_err_mesg').text("Database login password can not be blank.") ;
		return false ;
	}
	else 
		$('#config_err_pwd').hide() ;
		
	return true ;
}
function isInt(x) { 
   var y=parseInt(x); 
   if (isNaN(y)) return false; 
   return x==y && x.toString()==y.toString(); 
 } 
