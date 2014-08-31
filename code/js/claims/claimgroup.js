
var claim_group_obj = null ;
var claim_group_head_obj = null ;
var claim_group_employee_obj = null ;
var m_limit_head_idx = 0 ;
var m_limit_emp_idx = 0 ;
var claim_group_head_id_array = [];

$(document).ready(function() {
	$('#btnClaimGroupClear').button().bind('click',clearClaimGroup) ;
	$('#btnClaimGroupUpdate').button().bind('click',updateClaimGroup) ;
	$('#btnClaimGroupAdd').button().bind('click',addClaimGroup) ;
	
	$('#btnClaimGroupUpdate').hide() ;
	
	$('#claim-group-tabs').tabs() ;
	
	$('#cobDeptHead').change(populateHead);
	$('#cobDept').change(populateEmployee) ;
	
	showAllClaimGroups();
	
	$(window).resize(resizeClaimGroupGrid) ;
	resizeClaimGroupGrid() ;
}) ;
function resizeClaimGroupGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-claim-group-entry").outerHeight() - 55;
	$("div#sbg-claim-group-data").css("height", h +'px') ;
}
function resizeClaimGroupEmpGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-claim-group-entry").outerHeight() - 55;
	$("div#sbg-claim-group-data").css("height", h +'px') ;
}
function clearClaimGroup() {
	$('#txtClaimGroupId').val("Auto") ;
	$('#txtClaimGroupDesc').val("") ;
	
	$('#claim_group_err_mesg').text('') ;
	$('#claim_group_err_id').hide() ;
	$('#claim_group_err_desc').hide() ;
	
	$('#btnClaimGroupUpdate').hide() ;
	$('#btnClaimGroupAdd').show() ;
	
	clearClaimGroupHead();
	clearClaimGroupEmployee();
	
	claim_group_obj = null;
}
function clearClaimGroupHead() {
	claim_group_head_obj = null;
	claim_group_head_id_array = [];
	$('#tblHeadLimit').empty() ;
	m_limit_head_idx = 0 ;
}
function clearClaimGroupEmployee() {
	claim_group_employee_obj = null;
	$('#tblLimit').empty() ;
	m_limit_emp_idx = 0 ;
}
function updateClaimGroup() {
	saveClaimGroup(C_UPDATE) ;
}
function addClaimGroup() {
	saveClaimGroup(C_ADD) ;
}
function saveClaimGroup(type) {
	if (validateClaimGroup()) {
		var data = { "type": type, "id": $('#txtClaimGroupId').val(), "desc": $('#txtClaimGroupDesc').val(),
		 "headLimit": getHeadLimit(), "empLimit": getEmpLimit()	};
		var url = "request.pzx?c=" + claim_group_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimGroupResponse,claim_group_obj) ;
	}
}

function editClaimGroup(id,obj) {
	var data = { "type": C_GET,"id": id } ;
	var url = "request.pzx?c=" + claim_group_url + "&d=" + new Date().getTime();		
	claim_group_obj = obj ;
	callServer(url,"json",data,showClaimGroup,obj) ;
}

function deleteClaimGroup(id,obj) {
	if (confirm("Confirm you want to delete claim group : id-" + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + claim_group_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onClaimGroupResponse,obj) ;
	}
}
function showClaimGroup(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtClaimGroupId').val(resp.data.id) ;
		$('#txtClaimGroupDesc').val(resp.data.desc) ;
		
		showHeadLimit(resp.data.head);
		
		showEmpLimit(resp.data.emp);
			
		$('#txtClaimGroupDesc').focus() ;
		$('#btnClaimGroupUpdate').show() ;
		$('#btnClaimGroupAdd').hide() ;
		
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function showAllClaimGroups() {
	var data = { "type": C_LIST };
	var url = "request.pzx?c=" + claim_group_url + "&d=" + new Date().getTime() ;
	
	callServer(url,"json",data,onClaimGroupResponse,claim_group_obj) ;
}
function onClaimGroupResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-claim-group-table tr:first').after("<tr id='claim-group-rows'>" +
				"<td>" + resp.data + "</td>" + 
				"<td>" + $('#txtClaimGroupDesc').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editClaimGroup(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteClaimGroup(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
			clearClaimGroup();
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(0)').html($('#txtClaimGroupId').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtClaimGroupDesc').val()) ;
			
			$('#btnClaimGroupAdd').show() ;
			$('#btnClaimGroupClear').show() ;
			$('#btnClaimGroupUpdate').hide() ;
			
			clearClaimGroup();
		} else if (resp.type == C_LIST) {
			$("tr#claim-group-rows").remove() ;
			jQuery.each(resp.data, function() {
				$('#sbg-claim-group-table tr:first').after("<tr id='claim-group-rows'>" + 
					"<td>" + this.id + "</td>" + 
					"<td>" + this.desc + "</td>" + 
					"<td style='text-align:center'><a href='javascript:' onclick='editClaimGroup(" + this.id + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
					"<td style='text-align:center'><a href='javascript:' onclick='deleteClaimGroup(" + this.id + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
					"</tr>"); 
			});
		}else if (resp.type == C_GET + '_HEAD') {
			var lines = resp.data.empList;
			var line = lines.split('|') ;
			var fld = "" ;
			var opt = "";
			
			for (var i = 0 ; i < line.length ; i++) {
				fld = line[i].split(':') ;
				opt += "<option value='"+ fld[0] +"'>"+  fld[1] +"</option>";
			}
			$("#cobEmpHead").html(opt);
		}else if (resp.type == C_GET + '_EMP') {
			var lines = resp.data.empList;
			var line = lines.split('|') ;
			var fld = "" ;
			var opt = "";
			
			for (var i = 0 ; i < line.length ; i++) {
				fld = line[i].split(':') ;
				opt += "<option value='"+ fld[0] +"'>"+  fld[1] +"</option>";
			}
			$("#cobEmp").html(opt);
		}
		
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateClaimGroup() {
	var allValid = true;
	$('#claim_group_err_mesg').text('') ;
		
	if ($('#txtClaimGroupDesc').blank())
	{
		$('#claim_group_err_desc').show() ;
		$('#txtClaimGroupDesc').focus() ;
		$('#claim_group_err_mesg').text($('#claim_group_err_mesg').text() + "Description can not be blank. ") ;
		allValid = false ;
	}
	else 
		$('#claim_group_err_desc').hide() ;

	return allValid ;
}
function validateClaimGroupHead() {
	var allValid = true;
	$('#claim_group_err_mesg').text('') ;
		
	if ($('#txtHeadId').blank())
	{
		$('#claim_group_err_head_id').show() ;
		$('#txtHeadId').focus() ;
		$('#claim_group_err_mesg').text($('#claim_group_err_mesg').text() + "Head ID can not be blank. ") ;
		allValid = false ;
	} else { 
		$('#claim_group_err_head_id').hide() ;
	}

	return allValid ;
}

function getHeadLimit() {
	var lines = "";
	var sep = "" ;
	var head_id = "";
	var id = "" ;
	
	$('#tblHeadLimit tr').each(function() { 
		id = $(this).find("td").eq(0).html() ;
		head_id = $('#txtHeadId'+id).val() ;
		
		lines = lines + sep + head_id;
		sep = "|" ;
	}); 
	return lines;
}

function getEmpLimit() {
	var lines = "";
	var sep = "" ;
	var emp_id = "";
	var id = "" ;
	
	$('#tblLimit tr').each(function() { 
		id = $(this).find("td").eq(0).html() ;
		emp_id = $('#txtEmpId'+id).val() ;
		
		lines = lines + sep + emp_id;
		sep = "|" ;
	}); 
	return lines;
}

function showHeadLimit(lines) {
	$('#tblHeadLimit').empty() ;
	if (lines == undefined || lines == null || lines == "") return ;
	var line = lines.split('|') ;
	var fld = "" ;
	m_limit_head_idx = 0 ;
	for (var i = 0 ; i < line.length ; i++) {
		if (line[i] != "") {
			fld = line[i].split(':') ;
			m_limit_head_idx++;
			$('#tblHeadLimit').append("<tr id='tritem" + m_limit_head_idx + "'><td style='width:30px;'>" +m_limit_head_idx+ "</td>" +
			"<td style='width:50px;display:none'><input style='width:50px' id='txtHeadId"+m_limit_head_idx+ "' value='"+ fld[0] +"'></input></td>" +
			"<td style='width:120px;'><input style='width:200px' id='txtHead"+m_limit_head_idx+ "' value='"+ fld[1] +"'></input></td>" +
			"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeHeadLimit(" + m_limit_head_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
			$('#txtHeadId'+m_limit_head_idx).append($('#txtHeadId').html()) ;
			$('#txtHead'+m_limit_head_idx).append($('#txtHead').html()) ;
		}
	}

}

function showEmpLimit(lines) {
	$('#tblLimit').empty() ;
	if (lines == undefined || lines == null || lines == "") return ;
	var line = lines.split('|') ;
	var fld = "" ;
	m_limit_emp_idx = 0 ;
	for (var i = 0 ; i < line.length ; i++) {
		if (line[i] != "") {
			fld = line[i].split(':') ;
			m_limit_emp_idx++;
			$('#tblLimit').append("<tr id='tritem" + m_limit_emp_idx + "'><td style='width:30px;'>" +m_limit_emp_idx+ "</td>" +
			"<td style='width:50px;display:none'><input style='width:50px' id='txtEmpId"+m_limit_emp_idx+ "' value='"+ fld[0] +"'></input></td>" +
			"<td style='width:120px;'><input style='width:200px' id='txtEmployee"+m_limit_emp_idx+ "' value='"+ fld[1] +"'></input></td>" +
			"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeHeadLimit(" + m_limit_emp_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
			$('#txtEmpId'+m_limit_emp_idx).append($('#txtEmpId').html()) ;
			$('#txtEmployee'+m_limit_emp_idx).append($('#txtEmployee').html()) ;
		}
	}

}

function populateHead(){
	$dept_id = $('#cobDeptHead').val();	
	var data = { "type": C_GET + "_HEAD","id": $dept_id} ;
	var url = "request.pzx?c=" + claim_group_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimGroupResponse,claim_group_obj) ;
	
}

function populateEmployee(){
	$dept_id = $('#cobDept').val();	
	var data = { "type": C_GET + "_EMP","id": $dept_id} ;
	var url = "request.pzx?c=" + claim_group_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onClaimGroupResponse,claim_group_obj) ;
	
}

function addHeadLimit() {
	m_limit_head_idx++;
	$('#tblHeadLimit').append("<tr id='tritem" + m_limit_head_idx + "'><td style='width:30px;'>" +m_limit_head_idx+ "</td>" +
		"<td style='width:50px;display:none'><input style='width:50px' id='txtHeadId"+m_limit_head_idx+ "' value='"+ $('#cobEmpHead').val() +"'></input></td>" +
		"<td style='width:120px;'><input style='width:200px' id='txtHead"+m_limit_head_idx+ "' value='"+ $('#cobEmpHead :selected').text()+"'></input></td>" +
		"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeHeadLimit(" + m_limit_head_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
	$('#txtHeadId'+m_limit_head_idx).append($('#txtHeadId').html()) ; 
}
function addEmpLimit() {
	m_limit_emp_idx++;
	$('#tblLimit').append("<tr id='tritem" + m_limit_emp_idx + "'><td style='width:30px;'>" +m_limit_emp_idx+ "</td>" +
		"<td style='width:50px;display:none'><input style='width:50px' id='txtEmpId"+m_limit_emp_idx+ "' value='"+ $('#cobEmp').val() +"'></input></td>" +
		"<td style='width:120px;'><input style='width:200px' id='txtEmployee"+m_limit_emp_idx+ "' value='"+ $('#cobEmp :selected').text()+"'></input></td>" +
		"<td style='width:40px;text-align:center;'><a href='javascript:' onclick='removeEmpLimit(" + m_limit_emp_idx + ")'><img src='image/remove_16.png' title='Remove'></img></a></td></tr>"); 
	$('#txtEmpId'+m_limit_emp_idx).append($('#txtEmpId').html()) ; 
}

function removeEmpLimit(idx) {
	$('#tritem'+idx).remove() ;
}

