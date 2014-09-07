
var claim_doc_approval_obj = null ;

$(document).ready(function() {
	$('#btnClaimFilter').button().bind('click',showAllClaims) ;
	$('#btnClaimDocumentApprovalUpdate').button().bind('click',updateClaimDocumentApproval);
	$('#claim-filter-by').change(populateFilterOptions) ;
	$('#claim-tabs').tabs() ;
	
	showAllClaims();
	
	$(window).resize(resizeClaimDocGrid) ;
	resizeClaimDocGrid() ;
}) ;
function resizeClaimDocGrid() {
	var h = $("#sbg-center-panel").outerHeight() * 0.40;
	$("div#sbg-claim-data").css("height", h +'px') ;		
}
function updateClaimDocumentApproval() {
	var data = { "type": C_UPDATE, 
				"id": $('#lblHeaderClaimID').html(), 
				"doc_data" :getDocsList()};
	var url = "request.pzx?c=" + claim_doc_approval_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimDocumentApprovalResponse,claim_doc_approval_obj) ;
}
function getDocsList(){
	var lines = "";
	var sep = "" ;
	var head_id = "";
	var id = "" ;
	var line_no = "" ;
	
	$('#document-list tr').each(function() { 
		id = $(this).find("td").eq(0).html() ;
		doc_id = $(this).find("td").eq(1).html() ;
		if(isNaN(id)){
		} else {
			var str = id + '^' + 
						doc_id + '^' +
						$('#cobVerified' + doc_id).val();
			lines = lines + sep + str;
			sep = "|" ;		
		}
	}); 
	return lines;
}
function showAllClaims() {
	var data;
	if ($("#claim-filter-by").val() != "" || $("#claim-filter-option").val() != "") {
		data = { "type": C_LIST, "current_user": $("#claim-group-view-as").val(), "filter_conditions": $("#claim-filter-by").val() + " = '" + $("#claim-filter-option").val() + "'" };
	} else {
		data = { "type": C_LIST, "current_user": $("#claim-group-view-as").val() };
	}
	var url = "request.pzx?c=" + claim_doc_approval_url + "&d=" + new Date().getTime() ;
	
	callServer(url,"json",data,onClaimDocumentApprovalResponse,claim_doc_approval_obj) ;
}
function showClaimDocumentApproval(obj,resp) {
	if (resp.status == C_OK) {
		$('#lblHeaderClaimID').html(resp.data.id);
		$('#lblHeaderDesc').html(resp.data.desc);
		$('#lblHeaderDate').html(resp.data.date);
		$('#document-list').html(resp.data.list);
	
		$('#claim-document-header').show();
		$('#claimDocumentApprovalControlButton').show();
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function showDocument(id, doc_id){
	var url = "report.pzx?c=" + claim_doc_approval_url + "&d=" + new Date().getTime() +
		"&dt=" + id + "&dp=" + doc_id + 
		"&t=" + "showDoc" ;
	showReport(url); 
}

function editClaimDocumentApproval(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + claim_doc_approval_url + "&d=" + new Date().getTime();		
	claim_doc_approval_obj = obj ;
	callServer(url,"json",data,showClaimDocumentApproval,obj) ;
}
function onClaimDocumentApprovalResponse(obj,resp) {
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
					"<td style='text-align:center'><a href='javascript:' onclick='editClaimDocumentApproval(" + this.id + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"</tr>"); 
			});
			$('#claim-document-header').hide();
			$('#claimDocumentApprovalControlButton').hide();
		}
	}
	else 
		showDialog("Error",resp.mesg) ;
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