var emppay_obj = null ;
var emppay_id = "" ;

$(document).ready(function() 
{ 
	$('input').css('marginTop', 0); 
	$('input').css('marginBottom',0);
	$('select').css('marginTop',0) ;
	$('select').css('marginBottom',0) ;
	 
	var dteopt = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};

	$('#txtEmpPayStart').datepicker(dteopt) ;
	$('#txtEmpPayEnd').datepicker(dteopt) ;
	$('#txtEmpPayStart').keypress(function() { dateInput('/') } );
	$('#txtEmpPayEnd').keypress(function() { dateInput('/') } );
	$('#btnEmpPayClear').button().bind('click',clearEmpPay) ;
	$('#btnEmpPaySave').button().bind('click',saveEmpPay) ;
	$('#btnEmpPayList').button().bind('click',listEmpPay) ;
	$('#txtEmpPayValue').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	for (var i = 1;i< 7;i++) {
		$('#txtEmpPayIncome' + i).keypress(function() { numericInput(2,'.',0,2) ; }) ;
	}
	$('#emppay-tabs').tabs() ;
	$('#cobEmpPayCycle').focus() ;
	$(window).resize(resizeEmpPayGrid) ;
	resizeEmpPayGrid() ;
	clearEmpPay() ;
}) ;
function resizeEmpPayGrid() {

	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-emppay-entry").outerHeight() - $("#sbg-payentry-option").outerHeight() - 55;
	$("div#sbg-emppay-data").css("height", h +'px') ;		
}
function clearEmpPay() {
	$('#lblEmpPayIdName').text("") ;
	$('#lblEmpPayCoy').text("") ;
	$('#txtEmpPayStart').val("") ;
	$('#txtEmpPayEnd').val("") ;
	$('#txtEmpPayValue').val("") ;
	$('#chkEmpPaySdl').prop('checked',false) ;
	$('#chkEmpPayMbmf').prop('checked',false) ;
	$('#chkEmpPaySinda').prop('checked',false) ;
	$('#chkEmpPayCdac').prop('checked',false) ;
	$('#chkEmpPayEcf').prop('checked',false) ;
	$('#cobEmpPayCpf').val("") ;
	$('#txtEmpPayCpfNo').val("") ;
	//$('#cobEmpPayBank').val("") ;
	$('#txtEmpPayAcct').val("") ;
	for (var i =1 ;i< 7;i++) {
		$('#cobEmpPayIncome' + i).val("") ;
		$('#txtEmpPayIncome' + i).val("") ;
	}
	$('#emppay-tabs').tabs("option", "active", 0) ;
	$('#cobEmpPayCycle').focus() ;
	$('#btnEmpPaySave').attr('disabled','disabled') ;
	
	$('#emppay_err_mesg').text('') ;
	$('#emppay_err_cpfno').hide() ;
	$('#emppay_err_start').hide() ;
	$('#emppay_err_end').hide() ;
	
	emppay_obj = null;
}
function saveEmpPay() {
	if (validateEmpPay()) {
		var sdl = "0" ;
		var mbmf = "0" ;
		var sinda = "0" ;
		var cdac = "0" ;
		var ecf = "0" ;
		if ($('#chkEmpPaySdl').is(':checked'))
			sdl = "1" ;
		if ($('#chkEmpPayMbmf').is(':checked'))
			mbmf = "1" ;
		if ($('#chkEmpPaySinda').is(':checked'))
			sinda = "1" ;
		if ($('#chkEmpPayCdac').is(':checked'))
			cdac = "1" ;
		if ($('#chkEmpPayEcf').is(':checked'))
			ecf = "1" ;
		var data = { "type": C_UPDATE, "id": emppay_id, "bank": "",
			"acct": $('#txtEmpPayAcct').val(), "sdl": sdl, "mbmf": mbmf, "sinda": sinda, "cdac": cdac, "ecf": ecf, 
			"income": getEmpPayIncome(), "value": $('#txtEmpPayValue').val(), "cpftype": $('#cobEmpPayCpf').val(),
			"cpfno": $('#txtEmpPayCpfNo').val(),"start": $('#txtEmpPayStart').val, "end": $('#txtEmpPayEnd').val() };
		var url = "request.pzx?c=" + emppay_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onEmpPayResponse,emppay_obj) ;
	}
}

function editEmpPay(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + emppay_url + "&d=" + new Date().getTime();		
	emppay_obj = obj ;
	callServer(url,"json",data,showEmpPay,obj) ;
}
function listEmpPay() {
	var data = { "type": C_LIST, "coy": $('#cobEmpPayCoy').val(), "dept": $('#cobEmpPayDept').val() } ;
	var url = "request.pzx?c=" + emppay_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showEmpPayList,emppay_obj) ;
}
function showEmpPay(obj,resp) {
	if (resp.status == C_OK) {
		emppay_id = resp.data.id ;
		$('#lblEmpPayIdName').text(resp.data.id + " - " + resp.data.name) ;
		$('#lblEmpPayCoy').text(resp.data.coy + " - " + resp.data.dept) ;
		$('#txtEmpPayValue').val(resp.data.value) ;
		//$('#cobEmpPayBank').val(resp.data.bank) ;
		$('#txtEmpPayAcct').val(resp.data.acct) ;
		$('#cobEmpPayCpf').val(resp.data.cpftype) ;
		$('#txtEmpPayCpfNo').val(resp.data.cpfno) ;
		$('#txtEmpPayStart').val(resp.data.start) ;
		$('#txtEmpPayEnd').val(resp.data.end) ;
		if (resp.data.income != "")
			setEmpPayIncome(resp.data.income) ;
		if (resp.data.sdl == "1")
			$('#chkEmpPaySdl').prop("checked", true); 
		else
			$('#chkEmpPaySdl').prop("checked", false) ;
		if (resp.data.mbmf == "1")
			$('#chkEmpPayMbmf').prop("checked", true);
		else
			$('#chkEmpPayMbmf').prop("checked", false) ;
		if (resp.data.sinda == "1")
			$('#chkEmpPaySinda').prop("checked", true) ;
		else
			$('#chkEmpPaySinda').prop("checked", false) ;
		if (resp.data.cdac == "1")
			$('#chkEmpPayCdac').prop("checked", true) ;
		else
			$('#chkEmpPayCdac').prop("checked", false) ;
		if (resp.data.ecf == "1")
			$('#chkEmpPayEcf').prop("checked", true) ;
		else
			$('#chkEmpPayEcf').prop("checked", false) ;
		$('#txtEmpPayValue').focus() ;
		$('#btnEmpPaySave').prop('disabled','') ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function showEmpPayList(obj,resp) {
	var fr = '<tr><td style="width:50px;height:1px"></td>' +
			'<td style="width:200px"></td><td style="width:200px"></td>' +
			'<td style="width:150px"></td><td style="width:20px"></td></tr>' ;
	$('#sbg-emppay-table').html(fr + resp) ;
}
function onEmpPayResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearEmpPay() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateEmpPay() {
	$('#emppay_err_mesg').text('') ;
	if (!$('#cobEmpPayCpf').blank()) {
		if ($('#txtEmpPayCpfNo').blank()) {
			$('#emppay_err_cpfno').show() ;
			$('#txtEmpPayCpfNo').focus() ;
			$('#emppay_err_mesg').text("CPF No can not be blank.") ;
			return false ;
		} else 
			$('#emppay_err_cpfno').hide() ;
	}
	if ($('#txtEmpPayStart').val() != ""){
		if ($('#txtEmpPayStart').validDate())
			$('#emppay_err_start').hide() ;
		else {
			$('#emppay_err_start').show() ;
			$('#txtEmpPayStart').focus() ;
			$('#emppay_err_mesg').text("Invalid date entry for Pay start date") ;
			return false ;
		}
	}
	
	if ($('#txtEmpPayEnd').val() != "")	{
		if ($('#txtEmpPayEnd').validDate())
			$('#emppay_err_end').hide() ;
		else {
			$('#emppay_err_end').show() ;
			$('#txtEmpPayEnd').focus() ;
			$('#emppay_err_mesg').text("Invalid date entry for Pay end date") ;
			return false ;
		}
	}
	return true ;
}
function getEmpPayIncome() { 
	var data = "" ;
	var sep = "" ;
	for (var i = 1;i < 7;i++) {
		if ($('#cobEmpPayIncome' + i).val() != "" && $('#txtEmpPayIncome' + i).val() != "") {
			data = data + sep + $('#cobEmpPayIncome' + i).val() + ":" + $('#txtEmpPayIncome' + i).val() ;
			sep = "|";
		}
	}
	return data ;
}
function setEmpPayIncome(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	var fld = "" ;
	var idx = 1 ;
	for (var i = 0 ; i < data.length ; i++) {
		if (data[i] != "") {
			fld = data[i].split(':') ;
			$('#cobEmpPayIncome' + idx).val(fld[0]) ;
			$('#txtEmpPayIncome' + idx).val(fld[1]) ;
			idx++ ;
		}
	}
}