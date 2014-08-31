var rate_group_obj = null ;

$('#btnRateGroupClear').button().bind('click',clearRateGroup) ;
$('#btnRateGroupUpdate').button().bind('click',updateRateGroup) ;
$('#btnRateGroupAdd').button().bind('click',addRateGroup) ;
$('#btnRateGroupPrint').button().bind('click',printRateGroup) ;
$('#rate-group-tabs').tabs() ;
$('#txtRateGroupDesc').focus() ;
$(window).resize(resizeRateGroupGrid) ;
resizeRateGroupGrid() ;
clearRateGroup() ;

function resizeRateGroupGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-rate-group-entry").outerHeight() - 55;
	$("div#sbg-rate-group-data").css("height", h +'px') ;		
}
function clearRateGroup() {
	$('#txtRateGroupId').val("Auto") ;
	$('#txtRateGroupDesc').val("") ;
	$('#cobRateType').val("0") ;
	$('#txtRateNormalNormal').val("0") ;
	$('#txtRateNormalOT').val("0") ;
	$('#txtRateWeekendNormal').val("0") ;
	$('#txtRateWeekendOT').val("0") ;
	$('#txtRateHolidayNormal').val("0") ;
	$('#txtRateHolidayOT').val("0") ;
	$('#rate-group-tabs').tabs("option", "active", 0) ;
	$('#txtRateGroupDesc').focus() ;
	$('#btnRateGroupUpdate').prop('disabled','disabled') ;
	
	$('#rate_group_err_mesg').text('') ;
	$('#rate_group_err_desc').hide() ;
	rate_group_obj = null;
}
function updateRateGroup() {
	saveRateGroup(C_UPDATE) ;
}
function addRateGroup() {
	saveRateGroup(C_ADD) ;
}
function saveRateGroup(type) {
	if (validateRateGroup()) {
		var ratetype = $('#cobRateType').val();
		
		var data = { "type": type, "id": $('#txtRateGroupId').val(), "desc": $('#txtRateGroupDesc').val(),
			"ratetype": ratetype,
			"ratenormalnormal": $('#txtRateNormalNormal').val(), "ratenormalot": $('#txtRateNormalOT').val(),
			"rateweekendnormal": $('#txtRateWeekendNormal').val(), "rateweekendot": $('#txtRateWeekendOT').val(),
			"rateholidaynormal": $('#txtRateHolidayNormal').val(), "rateholidayot": $('#txtRateHolidayOT').val()};
		var url = "request.pzx?c=" + rate_group_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onRateGroupResponse,rate_group_obj) ;
	}
}
function printRateGroup() {
	var url = "report.pzx?c=" + rate_group_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editRateGroup(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + rate_group_url + "&d=" + new Date().getTime() ;		
	rate_group_obj = obj ;
	callServer(url,"json",data,showRateGroup,obj) ;
}

function deleteRateGroup(id,obj) {
	if (confirm("Confirm you want to delete rate group id : " + id + "?")) {
		//$($(obj).closest("tr")).remove() ;
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + rate_group_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onRateGroupResponse,obj) ;
	}
}
function showRateGroup(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtRateGroupId').val(resp.data.id) ;
		$('#txtRateGroupDesc').val(resp.data.desc) ;
		$('#btnRateGroupUpdate').prop('disabled','') ;
		$('#btnRateGroupAdd').prop('disabled','disabled') ;
		
		$('#cobRateType').val(resp.data.ratetype) ;
		$('#txtRateNormalNormal').val(resp.data.ratenormalnormal) ;
		$('#txtRateNormalOT').val(resp.data.ratenormalot) ;
		$('#txtRateWeekendNormal').val(resp.data.rateweekendnormal) ;
		$('#txtRateWeekendOT').val(resp.data.rateweekendot) ;
		$('#txtRateHolidayNormal').val(resp.data.rateholidaynormal) ;
		$('#txtRateHolidayOT').val(resp.data.rateholidayot) ;
		$('#txtRateGroupDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onRateGroupResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-rate-group-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtRateGroupDesc').val() + "</td>" + 
				"<td>" + $('#cobRateType :selected').text() + "</td>" + 
				"<td>" + $('#txtRateNormalNormal').val() + "</td>" + 
				"<td>" + $('#txtRateNormalOT').val() + "</td>" + 
				"<td>" + $('#txtRateWeekendNormal').val() + "</td>" + 
				"<td>" + $('#txtRateWeekendOT').val() + "</td>" + 
				"<td>" + $('#txtRateHolidayNormal').val() + "</td>" + 
				"<td>" + $('#txtRateHolidayOT').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editRateGroup(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteRateGroup(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtRateGroupDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').html($('#cobRateType :selected').text()) ;
			$($(obj).closest('tr')).children('td:eq(3)').html($('#txtRateNormalNormal').val()) ;
			$($(obj).closest('tr')).children('td:eq(4)').html($('#txtRateNormalOT').val()) ;
			$($(obj).closest('tr')).children('td:eq(5)').html($('#txtRateWeekendNormal').val()) ;
			$($(obj).closest('tr')).children('td:eq(6)').html($('#txtRateWeekendOT').val()) ;
			$($(obj).closest('tr')).children('td:eq(7)').html($('#txtRateHolidayNormal').val()) ;
			$($(obj).closest('tr')).children('td:eq(8)').html($('#txtRateHolidayOT').val()) ;
			
			
			$('#btnRateGroupUpdate').prop('disabled','disabled') ;
			$('#btnRateGroupAdd').prop('disabled','') ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearRateGroup() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateRateGroup() {
	$('#rate_group_err_mesg').text('') ;
	if ($('#txtRateGroupDesc').blank())
	{
		$('#rate_group_err_desc').show() ;
		$('#txtRateGroupDesc').focus() ;
		$('#rate_group_err_mesg').text("rate group description can not be blank.") ;
		return false ;
	}
	else 
		$('#rate_group_err_desc').hide() ;
			
	return true ;
}