
var claim_limit_obj = null ;
var claim_limit_data = "";

$(document).ready(function() {
	$('#btnClaimLimitAdd').button().bind('click',addClaimLimit) ;
}) ;
function clearClaimLimit() {
	claim_limit_obj = null;
	claim_limit_data = "";
	$("tr[id^='claim-limit-row']").remove() ;
}
function addClaimLimit() {
	if (validateClaimLimit()) {
		$('#sbg-claim-limit-table tr:last').after("<tr id='claim-limit-row_" + $('#menuClaimGroupId').val() + "'>" + 
			"<td align='center'>" + $('#menuClaimGroupId option:selected').text() + "</td>" + 
			"<td align='center'>" + $('#menuClaimLimitType').val() + "</td>" + 
			"<td align='center'>" + $('#txtClaimLimitAmount').val() + "</td>" + 
			"<td align='center'><a href='javascript:' onclick='removeClaimGroupHeadRow(" + $('#menuClaimGroupId').val() + ")'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
		"</tr>"); 
		
		if (claim_limit_data != "") {
			claim_limit_data += "|" + $('#menuClaimGroupId').val() + "--" + $("#menuClaimLimitType").val() + "--" + $("#txtClaimLimitAmount").val();
		} else {
			claim_limit_data += $('#menuClaimGroupId').val() + "--" + $("#menuClaimLimitType").val() + "--" + $("#txtClaimLimitAmount").val();
		}
		
		$('#menuClaimGroupId').val("");
		$('#menuClaimLimitType').val("");
		$('#txtClaimLimitAmount').val("");
	}
}
function addAllClaimLimit(expense_item) {
	if (claim_limit_data != "") {
		var data = { "type": C_ADD, "expense_item": expense_item, "data": claim_limit_data };
		var url = "request.pzx?c=" + claim_limit_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimLimitResponse,claim_limit_obj) ;
	}
}
function deleteAllClaimLimit(expense_item) {
	var data = {"type": C_DELETE,"expense_item": expense_item} ;
	var url = "request.pzx?c=" + claim_limit_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimLimitResponse,claim_limit_obj) ;
}
function deleteClaimLimit(expense_item,grp_id,obj) {
	if (confirm("Confirm you want to delete claim limit : expense_item-" + expense_item + ", claim_group_id-" + grp_id + "?")) {
		var data = {"type": C_DELETE,"expense_item": expense_item,"grp_id":grp_id} ;
		var url = "request.pzx?c=" + claim_limit_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimLimitResponse,obj) ;
	}
}
function showAllClaimLimits(expense_item) {
	var data = { "type": C_LIST, "expense_item": expense_item };
	var url = "request.pzx?c=" + claim_limit_url + "&d=" + new Date().getTime() ;
	
	callServer(url,"json",data,onClaimLimitResponse,claim_limit_obj) ;
}
function onClaimLimitResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_DELETE) {
			$("tr#claim-limit-row_" + resp.data).remove() ;
		} else if (resp.type == C_UPDATE) {
			showAllClaimLimits($('#txtClaimLimitClaimId').val());
			
			$('#btnClaimLimitAdd').show() ;
			$('#btnClaimLimitClear').show() ;
			$('#btnClaimLimitUpdate').hide() ;
			
			clearClaimLimit();
		} else if (resp.type == C_LIST) {
			clearClaimLimit();
			jQuery.each(resp.data, function() {
				$("#sbg-claim-limit-table tr:last").after("<tr id='claim-limit-row_" + this.grp_id + "'>" + 
					"<td align='center'>" + this.grp + "</td>" + 
					"<td align='center'>" + this.limit_type + "</td>" + 
					"<td align='center'>" + this.limit_amt + "</td>" + 
					"<td style='text-align:center'><a href='javascript:' onclick='deleteClaimLimit(" + this.expense_item + "," + this.grp_id + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
					"</tr>"); 
			});
		}
		
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateClaimLimit() {
	var allValid = true;
	$('#expense_item_err_mesg').text('') ;
	
	if ($('#menuClaimGroupId').blank())
	{
		$('#claim_limit_err_claim_group_id').show() ;
		$('#menuClaimGroupId').focus() ;
		$('#expense_item_err_mesg').text($('#expense_item_err_mesg').text() + "Claim Group ID can not be blank. ") ;
		allValid = false ;
	}
	else 
		$('#claim_limit_err_claim_group_id').hide() ;
	
	if ($('#menuClaimLimitType').blank())
	{
		$('#claim_limit_err_type').show() ;
		$('#menuClaimLimitType').focus() ;
		$('#expense_item_err_mesg').text($('#expense_item_err_mesg').text() + "Please select a Claim Limit Type. ") ;
		allValid = false ;
	}
	else 
		$('#claim_limit_err_type').hide() ;
		
	if ($('#txtClaimLimitAmount').blank())
	{
		$('#claim_limit_err_amount').show() ;
		$('#txtClaimLimitAmount').focus() ;
		$('#expense_item_err_mesg').text($('#expense_item_err_mesg').text() + "Claim Limit Amount can not be blank. ") ;
		allValid = false ;
	}
	else 
		$('#claim_limit_err_amount').hide() ;

	return allValid ;
}