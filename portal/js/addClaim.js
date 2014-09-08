var C_PORTAL_CLAIM = "p_e";

$('#btnClaimAdd').button().bind('click',addClaim) ;
$('#btnClaimEdit').button().bind('click',updateClaim) ;

function addClaim() {
	saveClaim(C_ADD) ;
}

function updateClaim() {
	saveClaim(C_UPDATE) ;
}

function saveClaim(type) {
	if (validateClaim()) {
		var data = { 
			"id"			: $('#txtClaimId').val(),
			"type"			: type, 
			"desc"			: $('#txtClaimDesc').val(), 
			"claim_type"	: $('#cobClaimType').val(), 
			"date"			: $('#txtClaimDate').val(), 
			"travel_plan"	: $('#cobTravelPlan').val(),
			"claim_by"		: $('#cobEmpId').val(), 
			};
		var url = "index.pzx?c=" + claim_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimResponse,null) ;
	}
}

function onClaimResponse(obj,resp) {
	var data = {'type':C_PORTAL_CLAIM} ;
	var url = "index.pzx?c=" + home_url + "&t=" + C_PORTAL_CLAIM  + "&d=" + new Date().getTime() ;
	$(".content").load(url,data,function() {hideProgress();}) ;
}

function validateClaim() {
	var allValid = true;

	$('#claim_err_mesg').text('') ;
	if ($('#txtClaimDesc').blank())
	{
		$('#claim_err_desc').show() ;
		$('#txtClaimDesc').focus() ;
		$('#claim_err_mesg').text("Please enter the required fields.") ;
		allValid = false ;
	}
	else 
		$('#claim_err_desc').hide() ;
	
	if ($('#cobClaimType').blank())
	{
		$('#claim_err_type').show() ;
		$('#cobClaimType').focus() ;
		$('#claim_err_mesg').text("Please enter the required fields.") ;
		allValid = false ;
	}
	else 
		$('#claim_err_type').hide() ;
		
	if ($('#txtClaimDate').blank())
	{
		$('#claim_err_date').show() ;
		$('#claim_err_mesg').text("Please enter the required fields.") ;
		allValid = false ;
	}
	else 
		$('#claim_err_date').hide() ;
		
	if ($('#cobEmpId').val() == 0)
	{
		$('#claim_by_err').show() ;
		$('#cobEmpId').focus() ;
		$('#claim_err_mesg').text("Please enter the required fields.") ;
		allValid = false ;
	}
	else 
		$('#claim_by_err').hide() ;
		
	if ($('#cobTravelPlan').val() == 0)
	{
		$('#travel_plan_err').show() ;
		$('#cobTravelPlan').focus() ;
		$('#claim_err_mesg').text("Please enter the required fields.") ;
		allValid = false ;
	}
	else 
		$('#travel_plan_err').hide() ;

	return allValid ;
}