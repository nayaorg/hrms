
var travel_plan_obj = null ;

$(document).ready(function() {
	$('#btnTravelPlanAdd').button().bind('click',addTravelPlan) ;
	$('#btnTravelPlanClear').button().bind('click',clearTravelPlan) ;
	$('#btnTravelPlanUpdate').button().bind('click',updateTravelPlan) ;
	$('#btnTravelPlanPrint').button().bind('click',printTravelPlan) ;
	$('#btnTravelPlanUpdate').hide() ;
	
	var dteopt = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};

	$("#txtTravelDate").datepicker(dteopt) ;
	$("#txtTravelExpiry").datepicker(dteopt) ;
	
	$('#travel-plan-tabs').tabs() ;
	$('#txtTravelPlan').focus() ;
	clearTravelPlan();
	$(window).resize(resizeTravelPlanGrid) ;
	resizeTravelPlanGrid() ;
}) ;
function resizeTravelPlanGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-travel-plan-entry").outerHeight() - 55;
	$("div#sbg-travel-plan-data").css("height", h +'px') ;
}
function clearTravelPlan() {
	$('#txtTravelPlanId').val("Auto") ;
	$('#txtTravelPlanTitle').val("") ;
	$('#txtTravelPlanDesc').val("") ;
	$('#cobTravelPlanCountry').val("") ;
	$('#txtTravelDate').val("") ;
	$('#txtTravelExpiry').val("") ;
	
	
	$('#travel-plan-tabs').tabs("option", "active", 0) ;
	$('#travel_plan_err_mesg').text('') ;
	$('#travel_plan_err_title').hide() ;
	$('#travel_plan_err_desc').hide() ;
	
	$('#btnTravelPlanUpdate').hide() ;
	$('#btnTravelPlanAdd').show() ;
	
	travel_plan_obj = null;
}
function updateTravelPlan() {
	saveTravelPlan(C_UPDATE) ;
}
function addTravelPlan() {
	saveTravelPlan(C_ADD) ;
}
function saveTravelPlan(type) {
	if (validateTravelPlan()) {
		var data = { "type": type, "id": $('#txtTravelPlanId').val(), "title": $('#txtTravelPlanTitle').val(), "desc": $('#txtTravelPlanDesc').val(), "country": $('#cobTravelPlanCountry').val(), "start": $('#txtTravelDate').val(), "expiry": $('#txtTravelExpiry').val() };
		var url = "request.pzx?c=" + travel_plan_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onTravelPlanResponse,travel_plan_obj) ;
	}
}

function editTravelPlan(id,obj) {
	var data = {"type": C_GET, "id": id} ;
	var url = "request.pzx?c=" + travel_plan_url + "&d=" + new Date().getTime();		
	travel_plan_obj = obj ;
	callServer(url,"json",data,showTravelPlan,obj) ;
}

function deleteTravelPlan(id,obj) {
	if (confirm("Confirm you want to delete travel plan id : " + id + "?")) {
		var data = {"type": C_DELETE, "id": id} ;
		var url = "request.pzx?c=" + travel_plan_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onTravelPlanResponse,obj) ;
	}
}
function showTravelPlan(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtTravelPlanId').val(resp.data.id) ;
		$('#txtTravelPlanTitle').val(resp.data.title) ;
		$('#txtTravelPlanDesc').val(resp.data.desc) ;
		$('#cobTravelPlanCountry').val(resp.data.country) ;
		$('#txtTravelDate').val(resp.data.start) ;
		$('#txtTravelExpiry').val(resp.data.expiry) ;
		
		$('#txtTravelPlanTitle').focus() ;
		$('#btnTravelPlanUpdate').show() ;
		$('#btnTravelPlanAdd').hide() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onTravelPlanResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-travel-plan-table tr:first').after("<tr>" + 
				"<td>" + resp.data + "</td>" + 
				"<td>" + $('#txtTravelPlanTitle').val() + "</td>" + 
				"<td>" + $('#txtTravelPlanDesc').val() + "</td>" + 
				"<td>" + $('#cobTravelPlanCountry :selected').text() + "</td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='editTravelPlan(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteTravelPlan(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(0)').html($('#txtTravelPlanId').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtTravelPlanTitle').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtTravelPlanDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(3)').text($('#cobTravelPlanCountry :selected').text()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearTravelPlan() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateTravelPlan() {
	$('#travel_plan_err_mesg').text('') ;
	
	if ($('#txtTravelPlanTitle').blank())
	{
		$('#travel_plan_err_title').show() ;
		$('#txtTravelPlanTitle').focus() ;
		$('#travel_plan_err_mesg').text($('#travel_plan_err_mesg').text() + "Title can not be blank.") ;
		return false ;
	}
	else 
		$('#travel_plan_err_title').hide() ;
		
	if ($('#txtTravelPlanDesc').blank())
	{
		$('#travel_plan_err_desc').show() ;
		$('#txtTravelPlanDesc').focus() ;
		$('#travel_plan_err_mesg').text($('#travel_plan_err_mesg').text() + "Description can not be blank.") ;
		return false ;
	}
	else 
		$('#travel_plan_err_desc').hide() ;
		
	return true ;
}
function printTravelPlan() {
	var url = "report.pzx?c=" + travel_plan_url + "&d=" + new Date().getTime();
	showReport(url) ;
}