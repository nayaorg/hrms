var C_PORTAL_CLAIM = "p_e";


//$('#btnClaimAdd').button().bind('click',addClaim) ;
$('#btnClaimEdit').button().bind('click',updateClaim) ;

function addClaim() {
	saveClaim(C_ADD) ;
}
function updateClaim() {
	saveClaim(C_UPDATE) ;
}

function saveClaim(type) {
	if (validateClaim()) {
		$("#addClaimHeaderView").modal("hide");
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
		callServer(url,"json",data,onAddClaimResponse,claim_obj) ;
	}
}

function onAddClaimResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			var claim_type = 'Personal';
			$('#expense-table tr:first').after("<tr style='background-color: #cccccc;' id='claim-rows'>" + 
				"<td>" + $('#txtClaimDesc').val() + "</td>" + 
				"<td>" + $('#cobClaimType option:selected').text() + "</td>" + 
				"<td>0.00</td>" +
				"<td>" + 'Pending' + "</td>" + 
				"<td>" + $('#txtClaimDate').val() + "</td>" + 
				"<td><button onclick=\"return editHeader("+resp.data+");\" id=\"btnEdit\" class=\"btn btn-primary\">Edit Header</button>" +
				"<button onclick=\"return uploadDoc("+resp.data+");\" id=\"btnUpload\" class=\"btn btn-primary\">Upload Doc</button>" +
				"<button onclick=\"return addItem("+resp.data+");\" id=\"btnAdd\" class=\"btn btn-primary\">Add Item</button></td>" +
				"</tr>"); 
		}
		else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(0)').html($('#txtClaimDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').text($('#cobClaimType option:selected').text()) ;
			var date = $('#txtClaimDate').val().split(" ");
			$($(obj).closest('tr')).children('td:eq(4)').text(date[0]) ;
		}
	}
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