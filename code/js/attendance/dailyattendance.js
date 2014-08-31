var daily_attendance_sort = new Array();

$(document).ready(function() 
{ 
	var dteopt = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};
	var i;
	for(i = 0; i < document.getElementById('sbg-daily-attendance-table').rows[0].cells.length; i++){
		daily_attendance_sort[i] = 1;
	}

	$('#txtDateReport').datepicker(dteopt) ;
	
	$('#btnDailyAttendanceView').button().bind('click',viewDailyAttendance) ;
	$('#btnDailyAttendancePrint').button().bind('click',printDailyAttendance) ;
	$('#btnDailyAttendanceExport').button().bind('click',exportDailyAttendance) ;
	$('#daily-attendance-tabs').tabs() ;
	$(window).resize(resizeDailyAttendanceGrid) ;
	resizeDailyAttendanceGrid() ;
}) ;

function sort_table(col){
	var asc = daily_attendance_sort[col];
	
	for(i = 0; i < document.getElementById('sbg-daily-attendance-table').rows[0].cells.length; i++){
		daily_attendance_sort[i] = (col == i ? daily_attendance_sort[i] * -1 : 1);
	}
	
	var tbody = document.getElementById('sbg-daily-attendance-table');
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

function resizeDailyAttendanceGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-daily-attendance-option").outerHeight() - 55;
	$("div#sbg-daily-attendance-data").css("height", h +'px') ;		
}
function exportDailyAttendance() {
	if (validateDailyAttendance()) {
		var url = "report.pzx?c=" + daily_attendance_url + "&d=" + new Date().getTime() +
			"&dt=" + $('#txtDateReport').val() + 
			"&dp=" + $('#cobDept').val() + 
			"&t=" + C_EXPORT ;
		showReport(url); ;
	}
}
function printDailyAttendance() {
	var url = "report.pzx?c=" + daily_attendance_url + "&d=" + new Date().getTime() +
			"&dt=" + $('#txtDateReport').val() + 
			"&dp=" + $('#cobDept').val() + 
			"&t=" + C_REPORT ;
	showReport(url) ;
}
function viewDailyAttendance() {
	var data = { "type": C_LIST, "dept": $('#cobDept').val(), 
			"dateReport": $('#txtDateReport').val() } ;
	var url = "request.pzx?c=" + daily_attendance_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showDailyAttendance) ;
}
function validateDailyAttendance(){
	
	return true;
}
function showDailyAttendance(obj,resp) {
	var fr = '<tr><td style="width:50px;height:1px"></td>' +
			'<td style="width:150px"></td>' + 
			'<td style="width:50px"></td>' +
			'<td style="width:50px"></td>' +
			'<td style="width:50px"></td>' + 
			'<td style="width:50px"></td>' +
			'<td style="width:50px"></td>' +
			'<td style="width:50px"></td>' +
			'<td style="width:56px"></td>' +
			'<td style="width:80px"></td>' +
			'</tr>' ;
	$('#sbg-daily-attendance-table').html(fr + resp) ;
}