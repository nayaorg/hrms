var dept_obj = null ;

$(document).ready(function() 
{ 
	$('#btnDeptClear').button().bind('click',clearDept) ;
	$('#btnDeptUpdate').button().bind('click',updateDept) ;
	$('#btnDeptAdd').button().bind('click',addDept) ;
	$('#btnDeptPrint').button().bind('click',printDept) ;
	$('#dept-tabs').tabs() ;
	$('#txtDeptDesc').focus() ;
	$(window).resize(resizeDeptGrid) ;
	resizeDeptGrid() ;
	clearDept() ;
}) ;
function resizeDeptGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-dept-entry").outerHeight() - 55;
	$("div#sbg-dept-data").css("height", h +'px') ;		
}
function clearDept() {
	$('#txtDeptId').val("Auto") ;
	$('#txtDeptDesc').val("") ;
	$('#txtDeptRef').val("") ;
	$('#dept-tabs').tabs("option", "active", 0) ;
	$('#txtDeptDesc').focus() ;
	$('#btnDeptUpdate').prop('disabled','disabled') ;
	
	$('#dept_err_mesg').text('') ;
	$('#dept_err_desc').hide() ;
	dept_obj = null;
}
function updateDept() {
	saveDept(C_UPDATE) ;
}
function addDept() {
	saveDept(C_ADD) ;
}
function saveDept(type) {
	if (validateDept()) {
		var data = { "type": type, "id": $('#txtDeptId').val(), "desc": $('#txtDeptDesc').val(),"refno": $('#txtDeptRef').val() };
		var url = "request.pzx?c=" + dept_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onDeptResponse,dept_obj) ;
	}
}
function printDept() {
	var url = "report.pzx?c=" + dept_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editDept(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + dept_url + "&d=" + new Date().getTime() ;		
	dept_obj = obj ;
	callServer(url,"json",data,showDept,obj) ;
}

function deleteDept(id,obj) {
	if (confirm("Confirm you want to delete department id : " + id + "?")) {
		//$($(obj).closest("tr")).remove() ;
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + dept_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onDeptResponse,obj) ;
	}
}
function showDept(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtDeptId').val(resp.data.id) ;
		$('#txtDeptDesc').val(resp.data.desc) ;
		$('#txtDeptRef').val(resp.data.refno) ;
		$('#btnDeptUpdate').prop('disabled','') ;
		$('#txtDeptDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onDeptResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-dept-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtDeptDesc').val() + "</td>" + 
				"<td>" + $('#txtDeptRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editDept(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteDept(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtDeptDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtDeptRef').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtDeptDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtDeptRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearDept() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateDept() {
	$('#dept_err_mesg').text('') ;
	if ($('#txtDeptDesc').blank())
	{
		$('#dept_err_desc').show() ;
		$('#txtDeptDesc').focus() ;
		$('#dept_err_mesg').text("department description can not be blank.") ;
		return false ;
	}
	else 
		$('#dept_err_desc').hide() ;
			
	return true ;
}