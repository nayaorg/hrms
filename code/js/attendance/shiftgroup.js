var shift_group_obj = null ;


$('#btnShiftGroupClear').button().bind('click',clearShiftGroup) ;
$('#btnShiftGroupUpdate').button().bind('click',updateShiftGroup) ;
$('#btnShiftGroupAdd').button().bind('click',addShiftGroup) ;
$('#btnShiftGroupPrint').button().bind('click',printShiftGroup) ;
$('#shift-group-tabs').tabs() ;
$('#txtShiftGroupDesc').focus() ;
$(window).resize(resizeShiftGroupGrid) ;
resizeShiftGroupGrid() ;
clearShiftGroup() ;

function resizeShiftGroupGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-shift-group-entry").outerHeight() - 55;
	$("div#sbg-shift-group-data").css("height", h +'px') ;		
}
function clearShiftGroup() {
	$('#txtShiftGroupId').val("Auto") ;
	$('#txtShiftGroupDesc').val("") ;
	$('#txtShiftGroupRef').val("") ;
	$('#shift-group-tabs').tabs("option", "active", 0) ;
	$('#txtShiftGroupDesc').focus() ;
	$('#btnShiftGroupUpdate').prop('disabled','disabled') ;
	
	$('#shift_group_err_mesg').text('') ;
	$('#shift_group_err_desc').hide() ;
	shift_group_obj = null;
}
function updateShiftGroup() {
	saveShiftGroup(C_UPDATE) ;
}
function addShiftGroup() {
	saveShiftGroup(C_ADD) ;
}
function saveShiftGroup(type) {
	if (validateShiftGroup()) {
		var data = { "type": type, "id": $('#txtShiftGroupId').val(), "desc": $('#txtShiftGroupDesc').val(),"refno": $('#txtShiftGroupRef').val() };
		var url = "request.pzx?c=" + shift_group_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onShiftGroupResponse,shift_group_obj) ;
	}
}
function printShiftGroup() {
	var url = "report.pzx?c=" + shift_group_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editShiftGroup(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + shift_group_url + "&d=" + new Date().getTime() ;		
	shift_group_obj = obj ;
	callServer(url,"json",data,showShiftGroup,obj) ;
}

function deleteShiftGroup(id,obj) {
	if (confirm("Confirm you want to delete shift group id : " + id + "?")) {
		//$($(obj).closest("tr")).remove() ;
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + shift_group_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onShiftGroupResponse,obj) ;
	}
}
function showShiftGroup(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtShiftGroupId').val(resp.data.id) ;
		$('#txtShiftGroupDesc').val(resp.data.desc) ;
		$('#txtShiftGroupRef').val(resp.data.refno) ;
		$('#btnShiftGroupUpdate').prop('disabled','') ;
		$('#txtShiftGroupDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onShiftGroupResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-shift-group-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtShiftGroupDesc').val() + "</td>" + 
				"<td>" + $('#txtShiftGroupRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editShiftGroup(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteShiftGroup(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtShiftGroupDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtShiftGroupRef').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtShiftGroupDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtShiftGroupRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearShiftGroup() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateShiftGroup() {
	$('#shift_group_err_mesg').text('') ;
	if ($('#txtShiftGroupDesc').blank())
	{
		$('#shift_group_err_desc').show() ;
		$('#txtShiftGroupDesc').focus() ;
		$('#shift_group_err_mesg').text("shift group description can not be blank.") ;
		return false ;
	}
	else 
		$('#shift_group_err_desc').hide() ;
			
	return true ;
}