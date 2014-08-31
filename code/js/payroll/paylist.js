$(document).ready(function() 
{ 
	$('#cobPayListMonth').val(new Date().getMonth()+1) ;
	$('#cobPayListYear').val(new Date().getFullYear()) ;
	$('#btnPayListView').button().bind('click',viewPayList) ;
	$('#btnPayListPrint').button().bind('click',printPayList) ;
	$('#btnPayListExport').button().bind('click',exportPayList) ;
	$('#cobPayListMonth').focus() ;
	$(window).resize(resizePayListGrid) ;
	resizePayListGrid() ;
}) ;
function resizePayListGrid() {
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-paylist-option").outerHeight() - 55;
	$("div#sbg-paylist-data").css("height", h +'px') ;		
}
function exportPayList() {
	if (validatePayList()) {
		var url = "report.pzx?c=" + paylist_url + "&d=" + new Date().getTime() +
			"&co=" + $('#cobPayListCoy').val() + "&dp=" + $('#cobPayListDept').val() +
			"&m=" + $('#cobPayListMonth').val() + "&y=" + $('#cobPayListYear').val() + "&t=" + C_EXPORT ;
		showReport(url); ;
	}
}
function printPayList() {
	var url = "report.pzx?c=" + paylist_url + "&d=" + new Date().getTime() +
		"&co=" + $('#cobPayListCoy').val() + "&dp=" + $('#cobPayListDept').val() +
		"&m=" + $('#cobPayListMonth').val() + "&y=" + $('#cobPayListYear').val() + "&t=" + C_REPORT ;
	showReport(url) ;
}
function viewPayList() {
	var data = { "type": C_LIST, "coy": $('#cobPayListCoy').val(), "dept": $('#cobPayListDept').val(), 
			"month": $('#cobPayListMonth').val(), "year": $('#cobPayListYear').val() } ;
	var url = "request.pzx?c=" + paylist_url + "&d=" + new Date().getTime() ;
	callServer(url,"html",data,showPayList) ;
}
function validatePayList() {
	if ($('#cobPayListCoy').blank()) {
		alert('You must select a Company for the Export.') ;
		$('#cobPayListCoy').focus() ;
		return false ;
	}
	return true ;
}
function showPayList(obj,resp) {
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
	$('#sbg-paylist-table').html(fr + resp) ;
}