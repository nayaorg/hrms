var attendance_obj = null ;
var updateProcess = false;

$(document).ready(function() 
{ 
	var dteopt = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};
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

	$('#txtDateAttendance').datepicker(dteopt) ;
	$('#txtPeriodStart').datepicker(dteperiod) ;
	$('#txtPeriodEnd').datepicker(dteperiod2) ;
	
	$('#btnAttendanceClear').button().bind('click',clearAttendance) ;
	$('#btnAttendanceUpdate').button().bind('click',updateAttendance) ;
	$('#btnAttendanceAdd').button().bind('click',addAttendance) ;
	$('#btnAttendancePrint').button().bind('click',printAttendance) ;
	$('#btnAttendanceApply').button().bind('click',getList) ;
	$('#btnAttendanceInputControl').button().bind('click',showInput) ;
	$('#btnAttendanceHide').button().bind('click',hideInput) ;

	$('#cobDept').change(populateEmployee) ;
	$('#attendance-tabs').tabs() ;
	$('#txtAttendanceDesc').focus() ;
	
	$(window).resize(resizeAttendanceGrid) ;
	resizeAttendanceGrid() ;
	
	getList();
	
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
	
	$('#sbg-attendance-entry').hide();
	clearAttendance() ;
}) ;
function hideInput(){
	$('#sbg-attendance-entry').hide('slow');
}
function showInput(){
	$('#sbg-attendance-entry').show('slow');
}
function resizeAttendanceGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-attendance-entry").outerHeight() - 55;
	$("div#sbg-attendance-data").css("height", h +'px') ;		
}
function clearAttendance() {
	$('#txtDateAttendance').val("") ;
	
	$('#cobDept').val("");
	populateEmployee();
	
	$('#cboTimeStartHour').val("00");
	$('#cboTimeStartMinute').val("00");
	$('#cboTimeEndHour').val("00");
	$('#cboTimeEndMinute').val("00");
	
	$('#cboBreakStartHour').val("00");
	$('#cboBreakStartMinute').val("00");
	$('#cboBreakEndHour').val("00");
	$('#cboBreakEndMinute').val("00");
	
	$('#inpShiftStart').val("--:--") ;
	$('#inpShiftEnd').val("--:--") ;
			
	$('#inpOTStart').val(0);
	$('#inpOTEnd').val(0);
	$('#inpLateIn').val(0);
	$('#inpEarlyOut').val(0);
	
	$('#cobProject').val("");
	
	$('#seq_number').val(-1);
	
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;
	var yyyy = today.getFullYear();

	if(dd<10) {
		dd='0'+dd;
	} 

	if(mm<10) {
		mm='0'+mm;
	} 
	
	
	$('#Attendance-tabs').tabs('select',0) ;
	$('#cobEmpId').focus() ;
	$('#btnAttendanceUpdate').prop('disabled','disabled') ;
	
	$('#attendance_err_mesg').text('') ;
	$('#attendance_err_desc').hide() ;
	
	$('#attendance_emp_id').text('');
	$('#attendance_emp_id').show();
	
	$('#cobEmpId').val("");
	$('#cobDept').prop('disabled','') ;
	$('#cobEmpId').prop('disabled','') ;
	$('#txtDateAttendance').prop('disabled','') ;
	attendance_obj = null;
}
function recalculateTime(){
	if($('#inpShiftStart').val() != '--:--'){
		
		var diff;
		var minute;
		var hour;
		
		var countLateIn = 0;
		var countEarlyOut = 0;
		var countOvertimeIn = 0;
		var countOvertimeOut = 0;
		
		var shift_start = new Date(2000, 0, 1,  parseInt($('#inpShiftStart').val().substr(0,2)), parseInt($('#inpShiftStart').val().substr(3,2)));;
		var shift_end = new Date(2000, 0, 1,  parseInt($('#inpShiftEnd').val().substr(0,2)), parseInt($('#inpShiftEnd').val().substr(3,2)));;
		
		var time_start = new Date(2000, 0, 1,  parseInt($('#cboTimeStartHour').val()), parseInt($('#cboTimeStartMinute').val()));
		var time_end = new Date(2000, 0, 1,  parseInt($('#cboTimeEndHour').val()), parseInt($('#cboTimeEndMinute').val()));
		var break_start = new Date(2000, 0, 1,  parseInt($('#cboBreakStartHour').val()), parseInt($('#cboBreakStartMinute').val()));
		var break_end = new Date(2000, 0, 1,  parseInt($('#cboBreakEndHour').val()), parseInt($('#cboBreakEndMinute').val()));
		
		var limit_before = parseInt($('#limit_before').val());
		var limit_after = parseInt($('#limit_after').val());
		var tolerance = parseInt($('#tolerance').val());
		
		if(time_start > shift_start){
			diff = time_start - shift_start;
			minute = Math.floor(diff / 1000 / 60);
			if(minute > tolerance){
				countLateIn = countLateIn + (minute - tolerance);
			}
		} else {
			diff = shift_start - time_start;
			minute = Math.floor(diff / 1000 / 60);
			minute = (minute < tolerance) ? 0 : minute - tolerance;
			countOvertimeIn = countOvertimeIn + ( (minute > limit_before * 60) ? (limit_before * 60) : minute);
		}
		
		if(time_end < shift_end){
			diff = shift_end - time_end;
			minute = Math.floor(diff / 1000 / 60);
			if(minute > tolerance){
				countEarlyOut = countEarlyOut + (minute - tolerance);
			}
		} else {
			diff = time_end - shift_end;
			minute = Math.floor(diff / 1000 / 60);
			minute = (minute < tolerance) ? 0 : minute - tolerance;
			countOvertimeOut = countOvertimeOut + (( (minute > limit_after * 60) ? (limit_after * 60) : minute));
		}
		
		$('#inpOTStart').val(countOvertimeIn);
		$('#inpOTEnd').val(countOvertimeOut);
		$('#inpLateIn').val(countLateIn);
		$('#inpEarlyOut').val(countEarlyOut);
	}
}
function updateAttendance() {
	saveAttendance(C_UPDATE) ;
}
function addAttendance() {
	saveAttendance(C_ADD) ;
}
function saveAttendance(type) {
	if (validateAttendance()) {
		var data = { "type": type,"emp_id": $('#cobEmpId').val(), "date": $('#txtDateAttendance').val(),
			"project_id": $('#cobProject').val(), "seq_number": $('#seq_number').val(),
			"time_start": $('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val(), 
			"time_end": $('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val(),
			"break_start": $('#cboBreakStartHour').val() + ":" + $('#cboBreakStartMinute').val(), 
			"break_end": $('#cboBreakEndHour').val() + ":" + $('#cboBreakEndMinute').val(),
			"ot_start": $('#inpOTStart').val(), "ot_end": $('#inpOTEnd').val(), 
			"shift_start": $('#inpShiftStart').val(), "shift_end": $('#inpShiftEnd').val(), 
			"limit_before": $('#limit_before').val(), "limit_after": $('#limit_after').val(), 
			"tolerance": $('#tolerance').val(),
			"late_in": $('#inpLateIn').val(), "early_out": $('#inpEarlyOut').val()};
		var url = "request.pzx?c=" + attendance_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onAttendanceResponse,attendance_obj) ;
	}
}
function printAttendance() {
	var url = "report.pzx?c=" + attendance_url + "&d=" + new Date().getTime() + 
			"&dt=" + $('#txtPeriodStart').val() +
			"&dtend=" + $('#txtPeriodEnd').val();
	showReport(url) ;
}
function editAttendance(id, date_attendance, seq_number, obj) {
	$('#cobDept').val("");
	populateEmployee();
	
	var data = { "type": C_GET,"id": id, "date_attendance":date_attendance, "seq_number":seq_number} ;
	var url = "request.pzx?c=" + attendance_url + "&d=" + new Date().getTime() ;		
	attendance_obj = obj ;
	callServer(url,"json",data,showAttendance,obj) ;
}

function deleteAttendance(id, date_attendance, seq_number, obj) {
	if (confirm("Confirm you want to delete attendance for employee id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id, "date_attendance":date_attendance, "seq_number":seq_number} ;
		var url = "request.pzx?c=" + attendance_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onAttendanceResponse,obj) ;
	}
}
function showAttendance(obj,resp) {
	if (resp.status == C_OK) {
		showInput();
		$('#txtDateAttendance').val(resp.data.date) ;
		$('#cobProject').val(resp.data.project_id) ;
		$('#seq_number').val(resp.data.seq_number) ;
	
		$('#cboTimeStartHour').val(resp.data.time_start.substr(0,2));
		$('#cboTimeStartMinute').val(resp.data.time_start.substr(3,2));
		$('#cboTimeEndHour').val(resp.data.time_end.substr(0,2));
		$('#cboTimeEndMinute').val(resp.data.time_end.substr(3,2));
		
		$('#cboBreakStartHour').val(resp.data.break_start.substr(0,2));
		$('#cboBreakStartMinute').val(resp.data.break_start.substr(3,2));
		$('#cboBreakEndHour').val(resp.data.break_end.substr(0,2));
		$('#cboBreakEndMinute').val(resp.data.break_end.substr(3,2));
		
		updateProcess = true;
		
		$('#cobEmpId').val(resp.data.emp_id) ;
		getShiftHour();
		
		$('#btnAttendanceUpdate').prop('disabled','') ;
		$('#cobDept').prop('disabled','disabled') ;
		$('#cobEmpId').prop('disabled','disabled') ;
		$('#txtDateAttendance').prop('disabled','disabled') ;
		$('#txtAttendanceDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onAttendanceResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == "emp") {
		
			$('#attendance_emp_id').show();
			$('#attendance_emp_id').text(resp.data.name);
			$('#shiftType').val(resp.data.shiftType);
			$('#shiftGroupID').val(resp.data.shiftGroup);
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
		} else if (resp.type == "shifthour") {
			if(!updateProcess){
		
				$('#cboTimeStartHour').val(resp.data.start.substr(0,2));
				$('#cboTimeStartMinute').val(resp.data.start.substr(3,2));
				$('#cboTimeEndHour').val(resp.data.end.substr(0,2));
				$('#cboTimeEndMinute').val(resp.data.end.substr(3,2));
				
				$('#cboBreakStartHour').val(resp.data.break_start.substr(0,2));
				$('#cboBreakStartMinute').val(resp.data.break_start.substr(3,2));
				$('#cboBreakEndHour').val(resp.data.break_end.substr(0,2));
				$('#cboBreakEndMinute').val(resp.data.break_end.substr(3,2));
			
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
			var fr = '<tr><td style="width:50px;height:1px"></td>' +
					'<td style="width:150px"></td>' + 
					'<td style="width:100px"></td>' +
					'<td style="width:100px"></td>' +
					'<td style="width:50px;display:none"></td>' +
					'<td style="width:50px;display:none"></td>' +
					'<td style="width:50px"></td>' +
					'<td style="width:50px"></td>' + 
					'<td style="width:50px"></td>' +
					'<td style="width:50px"></td>' +
					'<td style="width:25px"></td>' +
					'<td style="width:25px"></td>' +
					'</tr>' ;
			$('#sbg-attendance-table').html(fr + resp.data) ;
		} else {
			if (resp.type == C_ADD) {
				var date_attendance = $('#txtDateAttendance').val().substr(6, 4) + $('#txtDateAttendance').val().substr(3, 2) + $('#txtDateAttendance').val().substr(0, 2);
				$('#sbg-attendance-table tr:first').after("<tr>" + 
					"<td>" + $('#cobEmpId').val() + "</td>" + 
					'<td>' + $('#cobEmpId :selected').text() + "</td>" + 
					"<td>" + $('#txtDateAttendance').val() + "</td>" + 
					'<td>' + $('#cobProject :selected').text() + "</td>" + 
					'<td style="display:none">' + $('#cobProject').val() + "</td>" + 
					'<td style="display:none">' + resp.data + "</td>" + 
					"<td>" + $('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val() + "</td>" + 
					"<td>" + $('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val() + "</td>" + 
					"<td>" + $('#cboBreakStartHour').val() + ":" + $('#cboBreakStartMinute').val() + "</td>" + 
					"<td>" + $('#cboBreakEndHour').val() + ":" + $('#cboBreakEndMinute').val() + "</td>" + 
					"<td style='text-align:center'><a href='javascript:' onclick='editAttendance(" + $('#cobEmpId').val() + ", " + date_attendance + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
					"<td style='text-align:center'><a href='javascript:' onclick='deleteAttendance(" + $('#cobEmpId').val() + ", " + date_attendance + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
					"</tr>"); 
			} else if (resp.type == C_DELETE) {
				$($(obj).closest("tr")).remove() ;
			} else if (resp.type == C_UPDATE) {
				//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtAttendanceDesc').val()) ;
				//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtAttendanceRef').val()) ;
				$($(obj).closest('tr')).children('td:eq(0)').html($('#cobEmpId').val()) ;
				$($(obj).closest('tr')).children('td:eq(1)').html($('#cobEmpId :selected').text()) ;
				$($(obj).closest('tr')).children('td:eq(2)').text($('#txtDateAttendance').val()) ;
				$($(obj).closest('tr')).children('td:eq(3)').text($('#cobProject :selected').text()) ;
				$($(obj).closest('tr')).children('td:eq(4)').text($('#cobProject').val()) ;
				$($(obj).closest('tr')).children('td:eq(5)').text($('#seq_number').val()) ;
				$($(obj).closest('tr')).children('td:eq(6)').html($('#cboTimeStartHour').val() + ":" + $('#cboTimeStartMinute').val() ) ;
				$($(obj).closest('tr')).children('td:eq(7)').html($('#cboTimeEndHour').val() + ":" + $('#cboTimeEndMinute').val() ) ;
				$($(obj).closest('tr')).children('td:eq(8)').html($('#cboBreakStartHour').val() + ":" + $('#cboBreakStartMinute').val() ) ;
				$($(obj).closest('tr')).children('td:eq(9)').html($('#cboBreakEndHour').val() + ":" + $('#cboBreakEndMinute').val() ) ;
			}
			$('#sbg-popup-box').html(resp.mesg) ;
			$('#sbg-popup-box').show() ;
			$('#sbg-popup-box').fadeOut(10000) ;
			clearAttendance() ;
		}
	}
	else {
		if (resp.type == "emp"){
			$('#attendance_emp_id').show();
			$('#attendance_emp_id').text('Invalid employee ID.');
		} else{
			showDialog("System Message",resp.mesg) ;
		}
	}
}
function validateAttendance() {
	if (isNaN($('#cobEmpId').val())){
		showDialog("Error", "Employee id can not be blank.") ;
		$('#cobEmpId').focus();
		return false ;
	} 
	
	$('#attendance_err_mesg').text('') ;
	
	if($('#cobProject').val() == 0){
		showDialog("Error", "Project can not be blank.") ;
		$('#cobProject').focus();
		return false ;
	}
			
	return true ;
}
function getEmpId(){
	if($('#txtEmpId').val() != ''){
	
		if (isNaN($('#txtEmpId').val())){
			$('#attendance_emp_id').show();
			$('#attendance_emp_id').text('Invalid employee ID.');
		} else{
			var data = {"type": "emp","id": $('#txtEmpId').val() } ;
			var url = "request.pzx?c=" + attendance_url + "&d=" + new Date().getTime() ;
			callServer(url,"json",data,onAttendanceResponse,attendance_obj) ;
		}
	} else {
		$('#attendance_emp_id').text('');
	}
}
function getShiftHour(){
	if($('#txtDateAttendance').val() != ''){
		var data = {"type": "shifthour", "empId":$('#cobEmpId').val(), "date": $('#txtDateAttendance').val() } ;
		var url = "request.pzx?c=" + attendance_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onAttendanceResponse,attendance_obj) ;
			
	} else {
			$('#shiftHourMsg').hide(); 
			$('#shiftHourMsg').text(''); 
	}
}
function getList(){
	var data = { "type": "LIST","date_start": $('#txtPeriodStart').val(), "date_end": $('#txtPeriodEnd').val()} ;
	var url = "request.pzx?c=" + attendance_url + "&d=" + new Date().getTime() ;		
	callServer(url,"json",data,onAttendanceResponse,attendance_obj) ;
}

function populateEmployee(){
	$dept_id = $('#cobDept').val();	
	var data = { "type": C_GET + "_EMP","id": $dept_id} ;
	var url = "request.pzx?c=" + attendance_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onAttendanceResponse,attendance_obj) ;
	
}