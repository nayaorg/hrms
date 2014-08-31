var currency_obj = null ;

$(document).ready(function() 
{ 
	$('#btnCurrencyClear').button().bind('click',clearCurrency) ;
	$('#btnCurrencyAdd').button().bind('click',addCurrency) ;
	$('#btnCurrencyUpdate').button().bind('click',updateCurrency) ;
	$('#btnCurrencyPrint').button().bind('click',printCurrency) ;
	$('#currency-tabs').tabs() ;
	$('#txtCurrencyDesc').focus() ;
	$(window).resize(resizeCurrencyGrid) ;
	resizeCurrencyGrid() ;
	clearCurrency() ;
}) ;
function resizeCurrencyGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-currency-entry").outerHeight() - 55;
	$("div#sbg-currency-data").css("height", h +'px') ;		
}
function clearCurrency() {
	$('#txtCurrencyId').val("Auto") ;
	$('#txtCurrencyDesc').val("") ;
	$('#txtCurrencyRef').val("") ;
	$('#currency-tabs').tabs("option", "active", 0) ;
	$('#txtCurrencyDesc').focus() ;
	$('#btnCurrencyUpdate').prop('disabled','disabled') ;
	$('#currency_err_mesg').text('') ;
	$('#currency_err_desc').hide() ;
	$('#currency_err_ref').hide() ;
	currency_obj = null;
}
function updateCurrency() {
	saveCurrency(C_UPDATE) ;
}
function addCurrency() {
	saveCurrency(C_ADD) ;
}
function saveCurrency(type) {
	if (validateCurrency()) {
		var data = { "type": type, "id": $('#txtCurrencyId').val(), "desc": $('#txtCurrencyDesc').val(),"ref": $('#txtCurrencyRef').val()};
		var url = "request.pzx?c=" + currency_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onCurrencyResponse,currency_obj) ;
	}
}
function printCurrency() {
	var url = "report.pzx?c=" + currency_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editCurrency(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + currency_url + "&d=" + new Date().getTime();		
	currency_obj = obj ;
	callServer(url,"json",data,showCurrency,obj) ;
}

function deleteCurrency(id,obj) {
	if (confirm("Confirm you want to delete currency id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + currency_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onCurrencyResponse,obj) ;
	}
}
function showCurrency(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtCurrencyId').val(resp.data.id) ;
		$('#txtCurrencyDesc').val(resp.data.desc) ;
		$('#txtCurrencyRef').val(resp.data.ref) ;
		$('#btnCurrencyUpdate').prop('disabled','') ;
		$('#txtCurrencyDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onCurrencyResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-currency-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtCurrencyDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editCurrency(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteCurrency(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtCurrencyDesc').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearCurrency() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateCurrency() {
	$('#currency_err_mesg').text('') ;
	if ($('#txtCurrencyDesc').blank())
	{
		$('#currency_err_desc').show() ;
		$('#txtCurrencyDesc').focus() ;
		$('#currency_err_mesg').text("currency description can not be blank.") ;
		return false ;
	}
	else 
		$('#currency_err_desc').hide() ;
		
	//if ($('#txtCurrencyRef').blank())
	//{
	//	$('#currency_err_ref').show() ;
	//	$('#txtCurrencyRef').focus() ;
	//	$('#currency_err_mesg').text("currency ref can not be blank. This ref is needed for e-submission of employee income.") ;
	//	return false ;
	//}
	//else 
	//	$('#currency_err_ref').hide() ;		
	return true ;
}