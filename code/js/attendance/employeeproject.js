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

	$('#txtDateReportEnd').datepicker(dteopt2) ;
	
	$('#btnEmployeeProjectView').button().bind('click',viewEmployeeProject) ;
	$('#btnEmployeeProjectPrint').button().bind('click',printEmployeeProject) ;
	$('#btnEmployeeProjectExport').button().bind('click',exportEmployeeProject) ;
	$('#txtDateReport').focus() ;
	$(window).resize(resizeEmployeeProjectGrid) ;
	resizeEmployeeProjectGrid() ;
}) ;
function resizeEmployeeProjectGrid() {
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-employee-project-option").outerHeight() - 55;
	$("div#sbg-employee-project-data").css("height", h +'px') ;		
}
function exportEmployeeProject() {
	if (validateEmployeeProject()) {
		var dp = $('#cobProject').val();
		var empIdBegin = $('#txtEmpId').val();
		var empIdEnd = $('#cobDepartment').val();
		var url = "report.pzx?c=" + employee_project_url + "&d=" + new Date().getTime() +
			"&reporttype=" + $('#cboType').val() + 
			"&empIdBegin=" + empIdBegin + "&empIdEnd=" + empIdEnd +
			"&dt=" + $('#txtDateReportBegin').val() + "&dtend=" + $('#txtDateReportEnd').val() + 
			"&dp=" + dp + 
			"&t=" + C_EXPORT ;
		showReport(url); 
	}
}
function printEmployeeProject() {
	if(validateEmployeeProject()){
		var dp = $('#cobProject').val();
		var empIdBegin = $('#txtEmpId').val();
		var empIdEnd = $('#cobDepartment').val();
		var url = "report.pzx?c=" + employee_project_url + "&d=" + new Date().getTime() +
			"&reporttype=" + $('#cboType').val() + 
			"&empIdBegin=" + empIdBegin + "&empIdEnd=" + empIdEnd +
			"&dt=" + $('#txtDateReportBegin').val() + "&dtend=" + $('#txtDateReportEnd').val() + 
			"&dp=" + dp + 
			"&t=" + C_REPORT ;
		showReport(url) ;
	}
}
function viewEmployeeProject() {
	if(validateEmployeeProject()){
		var dp = $('#cobProject').val();
		var empIdBegin = $('#txtEmpId').val();
		var empIdEnd = $('#cobDepartment').val();
		var data = { "type": C_LIST, "empIdBegin": empIdBegin, "empIdEnd": empIdEnd, "dept": dp, 
				"dateReportBegin": $('#txtDateReportBegin').val(), "dateReportEnd": $('#txtDateReportEnd').val(), "reporttype": $('#cboType').val()} ;
		var url = "request.pzx?c=" + employee_project_url + "&d=" + new Date().getTime() ;
		callServer(url,"html",data,showEmployeeProject) ;
	}
}
function validateEmployeeProject(){
	return true;
}
function showEmployeeProject(obj,resp) {
	var fr = '<tr><td style="width:100px;height:1px""></td>' + 
			'<td style="width:100px"></td>' +
			'<td style="width:100px"></td>' +
			'<td style="width:150px"></td>' +
			'</tr>' ;
	$('#sbg-employee-project-table').html(fr + resp) ;
	
	$('tr.empName').click(function(){
		$(this).nextUntil('tr.empName').slideToggle();
	});
}