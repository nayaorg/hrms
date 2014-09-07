var activity_obj = null ;


$('#btnActivityClear').button().bind('click',clearActivity) ;
$('#btnActivityUpdate').button().bind('click',updateActivity) ;
$('#btnActivityAdd').button().bind('click',addActivity) ;
$('#btnActivityPrint').button().bind('click',printActivity) ;
$('#activity-tabs').tabs() ;
$('#txtActivityDesc').focus() ;
$(window).resize(resizeActivityGrid) ;
resizeActivityGrid() ;
clearActivity() ;

function resizeActivityGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-activity-entry").outerHeight() - 55;
	$("div#sbg-activity-data").css("height", h +'px') ;		
}
function clearActivity() {
	$('#txtActivityId').val("Auto") ;
	$('#txtActivityDesc').val("") ;
	$('#txtActivityRef').val("") ;
	$('#activity-tabs').tabs("option", "active", 0) ;
	$('#txtActivityDesc').focus() ;
	$('#btnActivityUpdate').prop('disabled','disabled') ;
	
	$('#activity_err_mesg').text('') ;
	$('#activity_err_desc').hide() ;
	activity_obj = null;
}
function updateActivity() {
	saveActivity(C_UPDATE) ;
}
function addActivity() {
	saveActivity(C_ADD) ;
}
function saveActivity(type) {
	if (validateActivity()) {
		var data = { "type": type, "id": $('#txtActivityId').val(), "desc": $('#txtActivityDesc').val(),"refno": $('#txtActivityRef').val() };
		var url = "request.pzx?c=" + activity_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onActivityResponse,activity_obj) ;
	}
}
function printActivity() {
	var url = "report.pzx?c=" + activity_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editActivity(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + activity_url + "&d=" + new Date().getTime() ;		
	activity_obj = obj ;
	callServer(url,"json",data,showActivity,obj) ;
}

function deleteActivity(id,obj) {
	if (confirm("Confirm you want to delete department id : " + id + "?")) {
		//$($(obj).closest("tr")).remove() ;
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + activity_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onActivityResponse,obj) ;
	}
}
function showActivity(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtActivityId').val(resp.data.id) ;
		$('#txtActivityDesc').val(resp.data.desc) ;
		$('#txtActivityRef').val(resp.data.refno) ;
		$('#btnActivityUpdate').prop('disabled','') ;
		$('#txtActivityDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onActivityResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-activity-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtActivityDesc').val() + "</td>" + 
				"<td>" + $('#txtActivityRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editActivity(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteActivity(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtActivityDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtActivityRef').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtActivityDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtActivityRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearActivity() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateActivity() {
	$('#activity_err_mesg').text('') ;
	if ($('#txtActivityDesc').blank())
	{
		$('#activity_err_desc').show() ;
		$('#txtActivityDesc').focus() ;
		$('#activity_err_mesg').text("Activity description can not be blank.") ;
		return false ;
	}
	else 
		$('#activity_err_desc').hide() ;
			
	return true ;
}