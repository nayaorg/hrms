var cpfentry_obj = null ;
var cpfentry_id = "" ;

$(document).ready(function() 
{ 
	$('input').css('marginTop', 0); 
	$('input').css('marginBottom',0);
	$('select').css('marginTop',0) ;
	$('select').css('marginBottom',0) ;	

	$('#cobCpfEntryYear').val(new Date().getFullYear()) ;
	$('#cobCpfEntryMonth').val(new Date().getMonth()+1) ;
	$('#btnCpfEntryClear').button().bind('click',clearCpfEntry) ;
	$('#btnCpfEntrySave').button().bind('click',saveCpfEntry) ;
	$('#btnCpfEntryList').button().bind('click',listCpfEntry) ;
	$('#btnCpfEntryReset').button().bind('click',resetCpfEntry) ;
	$('#txtCpfEntryEmp').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#txtCpfEntryCoy').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#txtCpfEntrySdl').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#txtCpfEntryMbmf').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#txtCpfEntrySinda').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#txtCpfEntryCdac').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#txtCpfEntryEcf').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#cpfentry-tabs').tabs() ;
	$('#txtCpfEntryEmp').focus() ;
	$(window).resize(resizeCpfEntryGrid) ;
	resizeCpfEntryGrid() ;
	clearCpfEntry() ;
}) ;
function resizeCpfEntryGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-cpfentry-entry").outerHeight() - $("#sbg-cpfentry-option").outerHeight() - 55;
	$("div#sbg-cpfentry-data").css("height", h +'px') ;		
}
function clearCpfEntry() {
	$('#lblCpfEntryIdName').text("") ;
	$('#lblCpfEntryCoy').text("") ;
	$('#lblCpfEntryDept').text("");
	$('#txtCpfEntryEmp').val("");
	$('#txtCpfEntryCoy').val("") ;
	$('#lblCpfEntryOw').text("");
	$('#lblCpfEntryAw').text("");
	$('#txtCpfEntrySdl').val("");
	$('#txtCpfEntryMbmf').val("") ;
	$('#txtCpfEntrySinda').val("");
	$('#txtCpfEntryCdac').val("") ;
	$('#txtCpfEntryEcf').val("");
	$('#cpfentry-tabs').tabs("option", "active", 0) ;
	$('#txtCpfEntryEmp').focus() ;
	$('#btnCpfEntrySave').prop('disabled','disabled') ;
	cpfentry_obj = null;
	cpfentry_id = "" ;
}
function saveCpfEntry() {
	if (validateCpfEntry()) {
		var data = { "type": C_UPDATE, "id": cpfentry_id, "cpfemp": $('#txtCpfEntryEmp').val(),
			"cpfcoy": $('#txtCpfEntryCoy').val(),"month": $('#cobCpfEntryMonth').val(),
			"year": $('#cobCpfEntryYear').val(), "sdl": $('#txtCpfEntrySdl').val(),
			"mbmf": $('#txtCpfEntryMbmf').val(),"sinda": $('#txtCpfEntrySinda').val(),
			"cdac": $('#txtCpfEntryCdac').val(),"ecf": $('#txtCpfEntryEcf').val() };
		//alert(JSON.stringify(data)) ;	
		var url = "request.pzx?c=" + cpfentry_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onCpfEntryResponse,cpfentry_obj) ;
	}
}
function editCpfEntry(id,obj) {
	var data = { "type": C_GET,"id": id, "month": $('#cobCpfEntryMonth').val(), "year": $('#cobCpfEntryYear').val() } ;
	var url = "request.pzx?c=" + cpfentry_url + "&d=" + new Date().getTime();		
	cpfentry_obj = obj ;
	callServer(url,"json",data,showCpfEntry,obj) ;
}
function listCpfEntry() {
	if (validateCpfEntry()) {
		var data = { "type": C_LIST, "coy": $('#cobCpfEntryCoy').val(), "dept": $('#cobCpfEntryDept').val(), 
			"year": $('#cobCpfEntryYear').val(),"month": $('#cobCpfEntryMonth').val() } ;
		var url = "request.pzx?c=" + cpfentry_url + "&d=" + new Date().getTime() ;
		callServer(url,"html",data,showCpfEntryList,cpfentry_obj) ;
	}
}
function showCpfEntry(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		//clearCpfEntry() ;
		cpfentry_id = resp.data.id ;
		$('#lblCpfEntryIdName').text(resp.data.id + " - " + resp.data.name) ;
		$('#lblCpfEntryCoy').text(resp.data.coy) ;
		$('#lblCpfEntryDept').text(resp.data.dept) ;
		$('#lblCpfEntryOw').text(resp.data.ow) ;
		$('#lblCpfEntryAw').text(resp.data.aw) ;
		$('#txtCpfEntryEmp').val(resp.data.cpfemp);
		$('#txtCpfEntryCoy').val(resp.data.cpfcoy) ;
		$('#txtCpfEntrySdl').val(resp.data.sdl);
		$('#txtCpfEntryMbmf').val(resp.data.mbmf) ;
		$('#txtCpfEntrySinda').val(resp.data.sinda);
		$('#txtCpfEntryCdac').val(resp.data.cdac) ;
		$('#txtCpfEntryEcf').val(resp.data.ecf);
		$('#txtCpfEntryEmp').focus() ;
		$('#btnCpfEntrySave').prop('disabled','') ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function showCpfEntryList(obj,resp) {
	var fr = '<tr><td style="width:50px;height:1px"></td>' +
			'<td style="width:150px"></td>' + 
			'<td style="width:200px"></td>' +
			'<td style="width:150px"></td>' +
			'<td style="width:80px"></td>' + 
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'<td style="width:20px"></td></tr>' ;
	$('#sbg-cpfentry-table').html(fr + resp) ;
	$('#cobCpfEntryMonth').prop('disabled','disabled') ;
	$('#cobCpfEntryYear').prop('disabled','disabled') ;
}
function onCpfEntryResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		$($(obj).closest('tr')).children('td:eq(6)').text($('#txtCpfEntryEmp').val()) ;
		$($(obj).closest('tr')).children('td:eq(7)').text($('#txtCpfEntryCoy').val()) ;
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearCpfEntry() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateCpfEntry() {
	$('#cpfentry_err_mesg').text('') ;
	return true ;
}
function resetCpfEntry() {
	$('#cobCpfEntryMonth').prop('disabled','') ;
	$('#cobCpfEntryYear').prop('disabled','') ;
	$('#sbg-cpfentry-table').html('') ;
}