var empleave_obj = null ;
var empleave_id = "" ;

$(document).ready(function() 
{ 
	 
		$('input').css('marginTop', 0); 
		$('input').css('marginBottom',0);
		$('select').css('marginTop',0) ;
		$('select').css('marginBottom',0) ;
	

	$('#btnEmpLeaveClear').button().bind('click',clearEmpLeave) ;
	$('#btnEmpLeaveSave').button().bind('click',saveEmpLeave) ;
	$('#btnEmpLeaveList').button().bind('click',listEmpLeave) ;
	$('#empleave-tabs').tabs() ;
	$('#cobEmpLeaveGroup').focus() ;
	$(window).resize(resizeEmpLeaveGrid) ;
	resizeEmpLeaveGrid() ;
	clearEmpLeave() ;
}) ;
function resizeEmpLeaveGrid() {

	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-empleave-entry").outerHeight() - $("#sbg-empleave-option").outerHeight() - 55;
	$("div#sbg-empleave-data").css("height", h +'px') ;		
}
function clearEmpLeave() {
	$('#lblEmpLeaveIdName').text("") ;
	$('#lblEmpLeaveCoy').text("") ;
	$('#cobEmpLeaveGroup').val("") ;
	$('#empleave-tabs').tabs("option", "active", 0) ;
	$('#cobEmpLeaveGroup').focus() ;
	$('#btnEmpLeaveSave').attr('disabled','disabled') ;
	
	$('#empleave_err_mesg').text('') ;
	$('#empleave_err_group').hide() ;
	
	empleave_obj = null;
}
function saveEmpLeave() {
	if (validateEmpLeave()) {
		var data = { "type": C_UPDATE, "id": empleave_id,
			"group": $('#cobEmpLeaveGroup').val()};
		var url = "request.pzx?c=" + empleave_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onEmpLeaveResponse,empleave_obj) ;
	}
}

function editEmpLeave(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + empleave_url + "&d=" + new Date().getTime();		
	empleave_obj = obj ;
	callServer(url,"json",data,showEmpLeave,obj) ;
}
function listEmpLeave() {
	var data = { "type": C_LIST, "coy": $('#cobEmpLeaveCoy').val(), "dept": $('#cobEmpLeaveDept').val() } ;
	var url = "request.pzx?c=" + empleave_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showEmpLeaveList,empleave_obj) ;
}
function showEmpLeave(obj,resp) {
	if (resp.status == C_OK) {
		empleave_id = resp.data.id ;
		$('#lblEmpLeaveIdName').text(resp.data.id + " - " + resp.data.name) ;
		$('#lblEmpLeaveCoy').text(resp.data.coy + " - " + resp.data.dept) ;
		$('#cobEmpLeaveGroup').val(resp.data.group) ;
		$('#cobEmpLeaveGroup').focus() ;
		$('#btnEmpLeaveSave').prop('disabled','') ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function showEmpLeaveList(obj,resp) {
	var fr = '<tr><td style="width:50px;height:1px"></td>' +
			'<td style="width:200px"></td><td style="width:200px"></td>' +
			'<td style="width:150px"></td><td style="width:20px"></td></tr>' ;
	$('#sbg-empleave-table').html(fr + resp) ;
}
function onEmpLeaveResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearEmpLeave() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateEmpLeave() {
	$('#empleave_err_mesg').text('') ;
	return true ;
}
