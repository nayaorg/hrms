var staff_periodic_attendance_sort = new Array();

$(document).ready(function() 
{ 
	var dteopt = {
		dateFormat: "dd/mm/yy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};

	$('#txtDateReportBegin').datepicker(dteopt) ;
	
	var dteopt2 = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};
	var i;
	for(i = 0; i < document.getElementById('sbg-staff-periodic-attendance-table').rows[0].cells.length; i++){
		staff_periodic_attendance_sort[i] = 1;
	}

	$('#txtDateReportEnd').datepicker(dteopt2) ;
	
	$('#btnStaffPeriodicAttendanceView').button().bind('click',viewStaffPeriodicAttendance) ;
	$('#btnStaffPeriodicAttendancePrint').button().bind('click',printStaffPeriodicAttendance) ;
	$('#btnStaffPeriodicAttendanceExport').button().bind('click',exportStaffPeriodicAttendance) ;
	$('#txtDateReport').focus() ;
	$(window).resize(resizeStaffPeriodicAttendanceGrid) ;
	resizeStaffPeriodicAttendanceGrid() ;
}) ;

function sort_table(col){
	var asc = staff_periodic_attendance_sort[col];
	
	for(i = 0; i < document.getElementById('sbg-staff-periodic-attendance-table').rows[0].cells.length; i++){
		staff_periodic_attendance_sort[i] = (col == i ? staff_periodic_attendance_sort[i] * -1 : 1);
	}
	
	var tbody = document.getElementById('sbg-staff-periodic-attendance-table');
    var rows = tbody.rows, rlen = rows.length, arr = new Array(), i, j, cells, clen;
	
    for(i = 1; i < rlen; i++){
		cells = rows[i].cells;
		clen = cells.length;
		arr[i-1] = new Array();
        for(j = 0; j < clen; j++){
			arr[i-1][j] = cells[j].innerHTML;
			cells[j].innerHTML = '';
        }
    }
    arr.sort(function(a, b){
        return (a[col] == b[col]) ? 0 : ((a[col] > b[col]) ? asc : -1*asc);
    });
	
    for(i = 1; i < rlen; i++){
		cells = rows[i].cells;
		clen = cells.length;
        for(j = 0; j < clen; j++){
			cells[j].innerHTML = arr[i-1][j];
        }
    }
}
function resizeStaffPeriodicAttendanceGrid() {
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-staff-periodic-attendance-option").outerHeight() - 55;
	$("div#sbg-staff-periodic-attendance-data").css("height", h +'px') ;		
}
function exportStaffPeriodicAttendance() {
	if (validateStaffPeriodicAttendance()) {
		var dp = ($('input:radio[name=rdoCriteria]:checked').val() == 'D') ? $('#cobDepartment').val() : "-1";
		var empIdBegin = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdBegin').val() : "-1";
		var empIdEnd = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdEnd').val() : "-1";
		var url = "report.pzx?c=" + staff_periodic_attendance_url + "&d=" + new Date().getTime() +
			"&empIdBegin=" + empIdBegin + "&empIdEnd=" + empIdEnd +
			"&dt=" + $('#txtDateReportBegin').val() + "&dtend=" + $('#txtDateReportEnd').val() + 
			"&dp=" + $('#cobDepartment').val() + 
			"&t=" + C_EXPORT ;
		showReport(url); ;
	}
}
function printStaffPeriodicAttendance() {
	var dp = ($('input:radio[name=rdoCriteria]:checked').val() == 'D') ? $('#cobDepartment').val() : "-1";
	var empIdBegin = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdBegin').val() : "-1";
	var empIdEnd = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdEnd').val() : "-1";
	var url = "report.pzx?c=" + staff_periodic_attendance_url + "&d=" + new Date().getTime() +
		"&empIdBegin=" + empIdBegin + "&empIdEnd=" + empIdEnd +
		"&dt=" + $('#txtDateReportBegin').val() + "&dtend=" + $('#txtDateReportEnd').val() + 
		"&dp=" + dp + 
		"&t=" + C_REPORT ;
	showReport(url) ;
}
function viewStaffPeriodicAttendance() {
	var dp = ($('input:radio[name=rdoCriteria]:checked').val() == 'D') ? $('#cobDepartment').val() : "-1";
	var empIdBegin = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdBegin').val() : "-1";
	var empIdEnd = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdEnd').val() : "-1";
	var data = { "type": C_LIST, "empIdBegin": empIdBegin, "empIdEnd": $('#txtEmpIdEnd').val(), "dept": dp, 
			"dateReportBegin": $('#txtDateReportBegin').val(), "dateReportEnd": $('#txtDateReportEnd').val()} ;
	var url = "request.pzx?c=" + staff_periodic_attendance_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showStaffPeriodicAttendance) ;
}
function validateStaffPeriodicAttendance(){
	if (isNaN($('#txtEmpIdBegin').val())){
		if(!isNaN($('#txtEmpIdEnd').val())){
			showDialog("Error", "Invalid employee id range.") ;
			return false ;
		}
	} 
	if (!isNaN($('#txtEmpIdBegin').val())){
		if(isNaN($('#txtEmpIdEnd').val())){
			showDialog("Error", "Invalid employee id range.") ;
			return false ;
		}
	} 
	
	return true;
}
function showStaffPeriodicAttendance(obj,resp) {
	var fr = '<tr><td style="width:30px;height:1px"></td>' +
			'<td style="width:100px"></td>' + 
			'<td style="width:80px"></td>' + 
			'<td style="width:50px"></td>' +
			'<td style="width:50px"></td>' +
			'<td style="width:50px"></td>' + 
			'<td style="width:50px"></td>' +
			'<td style="width:50px"></td>' +
			'<td style="width:50px"></td>' +
			'<td style="width:30px"></td>' +
			'<td style="width:80px"></td>' +
			'</tr>' ;
	$('#sbg-staff-periodic-attendance-table').html(fr + resp) ;
}