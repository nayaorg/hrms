var wp_obj = null ;

$(document).ready(function() 
{ 
	$('#btnWPClear').button().bind('click',clearWorkPermit) ;
	$('#btnWPAdd').button().bind('click',addWorkPermit) ;
	$('#btnWPUpdate').button().bind('click',updateWorkPermit) ;
	$('#btnWPPrint').button().bind('click',printWorkPermit) ;
	$('#txtWPLevy').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#wp-tabs').tabs() ;
	$('#txtWPDesc').focus() ;
	$(window).resize(resizeWorkPermitGrid) ;
	resizeWorkPermitGrid() ;
	clearWorkPermit() ;
}) ;
function resizeWorkPermitGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-wp-entry").outerHeight() - 55;
	$("div#sbg-wp-data").css("height", h +'px') ;		
}
function clearWorkPermit() {
	$('#txtWPId').val("Auto") ;
	$('#txtWPDesc').val("") ;
	$('#txtWPLevy').val("") ;
	$('#wp-tabs').tabs("option", "active", 0) ;
	$('#txtWPDesc').focus() ;
	$('#btnWPUpdate').prop('disabled','disabled') ;
	$('#wp_err_mesg').text('') ;
	$('#wp_err_desc').hide() ;
	wp_obj = null;
}
function updateWorkPermit() {
	saveWorkPermit(C_UPDATE) ;
}
function addWorkPermit() {
	saveWorkPermit(C_ADD) ;
}
function saveWorkPermit(type) {
	if (validateWorkPermit()) {
		var data = { "type": type, "id": $('#txtWPId').val(), "desc": $('#txtWPDesc').val(), "levy": $('#txtWPLevy').val()};
		var url = "request.pzx?c=" + wp_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onWorkPermitResponse,wp_obj) ;
	}
}
function printWorkPermit() {
	var url = "report.pzx?c=" + wp_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editWorkPermit(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + wp_url + "&d=" + new Date().getTime();		
	wp_obj = obj ;
	callServer(url,"json",data,showWorkPermit,obj) ;
}

function deleteWorkPermit(id,obj) {
	if (confirm("Confirm you want to delete work permit id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + wp_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onWorkPermitResponse,obj) ;
	}
}
function showWorkPermit(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtWPId').val(resp.data.id) ;
		$('#txtWPDesc').val(resp.data.desc) ;
		$('#txtWPLevy').val(resp.data.levy) ;
		$('#btnWPUpdate').prop('disabled','') ;
		$('#txtWPDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onWorkPermitResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-wp-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtWPDesc').val() + "</td>" + 
				"<td style='text-align:right'>" + $('#txtWPLevy').val() + "</td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='editWorkPermit(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteWorkPermit(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtWPDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').html($('#txtWPLevy').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearWorkPermit() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateWorkPermit() {
	$('#wp_err_mesg').text('') ;
	if ($('#txtWPDesc').blank())
	{
		$('#wp_err_desc').show() ;
		$('#txtWPDesc').focus() ;
		$('#wp_err_mesg').text("work permit description can not be blank.") ;
		return false ;
	}
	else 
		$('#wp_err_desc').hide() ;
			
	return true ;
}