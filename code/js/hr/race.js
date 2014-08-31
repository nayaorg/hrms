var race_obj = null ;

$(document).ready(function() 
{ 
	$('#btnRaceClear').button().bind('click',clearRace) ;
	$('#btnRaceAdd').button().bind('click',addRace) ;
	$('#btnRaceUpdate').button().bind('click',updateRace) ;
	$('#btnRacePrint').button().bind('click',printRace) ;
	$('#race-tabs').tabs() ;
	$('#txtRaceDesc').focus() ;
	$(window).resize(resizeRaceGrid) ;
	resizeRaceGrid() ;
	clearRace() ;
}) ;
function resizeRaceGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-race-entry").outerHeight() - 55;
	$("div#sbg-race-data").css("height", h +'px') ;		
}
function clearRace() {
	$('#txtRaceId').val("Auto") ;
	$('#txtRaceDesc').val("") ;
	$('#race-tabs').tabs("option", "active", 0) ;
	$('#txtRaceDesc').focus() ;
	$('#btnRaceUpdate').prop('disabled','disabled') ;
	$('#race_err_mesg').text('') ;
	$('#race_err_desc').hide() ;
	race_obj = null;
}
function updateRace() {
	saveRace(C_UPDATE) ;
}
function addRace() {
	saveRace(C_ADD) ;
}
function saveRace(type) {
	if (validateRace()) {
		var data = { "type": type, "id": $('#txtRaceId').val(), "desc": $('#txtRaceDesc').val()};
		var url = "request.pzx?c=" + race_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onRaceResponse,race_obj) ;
	}
}
function printRace() {
	var url = "report.pzx?c=" + race_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editRace(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + race_url + "&d=" + new Date().getTime();		
	race_obj = obj ;
	callServer(url,"json",data,showRace,obj) ;
}

function deleteRace(id,obj) {
	if (confirm("Confirm you want to delete race id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + race_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onRaceResponse,obj) ;
	}
}
function showRace(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtRaceId').val(resp.data.id) ;
		$('#txtRaceDesc').val(resp.data.desc) ;
		$('#btnRaceUpdate').prop('disabled','') ;
		$('#txtRaceDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onRaceResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-race-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtRaceDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editRace(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteRace(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtRaceDesc').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearRace() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateRace() {
	$('#race_err_mesg').text('') ;
	if ($('#txtRaceDesc').blank())
	{
		$('#race_err_desc').show() ;
		$('#txtRaceDesc').focus() ;
		$('#race_err_mesg').text("race description can not be blank.") ;
		return false ;
	}
	else 
		$('#race_err_desc').hide() ;
			
	return true ;
}