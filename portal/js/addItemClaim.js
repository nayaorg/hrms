var m_item_idx = 0 ;


function saveAllItem() {
	var data = { 
			"type"		: C_ADD_ITEM, 
			"id"		: $('#txtClaimId').val(),
			"items_data": getItemList()
			};
	
	var url = "index.pzx?c=" + claim_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimResponse,null) ;
}


function onClaimResponse() {
	var data = {'type':C_PORTAL_CLAIM} ;
	var url = "index.pzx?c=" + home_url + "&t=" + C_PORTAL_CLAIM  + "&d=" + new Date().getTime() ;
	$(".content").load(url,data,function() {hideProgress();}) ;
}

function addItem() {
	if(validateItem()){
		m_item_idx++;
		$('#tblItems').append("<tr id='tritem" + m_item_idx + "'>" + 
			"<td>" +m_item_idx+ "</td>" +
			"<td ><input readonly id='txtItemDesc"+m_item_idx+ "' value='"+ $('#txtClaimItemDesc').val() +"'></input></td>" +
			"<td style='display:none'><input readonly style='width:90px' id='txtItemItemId"+m_item_idx+ "' value='"+ $('#cobExpenseItem').val()+"'></input></td>" +
			"<td ><input readonly  id='txtItemItem"+m_item_idx+ "' value='"+ $('#cobExpenseItem :selected').text()+"'></input></td>" +
			"<td ><input readonly  id='txtItemAmount"+m_item_idx+ "' value='"+ $('#txtClaimItemAmount').val() +"'></input></td>" +
			"<td style='display:none'><input readonly style='width:90px' id='txtItemCurrID"+m_item_idx+ "' value='"+ $('#cobCurrency').val() +"'></input></td>" +
			"<td ><input readonly  id='txtItemCurr"+m_item_idx+ "' value='"+ $('#cobCurrency :selected').text() +"'></input></td>" +
			"<td ><input readonly  id='txtItemDocID"+m_item_idx+ "' value='"+ $('#txtClaimItemDocumentId').val() +"'></input></td>" +
			"<td style='text-align:center;'><a href='javascript:' onclick='removeItem(" + m_item_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
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