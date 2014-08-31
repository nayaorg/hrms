var holiday_obj = null ;
 
var dteopt = {
	dateFormat: "dd/mm/yy",
	appendText: "  dd/mm/yyyy",
	showOn: "button",
	buttonImage: "image/calendar.gif",
	buttonImageOnly: true
};

$('#txtHolidayDate').datepicker(dteopt) ;


$('#btnHolidayClear').button().bind('click',clearHoliday) ;
$('#btnHolidayUpdate').button().bind('click',updateHoliday) ;
$('#btnHolidayAdd').button().bind('click',addHoliday) ;
$('#btnHolidayPrint').button().bind('click',printHoliday) ;
$('#holiday-tabs').tabs() ;
$('#txtHolidayDate').focus() ;
$(window).resize(resizeHolidayGrid) ;
resizeHolidayGrid() ;
clearHoliday() ;

function resizeHolidayGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-holiday-entry").outerHeight() - 55;
	$("div#sbg-holiday-data").css("height", h +'px') ;		
}
function clearHoliday() {
	$('#txtHolidayId').val("Auto") ;
	$('#txtHolidayDate').val("") ;
	$('#txtHolidayDesc').val("") ;
	$('#holiday-tabs').tabs("option", "active", 0) ;
	$('#txtHolidayDate').focus() ;
	$('#btnHolidayUpdate').prop('disabled','disabled') ;
	
	$('#holiday_err_mesg').text('') ;
	$('#holiday_err_desc').hide() ;
	holiday_obj = null;
}
function updateHoliday() {
	saveHoliday(C_UPDATE) ;
}
function addHoliday() {
	saveHoliday(C_ADD) ;
}
function saveHoliday(type) {
	if (validateHoliday()) {
		var data = { "type": type, "id": $('#txtHolidayId').val(), "date": $('#txtHolidayDate').val(), "desc": $('#txtHolidayDesc').val() };
		var url = "request.pzx?c=" + holiday_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onHolidayResponse,holiday_obj) ;
	}
}
function printHoliday() {
	var url = "report.pzx?c=" + holiday_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editHoliday(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + holiday_url + "&d=" + new Date().getTime() ;		
	holiday_obj = obj ;
	callServer(url,"json",data,showHoliday,obj) ;
}

function deleteHoliday(id,obj) {
	if (confirm("Confirm you want to delete holiday : " + id + "?")) {
		//$($(obj).closest("tr")).remove() ;
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + holiday_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onHolidayResponse,obj) ;
	}
}
function showHoliday(obj,resp) {
	if (resp.status == C_OK) {
	
		$('#txtHolidayId').val(resp.data.id) ;
		$('#txtHolidayDate').val(resp.data.date) ;
		$('#txtHolidayDesc').val(resp.data.desc) ;
		$('#btnHolidayUpdate').prop('disabled','') ;
		$('#txtHolidayDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onHolidayResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-holiday-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtHolidayDate').val() + "</td>" + 
				"<td>" + $('#txtHolidayDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editHoliday(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteHoliday(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtHolidayDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtHolidayRef').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtHolidayDate').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtHolidayDesc').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearHoliday() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateHoliday() {
	$('#holiday_err_mesg').text('') ;
	if ($('#txtHolidayDate').blank())
	{
		$('#holiday_err_date').show() ;
		$('#txtHolidayDate').focus() ;
		$('#holiday_err_mesg').text("Holiday date can not be blank.") ;
		return false ;
	}
	else 
		$('#holiday_err_date').hide() ;
	
	if ($('#txtHolidayDesc').blank())
	{
		$('#holiday_err_desc').show() ;
		$('#txtHolidayDesc').focus() ;
		$('#holiday_err_mesg').text("Holiday description can not be blank.") ;
		return false ;
	}
	else 
		$('#holiday_err_date').hide() ;
	
	
			
	return true ;
}