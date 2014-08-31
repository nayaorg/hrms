var time_card_obj = null ;

$('#btnTimeCardClear').button().bind('click',clearTimeCard) ;
$('#btnTimeCardUpdate').button().bind('click',updateTimeCard) ;
$('#btnTimeCardAdd').button().bind('click',addTimeCard) ;
$('#btnTimeCardPrint').button().bind('click',printTimeCard) ;
$('#timecard-tabs').tabs() ;
$('#txtTimeCardDesc').focus() ;
$(window).resize(resizeTimeCardGrid) ;
resizeTimeCardGrid() ;

var TimeStart = document.getElementById("cboTimeStartHour");
var TimeEnd = document.getElementById("cboTimeEndHour");
var BreakStart = document.getElementById("cboBreakStartHour");
var BreakEnd = document.getElementById("cboBreakEndHour");
for(var i = 0; i <= 23; ++i) {
	var option = document.createElement('option');
	var option2 = document.createElement('option');
	var option3 = document.createElement('option');
	var option4 = document.createElement('option');
	var StrOp = "";
	if(i < 10)
		StrOp = "0";
	StrOp += "" + i;
	option.text = option.value = StrOp;
	option2.text = option2.value = StrOp;
	option3.text = option3.value = StrOp;
	option4.text = option4.value = StrOp;
	TimeStart.add(option, 0);
	TimeEnd.add(option2, 0);
	BreakStart.add(option3, 0);
	BreakEnd.add(option4, 0);
}

var TimeStart = document.getElementById("cboTimeStartMinute");
var TimeEnd = document.getElementById("cboTimeEndMinute");
var BreakStart = document.getElementById("cboBreakStartMinute");
var BreakEnd = document.getElementById("cboBreakEndMinute");
for(var i = 0; i <= 59; ++i) {
	var option = document.createElement('option');
	var option2 = document.createElement('option');
	var option3 = document.createElement('option');
	var option4 = document.createElement('option');
	var StrOp = "";
	if(i < 10)
		StrOp = "0";
	StrOp += "" + i;
	option.text = option.value = StrOp;
	option2.text = option2.value = StrOp;
	option3.text = option3.value = StrOp;
	option4.text = option4.value = StrOp;
	TimeStart.add(option, 0);
	TimeEnd.add(option2, 0);
	BreakStart.add(option3, 0);
	BreakEnd.add(option4, 0);
}
clearTimeCard() ;

function resizeTimeCardGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-timecard-entry").outerHeight() - 55;
	$("div#sbg-timecard-data").css("height", h +'px') ;		
}
function clearTimeCard() {
	$('#txtTimeCardId').val("Auto") ;
	$('#txtTimeCardDesc').val("") ;
	$('#txtTimeCardRef').val("") ;
	$('#inpTolerance').val(0) ;
	
	$('#cboTimeStartHour').val("00");
	$('#cboTimeStartMinute').val("00");
	$('#cboTimeEndHour').val("00");
	$('#cboTimeEndMinute').val("00");
	
	$('#cboBreakStartHour').val("00");
	$('#cboBreakStartMinute').val("00");
	$('#cboBreakEndHour').val("00");
	$('#cboBreakEndMinute').val("00");
	
	
	$('#timecard-tabs').tabs("option", "active", 0) ;
	$('#txtTimeCardDesc').focus() ;
	$('#btnTimeCardUpdate').prop('disabled','disabled') ;
	
	$('#timecard_err_mesg').text('') ;
	$('#timecard_err_desc').hide() ;
	timecard_obj = null;
}
function updateTimeCard() {
	saveTimeCard(C_UPDATE) ;
}
function addTimeCard() {
	saveTimeCard(C_ADD) ;
}
function saveTimeCard(type) {
	if (validateTimeCard()) {
		var data = { "type": type, "id": $('#txtTimeCardId').val(), 
			"desc": $('#txtTimeCardDesc').val(),"refno": $('#txtTimeCardRef').val(), 
			"time_break": 0, 
			"tolerance": $('#inpTolerance').val(), 
			"time_start": $('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val(), 
			"time_end": $('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val(),
			"break_start": $('#cboBreakStartHour').val() + ":" + $('#cboBreakStartMinute').val(), 
			"break_end": $('#cboBreakEndHour').val() + ":" + $('#cboBreakEndMinute').val()
		};
		var url = "request.pzx?c=" + timecard_url + "&d=" + new Date().getTime() ;
		//alert(data["tolerance"]);
		callServer(url,"json",data,onTimeCardResponse,timecard_obj) ;
	}
}
function printTimeCard() {
	var url = "report.pzx?c=" + timecard_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editTimeCard(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + timecard_url + "&d=" + new Date().getTime() ;		
	timecard_obj = obj ;
	callServer(url,"json",data,showTimeCard,obj) ;
}

function deleteTimeCard(id,obj) {
	if (confirm("Confirm you want to delete time card id : " + id + "?")) {
		//$($(obj).closest("tr")).remove() ;
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + timecard_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onTimeCardResponse,obj) ;
	}
}
function showTimeCard(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtTimeCardId').val(resp.data.id) ;
		$('#txtTimeCardDesc').val(resp.data.desc) ;
		$('#txtTimeCardRef').val(resp.data.refno) ;
		$('#inpTolerance').val(resp.data.tolerance) ;
		
		$('#cboTimeStartHour').val(resp.data.time_start.substr(0,2));
		$('#cboTimeStartMinute').val(resp.data.time_start.substr(3,2));
		$('#cboTimeEndHour').val(resp.data.time_end.substr(0,2));
		$('#cboTimeEndMinute').val(resp.data.time_end.substr(3,2));
		
		$('#cboBreakStartHour').val(resp.data.break_start.substr(0,2));
		$('#cboBreakStartMinute').val(resp.data.break_start.substr(3,2));
		$('#cboBreakEndHour').val(resp.data.break_end.substr(0,2));
		$('#cboBreakEndMinute').val(resp.data.break_end.substr(3,2));
		
		$('#btnTimeCardUpdate').prop('disabled','') ;
		$('#txtTimeCardDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onTimeCardResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-timecard-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtTimeCardDesc').val() + "</td>" + 
				"<td>" + $('#txtTimeCardRef').val() + "</td>" + 
				"<td>" + $('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val() + "</td>" + 
				"<td>" + $('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editTimeCard(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteTimeCard(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtTimeCardDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtTimeCardRef').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtTimeCardDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtTimeCardRef').val()) ;
			$($(obj).closest('tr')).children('td:eq(3)').html($('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val() ) ;
			$($(obj).closest('tr')).children('td:eq(4)').html($('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val() ) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearTimeCard() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateTimeCard() {
	$('#timecard_err_mesg').text('') ;
	if ($('#txtTimeCardDesc').blank())
	{
		$('#timecard_err_desc').show() ;
		$('#txtTimeCardDesc').focus() ;
		$('#timecard_err_mesg').text("time card description can not be blank.") ;
		return false ;
	}
	else 
		$('#time_card_err_desc').hide() ;
			
	return true ;
}