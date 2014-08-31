$(document).ready(function() 
{ 
	$('#btnCpfListView').button().bind('click',viewCpfList) ;
	$('#btnCpfListPrint').button().bind('click',printCpfList) ;
	$('#btnCpfListExport').button().bind('click',exportCpfList) ;
	$('#cobCpfListMonth').val(new Date().getMonth()+1) ;
	$('#cobCpfListYear').val(new Date().getFullYear()) ;
	$('#cobCpfListMonth').focus() ;
	$(window).resize(resizeCpfListGrid) ;
	//scrollbarWidth = getScrollbarWidth() ;
	resizeCpfListGrid() ;
}) ;
function resizeCpfListGrid() {
	
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-cpflist-option").outerHeight() - 70;
	$("div#sbg-cpflist-data").css("height", h +'px') ;		
}
function exportCpfList() {
	if (validateCpfList()) {
		var url = "report.pzx?c=" + cpflist_url + "&d=" + new Date().getTime() + "&t=" + C_EXPORT +
			"&co=" + $('#cobCpfListCoy').val() + "&m=" + $('#cobCpfListMonth').val() + "&y=" + $('#cobCpfListYear').val() ;
		showReport(url) ;
	}
}
function printCpfList() {
	var data = { "type": C_REPORT, "co": $('#cobCpfListCoy').val(), "m": $('#cobCpfListMonth').val(), "y": $('#cobCpfListYear').val() } ;
	var url = "report.pzx?c=" + cpflist_url + "&d=" + new Date().getTime() + "&t=" + C_REPORT +
		"&co=" + $('#cobCpfListCoy').val() + "&m=" + $('#cobCpfListMonth').val() + "&y=" + $('#cobCpfListYear').val() ;
	showReport(url) ;
}
function viewCpfList() {
	var data = { "type": C_LIST, "coy": $('#cobCpfListCoy').val(), "month": $('#cobCpfListMonth').val(), "year": $('#cobCpfListYear').val() } ;
	var url = "request.pzx?c=" + cpflist_url + "&d=" + new Date().getTime() ;
	//alert(JSON.stringify(data)) ;
	callServer(url,"html",data,showCpfList) ;
}
function validateCpfList() {
	if ($('#cobCpfListCoy').blank()) {
		alert('You must select a Company for the Export.') ;
		$('#cobCpfListCoy').focus() ;
		return false ;
	}
	return true ;
}
function showCpfList(obj,resp) {
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
	$('#sbg-cpflist-table').html(fr + resp) ;
}