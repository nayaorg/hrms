var coy_obj = null ;
$(document).ready(function() 
{ 
	//if ($.browser.webkit) { 
		$('input').css('marginTop', 0); 
		$('input').css('marginBottom',0);
		$('select').css('marginTop',0) ;
		$('select').css('marginBottom',0) ;
	//} 
	$('#company-tabs').tabs() ;
	$('#btnCoyClear').button().bind('click',clearCompany) ;
	$('#btnCoyUpdate').button().bind('click',updateCompany) ;
	$('#btnCoyAdd').button().bind('click',addCompany) ;
	$('#btnCoyPrint').button().bind('click',printCompany) ;
	$('#txtCoyCpfRef').keypress(function() { numericInput(0,'',0,0) ; }) ;
	$('#txtCoyEgm').keypress(function() { numericInput(0,'',0,0) ; }) ;
	$('#txtCoyBonus').keypress(function() { numericInput(0,'',0,0) ; }) ;
	$(window).resize(resizeCoyGrid) ;
	resizeCoyGrid() ;
	clearCompany();
}) ;
function resizeCoyGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-company-entry").outerHeight() - 55;
	$("div#sbg-company-data").css("height", h +'px') ;		
}
function clearCompany() {
	$('#txtCoyId').val("Auto") ;
	$('#txtCoyName').val("") ;
	$('#txtCoyRef').val("") ;
	$('#cobCoyBank').val("") ;
	$('#txtCoyAcctNo').val("") ;
	$('#txtCoyTel').val("") ;
	$('#txtCoyFax').val("") ;
	$('#txtCoyAddr1').val("") ;
	$('#txtCoyAddr2').val("") ;
	$('#txtCoyAddr3').val("") ;
	$('#txtCoyAddr4').val("") ;
	$('#txtCoyAddr5').val("") ;
	$('#txtCoyRegNo').val("") ;
	$('#cobCoyRegType').val("") ;
	$('#txtCoyCpfNo').val("") ;
	$('#txtCoyCpfRef').val("") ;
	$('#txtCoyAuthName').val("") ;
	$('#txtCoyAuthTitle').val("") ;
	$('#txtCoyEgm').val("") ;
	$('#txtCoyBonus').val("") ;
	$('#txtCoyAuthTel').val("") ;
	$('#company-tabs').tabs("option", "active", 0) ;
	$('#txtCoyName').focus() ;
	$('#btnCoyUpdate').prop('disabled','disabled') ;
	
	$('#coy_err_name').hide() ;
	$('#coy_err_cpfno').hide() ;
	$('#coy_err_regtype').hide() ;
	$('#coy_err_regno').hide() ;
	$('#coy_err_cpfref').hide() ;
	$('#coy_err_authname').hide() ;
	$('#coy_err_mesg').text('') ;
	coy_obj = null ;
}
function updateCompany() {
	saveCompany(C_UPDATE) ;
}
function addCompany() {
	saveCompany(C_ADD) ;
}
function saveCompany(type) {
	if (validateCompany()) {
		var data = { "type": type, "id": $('#txtCoyId').val(), "name": $('#txtCoyName').val(), "name2": "",
			"regno": $('#txtCoyRegNo').val(), "cpfno": $('#txtCoyCpfNo').val(), "cpfref": $('#txtCoyCpfRef').val(),
			"bank": $('#cobCoyBank').val(), "acctno": $('#txtCoyAcctNo').val(), "regtype": $('#cobCoyRegType').val(),
			"refno": $('#txtCoyRef').val(),"authname": $('#txtCoyAuthName').val(), "authtitle": $('#txtCoyAuthTitle').val(),
			"authtelno": $('#txtCoyAuthTel').val(), "egmdate": $('#txtCoyEgm').val(), "bonusdate": $('#txtCoyBonus').val(),
			"tel": $('#txtCoyTel').val(), "fax": $('#txtCoyFax').val(), "addr1": $('#txtCoyAddr1').val(), "addr2": $('#txtCoyAddr2').val(), 
			"addr3": $('#txtCoyAddr3').val(), "addr4": $('#txtCoyAddr4').val(), "addr5": $('#txtCoyAddr5').val() } ;
		var url = "request.pzx?c=" + coy_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onCompanyResponse,coy_obj) ;
	}
}
function printCompany() {
	var url = "report.pzx?c=" + coy_url + "&d=" + new Date().getTime() ;
	showReport(url) ;
}
function editCompany(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + coy_url + "&d=" + new Date().getTime() ;
	coy_obj = obj ;
	callServer(url,"json",data,showCompany,obj) ;
}

function deleteCompany(id,obj) {
	if (confirm("Confirm you want to delete company id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + coy_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onCompanyResponse,obj) ;
	}
}
function showCompany(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		$('#txtCoyId').val(resp.data.id) ;
		$('#txtCoyAcctNo').val(resp.data.acctno) ;
		$('#cobCoyBank').val(resp.data.bank) ;
		$('#txtCoyName').val(resp.data.name) ;
		$('#txtCoyRef').val(resp.data.refno) ;
		$('#txtCoyTel').val(resp.data.tel) ;
		$('#txtCoyFax').val(resp.data.fax) ;
		$('#txtCoyAddr1').val(resp.data.addr1) ;
		$('#txtCoyAddr2').val(resp.data.addr2) ;
		$('#txtCoyAddr3').val(resp.data.addr3) ;
		$('#txtCoyAddr4').val(resp.data.addr4) ;
		$('#txtCoyAddr5').val(resp.data.addr5) ;
		$('#txtCoyRegNo').val(resp.data.regno) ;
		$('#cobCoyRegType').val(resp.data.regtype) ;
		$('#txtCoyCpfNo').val(resp.data.cpfno) ;
		$('#txtCoyCpfRef').val(resp.data.cpfref) ;
		$('#txtCoyAuthName').val(resp.data.authname) ;
		$('#txtCoyAuthTitle').val(resp.data.authtitle) ;
		$('#txtCoyAuthTel').val(resp.data.authtelno) ;
		$('#txtCoyEgm').val(resp.data.egmdate) ;
		$('#txtCoyBonus').val(resp.data.bonusdate) ;
		$('#btnCoyUpdate').prop('disabled','') ;
		$('#txtCoyCode').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onCompanyResponse(obj,resp) {
	//$("#templateRow").clone().removeAttr("id").appendTo( $("#templateRow").parent() ); 
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-company-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtCoyName').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editCompany(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteCompany(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').text($('#txtCoyName').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearCompany() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateCompany() {
	$('#coy_err_mesg').text('') ;
	if ($('#txtCoyName').blank())
	{
		$('#coy_err_name').show() ;
		$('#txtCoyName').focus() ;
		$('#coy_err_mesg').text("company name can not be blank.") ;
		return false ;
	}
	else 
		$('#coy_err_name').hide() ;
	
	if ($('#txtCoyCpfNo').blank())
	{
		$('#coy_err_cpfno').show() ;
		$('#txtCoyCpfNo').focus() ;
		$('#coy_err_mesg').text("CPF CSN number can not be blank.") ;
		return false ;
	}
	else 
		$('#coy_err_cpfno').hide() ;
	
	if ($('#txtCoyCpfRef').blank())
	{
		$('#coy_err_cpfref').show() ;
		$('#txtCoyCpfRef').focus() ;
		$('#coy_err_mesg').text("CPF Ref code can not be blank.") ;
		return false ;
	}
	else 
		$('#coy_err_cpfref').hide() ;
		
	if ($('#txtCoyRegNo').blank())
	{
		$('#coy_err_regno').show() ;
		$('#txtCoyRegNo').focus() ;
		$('#coy_err_mesg').text("Registeration no can not be blank.") ;
		return false ;
	}
	else 
		$('#coy_err_regno').hide() ;
	
	if ($('#cobCoyRegType').blank())
	{
		$('#coy_err_regtype').show() ;
		$('#cobCoyRegType').focus() ;
		$('#coy_err_mesg').text("You must select a Registeration no type.") ;
		return false ;
	}
	else 
		$('#coy_err_regtype').hide() ;
	
	if ($('#txtCoyAuthName').blank())
	{
		$('#coy_err_authname').show() ;
		$('#txtCoyAuthName').focus() ;
		$('#coy_err_mesg').text("Authorised person name can not be blank.") ;
		return false ;
	}
	else 
		$('#coy_err_authname').hide() ;
	return true ;
}