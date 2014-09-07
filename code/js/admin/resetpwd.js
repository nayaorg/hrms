$(document).ready(function() 
{ 
	$('#resetpwd-tabs').tabs() ;
	$('#btnResetPwdClear').button().bind('click',clearResetPwd) ;
	$('#btnResetPwd').button().bind('click',resetPassword) ;
	
	$(window).resize(resizeResetPwdGrid) ;
	//scrollbarWidth = getScrollbarWidth() ;
	resizeResetPwdGrid() ;
	clearResetPwd() ;
}) ;
function resizeResetPwdGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-resetpwd-entry").outerHeight() - 55;
	$("div#sbg-resetpwd-data").css("height", h +'px') ;		
}
function clearResetPwd() {
	$('#lblResetPwdId').text("") ;
	$('#lblResetPwdName').text("") ;
	$('#lblResetPwdFull').text("") ;
	$('#btnResetPwd').prop('disabled','disabled') ;
}
function resetPassword() {
	if (validateResetPwd()) {
		var data = { "type": C_NEW, "id": $('#lblResetPwdId').text() } ;
		var url = "request.pzx?c=" + resetpwd_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onResetPwdResponse,null) ;
	}
}
function editResetPwd(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + resetpwd_url + "&d=" + new Date().getTime();		
	callServer(url,"json",data,showResetPwd,null) ;
}

function showResetPwd(obj,resp) {
	if (resp.status == C_OK) {
		$('#lblResetPwdId').text(resp.data.id) ;
		$('#lblResetPwdName').text(resp.data.name) ;
		$('#lblResetPwdFull').text(resp.data.fullname) ;
		$('#lblResetPwdLogin').text(resp.data.login) ;
		$('#btnResetPwd').prop('disabled','') ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onResetPwdResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		showDialog("System Message",resp.mesg) ;
		clearResetPwd() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateResetPwd() {
	return true ;
}