$(document).ready(function() 
{ 
	$('#btnEmployeeClaimView').button().bind('click',viewEmployeeClaim) ;
	$('#btnEmployeeClaimPrint').button().bind('click',printEmployeeClaim) ;
	$('#btnEmployeeClaimExport').button().bind('click',exportEmployeeClaim) ;
	$('#txtDateReport').focus() ;
	
	var temp = new Date();
	
	$('#cobMonth option[value="' + ("0" + (temp.getMonth() + 1)).slice(-2) + '"]').attr("selected",true);
	$('#cobYear option[value="' + temp.getFullYear() + '"]').attr("selected",true);
	
	$(window).resize(resizeEmployeeClaimGrid) ;
	resizeEmployeeClaimGrid() ;
}) ;
function resizeEmployeeClaimGrid() {
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-employee-claim-option").outerHeight() - 55;
	$("div#sbg-employee-claim-data").css("height", h +'px') ;		
}
function exportEmployeeClaim() {
	if (validateEmployeeClaim()) {
		var url = "report.pzx?c=" + employee_claim_url + "&d=" + new Date().getTime()  +
				"&dtend=" + $('#cobYear').val() +
				"&dt=" + $('#cobMonth').val() + 
				"&department=" + $('#cobDept').val() + "&t=" + C_EXPORT ;
		
		showReport(url); ;
	}
}
function printEmployeeClaim() {
	var url = "report.pzx?c=" + employee_claim_url + "&d=" + new Date().getTime() +
			"&dtend=" + $('#cobYear').val() +
			"&dt=" + $('#cobMonth').val() + 
			"&department=" + $('#cobDept').val() + "&t=" + C_REPORT ;
	
	showReport(url) ;
}
function viewEmployeeClaim() {
	var data = { "type": C_LIST, "dept": $('#cobDept').val(), "empIdBegin": $('#cobMonth').val(), "empIdEnd":$('#cobYear').val()};
	var url = "request.pzx?c=" + employee_claim_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showEmployeeClaim) ;
}
function validateEmployeeClaim(){
	return true;
}
function showEmployeeClaim(obj,resp) {
	var fr = '<tr><td style="width:50px;height:1px"></td>' + 
			'<td style="width:150px"></td>' + 
			'<td style="width:200px"></td>' +
			'</tr>' ;
	$('#sbg-employee-claim-table').html(fr + resp) ;
}