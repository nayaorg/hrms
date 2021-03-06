var timeoff_obj = null ;
 
var dteopt = {
	dateFormat: "dd/mm/yy",
	appendText: "  dd/mm/yyyy",
	showOn: "button",
	buttonImage: "image/calendar.gif",
	buttonImageOnly: true
};

$('#txtDateOff').datepicker(dteopt) ;

var dteperiod = {
	dateFormat: "dd/mm/yy",
	showOn: "button",
	buttonImage: "image/calendar.gif",
	buttonImageOnly: true
};
var dteperiod2 = {
	dateFormat: "dd/mm/yy",
	appendText: "  dd/mm/yyyy",
	showOn: "button",
	buttonImage: "image/calendar.gif",
	buttonImageOnly: true
};
$('#txtPeriodStart').datepicker(dteperiod) ;
$('#txtPeriodEnd').datepicker(dteperiod2) ;

$('#btnTimeOffClear').button().bind('click',clearTimeOff) ;
$('#btnTimeOffUpdate').button().bind('click',updateTimeOff) ;
$('#btnTimeOffAdd').button().bind('click',addTimeOff) ;
$('#btnTimeOffPrint').button().bind('click',printTimeOff) ;
$('#btnTimeOffApply').button().bind('click',getList) ;

$('#btnTimeOffInputControl').button().bind('click',showInput) ;
$('#btnTimeOffHide').button().bind('click',hideInput) ;
$('#cobDept').change(populateEmployee) ;
$('#timeoff-tabs').tabs() ;
$('#txtTimeOffDesc').focus() ;
$(window).resize(resizeTimeOffGrid) ;
resizeTimeOffGrid() ;

getList();

var TimeStart = document.getElementById("cboTimeStartHour");
var TimeEnd = document.getElementById("cboTimeEndHour");
for(var i = 0; i <= 23; ++i) {
	var option = document.createElement('option');
	var option2 = document.createElement('option');
	var StrOp = "";
	if(i < 10)
		StrOp = "0";
	StrOp += "" + i;
	option.text = option.value = StrOp;
	option2.text = option2.value = StrOp;
	TimeStart.add(option, 0);
	TimeEnd.add(option2, 0);
}

var TimeStart = document.getElementById("cboTimeStartMinute");
var TimeEnd = document.getElementById("cboTimeEndMinute");
for(var i = 0; i <= 59; ++i) {
	var option = document.createElement('option');
	var option2 = document.createElement('option');
	var StrOp = "";
	if(i < 10)
		StrOp = "0";
	StrOp += "" + i;
	option.text = option.value = StrOp;
	option2.text = option2.value = StrOp;
	TimeStart.add(option, 0);
	TimeEnd.add(option2, 0);
}
clearTimeOff() ;

$('#sbg-timeoff-entry').hide();

function hideInput(){
	$('#sbg-timeoff-entry').hide('slow');
}
function showInput(){
	$('#sbg-timeoff-entry').show('slow');
}
function resizeTimeOffGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-timeoff-entry").outerHeight() - 55;
	$("div#sbg-timeoff-data").css("height", h +'px') ;		
}
function clearTimeOff() {
	$('#cobDept').val("");
	populateEmployee();
	$('#txtTimeOffDesc').val("") ;
	
	$('#cboTimeStartHour').val("00");
	$('#cboTimeStartMinute').val("00");
	$('#cboTimeEndHour').val("00");
	$('#cboTimeEndMinute').val("00");
	
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;
	var yyyy = today.getFullYear();

	if(dd<10) {
		dd='0'+dd
	} 

	if(mm<10) {
		mm='0'+mm
	} 
	
	$('#txtDateOff').val(dd+'/'+mm+'/'+yyyy) ;
	
	$('#timeoff-tabs').tabs("option", "active", 0) ;
	$('#cobEmpId').focus() ;
	$('#btnTimeOffUpdate').prop('disabled','disabled') ;
	
	$('#timeoff_err_mesg').text('') ;
	$('#timeoff_err_desc').hide() ;
		
	$('#cobEmpId').val("");
	$('#cobDept').prop('disabled','') ;
	$('#cobEmpId').prop('disabled','') ;
	$('#txtDateOff').prop('disabled','') ;
	timeoff_obj = null;
}
function updateTimeOff() {
	saveTimeOff(C_UPDATE) ;
}
function addTimeOff() {
	saveTimeOff(C_ADD) ;
}
function saveTimeOff(type) {
	if (validateTimeOff()) {
		var data = { "type": type, "desc": $('#txtTimeOffDesc').val(), 
			"time_start": $('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val(), 
			"time_end": $('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val(),
			"emp_id": $('#cobEmpId').val(), "date_off": $('#txtDateOff').val()};
		var url = "request.pzx?c=" + timeoff_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onTimeOffResponse,timeoff_obj) ;
	}
}
function printTimeOff() {
	var url = "report.pzx?c=" + timeoff_url + "&d=" + new Date().getTime() + 
			"&dt=" + $('#txtPeriodStart').val() +
			"&dtend=" + $('#txtPeriodEnd').val();
	showReport(url) ;
}
function editTimeOff(id, date_off, obj) {
	$('#cobDept').val("");
	populateEmployee();
	
	var data = { "type": C_GET,"id": id,"date_off":date_off} ;
	var url = "request.pzx?c=" + timeoff_url + "&d=" + new Date().getTime() ;		
	timeoff_obj = obj ;
	callServer(url,"json",data,showTimeOff,obj) ;
}

function deleteTimeOff(id, date_off, obj) {
	if (confirm("Confirm you want to delete time off employee id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id,"date_off":date_off} ;
		var url = "request.pzx?c=" + timeoff_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onTimeOffResponse,obj) ;
	}
}
function showTimeOff(obj,resp) {
	if (resp.status == C_OK) {
		showInput();
		$('#txtTimeOffDesc').val(resp.data.desc) ;
		$('#cobEmpId').val(resp.data.emp_id) ;
		$('#cboTimeStartHour').val(resp.data.time_start.substr(0,2));
		$('#cboTimeStartMinute').val(resp.data.time_start.substr(3,2));
		$('#cboTimeEndHour').val(resp.data.time_end.substr(0,2));
		$('#cboTimeEndMinute').val(resp.data.time_end.substr(3,2));
		$('#txtDateOff').val(resp.data.date_off) ;
		$('#btnTimeOffUpdate').prop('disabled','') ;
		$('#txtTimeOffDesc').focus() ;
		
		$('#cobDept').prop('disabled','disabled') ;
		$('#cobEmpId').prop('disabled','disabled') ;
		$('#txtDateOff').prop('disabled','disabled') ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onTimeOffResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == "emp") {
			$('#timeoff_emp_id').show();
			$('#timeoff_emp_id').text(resp.data);
		} else if (resp.type == C_GET + '_EMP'){
			var lines = resp.data.empList;
			var line = lines.split('|') ;
			var fld = "" ;
			var opt = "";
			
			for (var i = 0 ; i < line.length ; i++) {
				fld = line[i].split(':') ;
				opt += "<option value='"+ fld[0] +"'>"+  fld[1] +"</option>";
			}
			$("#cobEmpId").html(opt);
		} else if (resp.type == "LIST"){
			var fr = '<tr><td style="width:50px;height:1px"></td>' +
					'<td style="width:150px"></td>' + 
					'<td style="width:75px"></td>' +
					'<td style="width:50px"></td>' +
					'<td style="width:50px"></td>' + 
					'<td style="width:200px"></td>' +
					'<td style="width:25px"></td>' +
					'<td style="width:25px"></td>' +
					'</tr>' ;
			$('#sbg-timeoff-table').html(fr + resp.data) ;
		} else {
			if (resp.type == C_ADD) {
				var date_off = $('#txtDateOff').val().substr(6, 4) + $('#txtDateOff').val().substr(3, 2) + $('#txtDateOff').val().substr(0, 2);
				$('#sbg-timeoff-table tr:first').after("<tr>" + 
					"<td>" + $('#cobEmpId').val() + "</td>" + 
					"<td>" + $('#cobEmpId :selected').text() + "</td>" + 
					"<td>" + $('#txtDateOff').val() + "</td>" + 
					"<td>" + $('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val() + "</td>" + 
					"<td>" + $('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val() + "</td>" + 
					"<td>" + $('#txtTimeOffDesc').val() + "</td>" + 
					"<td style='text-align:center'><a href='javascript:' onclick='editTimeOff(" + $('#cobEmpId').val() + "," + date_off + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
					"<td style='text-align:center'><a href='javascript:' onclick='deleteTimeOff(" + $('#cobEmpId').val() + "," + date_off + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
					"</tr>"); 
			} else if (resp.type == C_DELETE) {
				$($(obj).closest("tr")).remove() ;
			} else if (resp.type == C_UPDATE) {
				$($(obj).closest('tr')).children('td:eq(0)').html($('#cobEmpId').val()) ;
				$($(obj).closest('tr')).children('td:eq(1)').html($('#cobEmpId :selected').text()) ;
				$($(obj).closest('tr')).children('td:eq(2)').text($('#txtDateOff').val()) ;
				$($(obj).closest('tr')).children('td:eq(3)').html($('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val() ) ;
				$($(obj).closest('tr')).children('td:eq(4)').html($('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val() ) ;
				$($(obj).closest('tr')).children('td:eq(5)').text($('#txtTimeOffDesc').val()) ;
			}
			$('#sbg-popup-box').html(resp.mesg) ;
			$('#sbg-popup-box').show() ;
			$('#sbg-popup-box').fadeOut(10000) ;
			clearTimeOff() ;
		}
	}
	else {
		if (resp.type == "emp"){
			$('#timeoff_emp_id').show();
			$('#timeoff_emp_id').text('Invalid employee ID.');
		} else {
			showDialog("System Message",resp.mesg) ;
		}
	}
}
function validateTimeOff() {
	if (isNaN($('#cobEmpId').val())){
		showDialog("Error", "Employee id can not be blank.") ;
		$('#cobEmpId').focus();
		return false ;
	} 
	
	$('#timeoff_err_mesg').text('') ;
	if ($('#txtTimeOffDesc').blank())
	{
		$('#timeoff_err_desc').show() ;
		$('#txtTimeOffDesc').focus() ;
		$('#timeoff_err_mesg').text("time off description can not be blank.") ;
		return false ;
	}
	else 
		$('#time_off_err_desc').hide() ;
			
	return true ;
}
function getList(){
	var data = { "type": "LIST","date_start": $('#txtPeriodStart').val(), "date_end": $('#txtPeriodEnd').val()} ;
	var url = "request.pzx?c=" + timeoff_url + "&d=" + new Date().getTime() ;		
	//attendance_obj = obj ;
	callServer(url,"json",data,onTimeOffResponse,timeoff_obj) ;
}

function populateEmployee(){
	$dept_id = $('#cobDept').val();	
	var data = { "type": C_GET + "_EMP","id": $dept_id} ;
	var url = "request.pzx?c=" + timeoff_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onTimeOffResponse,timeoff_obj) ;
	
}