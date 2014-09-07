
var claim_item_approval_obj = null ;

$(document).ready(function() {
	$('#btnClaimFilter').button().bind('click',showAllClaims) ;
	$('#claim-filter-by').change(populateFilterOptions) ;
	$('#btnClaimItemApprovalUpdate').button().bind('click',updateClaimItemApproval);
	$('#claim-tabs').tabs() ;
	$('#claimItemApprovalControlButton').hide();
	
	showAllClaims();
	
	$(window).resize(resizeClaimItemGrid) ;
	resizeClaimItemGrid() ;
}) ;
function resizeClaimItemGrid() {
	var h = $("#sbg-center-panel").outerHeight() * 0.40;
	$("div#sbg-claim-data").css("height", h +'px') ;		
}
function updateClaimItemApproval() {
	var data = { "type": C_UPDATE, 
				"id": $('#lblHeaderClaimID').html(), 
				"item_data" :getItemList()};
	var url = "request.pzx?c=" + claim_item_approval_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimItemApprovalResponse,claim_item_approval_obj) ;
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
						$('#lblExpenseID' + line_no).html() + '^' +
						$('#txtAppAmount' + line_no).val();
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
	var url = "request.pzx?c=" + claim_item_approval_url + "&d=" + new Date().getTime() ;
	
	callServer(url,"json",data,onClaimItemApprovalResponse,claim_item_approval_obj) ;
}
function showClaimItemApproval(obj,resp) {
	if (resp.status == C_OK) {
		$('#lblHeaderClaimID').html(resp.data.id);
		$('#lblHeaderDesc').html(resp.data.desc);
		$('#lblHeaderDate').html(resp.data.date);
		$('#item-list').html(resp.data.list);
	
		$('#claim-item-header').show();
		$('#claimItemApprovalControlButton').show();
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}

function checkItem(line_no){
	var exp_id = $('#lblExpenseID' + line_no).html();
	var total = 0;
	$('#item-list tr').each(function() { 
		if(exp_id == $(this).find("td").eq(3).html()){
			var l = $(this).find("td").eq(1).html();
			total += parseFloat($('#txtAppAmount' + l).val());
		}
	}); 
	
	$('#item-list tr').each(function() { 
		if(exp_id == $(this).find("td").eq(3).html()){
			var l = $(this).find("td").eq(1).html();
			if(total > parseFloat($('#lblLimit' + l).html())){
				$('#lblImage' + l).show();
			}else{
				$('#lblImage' + l).hide();
			}
		}
	}); 
}

function showWarning(line_no){
	showDialog("System Message", "The total approved amount for this expense item is more than the limit allowed") ;
}

function editClaimItemApproval(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + claim_item_approval_url + "&d=" + new Date().getTime();		
	claim_item_approval_obj = obj ;
	callServer(url,"json",data,showClaimItemApproval,obj) ;
}
function onClaimItemApprovalResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_UPDATE) {
			showAllClaims();
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
					"<td style='text-align:center'><a href='javascript:' onclick='editClaimItemApproval(" + this.id + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"</tr>"); 
			});
			$('#claim-item-header').hide();
			$('#claimItemApprovalControlButton').hide();
		}
		
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
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