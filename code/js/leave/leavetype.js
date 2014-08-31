var leavetype_obj = null ;

$('#btnLeaveTypeClear').button().bind('click',clearLeaveType) ;
$('#btnLeaveTypeUpdate').button().bind('click',updateLeaveType) ;
$('#btnLeaveTypeAdd').button().bind('click',addLeaveType) ;
$('#btnLeaveTypePrint').button().bind('click',printLeaveType) ;
$('#leavetype-tabs').tabs() ;
$('#txtLeaveTypeDesc').focus() ;
clearLeaveType();
$(window).resize(resizeLeaveTypeGrid) ;
resizeLeaveTypeGrid() ;

function resizeLeaveTypeGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-leavetype-entry").outerHeight() - 55;
	$("div#sbg-leavetype-data").css("height", h +'px') ;		
}
function clearLeaveType() {
	$('#txtLeaveTypeId').val("Auto") ;
	$('#txtLeaveTypeDesc').val("") ;
	$('#txtLeaveTypePeriod').val("1") ;
	$('#leavetype-tabs').tabs("option", "active", 0) ;
	$('#txtLeaveTypeDesc').focus() ;
	$('#btnLeaveTypeUpdate').prop('disabled', 'disabled');
	$('#leavetype_err_mesg').text('') ;
	$('#leavetype_err_desc').hide() ;
	leavetype_obj = null;
}
function updateLeaveType() {
	saveLeaveType(C_UPDATE) ;
}
function addLeaveType() {
	saveLeaveType(C_ADD) ;
}
function saveLeaveType(type) {
	if (validateLeaveType()) {
		var data = { "type": type, "id": $('#txtLeaveTypeId').val(), "desc": $('#txtLeaveTypeDesc').val(),
			"period": $('#txtLeaveTypePeriod').val()};
		var url = "request.pzx?c=" + leavetype_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onLeaveTypeResponse,leavetype_obj) ;
	}
}
function printLeaveType() {
	var url = "report.pzx?c=" + leavetype_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editLeaveType(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + leavetype_url + "&d=" + new Date().getTime();		
	leavetype_obj = obj ;
	callServer(url,"json",data,showLeaveType,obj) ;
}

function deleteLeaveType(id,obj) {
	if (confirm("Confirm you want to delete leave type id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + leavetype_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onLeaveTypeResponse,obj) ;
	}
}
function showLeaveType(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		$('#txtLeaveTypeId').val(resp.data.id) ;
		$('#txtLeaveTypeDesc').val(resp.data.desc) ;
		$('#txtLeaveTyepPeriod').val(resp.data.period) ;
		$('#txtLeaveTypeDesc').focus() ;
		$('#btnLeaveTypeUpdate').prop('disabled','');
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onLeaveTypeResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-leavetype-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtLeaveTypeDesc').val() + "</td>" + 
				"<td>" + $('#txtLeaveTypePeriod').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editLeaveType(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteLeaveType(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtLeaveTypeDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtLeaveTypePeriod').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearLeaveType() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateLeaveType() {
	$('#leavetype_err_mesg').text('') ;
	if ($('#txtLeaveTypeDesc').blank())
	{
		$('#leavetype_err_desc').show() ;
		$('#txtLeaveTypeDesc').focus() ;
		$('#leavetype_err_mesg').text("Leave Type description can not be blank.") ;
		return false ;
	}
	else 
		$('#leavetype_err_desc').hide() ;

	return true ;
}