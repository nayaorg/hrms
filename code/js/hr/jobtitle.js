var job_obj = null ;

$(document).ready(function() 
{ 
	$('#btnJobClear').button().bind('click',clearJob) ;
	$('#btnJobUpdate').button().bind('click',updateJob) ;
	$('#btnJobAdd').button().bind('click',addJob) ;
	$('#btnJobPrint').button().bind('click',printJob) ;
	$('#job-tabs').tabs() ;
	$('#txtJobDesc').focus() ;
	$(window).resize(resizeJobGrid) ;
	resizeJobGrid() ;
	clearJob();
}) ;
function resizeJobGrid() {
	//var tblWidth = $("div#sbg-grid-data").outerWidth(true);
			
	//elem = document.getElementById("sbg-grid-data"); 
	//if (elem.clientHeight < elem.scrollHeight) {	//scrollbar detected
		//tblWidth = tblWidth - scrollbarWidth ;
	//}
	//$("table#sbg-table-data").width(tblWidth + 'px');
	//$("table#sbg-table-header").width(tblWidth + 'px');
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-job-entry").outerHeight() - 55;
	$("div#sbg-job-data").css("height", h +'px') ;		
}
function clearJob() {
	$('#txtJobId').val("Auto") ;
	$('#txtJobDesc').val("") ;
	$('#job-tabs').tabs("option", "active", 0) ;
	$('#txtJobDesc').focus() ;
	$('#btnJobUpdate').prop('disabled','disabled') ;
	$('#job_err_mesg').text('') ;
	$('#job_err_desc').hide() ;
	job_obj = null;
}
function updateJob() {
	saveJob(C_UPDATE) ;
}
function addJob() {
	saveJob(C_ADD) ;
}
function saveJob(type) {
	if (validateJob()) {
		var data = { "type": type, "id": $('#txtJobId').val(), "desc": $('#txtJobDesc').val()};
		var url = "request.pzx?c=" + job_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onJobResponse,job_obj) ;
	}
}
function printJob() {
	var url = "report.pzx?c=" + job_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editJob(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + job_url + "&d=" + new Date().getTime();		
	job_obj = obj ;
	callServer(url,"json",data,showJob,obj) ;
}

function deleteJob(id,obj) {
	if (confirm("Confirm you want to delete job title id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + job_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onJobResponse,obj) ;
	}
}
function showJob(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtJobId').val(resp.data.id) ;
		$('#txtJobDesc').val(resp.data.desc) ;
		$('#btnJobUpdate').prop('disabled','') ;
		$('#txtJobDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onJobResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-job-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtJobDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editJob(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteJob(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtJobDesc').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearJob() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateJob() {
	$('#job_err_mesg').text('') ;
	if ($('#txtJobDesc').blank())
	{
		$('#job_err_desc').show() ;
		$('#txtJobDesc').focus() ;
		$('#job_err_mesg').text("Job Title description can not be blank.") ;
		return false ;
	}
	else 
		$('#job_err_desc').hide() ;
			
	return true ;
}