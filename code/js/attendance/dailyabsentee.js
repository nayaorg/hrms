var daily_absentee_sort = new Array();

$(document).ready(function() 
{ 
	var dteopt = {
		dateFormat: "dd/mm/yy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};

	$('#txtDateReportStart').datepicker(dteopt) ;
	
	var dteopt2 = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};
	var i;
	for(i = 0; i < document.getElementById('sbg-daily-absentee-table').rows[0].cells.length; i++){
		daily_absentee_sort[i] = 1;
	}

	$('#txtDateReportEnd').datepicker(dteopt2) ;
	
	$('#btnDailyAbsenteeView').button().bind('click',viewDailyAbsentee) ;
	$('#btnDailyAbsenteePrint').button().bind('click',printDailyAbsentee) ;
	$('#btnDailyAbsenteeExport').button().bind('click',exportDailyAbsentee) ;
	$('#txtDateReport').focus() ;
	$(window).resize(resizeDailyAbsenteeGrid) ;
	resizeDailyAbsenteeGrid() ;
}) ;
function sort_table(col){
	var asc = daily_attendance_sort[col];
	
	for(i = 0; i < document.getElementById('sbg-daily-absentee-table').rows[0].cells.length; i++){
		daily_attendance_sort[i] = (col == i ? daily_attendance_sort[i] * -1 : 1);
	}
	
	var tbody = document.getElementById('sbg-daily-absentee-table');
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
function resizeDailyAbsenteeGrid() {
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-daily-absentee-option").outerHeight() - 55;
	$("div#sbg-daily-absentee-data").css("height", h +'px') ;		
}
function exportDailyAbsentee() {
	if (validateDailyAbsentee()) {
		var url = "report.pzx?c=" + daily_absentee_url + "&d=" + new Date().getTime()  +
				"&dtend=" + $('#txtDateReportEnd').val() +
				"&dt=" + $('#txtDateReportStart').val() + 
				"&department=" + $('#cobDept').val() + "&t=" + C_EXPORT ;
		
		showReport(url); ;
	}
}
function printDailyAbsentee() {
	var url = "report.pzx?c=" + daily_absentee_url + "&d=" + new Date().getTime() +
			"&dtend=" + $('#txtDateReportEnd').val() +
			"&dt=" + $('#txtDateReportStart').val() + 
			"&department=" + $('#cobDept').val() + "&t=" + C_REPORT ;
	
	showReport(url) ;
}
function viewDailyAbsentee() {
	var data = { "type": C_LIST, "dept": $('#cobDept').val(), "dateReportStart": $('#txtDateReportStart').val(), "dateReportEnd":$('#txtDateReportEnd').val()};
	var url = "request.pzx?c=" + daily_absentee_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showDailyAbsentee) ;
}
function validateDailyAbsentee(){
	return true;
}
function showDailyAbsentee(obj,resp) {
	var fr = '<tr><td style="width:100px;height:1px"></td>' +
			'<td style="width:50px"></td>' + 
			'<td style="width:150px"></td>' + 
			'<td style="width:200px"></td>' +
			'</tr>' ;
	$('#sbg-daily-absentee-table').html(fr + resp) ;
}