var emptype_obj = null ;

$(document).ready(function() 
{ 
	$('#btnEmpTypeClear').button().bind('click',clearEmpType) ;
	$('#btnEmpTypeAdd').button().bind('click',addEmpType) ;
	$('#btnEmpTypeUpdate').button().bind('click',updateEmpType) ;
	$('#btnEmpTypePrint').button().bind('click',printEmpType) ;
	$('#emptype-tabs').tabs() ;
	$('#txtEmpTypeDesc').focus() ;
	$(window).resize(resizeEmpTypeGrid) ;
	resizeEmpTypeGrid() ;
	clearEmpType();
}) ;
function resizeEmpTypeGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-emptype-entry").outerHeight() - 55;
	$("div#sbg-emptype-data").css("height", h +'px') ;		
}
function clearEmpType() {
	$('#txtEmpTypeId').val("Auto") ;
	$('#txtEmpTypeDesc').val("") ;
	$('#txtEmpTypeRef').val("") ;
	$('#emptype-tabs').tabs("option", "active", 0) ;
	$('#txtEmpTypeDesc').focus() ;
	$('#btnEmpTypeUpdate').prop('disabled','disabled') ;
	
	$('#emptype_err_mesg').text('') ;
	$('#emptype_err_desc').hide() ;
	emptype_obj = null;
}
function updateEmpType() {
	saveEmpType(C_UPDATE) ;
}
function addEmpType() {
	saveEmpType(C_ADD) ;
}
function saveEmpType(type) {
	if (validateEmpType()) {
		var data = { "type": type, "id": $('#txtEmpTypeId').val(), "desc": $('#txtEmpTypeDesc').val(),"refno": $('#txtEmpTypeRef').val() };
		var url = "request.pzx?c=" + emptype_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onEmpTypeResponse,emptype_obj) ;
	}
}
function printEmpType() {
	var url = "report.pzx?c=" + emptype_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editEmpType(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + emptype_url + "&d=" + new Date().getTime();		
	emptype_obj = obj ;
	callServer(url,"json",data,showEmpType,obj) ;
}

function deleteEmpType(id,obj) {
	if (confirm("Confirm you want to delete employee type id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + emptype_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onEmpTypeResponse,obj) ;
	}
}
function showEmpType(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtEmpTypeId').val(resp.data.id) ;
		$('#txtEmpTypeDesc').val(resp.data.desc) ;
		$('#txtEmpTypeRef').val(resp.data.refno) ;
		$('#btnEmpTypeUpdate').prop('disabled','') ;
		$('#txtEmpTypeDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onEmpTypeResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-emptype-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtEmpTypeDesc').val() + "</td>" + 
				"<td>" + $('#txtEmpTypeRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editEmpType(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteEmpType(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtEmpTypeDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtEmpTypeRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearEmpType() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateEmpType() {
	$('#emptype_err_mesg').text('') ;
	if ($('#txtEmpTypeDesc').blank())
	{
		$('#emptype_err_desc').show() ;
		$('#txtEmpTypeDesc').focus() ;
		$('#emptype_err_mesg').text("employee type description can not be blank.") ;
		return false ;
	}
	else 
		$('#emptype_err_desc').hide() ;
			
	return true ;
}