var payentry_obj = null ;
var payentry_id = "" ;

$(document).ready(function() 
{ 
	$('input').css('marginTop', 0); 
	$('input').css('marginBottom',0);
	$('select').css('marginTop',0) ;
	$('select').css('marginBottom',0) ;	

	$('#cobPayEntryYear').val(new Date().getFullYear()) ;
	$('#cobPayEntryMonth').val(new Date().getMonth()+1) ;
	$('#btnPayEntryClear').button().bind('click',clearPayEntry) ;
	$('#btnPayEntrySave').button().bind('click',savePayEntry) ;
	$('#btnPayEntryList').button().bind('click',listPayEntry) ;
	$('#btnPayEntryTotal').button().bind('click',refreshPayEntry) ;
	$('#btnPayEntryReset').button().bind('click',resetPayEntry) ;
	$('#txtPayEntryValue').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#txtPayEntryQty').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	for (var i = 1;i < 7;i++) {
		$('#txtPayEntryIncome' + i).keypress(function() { numericInput(2,'.',0,2) ; }) ;
		$('#txtPayEntryDeduct' + i).keypress(function() { numericInput(2,'.',0,2) ; }) ;
	}
	$('#payentry-tabs').tabs() ;
	$('#txtPayEntryValue').focus() ;
	$(window).resize(resizePayEntryGrid) ;
	resizePayEntryGrid() ;
	clearPayEntry() ;
}) ;
function resizePayEntryGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-payentry-entry").outerHeight() - $("#sbg-payentry-option").outerHeight() - 55;
	$("div#sbg-payentry-data").css("height", h +'px') ;		
}
function clearPayEntry() {
	$('#lblPayEntryIdName').text("") ;
	$('#lblPayEntryCoy').text("") ;
	$('#lblPayEntryDept').text("");
	$('#txtPayEntryQty').val("1");
	$('#txtPayEntryValue').val("") ;
	
	for (var i = 1;i < 7 ;i++) {
		$('#cobPayEntryIncome' + i).val("") ;
		$('#cobPayEntryDeduct' + i).val("") ;
		$('#txtPayEntryIncome' + i).val("") ;
		$('#txtPayEntryDeduct' + i).val("") ;
	}
	$('#lblPayEntryBasic').text("");
	$('#lblPayEntryIncome').text("");
	$('#lblPayEntryDeduct').text("");
	$('#lblPayEntryNet').text("");
	$('#payentry-tabs').tabs("option", "active", 0) ;
	$('#txtPayEntryValue').focus() ;
	$('#btnPayEntrySave').prop('disabled','disabled') ;
	payentry_obj = null;
	payentry_id = "" ;
}
function savePayEntry() {
	if (validatePayEntry()) {
		var data = { "type": C_UPDATE, "id": payentry_id, "qty": $('#txtPayEntryQty').val(),
			"value": $('#txtPayEntryValue').val(),"month": $('#cobPayEntryMonth').val(),
			"year": $('#cobPayEntryYear').val(), "income": getPayEntryIncome(),"deduct": getPayEntryDeduct()
		};
		//alert(JSON.stringify(data)) ;	
		var url = "request.pzx?c=" + payentry_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onPayEntryResponse,payentry_obj) ;
	}
}
function editPayEntry(id,obj) {
	var data = { "type": C_GET,"id": id, "month": $('#cobPayEntryMonth').val(), "year": $('#cobPayEntryYear').val() } ;
	var url = "request.pzx?c=" + payentry_url + "&d=" + new Date().getTime();		
	payentry_obj = obj ;
	callServer(url,"json",data,showPayEntry,obj) ;
}
function listPayEntry() {
	if (validatePayEntry()) {
		var data = { "type": C_LIST, "coy": $('#cobPayEntryCoy').val(), "dept": $('#cobPayEntryDept').val(), 
			"year": $('#cobPayEntryYear').val(),"month": $('#cobPayEntryMonth').val() } ;
		var url = "request.pzx?c=" + payentry_url + "&d=" + new Date().getTime() ;
		callServer(url,"html",data,showPayEntryList,payentry_obj) ;
	}
}
function getPayEntryIncome() { 
	var data = "" ;
	var sep = "" ;
	for (var i = 1;i < 7;i++) {
		if ($('#cobPayEntryIncome' + i).val() != "" && $('#txtPayEntryIncome' + i).val() != "") {
			data = data + sep + $('#cobPayEntryIncome' + i).val() + ":" + $('#txtPayEntryIncome' + i).val() ;
			sep = "|";
		}
	}
	return data ;
}
function getPayEntryDeduct() {
	var data = "" ;
	var sep = "" ;
	for (var i = 1;i < 7;i++) {
		if ($('#cobPayEntryDeduct' + i).val() != "" && $('#txtPayEntryDeduct' + i).val() != "") {
			data = data + sep + $('#cobPayEntryDeduct' + i).val() + ":" + $('#txtPayEntryDeduct' + i).val() ;
			sep = "|";
		}
	}
	return data ;
}
function setPayEntryIncome(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	var fld = "" ;
	var idx = 1 ;
	for (var i = 0 ; i < data.length ; i++) {
		if (data[i] != "") {
			fld = data[i].split(':') ;
			$('#cobPayEntryIncome' + idx).val(fld[0]) ;
			$('#txtPayEntryIncome' + idx).val(fld[1]) ;
			idx++ ;
		}
	}
}
function setPayEntryDeduct(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	var fld = "" ;
	var idx = 1 ;
	for (var i = 0 ; i < data.length ; i++) {
		if (data[i] != "") {
			fld = data[i].split(':') ;
			$('#cobPayEntryDeduct' + idx).val(fld[0]) ;
			$('#txtPayEntryDeduct' + idx).val(fld[1]) ;
			idx++ ;
		}
	}
}
function showPayEntry(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		clearPayEntry() ;
		payentry_id = resp.data.id ;
		$('#lblPayEntryIdName').text(resp.data.id + " - " + resp.data.name) ;
		$('#lblPayEntryCoy').text(resp.data.coy) ;
		$('#lblPayEntryDept').text(resp.data.dept) ;
		$('#txtPayEntryQty').val(resp.data.qty);
		$('#txtPayEntryValue').val(resp.data.value) ;
		if (resp.data.adds != "")
			setPayEntryIncome(resp.data.income) ;
		if (resp.data.deducts != "")
			setPayEntryDeduct(resp.data.deduct) ;
		$('#txtPayEntryQty').focus() ;
		$('#btnPayEntrySave').prop('disabled','') ;
		refreshPayEntry() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function showPayEntryList(obj,resp) {
	var fr = '<tr><td style="width:50px;height:1px"></td>' +
			'<td style="width:150px"></td>' + 
			'<td style="width:200px"></td>' +
			'<td style="width:150px"></td>' +
			'<td style="width:80px"></td>' + 
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'<td style="width:20px"></td></tr>' ;
	$('#sbg-payentry-table').html(fr + resp) ;
	$('#cobPayEntryMonth').prop('disabled','disabled') ;
	$('#cobPayEntryYear').prop('disabled','disabled') ;
}
function onPayEntryResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearPayEntry() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validatePayEntry() {
	$('#payentry_err_mesg').text('') ;
	return true ;
}
function refreshPayEntry() {
	var qty = 1 ;
	var amt = 0 ;
	var basic = 0 ;
	var income = 0 ;
	var deduct = 0 ;
	var tot = 0 ;
	if ($('#txtPayEntryQty').val() == "")
		qty = 1 ;
	else
		qty = parseFloat($('#txtPayEntryQty').val().replace(",","")) ;
	
	if ($('#txtPayEntryValue').val() == "")
		amt = 0 ;
	else
		amt = parseFloat($('#txtPayEntryValue').val().replace(",","")) ;
	
	basic = qty * amt ;
	for (var i = 1;i < 7 ;i++) {
		if ($('#cobPayEntryIncome' + i).val() !="" && $('#txtPayEntryIncome'+i).val != "") {
			amt = $('#txtPayEntryIncome'+i).val().replace(",","") ;
			income = income + parseFloat(amt) ;
		}
		
		if ($('#cobPayEntryDeduct' + i).val() != "" && $('#txtPayEntryDeduct' + i).val() != "") {
			amt = $('#txtPayEntryDeduct'+i).val().replace(",","") ;
			deduct = deduct + parseFloat(amt) ;
		}
	}
	deduct = deduct * -1 ;
	tot = basic + income + deduct ;
	$('#lblPayEntryNet').text(FormatCurrency(tot)) ;
	$('#lblPayEntryBasic').text(FormatCurrency(basic)) ;
	$('#lblPayEntryIncome').text(FormatCurrency(income)) ;
	$('#lblPayEntryDeduct').text(FormatCurrency(deduct)) ;
}
function resetPayEntry() {
	$('#cobPayEntryMonth').prop('disabled','') ;
	$('#cobPayEntryYear').prop('disabled','') ;
	$('#sbg-payentry-table').html('') ;
}