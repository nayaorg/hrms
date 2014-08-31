var usrgrp_obj = null ;

$(document).ready(function() 
{ 
	$('#btnUsrGrpClear').button().bind('click',clearUserGroup) ;
	$('#btnUsrGrpUpdate').button().bind('click',updateUserGroup) ;
	$('#btnUsrGrpAdd').button().bind('click',addUserGroup) ;
	$('#btnUsrGrpPrint').button().bind('click',printUserGroup) ;
	$('#usrgrp-tabs').tabs() ;
	$('#txtUsrGrpDesc').focus() ;
	$(window).resize(resizeUsrGrpGrid) ;
	resizeUsrGrpGrid() ;
	clearUserGroup();
}) ;
function resizeUsrGrpGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-usrgrp-entry").outerHeight() - 55;
	$("div#sbg-usrgrp-data").css("height", h +'px') ;		
}
function clearUserGroup() {
	$('#txtUsrGrpId').val("Auto") ;
	$('#txtUsrGrpDesc').val("") ;
	$('#usrgrp-tabs').find(':checkbox').each(function() {
		$(this).prop('checked',false) ;
	});
	$('#usrgrp-tabs').tabs("option", "active", 0) ;
	$('#txtUsrGrpDesc').focus() ;
	$('#btnUsrGrpUpdate').prop('disabled','disabled');
	$('#usrgrp_err_desc').hide() ;
	$('#usrgrp_err_mesg').text('') ;
	usrgrp_obj = null;
}
function updateUserGroup() {
	saveUserGroup(C_UPDATE) ;
}
function addUserGroup() {
	saveUserGroup(C_ADD) ;
}
function saveUserGroup(type) {
	if (validateUserGroup()) {
		var data = { "type": type, "id": $('#txtUsrGrpId').val(), "desc": $('#txtUsrGrpDesc').val(),
		"admin": getAdmin(),"hr": getHr(), "payroll": getPayroll()};
		var url = "request.pzx?c=" + usrgrp_url + "&d=" + new Date().getTime() ;
		//alert(JSON.stringify(data));
		callServer(url,"json",data,onUserGroupResponse,usrgrp_obj) ;
	}
}
function printUserGroup() {
	var url = "report.pzx?c=" + usrgrp_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editUserGroup(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + usrgrp_url + "&d=" + new Date().getTime();		
	usrgrp_obj = obj ;
	callServer(url,"json",data,showUserGroup,obj) ;
}

function deleteUserGroup(id,obj) {
	if (confirm("Confirm you want to delete user group id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + usrgrp_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onUserGroupResponse,obj) ;
	}
}
function showUserGroup(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtUsrGrpId').val(resp.data.id) ;
		$('#txtUsrGrpDesc').val(resp.data.desc) ;
		$('#btnUsrGrpUpdate').prop('disabled','') ;
		$('#chkAdmSet').prop('checked',resp.data.admin.setting) ;
		$('#chkAdmCoy').prop('checked',resp.data.admin.company) ;
		$('#chkAdmUser').prop('checked',resp.data.admin.user) ;
		$('#chkAdmGroup').prop('checked',resp.data.admin.group);
		$('#chkAdmReset').prop('checked',resp.data.admin.reset) ;
		$('#chkHrEmp').prop('checked',resp.data.hr.employee) ;
		$('#chkHrDept').prop('checked',resp.data.hr.dept) ;
		$('#chkHrType').prop('checked',resp.data.hr.type) ;
		$('#chkHrJob').prop('checked',resp.data.hr.job) ;
		$('#chkHrNat').prop('checked',resp.data.hr.nat) ;
		$('#chkHrRace').prop('checked',resp.data.hr.race) ;
		$('#chkHrPermit').prop('checked',resp.data.hr.permit) ;
		$('#chkPayBank').prop('checked',resp.data.payroll.bank) ;
		$('#chkPayCpf').prop('checked',resp.data.payroll.cpf) ;
		$('#chkPayType').prop('checked',resp.data.payroll.type) ;
		$('#chkPayEmp').prop('checked',resp.data.payroll.employee) ;
		$('#chkPayCreate').prop('checked',resp.data.payroll.create) ;
		$('#chkPayEntry').prop('checked',resp.data.payroll.entry) ;
		$('#chkPayList').prop('checked',resp.data.payroll.paylist) ;
		$('#chkPaySlip').prop('checked',resp.data.payroll.payslip) ;
		$('#chkCpfList').prop('checked',resp.data.payroll.cpflist) ;
		$('#chkCpfEntry').prop('checked',resp.data.payroll.cpfentry) ;
		$('#chkIncomeYear').prop('checked',resp.data.payroll.incomeyear) ;
		$('#usrgrp-tabs').tabs('select',0) ;
		$('#txtUsrGrpDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onUserGroupResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-usrgrp-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtUsrGrpDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editUserGroup(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteUserGroup(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtUsrGrpDesc').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearUserGroup() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateUserGroup() {
	$('#usrgrp_err_mesg').text('') ;
	
	if ($('#txtUsrGrpDesc').blank())
	{
		$('#usrgrp_err_desc').show() ;
		$('#txtUsrGrpDesc').focus() ;
		$('#usrgrp_err_mesg').text("User Group description can not be blank.") ;
		return false ;
	}
	else 
		$('#usrgrp_err_desc').hide() ;
			
	return true ;
}
function getAdmin() {
	return { "setting": getAccess('chkAdmSet'),
		"company": getAccess('chkAdmCoy'),
		"user": getAccess('chkAdmUser'),
		"group": getAccess('chkAdmGroup'),
		"reset": getAccess('chkAdmReset') } ;
}
function getHr() {
	return { "employee": getAccess('chkHrEmp'),
		"type": getAccess('chkHrType'),
		"dept": getAccess('chkHrDept'),
		"job": getAccess('chkHrJob'),
		"nat": getAccess('chkHrNat'),
		"race": getAccess('chkHrRace'),
		"permit": getAccess('chkHrPermit') } ;
}
function getPayroll() {
	return { "bank": getAccess('chkPayBank'),
		"employee": getAccess('chkPayEmp'),
		"type": getAccess('chkPayType'),
		"cpf": getAccess('chkPayCpf'),
		"create": getAccess('chkPayCreate'),
		"entry": getAccess('chkPayEntry'),
		"paylist": getAccess('chkPayList'),
		"payslip": getAccess('chkPaySlip'),
		"cpflist": getAccess('chkCpfList'),
		"cpfentry": getAccess('chkCpfEntry'),
		"incomeyear": getAccess('chkIncomeYear')} ;
}
function getAccess(id) {
	if ($('#' + id).is(':checked'))
		return "1";
	else 
		return "0";
}