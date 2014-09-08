var C_CLAIM_FILTER 	= "c_f";
var	C_OK 			= "0";
var C_UPDATE_VIEW	= "p_c_u_v";

$(document).ready(function () {
	$('#fromDate').datepicker({
	    format: "dd/mm/yyyy"
	}); 
	
	$('#toDate').datepicker({
	    format: "dd/mm/yyyy"
	});
});


function editHeader(id) {
	showProgress("loading Claim Header" ) ;
	var data = { "type": C_UPDATE_VIEW, "id" : id} ;
	var url  = "index.pzx?c=" + claim_url + "&t=" + C_UPDATE_VIEW  + "&d=" + new Date().getTime() ;
	$(".content").load(url,data,function() {hideProgress();}) ;
	// change breadcrum
}

function uploadDoc(id) {
	alert(id);
}

function addItem(id) {
	alert(id);
}



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