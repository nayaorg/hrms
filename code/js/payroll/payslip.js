$(document).ready(function() 
{ 
	$('#btnPaySlipPrint').button().bind('click',printPaySlip) ;
	$('#cobPaySlipYear').val(new Date().getFullYear()) ;
	$('#cobPaySlipMonth').val(new Date().getMonth()+1) ;
	$('#cobPaySlipMonth').focus() ;
}) ;
function printPaySlip() {
	if (validatePaySlip()) {
		var url = "report.pzx?c=" + payslip_url + "&d=" + new Date().getTime() +
			"&co=" + $('#cobPaySlipCoy').val() + "&dp=" + $('#cobPaySlipDept').val() +
			"&m=" + $('#cobPaySlipMonth').val() + "&y=" + $('#cobPaySlipYear').val() ;
		showReport(url) ;
	}
}
function validatePaySlip() {
	$('#payslip_err_mesg').text('') ;
	return true ;
}