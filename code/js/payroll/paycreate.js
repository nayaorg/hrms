$(document).ready(function() 
{ 
	$('#cobPayCreateYear').val(new Date().getFullYear()) ;
	$('#cobPayCreateMonth').val(new Date().getMonth()+1) ;
	$('#btnPayCreate').button().bind('click',createPay) ;
	$('#cobPayCreateMonth').focus() ;
}) ;
function clearPayCreate() {
	$('#cobPayCreateMonth').val(new Date().getMonth()+1) ;
	$('#cobPayCreateYear').val(new Date().getFullYear());
	$('#cobPayCreateCoy').val("") ;
	$('#cobPayCreateMonth').focus() ;
}
function createPay() {
	if (validatePayCreate()) {
		var data = { "type": C_UPDATE, "coy": $('#cobPayCreateCoy').val(), 
			"year": $('#cobPayCreateYear').val(), "month": $('#cobPayCreateMonth').val() } ;
		var url = "request.pzx?c=" + paycreate_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onPayCreateResponse,null) ;
	}
}
function onPayCreateResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		showDialog("System Message",resp.mesg + "<br />" + "No Of Employee created : " + resp.data) ;
		clearPayCreate() ;
	} else if (resp.stats == C_CONFIRM) {
		
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validatePayCreate() {
	$('#paycreate_err_mesg').text('') ;
	return true ;
}