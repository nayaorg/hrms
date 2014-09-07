var nat_obj = null ;

$(document).ready(function() 
{ 
	$('#btnNatClear').button().bind('click',clearNat) ;
	$('#btnNatAdd').button().bind('click',addNat) ;
	$('#btnNatUpdate').button().bind('click',updateNat) ;
	$('#btnNatPrint').button().bind('click',printNat) ;
	$('#nat-tabs').tabs() ;
	$('#txtNatDesc').focus() ;
	$(window).resize(resizeNatGrid) ;
	resizeNatGrid() ;
	clearNat() ;
}) ;
function resizeNatGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-nat-entry").outerHeight() - 55;
	$("div#sbg-nat-data").css("height", h +'px') ;		
}
function clearNat() {
	$('#txtNatId').val("Auto") ;
	$('#txtNatDesc').val("") ;
	$('#txtNatRef').val("") ;
	$('#nat-tabs').tabs("option", "active", 0) ;
	$('#txtNatDesc').focus() ;
	$('#btnNatUpdate').prop('disabled','disabled') ;
	$('#nat_err_mesg').text('') ;
	$('#nat_err_desc').hide() ;
	$('#nat_err_ref').hide() ;
	nat_obj = null;
}
function updateNat() {
	saveNat(C_UPDATE) ;
}
function addNat() {
	saveNat(C_ADD) ;
}
function saveNat(type) {
	if (validateNat()) {
		var data = { "type": type, "id": $('#txtNatId').val(), "desc": $('#txtNatDesc').val(),"ref": $('#txtNatRef').val()};
		var url = "request.pzx?c=" + nat_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onNatResponse,nat_obj) ;
	}
}
function printNat() {
	var url = "report.pzx?c=" + nat_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editNat(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + nat_url + "&d=" + new Date().getTime();		
	nat_obj = obj ;
	callServer(url,"json",data,showNat,obj) ;
}

function deleteNat(id,obj) {
	if (confirm("Confirm you want to delete nationality id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + nat_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onNatResponse,obj) ;
	}
}
function showNat(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtNatId').val(resp.data.id) ;
		$('#txtNatDesc').val(resp.data.desc) ;
		$('#txtNatRef').val(resp.data.ref) ;
		$('#btnNatUpdate').prop('disabled','') ;
		$('#txtNatDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onNatResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-nat-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtNatDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editNat(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteNat(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtNatDesc').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearNat() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateNat() {
	$('#nat_err_mesg').text('') ;
	if ($('#txtNatDesc').blank())
	{
		$('#nat_err_desc').show() ;
		$('#txtNatDesc').focus() ;
		$('#nat_err_mesg').text("nationality description can not be blank.") ;
		return false ;
	}
	else 
		$('#nat_err_desc').hide() ;
		
	if ($('#txtNatRef').blank())
	{
		$('#nat_err_ref').show() ;
		$('#txtNatRef').focus() ;
		$('#nat_err_mesg').text("IRAS ref can not be blank. This ref is needed for e-submission of employee income.") ;
		return false ;
	}
	else 
		$('#nat_err_ref').hide() ;		
	return true ;
}