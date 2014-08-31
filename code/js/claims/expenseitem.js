
var expense_item_obj = null ;
var m_limitidx = 0 ;

$(document).ready(function() {
	$('#btnExpenseItemAdd').button().bind('click',addExpenseItem) ;
	$('#btnExpenseItemClear').button().bind('click',clearExpenseItem) ;
	$('#btnExpenseItemUpdate').button().bind('click',updateExpenseItem) ;
	$('#btnExpenseItemPrint').button().bind('click',printExpenseItem) ;
	$('#btnExpenseItemUpdate').hide() ;
	
	$('#expense-item-tabs').tabs() ;
	
	$('#txtExpenseItemDesc').focus() ;
	
	clearExpenseItem();
	$(window).resize(resizeExpenseItemGrid) ;
	resizeExpenseItemGrid() ;
}) ;
function resizeExpenseItemGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-expense-item-entry").outerHeight() - 55;
	$("div#sbg-expense-item-data").css("height", h +'px') ;
}
function clearExpenseItem() {
	$('#txtExpenseItemId').val("Auto") ;
	$('#txtExpenseItemRef').val("") ;
	$('#cobExpenseItemGroup').val("") ;
	$('#txtExpenseItemDesc').val("") ;
	$('#tblLimit').empty() ;
	$('#expense-item-tabs').tabs("option", "active", 0) ;
	$('#expense_item_err_mesg').text('') ;
	$('#expense_item_err_name').hide() ;
	$('#expense_item_err_group').hide() ;
	$('#expense_item_err_desc').hide() ;
	
	$('#btnExpenseItemUpdate').hide() ;
	$('#btnExpenseItemAdd').show() ;
	if (expense_item_obj != null && expense_item_obj != undefined)
	{
		$($(expense_item_obj).closest("tr")).removeClass("sbg-row-selected") ;
	}
	
	m_limitidx = 0 ;
	expense_item_obj = null;
}
function updateExpenseItem() {
	saveExpenseItem(C_UPDATE) ;
}
function addExpenseItem() {
	saveExpenseItem(C_ADD) ;
}
function saveExpenseItem(type) {
	if (validateExpenseItem()) {
		var data = { "type": type, "id": $('#txtExpenseItemId').val(), 
			"ref": $('#txtExpenseItemRef').val(), "group": $('#cobExpenseItemGroup').val(), 
			"desc": $('#txtExpenseItemDesc').val(), "limits": getClaimLimit() };
		var url = "request.pzx?c=" + expense_item_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onExpenseItemResponse,expense_item_obj) ;
	}
}

function editExpenseItem(id,obj) {
	var data = {"type": C_GET, "id": id} ;
	var url = "request.pzx?c=" + expense_item_url + "&d=" + new Date().getTime();	
	clearExpenseItem() ;
	expense_item_obj = obj ;
	$($(obj).closest("tr")).addClass("sbg-row-selected");
	callServer(url,"json",data,showExpenseItem,obj) ;
}

function deleteExpenseItem(id,obj) {
	if (confirm("Confirm you want to delete expense item id : " + id + "?")) {
		var data = {"type": C_DELETE, "id": id} ;
		var url = "request.pzx?c=" + expense_item_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onExpenseItemResponse,obj) ;
	}
}
function showExpenseItem(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtExpenseItemId').val(resp.data.id) ;
		$('#txtExpenseItemRef').val(resp.data.ref) ;
		$('#cobExpenseItemGroup').val(resp.data.group) ;
		$('#txtExpenseItemDesc').val(resp.data.desc) ;
		if (resp.data.limits != "")
			setClaimLimit(resp.data.limits) ;
		$('#txtExpenseItemDesc').focus() ;
		$('#btnExpenseItemUpdate').show() ;
		$('#btnExpenseItemAdd').hide() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onExpenseItemResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-expense-item-table tr:first').after("<tr>" + 
				"<td>" + resp.data + "</td>" + 
				"<td>" + $('#txtExpenseItemDesc').val() + "</td>" + 
				"<td>" + $('#cobExpenseItemGroup :selected').text() + "</td>" + 
				"<td>" + $('#txtExpenseItemRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editExpenseItem(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteExpenseItem(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(0)').html($('#txtExpenseItemId').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtExpenseItemDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#cobExpenseItemGroup :selected').text()) ;
			$($(obj).closest('tr')).children('td:eq(3)').text($('#txtExpenseItemRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearExpenseItem() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateExpenseItem() {
	
	$('#expense_item_err_mesg').text('') ;
	if ($('#txtExpenseItemDesc').blank())
	{
		$('#expense_item_err_desc').show() ;
		$('#txtExpenseItemDesc').focus() ;
		$('#expense_item_err_mesg').text($('#expense_item_err_mesg').text() + "Expense Item description can not be blank. ") ;
		return false ;
	}
	else 
		$('#expense_item_err_desc').hide() ;
		
	if ($('#cobExpenseItemGroup').blank())
	{
		$('#expense_item_err_group').show() ;
		$('#cobExpenseItemGroup').focus() ;
		$('#expense_item_err_mesg').text($('#expense_item_err_mesg').text() + "Please select an Expense Item Group. ") ;
		return false ;
	}
	else 
		$('#expense_item_err_group').hide() ;

	return true ;
}
function printExpenseItem() {
	var url = "report.pzx?c=" + expense_item_url + "&d=" + new Date().getTime();
	showReport(url) ;
}

function addClaimLimit() {
	m_limitidx++;
	$('#tblLimit').append("<tr id='tritem" + m_limitidx + "'><td style='width:30px;'>" +m_limitidx+ "</td>" +
		"<td style='width:200px;'><select style='width:200px' id='cobExpenseClaimGroup"+m_limitidx+ "'></select></td>" +
		"<td style='width:120px;'><select style='width:120px' id='cobExpenseClaimType"+m_limitidx+ "'></select></td>" +
		"<td style='width:80px;'><input type='text' maxlength='8' size='8' onkeypress=numericInput(2,'.',0,2) id='txtExpenseLimitAmt"+m_limitidx+"'></input></td>" +
		"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeClaimLimit(" + m_limitidx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
	$('#cobExpenseClaimGroup'+m_limitidx).append($('#cobExpenseClaimGroup').html()) ;
	$('#cobExpenseClaimType'+m_limitidx).append($('#cobExpenseClaimType').html()) ;
}
function removeClaimLimit(idx) {
	$('#tritem'+idx).remove() ;
}
function setClaimLimit(lines) {
	if (lines == undefined || lines == null || lines == "") return ;
	
	var line = lines.split('|') ;
	var fld = "" ;
	m_limitidx = 0 ;
	for (var i = 0 ; i < line.length ; i++) {
		if (line[i] != "") {
			fld = line[i].split(':') ;
			m_limitidx++;
			$('#tblLimit').append("<tr id='tritem" + m_limitidx + "'><td style='width:30px;'>" +m_limitidx+ "</td>" +
				"<td style='width:200px;'><select style='width:200px' id='cobExpenseClaimGroup"+m_limitidx+ "'></select></td>" +
				"<td style='width:120px;'><select style='width:120px' id='cobExpenseClaimType"+m_limitidx+ "'></select></td>" +
				"<td style='width:80px;'><input type='text' maxlength='8' size='8' onkeypress=numericInput(2,'.',0,2) id='txtExpenseLimitAmt"+m_limitidx+"'></input></td>" +
				"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeClaimLimit(" + m_limitidx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
			$('#cobExpenseClaimGroup'+m_limitidx).append($('#cobExpenseClaimGroup').html()) ;
			$('#cobExpenseClaimGroup'+m_limitidx).val(fld[0]) ;
			$('#cobExpenseClaimType'+m_limitidx).append($('#cobExpenseClaimType').html()) ;
			$('#cobExpenseClaimType'+m_limitidx).val(fld[1]) ;
			$('#txtExpenseLimitAmt'+m_limitidx).val(fld[2]) ;
		}
	}
}
function getClaimLimit() { 
	var lines = "" ;
	var sep = "" ;
	var id = "" ;
	var grp = "" ;
	var typ = "" ;
	var amt = "" ;
	$('#tblLimit tr').each(function() { 
		id = $(this).find("td").eq(0).html() ;
		grp = $('#cobExpenseClaimGroup'+id).val() ;
		typ = $('#cobExpenseClaimType'+id).val() ;
		amt = $('#txtExpenseLimitAmt'+id).val() ;
		if (grp == undefined || grp == null || grp == "") {
		} else {
			lines = lines + sep + grp  + ":" + typ + ":" + amt;
			sep = "|" ;
		}
	}); 
	return lines ;
}