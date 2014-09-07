var time_card_limit_obj = null ;


$('#btnTimeCardLimitUpdate').button().bind('click',updateTimeCardLimit) ;
$('#timecardlimit-tabs').tabs() ;
$('#inpTimeCardLimitBefore').focus() ;
//$(window).resize(resizeTimeCardLimitGrid) ;
//resizeTimeCardLimitGrid() ;
clearTimeCardLimit() ;

function resizeTimeCardLimitGrid() {
	//var h = $("#sbg-center-panel").outerHeight() - $("#sbg-timecard-entry").outerHeight() - 55;
	//$("div#sbg-timecard-data").css("height", h +'px') ;		
}
function clearTimeCardLimit() {
	
	$('#txtTimeCardLimitId').val("Auto") ;
	$('#txtTimeCardLimitId').hide() ;
	$('#labelTimeCardLimitId').hide() ;
	$('#inpTimeCardLimitBefore').val(0) ;
	$('#inpTimeCardLimitAfter').val(0) ;
	time_card_limit_obj = null;
	
	var data = { "type": C_GET,"id": 1} ;
	var url = "request.pzx?c=" + timecardlimit_url + "&d=" + new Date().getTime() ;		
	callServer(url,"json",data,showTimeCardLimit,time_card_limit_obj) ;
	
	$('#timecardlimit-tabs').tabs("option", "active", 0) ;
	$('#inpTimeCardLimitBefore').focus() ;
	//$('#btnTimeCardUpdate').prop('disabled','disabled') ;
	
	$('#timecardlimit_err_mesg').text('') ;
	$('#timecardlimit_err_desc').hide() ;
}
function updateTimeCardLimit() {
	saveTimeCardLimit(C_UPDATE) ;
}
function saveTimeCardLimit(type) {
	if (validateTimeCardLimit()) {
		var data = { "type": type, "id": $('#txtTimeCardLimitId').val(), "before": $('#inpTimeCardLimitBefore').val(),"after": $('#inpTimeCardLimitAfter').val()};
		var url = "request.pzx?c=" + timecardlimit_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onTimeCardLimitResponse,time_card_limit_obj) ;
	}
}
function editTimeCardLimit(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + timecard_url + "&d=" + new Date().getTime() ;		
	time_card_limit_obj = obj ;
	callServer(url,"json",data,showTimeCardLimit,obj) ;
}

function showTimeCardLimit(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtTimeCardLimitId').val(resp.data.id) ;
		$('#inpTimeCardLimitBefore').val(resp.data.before) ;
		$('#inpTimeCardLimitAfter').val(resp.data.after) ;
		$('#inpTimeCardLimitBefore').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onTimeCardLimitResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		//$('#txtTimeCardLimitId').val(resp.data.id) ;
		//$('#inpTimeCardLimitBefore').val(resp.data.before) ;
		//$('#inpTimeCardLimitAfter').val(resp.data.after) ;
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateTimeCardLimit() {
			
	return true ;
}