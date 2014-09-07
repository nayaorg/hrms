
var claim_approval_obj = null ;

$(document).ready(function() {
	$('#btnClaimFilter').button().bind('click',showAllClaims) ;
	$('#claim-filter-by').change(populateFilterOptions) ;
	$('#btnClaimUpdate').button().bind('click',updateClaimApproval) ;
	$('#btnClaimCancel').button().bind('click',cancelClaim) ;
	
	$('#claim-tabs').tabs() ;
	
	$('#claimApprovalControlButton').hide();
	
	showAllClaims();
	
	$(window).resize(resizeClaimGrid) ;
	resizeClaimGrid() ;
}) ;
function resizeClaimGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-claim-entry").outerHeight() - 55;
	$("div#sbg-claim-data").css("height", h +'px') ;		
}
function updateClaimApproval() {
	var data = { "type": C_UPDATE, "id": $('#lblClaimID').text(), "status": $('#cobStatus').val(), "item_data":getItemList()};
	var url = "request.pzx?c=" + claim_approval_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimApprovalResponse,claim_approval_obj) ;
}
function cancelClaim(){
	var id = $('#lblClaimID').text();
	if (confirm("Confirm you want to cancel claim id : " + id + "?")) {
		var data = {"type": "CANCEL","id": id} ;
		var url = "request.pzx?c=" + claim_approval_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimApprovalResponse,claim_approval_obj) ;
	}
}
function getItemList(){
	var lines = "";
	var sep = "" ;
	var head_id = "";
	var id = "" ;
	var line_no = "" ;
	
	$('#item-list tr').each(function() { 
		id = $(this).find("td").eq(0).html() ;
		line_no = $(this).find("td").eq(1).html() ;
		if(isNaN(id)){
		} else {
			var str = id + '^' + 
						line_no + '^' +
						$('#cobStatus' + line_no).val();
			lines = lines + sep + str;
			sep = "|" ;		
		}
	}); 
	return lines;
}
function viewClaim(claim_id) {
	hidePopup(false);	
	showAllClaimItems(claim_id);
	showAllClaimDocuments(claim_id);
	$("#claim-item-title").html("Claim (ID: " + claim_id + ")");
}

function editClaimApproval(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + claim_approval_url + "&d=" + new Date().getTime();		
	claim_approval_obj = obj ;
	callServer(url,"json",data,showClaimApproval,obj) ;
}
function showClaimApproval(obj,resp) {
	if (resp.status == C_OK) {
		$('#lblClaimID').text(resp.data.id) ;
		$('#lblDescription').text(resp.data.desc) ;
		$('#lblType').text(resp.data.type) ;
		$('#lblClaimDate').text(resp.data.date) ;
		$('#lblEmpName').text(resp.data.claim_by) ;
		$('#lblAmount').text(resp.data.amount) ;
		$('#lblApprovedAmount').text(resp.data.approved_amount) ;
		$('#lblTravelPlan').text(resp.data.travel_plan) ;
		$('#lblStatus').text(resp.data.status) ;
		$('#item-list').html(resp.data.item_list);
		
		if(resp.data.status == 'Cancelled'){
			$('#claimApprovalControlButton').hide();
		} else {
			$('#claimApprovalControlButton').show();
		}
		$('#btnClaimAdd').hide() ;
		
		$('#claim-header').show();
		$('#claim-item-header').show();
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onClaimApprovalResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_UPDATE) {
			showAllClaims();
		
			$('#sbg-popup-box').html(resp.mesg) ;
			$('#sbg-popup-box').show() ;
			$('#sbg-popup-box').fadeOut(5000) ;
		} else if (resp.type == C_LIST) {
			$("tr#claim-rows").remove() ;
			jQuery.each(resp.data, function() {
				var date = this.date.split(" ");
				$('#sbg-claim-table tr:first').after("<tr id='claim-rows'>" + 
					"<td>" + this.id + "</td>" + 
					"<td>" + this.desc + "</td>" + 
					"<td>" + this.type + "</td>" + 
					"<td>" + date[0] + "</td>" + 
					"<td>" + this.claim_by + "</td>" + 
					"<td>" + this.status + "</td>" + 
					"<td>" + this.amount + "</td>" + 
					"<td>" + this.approved_amount + "</td>" + 
					"<td>" + this.travel_plan + "</td>" + 
					"<td style='text-align:center'><a href='javascript:' onclick='editClaimApproval(" + this.id + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"</tr>"); 
			});
			$('#claim-header').hide();
			$('#claim-items').hide();
			
			$('#claimApprovalControlButton').hide();
		} else if (resp.type == "CANCEL"){
			$('#claim-header').hide();
			$('#claim-item-header').hide();
			$('#claimApprovalControlButton').hide();
		
			$('#sbg-popup-box').html(resp.mesg) ;
			$('#sbg-popup-box').show() ;
			$('#sbg-popup-box').fadeOut(5000) ;
		}
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function showAllClaims() {
	var data;
	if ($("#claim-filter-by").val() != "" || $("#claim-filter-option").val() != "") {
		data = { "type": C_LIST, "current_user": $("#claim-group-view-as").val(), "filter_conditions": $("#claim-filter-by").val() + " = '" + $("#claim-filter-option").val() + "'" };
	} else {
		data = { "type": C_LIST, "current_user": $("#claim-group-view-as").val() };
	}
	var url = "request.pzx?c=" + claim_approval_url + "&d=" + new Date().getTime() ;
	
	callServer(url,"json",data,onClaimApprovalResponse,claim_approval_obj) ;
}
function populateFilterOptions() {
	if ($("#claim-filter-by").val() == "CLAIM_STATUS") {
		$("#claim-filter-option").html("<option value='1'>Pending</option><option value='4'>Approved</option><option value='5'>Rejected</option><option value='6'>Cancelled</option>");
	} else if ($("#claim-filter-by").val() == "CLAIM_TYPE") {
		$("#claim-filter-option").html("<option value='0'>Personal</option><option value='1'>Business</option>");
	} else {
		$("#claim-filter-option").html("<option value=''>-</option>");
	}
}