var timesheet_obj = null ;


$(document).ready(function() 
{ 	
	$('input').css('marginTop', 0); 
	$('input').css('marginBottom',0);
	$('select').css('marginTop',0) ;
	$('select').css('marginBottom',0) ;
	
	var dteopt = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};

	$("#txtTSStart").datepicker(dteopt) ;
	$("#txtTSExpiry").datepicker(dteopt) ;
	$('#btnTSClear').button().bind('click',clearTS) ;
	$('#btnTSUpdate').button().bind('click',updateTS) ;
	$('#btnTSAdd').button().bind('click',addTS) ;
	$('#btnTSPrint').button().bind('click',printTS) ;
	$('#timesheet-tabs').tabs() ;
	$('#txtTSName').focus() ;
	$(window).resize(resizeTSGrid) ;
	resizeTSGrid() ;
	clearTS() ;
}) ;

function resizeTSGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-timesheet-entry").outerHeight() - 55;
	$("div#sbg-timesheet-data").css("height", h +'px') ;		
}

function clearTS() {
	$('#txtTSId').val("Auto") ;
	$('#txtTSDesc').val("") ;
	$('#txtTSRef').val("") ;
	$('#txtTSStart').val("") ;
	$('#txtTSExpiry').val("") ;
	
	$('#cobTSEmp').val("") ;
	$('#cobTSProj').val("") ;
	$('#cobTSAct').val("") ;
	$('#timesheet-tabs').tabs("option", "active", 0) ;
	$('#txtUserName').focus() ;
	$('#btnUserUpdate').prop('disabled','disabled') ;
	$('#cbBillable').prop("checked", false);
	
	$('#timesheet_err_name').hide() ;
	$('#timesheet_err_full').hide() ;
	$('#timesheet_err_group').hide() ;
	$('#timesheet_err_mesg').text('') ;
	timesheet_obj = null;
	
}

function updateTS() {
	saveTS(C_UPDATE) ;
}

function addTS() {
	saveTS(C_ADD) ;
}

function saveTS(type) {
	if (validateTS()) {
		var billable = "0" ;
		if ($('#cbBillable').is(':checked'))
			billable = "1" ;
		var data = { "type": type, "id": $('#txtTSId').val(), "empid": $('#cobTSEmp').val(), "desc": $('#txtTSDesc').val(),"refno": $('#txtTSRef').val(),
			"start": $('#txtTSStart').val(), "expiry": $('#txtTSExpiry').val(),
			"billable": billable,	"project": $('#cobTSProj').val(), "activity": $('#cobTSAct').val() };
		var url = "request.pzx?c=" + timesheet_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onTSResponse,timesheet_obj) ;
	}
}

function printTS() {
	var url = "report.pzx?c=" + timesheet_url + "&d=" + new Date().getTime();
	showReport(url) ;
}

function editTS(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + timesheet_url + "&d=" + new Date().getTime();		
	timesheet_obj = obj ;
	callServer(url,"json",data,showTS,obj) ;
}

function deleteTS(id,obj) {
	if (confirm("Confirm you want to delete timesheet id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + timesheet_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onTSResponse,obj) ;
	}
}
function showTS(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtTSId').val(resp.data.id) ;
		$('#txtTSDesc').val(resp.data.desc) ;
		$('#txtTSRef').val(resp.data.refno) ;
		$('#cobTSEmp').val(resp.data.empid) ;
		$('#cobTSProj').val(resp.data.project) ;
		$('#cobTSAct').val(resp.data.activity) ;
		$('#txtTSStart').val(resp.data.start) ;
		$('#txtTSExpiry').val(resp.data.expiry) ;
		if (resp.data.billable == "1")
			$('#cbBillable').prop("checked", true); 
		$('#btnUserUpdate').prop('disabled','') ;
		$('#txtUserName').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}

function onTSResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-timesheet-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtTSDesc').val() + "</td>" + 
				"<td>" + $('#cobTSProj :selected').text() + "</td>" +
				"<td>" + $('#cobTSAct :selected').text() + "</td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='editTS(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteTS(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').text($('#txtTSDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#cobTSProj :selected').text()) ;
			$($(obj).closest('tr')).children('td:eq(3)').text($('#cobTSAct :selected').text()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearTS() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}

function validateTS() {
	$('#timesheet_err_mesg').text('') ;
	if ($('#txtTSDesc').blank())
	{
		$('#timesheet_err_desc').show() ;
		$('#txtTSDesc').focus() ;
		$('#timesheet_err_mesg').text("Description can not be blank.") ;
		return false ;
	}
	else 
		$('#timesheet_err_desc').hide() ;
		
		
	if ($('#cobTSProj').val() == "") {
		$('#timesheet_err_project').show() ;
		$('#cobTSProj').focus() ;
		$('#timesheet_err_mesg').text("You must pick a project to this timesheet") ;
		return false 
	}
	else 
		$('#timesheet_err_project').hide() ;
		
		if ($('#cobTSAct').val() == "") {
		$('#timesheet_err_activity').show() ;
		$('#cobTSAct').focus() ;
		$('#timesheet_err_mesg').text("You must pick an activity to this timesheet") ;
		return false 
	}
	else 
		$('#timesheet_err_project').hide() ;
		
	return true ;
}