
var claim_obj = null ;
var claim_item_obj = null ;
var m_item_idx = 0 ;
var m_limit_emp_idx = 0 ;
var m_docs_idx = 0;

$(document).ready(function() {
	$('#btnClaimClear').button().bind('click',clearClaim) ;
	$('#btnClaimUpdate').button().bind('click',updateClaim) ;
	$('#btnClaimAdd').button().bind('click',addClaim) ;
	$('#btnClaimPrint').button().bind('click',printClaim) ;
	$('#btnClaimFilter').button().bind('click',showAllClaims) ;
	$('#claim-filter-by').change(populateFilterOptions) ;
	$("#fileClaimDoc").change(setUploadForm);
	$('#txtClaimDesc').focus() ;
	
	$('#btnClaimUpdate').hide() ;
	
	$('#cobDept').change(populateEmployee) ;
	
	var dteopt = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};

	$('#txtClaimDate').datepicker(dteopt) ;
	
	$('#claim-tabs').tabs() ;
	$('#claim-popup-tabs').tabs() ;
	
	showAllClaims();
	
	$(window).resize(resizeClaimGrid) ;
	resizeClaimGrid() ;
	$(window).resize(resizeClaimPopupGrid) ;
	resizeClaimPopupGrid() ;
	
	clearClaim();
}) ;
function resizeClaimGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-claim-entry").outerHeight() - 85;
	$("div#sbg-claim-data").css("height", h +'px') ;		
}
function resizeClaimPopupGrid() {
	var h = $("#sbg-center-panel").outerHeight();
	$("div#claim-popup").css("height", h +'px') ;		
}
function setUploadForm() {
	upload_form = this.form;
}
function hidePopup(hide) {
	if (hide == true) {
		$("#claim-popup").animate({
			opacity: 0.00,
			left: "+=150"
		}, 450, function() {
			$("#claim-popup").hide();
			showAllClaims();
		});
	} else {
		$("#claim-popup").show();
		$("#claim-popup").animate({
			opacity: 1.00,
			left: "-=150"
		}, 450, function() {
		
		});
	}
}
function clearClaim() {
	$('#txtClaimId').val("Auto") ;
	$('#txtClaimDesc').val("") ;
	$('#cobClaimType').val("") ;
	$('#claim-tabs').tabs("option", "active", 0) ;
	$('#claim_err_mesg').text('') ;
	$('#claim_err_desc').hide() ;
	$('#claim_err_type').hide() ;
	$('#claim_err_date').hide() ;
	$('#claim_by_err').hide() ;
	$('#travel_plan_err').hide() ;
	
	$('claim_item_err_desc').hide();
	$('claim_item_err_expense_item').hide();
	$('claim_item_err_amount').hide();
	$('claim_item_err_currency').hide();
	$('claim_item_err_expense_item').hide();
	
	$('claim_doc_err_ref').hide();
	$('claim_doc_err_desc').hide();
	
	var str_date = dateTime();
	$('#txtClaimDate').val(str_date) ;
	$('#cobTravelPlan').val("");
	
	$('#cobDept').val("");
	$('#cobEmpId').val("");
	
	$("#claim-popup").hide();
	
	$('#btnClaimAdd').show() ;
	$('#btnClaimUpdate').hide() ;
	$(".sbg-tab-title").html("Claim");
	
	
	$('#txtClaimItemDesc').val("") ;
	$('#cobExpenseItem').val("") ;
	$('#txtClaimItemAmount').val("") ;
	$('#cobCurrency').val("") ;
	$('#txtClaimItemDocumentId').val("") ;
	$('#tblItems').empty() ;
	m_item_idx = 0 ;
	
	$('#txtClaimDocRef').val("") ;
	$('#txtClaimDocDesc').val("") ;
	$('#fileClaimDoc').val("") ;
	
	$('#tblDocs').empty() ;
	m_docs_idx = 0 ;
	
	populateEmployee();
	
	claim_obj = null;
}
function dateTime() {
	var date = new Date();
	var month = "" + (date.getMonth() + 1);
	if (month < 10) {
		month = "0" + month;
	}
	
	var day = "" + date.getDate();
	if (day < 10) {
		day = "0" + day;
	}
	
	var hours = "" + date.getHours();
	if (hours < 10) {
		hours = "0" + hours;
	}
	
	var minutes = "" + date.getMinutes();
	if (minutes < 10) {
		minutes = "0" + minutes;
	}
	
	var seconds = "" + date.getSeconds();
	if (seconds < 10) {
		seconds = "0" + seconds;
	}
	
	/*return str_date = date.getFullYear() + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;*/
	return str_date = day + "/" + month + "/" + date.getFullYear();
}
function updateClaim() {
	saveClaim(C_UPDATE) ;
}
function addClaim() {
	saveClaim(C_ADD) ;
}
function saveClaim(type) {
	if (validateClaim()) {
		var data = { "type": type, 
			"id": $('#txtClaimId').val(), "desc": $('#txtClaimDesc').val(), 
			"claim_type": $('#cobClaimType').val(), "date": $('#txtClaimDate').val(), 
			"amount": $('#txtClaimAmount').val(), "claim_by": $('#cobEmpId').val(), 
			"status": $('#txtClaimStatus').val(), "approved_amount": $('#txtClaimApprovedAmount').val(), 
			"travel_plan": $('#cobTravelPlan').val(),
			"items_data": getItemList(), "docs_data" : getDocsList()};
		var url = "request.pzx?c=" + claim_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimResponse,claim_obj) ;
	}
}
function getItemList(){
	var lines = "";
	var sep = "" ;
	var head_id = "";
	var id = "" ;
	
	$('#tblItems tr').each(function() { 
		id = $(this).find("td").eq(0).html() ;
		var str = id + '^' + 
					$('#txtItemDesc' + id).val() + '^' +
					$('#txtItemItemId' + id).val() + '^' +
					$('#txtItemAmount' + id).val() + '^' +
					$('#txtItemCurrID' + id).val() + '^' +
					$('#txtItemDocID' + id).val();
		lines = lines + sep + str;
		sep = "|" ;
	}); 
	return lines;
}
function getDocsList(){
	var lines = "";
	var sep = "" ;
	var head_id = "";
	var id = "" ;
	
	$('#tblDocs tr').each(function() { 
		id = $(this).find("td").eq(0).html() ;
		var str = id + '^' + 
					$('#txtDocsRef' + id).val() + '^' +
					$('#txtDocsDesc' + id).val() + '^' +
					$('#txtDocsPath' + id).val();
		lines = lines + sep + str;
		sep = "|" ;
	}); 
	return lines;
}

function editClaim(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + claim_url + "&d=" + new Date().getTime();		
	claim_obj = obj ;
	callServer(url,"json",data,showClaim,obj) ;
}

function deleteClaim(id,obj) {
	if (confirm("Confirm you want to delete claim id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + claim_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimResponse,obj) ;
	}
}
function viewClaim(claim_id) {
	hidePopup(false);	
	showAllClaimItems(claim_id);
	showAllClaimDocuments(claim_id);
	$("#claim-item-title").html("Claim (ID: " + claim_id + ")");
}
function showClaim(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtClaimId').val(resp.data.id) ;
		$('#txtClaimDesc').val(resp.data.desc) ;
		$('#cobClaimType').val(resp.data.type) ;
		$('#txtClaimDate').val(resp.data.date) ;
		$('#txtClaimAmount').val(resp.data.amount) ;
		$('#cobDept').val(resp.data.dept);
		$('#cobEmpId').val(resp.data.claim_by) ;
		
		$('#txtClaimStatus').val(resp.data.status) ;
		$('#txtClaimApprovedAmount').val(resp.data.approved_amount) ;
		$('#cobTravelPlan').val(resp.data.travel_plan) ;
		
		showItems(resp.data.items);
		showDocs(resp.data.docs);
		
		$('#txtClaimDesc').focus() ;
		$('#btnClaimUpdate').show() ;
		$('#btnClaimAdd').hide() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onClaimResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			var claim_type = 'Personal';
			$('#sbg-claim-table tr:first').after("<tr id='claim-rows'><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtClaimDesc').val() + "</td>" + 
				"<td>" + $('#cobClaimType option:selected').text() + "</td>" + 
				"<td>" + $('#txtClaimDate').val() + "</td>" + 
				"<td>" + $('#cobEmpId option:selected').text() + "</td>" + 
				"<td>" + 'Pending' + "</td>" + 
				"<td>" + $('#txtClaimAmount').val() + "</td>" + 
				"<td>" + $('#txtClaimApprovedAmount').val() + "</td>" + 
				"<td>" + $('#cobTravelPlan option:selected').text() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editClaim(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteClaim(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtClaimDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#cobClaimType option:selected').text()) ;
			var date = $('#txtClaimDate').val().split(" ");
			$($(obj).closest('tr')).children('td:eq(3)').text(date[0]) ;
			$($(obj).closest('tr')).children('td:eq(4)').text($('#cobEmpId option:selected').text()) ;
			$($(obj).closest('tr')).children('td:eq(8)').text($('#cobTravelPlan option:selected').text()) ;
			$('#btnClaimAdd').show() ;
			$('#btnClaimUpdate').hide() ;
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
					"<td style='text-align:right'>" + this.amount + "</td>" + 
					"<td style='text-align:right'>" + this.approved_amount + "</td>" + 
					"<td>" + this.travel_plan_title + "</td>" + 
					"<td style='text-align:center'><a href='javascript:' onclick='editClaim(" + this.id + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
					"<td style='text-align:center'><a href='javascript:' onclick='deleteClaim(" + this.id + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
			});
		} else if (resp.type == 'DELETE'){
			$('#trdocs'+resp.data).remove() ;
		} else if (resp.type == C_GET + '_EMP'){
			var lines = resp.data.empList;
			var line = lines.split('|') ;
			var fld = "" ;
			var opt = "";
			
			for (var i = 0 ; i < line.length ; i++) {
				fld = line[i].split(':') ;
				opt += "<option value='"+ fld[0] +"'>"+  fld[1] +"</option>";
			}
			$("#cobEmpId").html(opt);
		}
		
		if(resp.type != 'DELETE' && resp.type != 'emp' && resp.type != C_GET + '_EMP' && resp.type != C_LIST){
			$('#sbg-popup-box').html(resp.mesg) ;
			$('#sbg-popup-box').show() ;
			$('#sbg-popup-box').fadeOut(5000) ;
			clearClaim() ;
		}
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateClaim() {
	var allValid = true;

	$('#claim_err_mesg').text('') ;
	if ($('#txtClaimDesc').blank())
	{
		$('#claim_err_desc').show() ;
		$('#txtClaimDesc').focus() ;
		$('#claim_err_mesg').text($('#claim_err_mesg').text() + "Claim description can not be blank. ") ;
		allValid = false ;
	}
	else 
		$('#claim_err_desc').hide() ;
	
	if ($('#cobClaimType').blank())
	{
		$('#claim_err_type').show() ;
		$('#cobClaimType').focus() ;
		$('#claim_err_mesg').text($('#claim_err_mesg').text() + "Please select a claim type. ") ;
		allValid = false ;
	}
	else 
		$('#claim_err_type').hide() ;
		
	if ($('#txtClaimDate').blank())
	{
		$('#claim_err_date').show() ;
		$('#txtClaimDate').focus() ;
		$('#claim_err_mesg').text($('#claim_err_mesg').text() + "Please enter a date. ") ;
		allValid = false ;
	}
	else 
		$('#claim_err_date').hide() ;
		
	if ($('#cobEmpId').val() == 0)
	{
		$('#claim_by_err').show() ;
		$('#cobEmpId').focus() ;
		$('#claim_err_mesg').text($('#claim_err_mesg').text() + "Please select an employee. ") ;
		allValid = false ;
	}
	else 
		$('#claim_by_err').hide() ;
		
	if ($('#cobTravelPlan').val() == 0)
	{
		$('#travel_plan_err').show() ;
		$('#cobTravelPlan').focus() ;
		$('#claim_err_mesg').text($('#claim_err_mesg').text() + "Please select an employee. ") ;
		allValid = false ;
	}
	else 
		$('#travel_plan_err').hide() ;

	return allValid ;
}
function showAllClaims() {
	var data;
	if ($("#claim-filter-by").val() != "" || $("#claim-filter-option").val() != "") {
		data = { "type": C_LIST, "filter_conditions": $("#claim-filter-by").val() + " = '" + $("#claim-filter-option").val() + "'" };
	} else {
		data = { "type": C_LIST };
	}
	var url = "request.pzx?c=" + claim_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimResponse,claim_obj) ;
}
function printClaim() {
	var url = "report.pzx?c=" + claim_url + "&d=" + new Date().getTime();
	showReport(url) ;
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

function addItem() {
	if(validateItem()){
		m_item_idx++;
		$('#tblItems').append("<tr id='tritem" + m_item_idx + "'>" + 
			"<td style='width:30px;'>" +m_item_idx+ "</td>" +
			"<td style='width:100px;'><input readonly style='width:90px' id='txtItemDesc"+m_item_idx+ "' value='"+ $('#txtClaimItemDesc').val() +"'></input></td>" +
			"<td style='width:100px;display:none'><input readonly style='width:90px' id='txtItemItemId"+m_item_idx+ "' value='"+ $('#cobExpenseItem').val()+"'></input></td>" +
			"<td style='width:100px;'><input readonly style='width:90px' id='txtItemItem"+m_item_idx+ "' value='"+ $('#cobExpenseItem :selected').text()+"'></input></td>" +
			"<td style='width:100px;'><input readonly style='width:90px' id='txtItemAmount"+m_item_idx+ "' value='"+ $('#txtClaimItemAmount').val() +"'></input></td>" +
			"<td style='width:100px;display:none'><input readonly style='width:90px' id='txtItemCurrID"+m_item_idx+ "' value='"+ $('#cobCurrency').val() +"'></input></td>" +
			"<td style='width:100px;'><input readonly style='width:90px' id='txtItemCurr"+m_item_idx+ "' value='"+ $('#cobCurrency :selected').text() +"'></input></td>" +
			"<td style='width:100px;'><input readonly style='width:90px' id='txtItemDocID"+m_item_idx+ "' value='"+ $('#txtClaimItemDocumentId').val() +"'></input></td>" +
			"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeItem(" + m_item_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
	}
}

function removeItem(idx) {
	$('#tritem'+idx).remove() ;
}

function validateItem(){
	if($('#txtClaimItemDesc').val() == ''){
		$('#claim_item_err_desc').show();
		return false;
	} else{
		$('#claim_item_err_desc').hide();
	}
	if($('#cobExpenseItem').val() == 0){
		$('#claim_item_err_expense_item').show();
		return false;
	} else{
		$('#claim_item_err_expense_item').hide();
	}
	if($('#txtClaimItemAmount').val() == ''){
		$('#claim_item_err_amount').show();
		$('#claim_item_err_amount').text('*');
		return false;
	} else {
		if(isNaN($('#txtClaimItemAmount').val())){
			$('#claim_item_err_amount').show();
			$('#claim_item_err_amount').text('Invalid amount');
			return false;
		} else{
			$('#claim_item_err_amount').hide();
		}
	}
	if($('#cobCurrency').val() == 0){
		$('#claim_item_err_currency').show();
		return false;
	} else{
		$('#claim_item_err_currency').hide();
	}
	if($('#txtClaimItemDocumentId').val() == ''){
		$('#claim_item_err_doc_id').show();
		return false;
	} else{
		$('#claim_item_err_doc_id').hide();
	}
	return true;
}

function showItems(lines) {
	$('#tblItems').empty() ;
	if (lines == undefined || lines == null || lines == "") return ;
	var line = lines.split('|') ;
	var fld = "" ;
	m_item_idx = 0 ;
	for (var i = 0 ; i < line.length ; i++) {
		if (line[i] != "") {
			fld = line[i].split('^') ;
			m_item_idx = fld[0];
			$('#tblItems').append("<tr id='tritem" + m_item_idx + "'>" + 
				"<td style='width:30px;'>" +m_item_idx+ "</td>" +
				"<td style='width:100px;'><input readonly style='width:90px' id='txtItemDesc"+m_item_idx+ "' value='"+ fld[1] +"'></input></td>" +
				"<td style='width:100px;display:none'><input readonly style='width:90px' id='txtItemItemId"+m_item_idx+ "' value='"+ fld[2]+"'></input></td>" +
				"<td style='width:100px;'><input readonly style='width:90px' id='txtItemItem"+m_item_idx+ "' value='"+ fld[3]+"'></input></td>" +
				"<td style='width:100px;'><input readonly style='width:90px' id='txtItemAmount"+m_item_idx+ "' value='"+ fld[4] +"'></input></td>" +
				"<td style='width:100px;display:none'><input readonly style='width:90px' id='txtItemCurrID"+m_item_idx+ "' value='"+ fld[5] +"'></input></td>" +
				"<td style='width:100px;'><input readonly style='width:90px' id='txtItemCurr"+m_item_idx+ "' value='"+ fld[6] +"'></input></td>" +
				"<td style='width:100px;'><input readonly style='width:90px' id='txtItemDocID"+m_item_idx+ "' value='"+ fld[7] +"'></input></td>" +
				"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeItem(" + m_item_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
		}
	}
}

function showDocs(lines) {
	$('#tblDocs').empty() ;
	if (lines == undefined || lines == null || lines == "") return ;
	var line = lines.split('|') ;
	var fld = "" ;
	m_docs_idx = 0 ;
	for (var i = 0 ; i < line.length ; i++) {
		if (line[i] != "") {
			fld = line[i].split('^') ;
			m_docs_idx = fld[0];
			$('#tblDocs').append("<tr id='trdocs" + m_docs_idx + "'>" + 
				"<td style='width:30px;'>" +m_docs_idx+ "</td>" +
				"<td style='width:100px;'><input readonly style='width:90px' id='txtDocsRef"+m_docs_idx+ "' value='"+ fld[1] +"'></input></td>" +
				"<td style='width:250px;'><input readonly style='width:240px' id='txtDocsDesc"+m_docs_idx+ "' value='"+ fld[2] +"'></input></td>" +
				"<td style='width:250px;'><input readonly style='width:240px' id='txtDocsPath"+m_docs_idx+ "' value='"+ fld[3] +"'></input></td>" +
				"<td style='width:10px;display:none'><input readonly style='width:10px' id='txtDocsClaimID"+m_docs_idx+ "' value='"+ fld[4] +"'></input></td>" +
				"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeDocs(" + m_docs_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
		}
	}
}



function addDocs() {
	uploadClaimDocument();
}

function removeDocs(idx) {
	var data = { "type": 'DELETE', "n": $('#txtDocsPath' + idx).val(),
		"id": $('#txtDocsClaimID' + idx).val(), "idx": $('#trdocs' + idx).find('td').eq(0).html()};
	var url = "request.pzx?c=" + claim_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimResponse,claim_obj) ;
}

function uploadClaimDocument() {
	var fn = $('#fileClaimDoc').val() ;
	if (validDocExt(fn)) {
		var url = "upload.pzx?t=c&n=fileClaimDoc&d=" + new Date().getTime() + "&i=" + (m_docs_idx + 1) + "&type=add" ;
		$('#claim_doc_upload').show() ;
		ajaxUpload(upload_form,url,onClaimDocUploadEnd);
	}
}
function validDocExt(file) {
	var extArray = new Array(".png", ".jpg", ".jpeg", ".pdf"); 
	var validext = false;
	if (!file) 
		return;
   
	while (file.indexOf("\\") != -1)
		file = file.slice(file.indexOf("\\") + 1);
            
	var ext = file.slice(file.indexOf(".")).toLowerCase();
	for (var n = 0; n < extArray.length; n++) 
	{
		if (extArray[n] == ext) { validext = true; break; }
	}
	if (validext) {}
	else {
		showDialog("System Message","You can only upload files in .png, .jpg, .jpeg, .pdf format") ;
		$('#fileClaimDoc').val("") ;
		return false ;
	}
	if($('#txtClaimDocRef').val() == ''){
		$('#claim_doc_err_ref').show();
		return false;
	} else {
		$('#claim_doc_err_ref').hide();
	}
	if($('#txtClaimDocDesc').val() == ''){
		$('#claim_doc_err_desc').show();
		return false;
	} else {
		$('#claim_doc_err_desc').hide();
	}
	return true;
}
function onClaimDocUploadEnd(resp) {
	$('#claim_doc_upload').hide() ;
	var s = resp.split("|") ;
	if (s[0]==C_OK) {
		if(s[1] == 'add'){
			m_docs_idx++;
			$('#tblDocs').append("<tr id='trdocs" + m_docs_idx + "'>" + 
				"<td style='width:30px;'>" +m_docs_idx+ "</td>" +
				"<td style='width:100px;'><input readonly style='width:90px' id='txtDocsRef"+m_docs_idx+ "' value='"+ $('#txtClaimDocRef').val() +"'></input></td>" +
				"<td style='width:250px;'><input readonly style='width:240px' id='txtDocsDesc"+m_docs_idx+ "' value='"+ $('#txtClaimDocDesc').val() +"'></input></td>" +
				"<td style='width:250px;'><input readonly style='width:240px' id='txtDocsPath"+m_docs_idx+ "' value='"+ s[2] +"'></input></td>" +
				"<td style='width:10px;display:none'><input readonly style='width:10px' id='txtDocsClaimID"+m_docs_idx+ "' value='"+ (-1) +"'></input></td>" +
				"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeDocs(" + m_docs_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
		} else if(s[1] == 'del'){
			$('#trdocs'+s[2]).remove() ;
		}
	} else {
		$('#sbg-popup-box').html(s[1]) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
	}
}

function populateEmployee(){
	$dept_id = $('#cobDept').val();	
	var data = { "type": C_GET + "_EMP","id": $dept_id} ;
	var url = "request.pzx?c=" + claim_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimResponse,claim_obj) ;
	
}