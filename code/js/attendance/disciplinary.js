var disciplinary_sort = new Array();

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
	for(i = 0; i < document.getElementById('sbg-disciplinary-table').rows[0].cells.length; i++){
		disciplinary_sort[i] = 1;
	}

	$('#txtDateReportEnd').datepicker(dteopt2) ;
	
	$('#btnDisciplinaryView').button().bind('click',viewDisciplinary) ;
	$('#btnDisciplinaryPrint').button().bind('click',printDisciplinary) ;
	$('#btnDisciplinaryExport').button().bind('click',exportDisciplinary) ;
	$('#txtDateReport').focus() ;
	$(window).resize(resizeDisciplinaryGrid) ;
	resizeDisciplinaryGrid() ;
}) ;


function sort_table(col){
	var asc = disciplinary_sort[col];
	
	for(i = 0; i < document.getElementById('sbg-disciplinary-table').rows[0].cells.length; i++){
		disciplinary_sort[i] = (col == i ? disciplinary_sort[i] * -1 : 1);
	}
	
	var tbody = document.getElementById('sbg-disciplinary-table');
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
function resizeDisciplinaryGrid() {
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-disciplinary-option").outerHeight() - 55;
	$("div#sbg-disciplinary-data").css("height", h +'px') ;		
}
function exportDisciplinary() {
	if (validateDisciplinary()) {
		var dp = ($('input:radio[name=rdoCriteria]:checked').val() == 'D') ? $('#cobDepartment').val() : "-1";
		var empIdBegin = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdBegin').val() : "-1";
		var empIdEnd = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdEnd').val() : "-1";
		var url = "report.pzx?c=" + disciplinary_url + "&d=" + new Date().getTime() +
			"&reporttype=" + $('#cboType').val() + 
			"&empIdBegin=" + empIdBegin + "&empIdEnd=" + empIdEnd +
			"&dt=" + $('#txtDateReportBegin').val() + "&dtend=" + $('#txtDateReportEnd').val() + 
			"&dp=" + dp + 
			"&t=" + C_EXPORT ;
		showReport(url); 
	}
}
function printDisciplinary() {
	var dp = ($('input:radio[name=rdoCriteria]:checked').val() == 'D') ? $('#cobDepartment').val() : "-1";
	var empIdBegin = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdBegin').val() : "-1";
	var empIdEnd = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdEnd').val() : "-1";
	var url = "report.pzx?c=" + disciplinary_url + "&d=" + new Date().getTime() +
		"&reporttype=" + $('#cboType').val() + 
		"&empIdBegin=" + empIdBegin + "&empIdEnd=" + empIdEnd +
		"&dt=" + $('#txtDateReportBegin').val() + "&dtend=" + $('#txtDateReportEnd').val() + 
		"&dp=" + dp + 
		"&t=" + C_REPORT ;
	showReport(url) ;
}
function viewDisciplinary() {
	var dp = ($('input:radio[name=rdoCriteria]:checked').val() == 'D') ? $('#cobDepartment').val() : "-1";
	var empIdBegin = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdBegin').val() : "-1";
	var empIdEnd = ($('input:radio[name=rdoCriteria]:checked').val() == 'P') ? $('#txtEmpIdEnd').val() : "-1";
	var data = { "type": C_LIST, "empIdBegin": empIdBegin, "empIdEnd": empIdEnd, "dept": dp, 
			"dateReportBegin": $('#txtDateReportBegin').val(), "dateReportEnd": $('#txtDateReportEnd').val(), "reporttype": $('#cboType').val()} ;
	var url = "request.pzx?c=" + disciplinary_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showDisciplinary) ;
}
function validateDisciplinary(){
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
function showDisciplinary(obj,resp) {
	var fr = '<tr><td style="width:50px;height:1px""></td>' + 
			'<td style="width:150px"></td>' +
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' + 
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'</tr>' ;
	$('#sbg-disciplinary-table').html(fr + resp) ;
}