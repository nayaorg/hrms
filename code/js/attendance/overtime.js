var overtime_obj = null ;
var updateProcess = false;

var dteopt = {
	dateFormat: "dd/mm/yy",
	appendText: "  dd/mm/yyyy",
	showOn: "button",
	buttonImage: "image/calendar.gif",
	buttonImageOnly: true
};

$('#txtDateOver').datepicker(dteopt) ;

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
$('#cobDept').change(populateEmployee) ;

$('#btnOverTimeAdd').button().bind('click',addOverTime) ;
$('#btnOverTimeClear').button().bind('click',clearOverTime) ;
$('#btnOverTimeUpdate').button().bind('click',updateOverTime) ;
$('#btnOverTimePrint').button().bind('click',printOverTime) ;

$('#btnOvertimeApply').button().bind('click',getList) ;

$('#btnOvertimeInputControl').button().bind('click',showInput) ;
$('#btnOverTimeHide').button().bind('click',hideInput) ;
$('#overtime-tabs').tabs() ;
$('#txtOverTimeDesc').focus() ;
$(window).resize(resizeOverTimeGrid) ;
resizeOverTimeGrid() ;

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
clearOverTime() ;

getList();

$('#sbg-overtime-entry').hide();

function hideInput(){
	$('#sbg-overtime-entry').hide('slow');
}
function showInput(){
	$('#sbg-overtime-entry').show('slow');
}
function resizeOverTimeGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-overtime-entry").outerHeight() - 55;
	$("div#sbg-overtime-data").css("height", h +'px') ;		
}
function clearOverTime() {
	$('#cobDept').val("");
	populateEmployee();
	$('#txtOverTimeDesc').val("") ;
	
	$('#cboTimeStartHour').val("00");
	$('#cboTimeStartMinute').val("00");
	$('#cboTimeEndHour').val("00");
	$('#cboTimeEndMinute').val("00");
	
	$('#overtimeId').val("-1");
	
	$('#cobProject').val("");
	
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
	
	$('#txtDateOver').val(dd+'/'+mm+'/'+yyyy) ;
	
	$('#overtime-tabs').tabs("option", "active", 0) ;
	$('#cobEmpId').focus() ;
	$('#btnOverTimeUpdate').prop('disabled','disabled') ;
	
	$('#overtime_err_mesg').text('') ;
	$('#overtime_err_desc').hide() ;
	
	$('#cobEmpId').val("");
	$('#cobDept').prop('disabled','') ;
	$('#cobEmpId').prop('disabled','') ;
	overtime_obj = null;
}
function recalculateTime(){
	var time_start = new Date(2000, 0, 1,  parseInt($('#cboTimeStartHour').val()), parseInt($('#cboTimeStartMinute').val()));
	var time_end = new Date(2000, 0, 1,  parseInt($('#cboTimeEndHour').val()), parseInt($('#cboTimeEndMinute').val()));
	
	var countOvertimeIn = 0;
	
	var diff = time_end - time_start;
	var minute = Math.floor(diff / 1000 / 60);
	countOvertimeIn = countOvertimeIn + (minute > 0 ? minute : 0);
	
	$('#txtOTHour').val(countOvertimeIn);
}
function updateOverTime() {
	saveOverTime(C_UPDATE) ;
}
function addOverTime() {
	saveOverTime(C_ADD) ;
}
function saveOverTime(type) {
	if (validateOverTime()) {
		var data = { "type": type, "desc": $('#txtOverTimeDesc').val(), 
			"project_id": $('#cobProject').val(),
			"overtime_id": $('#overtimeId').val(), 
			"time_start": $('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val(), 
			"time_end": $('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val(),
			"emp_id": $('#cobEmpId').val(), "date_over": $('#txtDateOver').val(), 
			"ot_hour": $('#txtOTHour').val(), 
			"late_in": $('#inpLateIn').val(), "early_out": $('#inpEarlyOut').val()};
		var url = "request.pzx?c=" + overtime_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onOverTimeResponse,overtime_obj) ;
	}
}
function printOverTime() {
	var url = "report.pzx?c=" + overtime_url + "&d=" + new Date().getTime() + 
			"&dt=" + $('#txtPeriodStart').val() +
			"&dtend=" + $('#txtPeriodEnd').val();
	showReport(url) ;
}
function editOverTime(id,date,overtimeId,obj) {
	$('#cobDept').val("");
	populateEmployee();
	var data = { "type": C_GET,"id": id, "date":date, "overtimeId":overtimeId} ;
	var url = "request.pzx?c=" + overtime_url + "&d=" + new Date().getTime() ;		
	overtime_obj = obj ;
	callServer(url,"json",data,showOverTime,obj) ;
}

function deleteOverTime(id,date,overtimeId,obj) {
	if (confirm("Confirm you want to delete over time id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id, "date":date, "overtimeId":overtimeId} ;
		var url = "request.pzx?c=" + overtime_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onOverTimeResponse,obj) ;
	}
}
function showOverTime(obj,resp) {
	if (resp.status == C_OK) {
		showInput();
		
		$('#txtOverTimeDesc').val(resp.data.desc) ;
		$('#cboTimeStartHour').val(resp.data.time_start.substr(0,2));
		$('#cboTimeStartMinute').val(resp.data.time_start.substr(3,2));
		$('#cboTimeEndHour').val(resp.data.time_end.substr(0,2));
		$('#cboTimeEndMinute').val(resp.data.time_end.substr(3,2));
		
		$('#txtDateOver').val(resp.data.date_over) ;
		$('#overtimeId').val(resp.data.overtime_id) ;
		$('#cobProject').val(resp.data.project_id) ;
		
		$('#btnOverTimeUpdate').prop('disabled','') ;
		$('#txtOverTimeDesc').focus() ;
		
		$('#cobEmpId').val(resp.data.emp_id) ;
		recalculateTime();
		
		updateProcess = true;
		getShiftHour();
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onOverTimeResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == "emp") {
			$('#overtime_emp_id').show();
			$('#overtime_emp_id').text(resp.data);
		}  else if (resp.type == C_GET + '_EMP'){
			var lines = resp.data.empList;
			var line = lines.split('|') ;
			var fld = "" ;
			var opt = "";
			
			for (var i = 0 ; i < line.length ; i++) {
				fld = line[i].split(':') ;
				opt += "<option value='"+ fld[0] +"'>"+  fld[1] +"</option>";
			}
			$("#cobEmpId").html(opt);
		} else if (resp.type == "shifthour") {
			if(!updateProcess){
				$('#cboTimeStartHour').val(resp.data.start.substr(0,2));
				$('#cboTimeStartMinute').val(resp.data.start.substr(3,2));
				$('#cboTimeEndHour').val(resp.data.end.substr(0,2));
				$('#cboTimeEndMinute').val(resp.data.end.substr(3,2));
			
				$('#inpOTStart').val(0);
				$('#inpOTEnd').val(0);
				$('#inpLateIn').val(0);
				$('#inpEarlyOut').val(0);
			} 
				
			$('#inpShiftStart').val(resp.data.start.substr(0,5));
			$('#inpShiftEnd').val(resp.data.end.substr(0,5));
			
			$('#tolerance').val(resp.data.tolerance);
			$('#limit_before').val(resp.data.limit_before);
			$('#limit_after').val(resp.data.limit_after);
			
			if(updateProcess){
				recalculateTime();
			}
			
			updateProcess = false;
			
		} else if (resp.type == "LIST"){
			var fr = '<tr><td style="width:30px;height:1px"></td>' +
					'<td style="width:100px"></td>' + 
					'<td style="width:80px"></td>' +
					'<td style="width:20px;display:none"></td>' +
					'<td style="width:80px"></td>' +
					'<td style="width:20px;display: none"></td>' +
					'<td style="width:50px"></td>' +
					'<td style="width:50px"></td>' + 
					'<td style="width:50px"></td>' +
					'<td style="width:150px"></td>' +
					'<td style="width:25px"></td>' +
					'<td style="width:25px"></td>' +
					'</tr>' ;
			$('#sbg-overtime-table').html(fr + resp.data) ;
		} else {
			if (resp.type == C_ADD) {
				var date_overtime = $('#txtDateOver').val().substr(6, 4) + $('#txtDateOver').val().substr(3, 2) + $('#txtDateOver').val().substr(0, 2);
				$('#sbg-overtime-table tr:first').after("<tr>" + 
					"<td>" + $('#cobEmpId').val() + "</td>" + 
					"<td>" + $('#cobEmpId :selected').text() + "</td>" + 
					"<td>" + $('#txtDateOver').val() + "</td>" + 
					'<td style="display:none">' + $('#cobProject').val() + "</td>" + 
					'<td>' + $('#cobProject :selected').text() + "</td>" + 
					'<td style="display: none">' + resp.data + "</td>" + 
					"<td>" + $('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val() + "</td>" + 
					"<td>" + $('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val() + "</td>" + 
					"<td>" + $('#txtOTHour').val() + "</td>" + 
					"<td>" + $('#txtOverTimeDesc').val() + "</td>" + 
					"<td style='text-align:center'><a href='javascript:' onclick='editOverTime(" + $('#cobEmpId').val() + ", " + date_overtime + ", " + resp.data + ", this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
					"<td style='text-align:center'><a href='javascript:' onclick='deleteOverTime(" + $('#cobEmpId').val() + ", " + date_overtime + ", " + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
					"</tr>"); 
			} else if (resp.type == C_DELETE) {
				$($(obj).closest("tr")).remove() ;
			} else if (resp.type == C_UPDATE) {
				var date_overtime = $('#txtDateOver').val().substr(6, 4) + $('#txtDateOver').val().substr(3, 2) + $('#txtDateOver').val().substr(0, 2);
				//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtOverTimeDesc').val()) ;
				//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtOverTimeRef').val()) ;
				$($(obj).closest('tr')).children('td:eq(0)').html($('#cobEmpId').val()) ;
				$($(obj).closest('tr')).children('td:eq(1)').html($('#cobEmpId :selected').text()) ;
				$($(obj).closest('tr')).children('td:eq(2)').text($('#txtDateOver').val()) ;
				$($(obj).closest('tr')).children('td:eq(3)').text($('#cobProject').val()) ;
				$($(obj).closest('tr')).children('td:eq(4)').text($('#cobProject :selected').text()) ;
				$($(obj).closest('tr')).children('td:eq(5)').text($('#overtimeId').val()) ;
				$($(obj).closest('tr')).children('td:eq(6)').html($('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val() ) ;
				$($(obj).closest('tr')).children('td:eq(7)').html($('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val() ) ;
				$($(obj).closest('tr')).children('td:eq(8)').html($('#txtOTHour').val()) ;
				$($(obj).closest('tr')).children('td:eq(9)').text($('#txtOverTimeDesc').val()) ;
			}
			$('#sbg-popup-box').html(resp.mesg) ;
			$('#sbg-popup-box').show() ;
			$('#sbg-popup-box').fadeOut(10000) ;
			clearOverTime() ;
		}
	}
	else {
		//showDialog("Error", resp.mesg) ;
		if (resp.type == "emp"){
			$('#txtEmpId').focus();
			$('#overtime_emp_id').text('Invalid employee ID.');
		} else {
			showDialog("System Message",resp.mesg) ;
		
		}
	}
}
function validateOverTime() {
	if (isNaN($('#cobEmpId').val())){
		showDialog("Error", "Employee id can not be blank.") ;
		$('#cobEmpId').focus();
		return false ;
	}
	
	$('#overtime_err_mesg').text('') ;
	alert($('#txtOverTimeDesc').val());
	if ($('#txtOverTimeDesc').blank())
	{
		$('#overtime_err_desc').show() ;
		$('#txtOverTimeDesc').focus() ;
		$('#overtime_err_mesg').text("Over time description can not be blank.") ;
		return false ;
	}
	else 
		$('#overtime_err_desc').hide() ;
		
	
	var time_start = new Date(2000, 0, 1,  parseInt($('#cboTimeStartHour').val()), parseInt($('#cboTimeStartMinute').val()));
	var time_end = new Date(2000, 0, 1,  parseInt($('#cboTimeEndHour').val()), parseInt($('#cboTimeEndMinute').val()));
	
	var diff = time_end - time_start;
	var minute = Math.floor(diff / 1000 / 60);
	
	if(minute > 0){
	
	} else {
		$('#overtime_err_desc').show() ;
		$('#cboTimeStartHour').focus() ;
		$('#overtime_err_mesg').text("Over time description can not be blank.") ;
		return false;
	}
			
	return true ;
}
function getShiftHour(){
	if($('#txtDateOver').val() != ''){
		var data = {"type": "shifthour", "empId":$('#cobEmpId').val(), "date": $('#txtDateOver').val() } ;
		var url = "request.pzx?c=" + attendance_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onOverTimeResponse,overtime_obj) ;
			
	} else {
			$('#shiftHourMsg').hide(); 
			$('#shiftHourMsg').text(''); 
	}
}
function getList(){
	var data = { "type": "LIST","date_start": $('#txtPeriodStart').val(), "date_end": $('#txtPeriodEnd').val()} ;
	var url = "request.pzx?c=" + overtime_url + "&d=" + new Date().getTime() ;	
	callServer(url,"json",data,onOverTimeResponse,overtime_obj) ;
}

function populateEmployee(){
	$dept_id = $('#cobDept').val();	
	var data = { "type": C_GET + "_EMP","id": $dept_id} ;
	var url = "request.pzx?c=" + overtime_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onOverTimeResponse,overtime_obj) ;
	
}