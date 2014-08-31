var bank_obj = null ;

$(document).ready(function() 
{ 
	$('#btnBankClear').button().bind('click',clearBank) ;
	$('#btnBankUpdate').button().bind('click',updateBank) ;
	$('#btnBankAdd').button().bind('click',addBank) ;
	$('#btnBankPrint').button().bind('click',printBank) ;
	$('#bank-tabs').tabs() ;
	$('#txtBankDesc').focus() ;
	clearBank();
	$(window).resize(resizeBankGrid) ;
	resizeBankGrid() ;
}) ;
function resizeBankGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-bank-entry").outerHeight() - 55;
	$("div#sbg-bank-data").css("height", h +'px') ;		
}
function clearBank() {
	$('#txtBankId').val("Auto") ;
	$('#txtBankDesc').val("") ;
	$('#txtBankRef').val("") ;
	$('#txtBankFile').val("") ;
	$('#bank-tabs').tabs("option", "active", 0) ;
	$('#txtBankDesc').focus() ;
	$('#btnBankUpdate').prop('disabled', 'disabled');
	$('#bank_err_mesg').text('') ;
	$('#bank_err_desc').hide() ;
	bank_obj = null;
}
function updateBank() {
	saveBank(C_UPDATE) ;
}
function addBank() {
	saveBank(C_ADD) ;
}
function saveBank(type) {
	if (validateBank()) {
		var data = { "type": type, "id": $('#txtBankId').val(), "desc": $('#txtBankDesc').val(),
			"acctno": "", "refno": $('#txtBankRef').val(),"file": $('#txtBankFile').val() };
		var url = "request.pzx?c=" + bank_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onBankResponse,bank_obj) ;
	}
}
function printBank() {
	var url = "report.pzx?c=" + bank_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editBank(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + bank_url + "&d=" + new Date().getTime();		
	bank_obj = obj ;
	callServer(url,"json",data,showBank,obj) ;
}

function deleteBank(id,obj) {
	if (confirm("Confirm you want to delete bank id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + bank_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onBankResponse,obj) ;
	}
}
function showBank(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		$('#txtBankId').val(resp.data.id) ;
		$('#txtBankDesc').val(resp.data.desc) ;
		$('#txtBankRef').val(resp.data.refno) ;
		$('#txtBankFile').val(resp.data.file) ;
		$('#txtBankDesc').focus() ;
		$('#btnBankUpdate').prop('disabled','');
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onBankResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-bank-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtBankDesc').val() + "</td>" + 
				"<td>" + $('#txtBankRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editBank(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteBank(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtBankDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtBankRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearBank() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateBank() {
	$('#bank_err_mesg').text('') ;
	if ($('#txtBankDesc').blank())
	{
		$('#bank_err_desc').show() ;
		$('#txtBankDesc').focus() ;
		$('#bank_err_mesg').text("Bank name can not be blank.") ;
		return false ;
	}
	else 
		$('#bank_err_desc').hide() ;

	return true ;
}