$(document).ready(function() 
{ 
	$('#cobIncomeYear').val(new Date().getFullYear()) ;
	$('#btnIncomeYearView').button().bind('click',viewIncomeYear) ;
	$('#btnIncomeYearPrint').button().bind('click',printIncomeYear) ;
	$('#btnIncomeYearExport').button().bind('click',exportIncomeYear) ;
	$('#cobIncomeYear').focus() ;
	$(window).resize(resizeIncomeYearGrid) ;
	resizeIncomeYearGrid() ;
}) ;
function resizeIncomeYearGrid() {
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-incomeyear-option").outerHeight() - 55;
	$("div#sbg-incomeyear-data").css("height", h +'px') ;		
}
function exportIncomeYear() {
	if (validateIncomeYear()) {
		var url = "report.pzx?c=" + incomeyear_url + "&d=" + new Date().getTime() +
			"&co=" + $('#cobIncomeYearCoy').val() + "&dp=" + $('#cobIncomeYearDept').val() +
			"&y=" + $('#cobIncomeYear').val() + "&t=" + C_EXPORT ;
		showReport(url); ;
	}
}
function printIncomeYear() {
	var url = "report.pzx?c=" + incomeyear_url + "&d=" + new Date().getTime() +
		"&co=" + $('#cobIncomeYearCoy').val() + "&dp=" + $('#cobIncomeYearDept').val() +
		"&y=" + $('#cobIncomeYear').val() + "&t=" + C_REPORT ;
	showReport(url) ;
}
function viewIncomeYear() {
	var data = { "type": C_LIST, "coy": $('#cobIncomeYearCoy').val(), "dept": $('#cobIncomeYearDept').val(), 
			"year": $('#cobIncomeYear').val() } ;
	var url = "request.pzx?c=" + incomeyear_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showIncomeYear) ;
}
function validateIncomeYear() {
	if ($('#cobIncomeYearCoy').blank()) {
		alert('You must select a Company for the Export.') ;
		$('#cobIncomeYearCoy').focus() ;
		return false ;
	}
	return true ;
}
function showIncomeYear(obj,resp) {
	var fr = '<tr><td style="width:50px;height:1px"></td>' +
			'<td style="width:150px"></td>' + 
			'<td style="width:200px"></td>' +
			'<td style="width:150px"></td>' +
			'<td style="width:80px"></td>' + 
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'<td style="width:80px"></td>' +
			'</tr>' ;
	$('#sbg-incomeyear-table').html(fr + resp) ;
}