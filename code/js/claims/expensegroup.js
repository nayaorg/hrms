
var expense_group_obj = null ;

$(document).ready(function() {
	$('#btnExpenseGroupAdd').button().bind('click',addExpenseGroup) ;
	$('#btnExpenseGroupClear').button().bind('click',clearExpenseGroup) ;
	$('#btnExpenseGroupUpdate').button().bind('click',updateExpenseGroup) ;
	$('#btnExpenseGroupPrint').button().bind('click',printExpenseGroup) ;
	$('#btnExpenseGroupUpdate').hide() ;
	
	$('#expense-group-tabs').tabs() ;
	
	$('#txtExpenseGroupDesc').focus() ;
	
	clearExpenseGroup();
	$(window).resize(resizeExpenseGroupGrid) ;
	resizeExpenseGroupGrid() ;
}) ;
function resizeExpenseGroupGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-expense-group-entry").outerHeight() - 55;
	$("div#sbg-expense-group-data").css("height", h +'px') ;
}
function clearExpenseGroup() {
	$('#txtExpenseGroupId').val("Auto") ;
	$('#txtExpenseGroupRef').val("") ;
	$('#txtExpenseGroupDesc').val("") ;
	$('#expense-group-tabs').tabs("option", "active", 0) ;
	$('#expense_group_err_mesg').text('') ;
	$('#expense_group_err_desc').hide() ;
	$('#expense_group_err_desc').hide() ;
	
	$('#btnExpenseGroupUpdate').hide() ;
	$('#btnExpenseGroupAdd').show() ;
	
	expense_group_obj = null;
}
function updateExpenseGroup() {
	saveExpenseGroup(C_UPDATE) ;
}
function addExpenseGroup() {
	saveExpenseGroup(C_ADD) ;
}
function saveExpenseGroup(type) {
	if (validateExpenseGroup()) {
		var data = { "type": type, "id": $('#txtExpenseGroupId').val(), "ref": $('#txtExpenseGroupRef').val(), "desc": $('#txtExpenseGroupDesc').val() };
		var url = "request.pzx?c=" + expense_group_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onExpenseGroupResponse,expense_group_obj) ;
	}
}

function editExpenseGroup(id,obj) {
	var data = {"type": C_GET, "id": id} ;
	var url = "request.pzx?c=" + expense_group_url + "&d=" + new Date().getTime();		
	expense_group_obj = obj ;
	callServer(url,"json",data,showExpenseGroup,obj) ;
}

function deleteExpenseGroup(id,obj) {
	if (confirm("Confirm you want to delete expense item group id : " + id + "?")) {
		var data = {"type": C_DELETE, "id": id} ;
		var url = "request.pzx?c=" + expense_group_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onExpenseGroupResponse,obj) ;
	}
}
function showExpenseGroup(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtExpenseGroupId').val(resp.data.id) ;
		$('#txtExpenseGroupRef').val(resp.data.ref) ;
		$('#txtExpenseGroupDesc').val(resp.data.desc) ;
		
		$('#txtExpenseGroupDesc').focus() ;
		$('#btnExpenseGroupUpdate').show() ;
		$('#btnExpenseGroupAdd').hide() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onExpenseGroupResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-expense-group-table tr:first').after("<tr>" + 
				"<td>" + resp.data + "</td>" + 
				"<td>" + $('#txtExpenseGroupDesc').val() + "</td>" + 
				"<td>" + $('#txtExpenseGroupRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editExpenseGroup(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteExpenseGroup(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(0)').html($('#txtExpenseGroupId').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtExpenseGroupDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtExpenseGroupRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearExpenseGroup() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateExpenseGroup() {
	$('#expense_group_err_mesg').text('') ;
	if ($('#txtExpenseGroupDesc').blank())
	{
		$('#expense_group_err_desc').show() ;
		$('#txtExpenseGroupDesc').focus() ;
		$('#expense_group_err_mesg').text("Expense Item Group Description can not be blank.") ;
		return false ;
	}
	else 
		$('#expense_group_err_desc').hide() ;

	return true ;
}
function printExpenseGroup() {
	var url = "report.pzx?c=" + expense_group_url + "&d=" + new Date().getTime();
	showReport(url) ;
}