
var claim_group_member_obj = null ;
var claim_group_member_id_array = [];

$(document).ready(function() {

}) ;
function clearClaimGroupMember() {
	claim_group_member_obj = null;
	claim_group_member_id_array = [];
	$("tr[id^='claim-group-member-row']").remove() ;
}
function addClaimGroupMember() {
	if (validateClaimGroupMember()) {
		$('#sbg-claim-group-member-table tr:last').after("<tr id='claim-group-member-row_" + $('#txtMemberId').val() + "'>" + 
			"<td align='center'>" + $('#txtMemberId').val() + "</td>" + 
			"<td align='center'><a href='javascript:' onclick='removeClaimGroupMemberRow(" + $('#txtMemberId').val() + ")'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
		"</tr>"); 
		claim_group_member_id_array.push($('#txtMemberId').val());
		$('#txtMemberId').val("");
	}
}
function addAllClaimGroupMember(id) {
	if (claim_group_member_id_array.length != 0) {
		var member_ids = "";
		for (var i=0;i<claim_group_member_id_array.length;i++) {
			if (i == 0) {
				member_ids += "" + claim_group_member_id_array[i];
			} else {
				member_ids += "|" + claim_group_member_id_array[i];
			}
		}
		var data = { "type": C_ADD, "id": id, "member_ids": member_ids };
		var url = "request.pzx?c=" + claim_group_member_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimGroupMemberResponse,claim_group_member_obj) ;
	}
}
function removeClaimGroupMemberRow(member_id) {
	$("tr#claim-group-member-row_" + member_id).remove();
	var i = jQuery.inArray( "" + member_id, claim_group_member_id_array );
	if (i != -1) {
		claim_group_member_id_array.splice(i, 1);
	}
}
function deleteAllClaimGroupMember(id) {
	var data = {"type": C_DELETE,"id": id} ;
	var url = "request.pzx?c=" + claim_group_member_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimGroupMemberResponse,claim_group_member_obj) ;
}
function deleteClaimGroupMember(member_id) {
	if (confirm("Confirm you want to delete member id: " + member_id + "?")) {
		$("tr#claim-group-member-row_" + member_id).remove();
		var data = { "type": C_DELETE, "id": $("#txtClaimGroupId").val(), "member_id": member_id } ;
		var url = "request.pzx?c=" + claim_group_member_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimGroupMemberResponse,null) ;
	}
}
function showAllClaimGroupMember() {
	var data = { "type": C_LIST, "id": $("#txtClaimGroupId").val() };
	var url = "request.pzx?c=" + claim_group_member_url + "&d=" + new Date().getTime() ;
	
	callServer(url,"json",data,onClaimGroupMemberResponse,claim_group_member_obj) ;
}
function onClaimGroupMemberResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			clearClaimGroupMember();
		} else if (resp.type == C_LIST) {
			clearClaimGroupMember();
			jQuery.each(resp.data, function() {
				$('#sbg-claim-group-member-table tr:last').after("<tr id='claim-group-member-row_" + this.member_id + "'>" + 
					"<td align='center'>" + this.member_id + "</td>" + 
					"<td align='center'><a href='javascript:' onclick='deleteClaimGroupMember(" + this.member_id + ")'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
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
function validateClaimGroupMember() {
	var allValid = true;
	$('#claim_group_err_mesg').text('') ;
		
	if ($('#txtMemberId').blank())
	{
		$('#claim_group_err_member_id').show() ;
		$('#txtMemberId').focus() ;
		$('#claim_group_err_mesg').text($('#claim_group_err_mesg').text() + "member id can not be blank. ") ;
		allValid = false ;
	} else { 
		$('#claim_group_err_member_id').hide() ;
	}

	return allValid ;
}