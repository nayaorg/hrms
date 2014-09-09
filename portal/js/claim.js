var C_CLAIM_FILTER 	= "c_f";
var	C_OK 			= "0";
var C_UPDATE_VIEW	= "p_c_u_v";
var C_UPLOAD_VIEW	= "p_c_up_v";
var C_ADD_ITEM_VIEW	= "p_c_a_i_v";
var C_UPLOAD		= "p_c_up";
var C_ADD_ITEM		= "p_c_a_i";

$(document).ready(function () {
	$('#fromDate').datepicker({
	    format: "dd/mm/yyyy"
	}); 
	
	$('#toDate').datepicker({
	    format: "dd/mm/yyyy"
	});
});


/* ADD/EDIT CLAIM HEADER*/

function editHeader(id) {
	showProgress("loading Claim Header" ) ;
	var data = { "type": C_UPDATE_VIEW, "id" : id} ;
	var url  = "index.pzx?c=" + claim_url + "&t=" + C_UPDATE_VIEW  + "&d=" + new Date().getTime() ;
	$(".content").load(url,data,function() {hideProgress();}) ;
	// change breadcrum
	
}

function uploadDoc(id) {
	showProgress("loading Claim Header" ) ;
	var data = { "type": C_UPLOAD_VIEW, "id" : id} ;
	var url  = "index.pzx?c=" + claim_url + "&t=" + C_UPLOAD_VIEW  + "&d=" + new Date().getTime() ;
	$(".content").load(url,data,function() {hideProgress();}) ;
	// change breadcrum
	
}

function addItem(id) {
	showProgress("loading Claim Header" ) ;
	var data = { "type": C_ADD_ITEM_VIEW, "id" : id} ;
	var url  = "index.pzx?c=" + claim_url + "&t=" + C_ADD_ITEM_VIEW  + "&d=" + new Date().getTime() ;
	$(".content").load(url,data,function() {hideProgress();}) ;
	// change breadcrum
}

/* FILTER BAR */
function filter() {
	var fromDate = document.getElementById('fromDate').value;
	var toDate 	 = document.getElementById('toDate').value;
	if(fromDate.trim() == "" || toDate.trim()=="") {
		var errorShow	= document.getElementById('errorShow');
		errorShow.innerHTML = "* Please input both 'From Date' and 'To Date'.";
		return false;
	}
	var data = { "type": C_CLAIM_FILTER, "fromDate": fromDate, "toDate": toDate };
	var url  = "index.pzx?c=" + claim_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimResponse,null) ;
	return false;
}

function onClaimResponse(obj,resp) {
	var div = document.getElementById('expense-table');
	div.innerHTML =  '';
	
	if (resp.status == C_OK) {
		var errorShow	= document.getElementById('errorShow');
		errorShow.innerHTML = "";
		
		$('#expense-table').append("<thead>" +
				"<tr>" +
					"<th width='20%'>Description</th>" +
					"<th width='20%'>Type</th>" +
					"<th width='20%'>Amount</th>" +
					"<th width='20%'>Status</th>" +
					"<th width='20%'>Date</th>" +
				"</tr>" +
			"</thead> <tbody>"); 
		
		jQuery.each(resp.data, function() {
			$('#expense-table').append("<tr>" + 
				"<td>" + this.desc + "</td>" + 
				"<td>" + this.type + "</td>" +
				"<td>" + this.amount + "</td>" + 
				"<td>" + this.status + "</td>" + 
				"<td>" + this.date + "</td>" + 
			"</tr>"); 
		});
		
		$('#expense-table').append("</tbody>");
	}
}
/* END FILTER BAR */
