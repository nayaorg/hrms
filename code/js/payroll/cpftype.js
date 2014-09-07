var cpftype_obj = null ;

$(document).ready(function() 
{ 
	
	$('input').css('marginTop', 0); 
	$('input').css('marginBottom',0);
	$('select').css('marginTop',0) ;
	$('select').css('marginBottom',0) ;
	
	$('#btnCpfTypeClear').button().bind('click',clearCpfType) ;
	$('#btnCpfTypeUpdate').button().bind('click',updateCpfType) ;
	$('#btnCpfTypeAdd').button().bind('click',addCpfType);
	$('#btnCpfTypePrint').button().bind('click',printCpfType) ;
	$('#txtCpfTypeOw').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	$('#txtCpfTypeAw').keypress(function() { numericInput(2,'.',0,2) ; }) ;
	for (var i = 1;i < 7;i++) {
		for (var j= 1;j< 7;j++) {
			$('#txtCpfTypeEmpFix_' + j + "_" + i).keypress(function() { numericInput(2,'.',0,4) ; }) ;
			$('#txtCpfTypeEmpRate_' + j + "_" + i).keypress(function() { numericInput(2,'.',0,4) ; }) ;
			$('#txtCpfTypeEmpOff_' + j + "_" + i).keypress(function() { numericInput(2,'.',0,4) ; }) ;
			$('#txtCpfTypeCoyRate_' + j + "_" + i).keypress(function() { numericInput(2,'.',0,4) ; }) ;
			$('#txtCpfTypeCoyFix_' + j + "_" + i).keypress(function() { numericInput(2,'.',0,4) ; }) ;
			$('#txtCpfTypeCoyOff_' + j + "_" + i).keypress(function() { numericInput(2,'.',0,4) ; }) ;
		}
	}
	$('#cpftype-tabs').tabs() ;
	$('#txtCpfTypeDesc').focus() ;
	$(window).resize(resizeCpfTypeGrid) ;
	resizeCpfTypeGrid() ;
	clearCpfType();
}) ;
function resizeCpfTypeGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-cpftype-entry").outerHeight() - 55;
	$("div#sbg-cpftype-data").css("height", h +'px') ;		
}
function clearCpfType() {
	$('#txtCpfTypeId').val("Auto") ;
	$('#txtCpfTypeDesc').val("") ;
	$('#txtCpfTypeOw').val("") ;
	$('#txtCpfTypeAw').val("") ;
	for (var i = 1;i < 7;i++) {
		for (var j= 1;j< 7;j++) {
			$('#txtCpfTypeEmpFix_' + j + "_" + i).val("");
			$('#txtCpfTypeEmpRate_' + j + "_" + i).val("");
			$('#txtCpfTypeEmpOff_' + j + "_" + i).val("");
			$('#txtCpfTypeCoyRate_' + j + "_" + i).val("");
			$('#txtCpfTypeCoyFix_' + j + "_" + i).val("");
			$('#txtCpfTypeCoyOff_' + j + "_" + i).val("") ;
		}
	}
	$('#cpftype-tabs').tabs("option", "active", 0) ;
	$('#txtCpfTypeDesc').focus() ;
	$('#btnCpfTypeUpdate').attr('disabled','disabled') ;
	$('#cpftype_err_mesg').text('') ;
	$('#cpftype_err_desc').hide() ;
	cpftype_obj = null;
}
function updateCpfType() {
	saveCpfType(C_UPDATE) ;
}
function addCpfType() {
	saveCpfType(C_ADD) ;
}
function saveCpfType(type) {
	if (validateCpfType()) {
		var data = { "type": type, "id": $('#txtCpfTypeId').val(), "desc": $('#txtCpfTypeDesc').val(),
			"ow": $('#txtCpfTypeOw').val(), "aw":$('#txtCpfTypeAw').val(),
			"empfix": getCpfTypeEmpFix(), "emprate": getCpfTypeEmpRate(), "empoff": getCpfTypeEmpOff(),
			"coyfix": getCpfTypeCoyFix(), "coyrate": getCpfTypeCoyRate(), "coyoff": getCpfTypeCoyOff() };
		var url = "request.pzx?c=" + cpftype_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onCpfTypeResponse,cpftype_obj) ;
	}
}
function printCpfType() {
	var url = "report.pzx?c=" + cpftype_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editCpfType(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + cpftype_url + "&d=" + new Date().getTime();		
	cpftype_obj = obj ;
	callServer(url,"json",data,showCpfType,obj) ;
}

function deleteCpfType(id,obj) {
	if (confirm("Confirm you want to delete cpf type id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + cpftype_url + "&d=" + new Date().getTime();
		callServer(url,"json",data,onCpfTypeResponse,obj) ;
	}
}
function showCpfType(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtCpfTypeId').val(resp.data.id) ;
		$('#txtCpfTypeDesc').val(resp.data.desc) ;
		$('#txtCpfTypeOw').val(resp.data.ow) ;
		$('#txtCpfTypeAw').val(resp.data.aw) ;
		setCpfTypeEmpFix(resp.data.empfix) ;
		setCpfTypeEmpRate(resp.data.emprate) ;
		setCpfTypeEmpOff(resp.data.empoff) ;
		setCpfTypeCoyFix(resp.data.coyfix) ;
		setCpfTypeCoyRate(resp.data.coyrate) ;
		setCpfTypeCoyOff(resp.data.coyoff) ;
		$('#txtCpfTypeDesc').focus() ;
		$('#btnCpfTypeUpdate').prop('disabled','') ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onCpfTypeResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-cpftype-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtCpfTypeDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editCpfType(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteCpfType(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtCpfTypeDesc').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearCpfType() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateCpfType() {
	$('#cpftype_err_mesg').text('') ;
	if ($('#txtCpfTypeDesc').blank())
	{
		$('#cpftype_err_desc').show() ;
		$('#txtCpfTypeDesc').focus() ;
		$('#cpftype_err_mesg').text("CPF type description can not be blank.") ;
		return false ;
	}
	else 
		$('#cpftype_err_desc').hide() ;
			
	return true ;
}
function setCpfTypeEmpFix(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	var data = datas.split('|') ;
	for (var i = 1 ; i < 7 ; i++) {
		if (data[i] != "") {
			fld = data[i-1].split(':') ;
			for (var j = 1 ; j < 7 ; j++) {
				$('#txtCpfTypeEmpFix_'+i+'_'+j).val(fld[j-1]) ;
			}
		}
	}
}
function setCpfTypeEmpRate(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	for (var i = 1 ; i < 7 ; i++) {
		if (data[i] != "") {
			fld = data[i-1].split(':') ;
			for (var j = 1 ; j < 7 ; j++) {
				$('#txtCpfTypeEmpRate_'+i+'_'+j).val(fld[j-1]) ;
			}
		}
	}
}
function setCpfTypeEmpOff(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	for (var i = 1 ; i < 7 ; i++) {
		if (data[i] != "") {
			fld = data[i-1].split(':') ;
			for (var j = 1 ; j < 7 ; j++) {
				$('#txtCpfTypeEmpOff_'+i+'_'+j).val(fld[j-1]) ;
			}
		}
	}
}
function setCpfTypeCoyFix(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	for (var i = 1 ; i < 7 ; i++) {
		if (data[i] != "") {
			fld = data[i-1].split(':') ;
			for (var j = 1 ; j < 7 ; j++) {
				$('#txtCpfTypeCoyFix_'+i+'_'+j).val(fld[j-1]) ;
			}
		}
	}
}
function setCpfTypeCoyRate(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	for (var i = 1 ; i < 7 ; i++) {
		if (data[i] != "") {
			fld = data[i-1].split(':') ;
			for (var j = 1 ; j < 7 ; j++) {
				$('#txtCpfTypeCoyRate_'+i+'_'+j).val(fld[j-1]) ;
			}
		}
	}
}
function setCpfTypeCoyOff(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	for (var i = 1 ; i < 7 ; i++) {
		if (data[i] != "") {
			fld = data[i-1].split(':') ;
			for (var j = 1 ; j < 7 ; j++) {
				$('#txtCpfTypeCoyOff_'+i+'_'+j).val(fld[j-1]) ;
			}
		}
	}
}
function getCpfTypeEmpFix() {
	var data = "" ;
	var sep1 = "" ;
	var sep2 = "" ;
	var income = "" ;
	for (var i = 1;i < 7;i++) {
		income = "" ;
		sep1 = "";
		for (var j = 1;j < 7;j++) {
			income = income + sep1 + $('#txtCpfTypeEmpFix_' + i + '_' + j).val() ;
			sep1 = ":";
		}
		data = data + sep2 + income ;
		sep2 = "|" ;
	}
	return data ;
}
function getCpfTypeEmpRate() {
	var data = "" ;
	var sep1 = "" ;
	var sep2 = "" ;
	var income = "" ;
	for (var i = 1;i < 7;i++) {
		income = "" ;
		sep1 = "";
		for (var j = 1;j < 7;j++) {
			income = income + sep1 + $('#txtCpfTypeEmpRate_' + i + '_' + j).val() ;
			sep1 = ":";
		}
		data = data + sep2 + income ;
		sep2 = "|" ;
	}
	return data ;
}
function getCpfTypeEmpOff() {
	var data = "" ;
	var sep1 = "" ;
	var sep2 = "" ;
	var income = "" ;
	for (var i = 1;i < 7;i++) {
		income = "" ;
		sep1 = "";
		for (var j = 1;j < 7;j++) {
			income = income + sep1 + $('#txtCpfTypeEmpOff_' + i + '_' + j).val() ;
			sep1 = ":";
		}
		data = data + sep2 + income ;
		sep2 = "|" ;
	}
	return data ;
}
function getCpfTypeCoyFix() {
	var data = "" ;
	var sep1 = "" ;
	var sep2 = "" ;
	var income = "" ;
	for (var i = 1;i < 7;i++) {
		income = "" ;
		sep1 = "";
		for (var j = 1;j < 7;j++) {
			income = income + sep1 + $('#txtCpfTypeCoyFix_' + i + '_' + j).val() ;
			sep1 = ":";
		}
		data = data + sep2 + income ;
		sep2 = "|" ;
	}
	return data ;
}
function getCpfTypeCoyRate() {
	var data = "" ;
	var sep1 = "" ;
	var sep2 = "" ;
	var income = "" ;
	for (var i = 1;i < 7;i++) {
		income = "" ;
		sep1 = "";
		for (var j = 1;j < 7;j++) {
			income = income + sep1 + $('#txtCpfTypeCoyRate_' + i + '_' + j).val() ;
			sep1 = ":";
		}
		data = data + sep2 + income ;
		sep2 = "|" ;
	}
	return data ;
}
function getCpfTypeCoyOff() {
	var data = "" ;
	var sep1 = "" ;
	var sep2 = "" ;
	var income = "" ;
	for (var i = 1;i < 7;i++) {
		income = "" ;
		sep1 = "";
		for (var j = 1;j < 7;j++) {
			income = income + sep1 + $('#txtCpfTypeCoyOff_' + i + '_' + j).val() ;
			sep1 = ":";
		}
		data = data + sep2 + income ;
		sep2 = "|" ;
	}
	return data ;
}