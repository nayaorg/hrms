var paytype_obj = null ;

$(document).ready(function() 
{ 
	$('input').css('marginTop', 0); 
	$('input').css('marginBottom',0);
	$('select').css('marginTop',0) ;
	$('select').css('marginBottom',0) ;
	
	$('#btnPayTypeClear').button().bind('click',clearPayType) ;
	$('#btnPayTypeUpdate').button().bind('click',updatePayType) ;
	$('#btnPayTypeAdd').button().bind('click',addPayType) ;
	$('#btnPayTypePrint').button().bind('click',printPayType) ;
	$('#paytype-tabs').tabs() ;
	$('#txtPayTypeDesc').focus() ;
	clearPayType();
	$(window).resize(resizePayTypeGrid) ;
	resizePayTypeGrid() ;
}) ;
function resizePayTypeGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-paytype-entry").outerHeight() - 55;
	$("div#sbg-paytype-data").css("height", h +'px') ;		
}
function clearPayType() {
	$('#txtPayTypeId').val("Auto") ;
	$('#txtPayTypeDesc').val("") ;
	$('#txtPayTypeRef').val("") ;
	$('#txtPayTypeText').val("") ;
	$('#cobPayTypeTax').val("0") ;
	$('#cobPayTypeIncome').val("0");
	$('#cobPayTypeWage').val("0") ;
	$('#paytype_err_desc').hide() ;
	$('#paytype-tabs').tabs("option", "active", 0) ;
	$('#txtPayTypeDesc').focus() ;
	$('#btnPayTypeUpdate').attr('disabled', 'disabled');
	
	$('#paytype_err_mesg').text('') ;
	$('#paytype_err_desc').hide() ;
	paytype_obj = null;
}
function updatePayType() {
	savePayType(C_UPDATE) ;
}
function addPayType() {
	savePayType(C_ADD) ;
}
function savePayType(type) {
	if (validatePayType()) {
		var data = { "type": type, "id": $('#txtPayTypeId').val(), "desc": $('#txtPayTypeDesc').val(),
			"refno": $('#txtPayTypeRef').val(), "text": $('#txtPayTypeText').val(), "wagetype": $('#cobPayTypeWage').val() , 
			"taxtype": $('#cobPayTypeTax').val(),"incometype": $('#cobPayTypeIncome').val(),"value": 0 };
		var url = "request.pzx?c=" + paytype_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onPayTypeResponse,paytype_obj) ;
	}
}
function printPayType() {
	var url = "report.pzx?c=" + paytype_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editPayType(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + paytype_url + "&d=" + new Date().getTime();		
	paytype_obj = obj ;
	callServer(url,"json",data,showPayType,obj) ;
}

function deletePayType(id,obj) {
	if (confirm("Confirm you want to delete pay type id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + paytype_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onPayTypeResponse,obj) ;
	}
}
function showPayType(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		$('#txtPayTypeId').val(resp.data.id) ;
		$('#txtPayTypeDesc').val(resp.data.desc) ;
		$('#txtPayTypeRef').val(resp.data.refno) ;
		$('#txtPayTypeText').val(resp.data.text) ;
		$('#cobPayTypeWage').val(resp.data.wagetype) ;
		$('#cobPayTypeTax').val(resp.data.taxtype);
		$('#cobPayTypeIncome').val(resp.data.incometype) ;
		$('#txtPayTypeDesc').focus() ;
		$('#btnPayTypeUpdate').removeAttr('disabled');
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onPayTypeResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-paytype-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtPayTypeDesc').val() + "</td>" + 
				"<td>" + $('#txtPayTypeRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editPayType(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deletePayType(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtPayTypeDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtPayTypeRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearPayType() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validatePayType() {
	$('#paytype_err_mesg').text('') ;
	if ($('#txtPayTypeDesc').blank())
	{
		$('#paytype_err_desc').show() ;
		$('#txtPayTypeDesc').focus() ;
		$('#paytype_err_mesg').text("Pay type description can not be blank.") ;
		return false ;
	}
	else 
		$('#paytype_err_desc').hide() ;
			
	return true ;
}