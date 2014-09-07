var country_obj = null ;

$(document).ready(function() 
{ 
	$('#btnCountryClear').button().bind('click',clearCountry) ;
	$('#btnCountryAdd').button().bind('click',addCountry) ;
	$('#btnCountryUpdate').button().bind('click',updateCountry) ;
	$('#btnCountryPrint').button().bind('click',printCountry) ;
	$('#country-tabs').tabs() ;
	$('#txtCountryDesc').focus() ;
	$(window).resize(resizeCountryGrid) ;
	resizeCountryGrid() ;
	clearCountry() ;
}) ;
function resizeCountryGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-country-entry").outerHeight() - 55;
	$("div#sbg-country-data").css("height", h +'px') ;		
}
function clearCountry() {
	$('#txtCountryId').val("Auto") ;
	$('#txtCountryDesc').val("") ;
	$('#txtCountryRef').val("") ;
	$('#country-tabs').tabs("option", "active", 0) ;
	$('#txtCountryDesc').focus() ;
	$('#btnCountryUpdate').prop('disabled','disabled') ;
	$('#country_err_mesg').text('') ;
	$('#country_err_desc').hide() ;
	$('#country_err_ref').hide() ;
	country_obj = null;
}
function updateCountry() {
	saveCountry(C_UPDATE) ;
}
function addCountry() {
	saveCountry(C_ADD) ;
}
function saveCountry(type) {
	if (validateCountry()) {
		var data = { "type": type, "id": $('#txtCountryId').val(), "desc": $('#txtCountryDesc').val(),"ref": $('#txtCountryRef').val()};
		var url = "request.pzx?c=" + country_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onCountryResponse,country_obj) ;
	}
}
function printCountry() {
	var url = "report.pzx?c=" + country_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editCountry(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + country_url + "&d=" + new Date().getTime();		
	country_obj = obj ;
	callServer(url,"json",data,showCountry,obj) ;
}

function deleteCountry(id,obj) {
	if (confirm("Confirm you want to delete country id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + country_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onCountryResponse,obj) ;
	}
}
function showCountry(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtCountryId').val(resp.data.id) ;
		$('#txtCountryDesc').val(resp.data.desc) ;
		$('#txtCountryRef').val(resp.data.ref) ;
		$('#btnCountryUpdate').prop('disabled','') ;
		$('#txtCountryDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onCountryResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-country-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtCountryDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editCountry(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteCountry(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtCountryDesc').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearCountry() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateCountry() {
	$('#country_err_mesg').text('') ;
	if ($('#txtCountryDesc').blank())
	{
		$('#country_err_desc').show() ;
		$('#txtCountryDesc').focus() ;
		$('#country_err_mesg').text("country description can not be blank.") ;
		return false ;
	}
	else 
		$('#country_err_desc').hide() ;
		
	//if ($('#txtCountryRef').blank())
	//{
	//	$('#country_err_ref').show() ;
	//	$('#txtCountryRef').focus() ;
	//	$('#country_err_mesg').text("country ref can not be blank. This ref is needed for e-submission of employee income.") ;
	//	return false ;
	//}
	//else 
	//	$('#country_err_ref').hide() ;		
	return true ;
}