var leavegroup_obj = null ;

$(document).ready(function() 
{ 
	$('input').css('marginTop', 0); 
	$('input').css('marginBottom',0);
	$('select').css('marginTop',0) ;
	$('select').css('marginBottom',0) ;
	
	$('#btnLeaveGroupClear').button().bind('click',clearLeaveGroup) ;
	$('#btnLeaveGroupUpdate').button().bind('click',updateLeaveGroup) ;
	$('#btnLeaveGroupAdd').button().bind('click',addLeaveGroup) ;
	$('#btnLeaveGroupPrint').button().bind('click',printLeaveGroup) ;
	for (var i = 1;i< 11;i++) {
		$('#txtLeaveGroupSickLen' + i).keypress(function() { numericInput(2,'.',0,2) ; }) ;
		$('#txtLeaveGroupSickDay' + i).keypress(function() { numericInput(0,'',0,0) ; }) ;
		$('#txtLeaveGroupAnnualLen' + i).keypress(function() { numericInput(2,'.',0,2) ; }) ;
		$('#txtLeaveGroupAnnualDay' + i).keypress(function() { numericInput(0,'',0,0) ; }) ;
	}
	$('#leavegroup-tabs').tabs() ;
	$('#txtLeaveGroupDesc').focus() ;
	clearLeaveGroup();
	$(window).resize(resizeLeaveGroupGrid) ;
	resizeLeaveGroupGrid() ;
}) ;
function resizeLeaveGroupGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-leavegroup-entry").outerHeight() - 55;
	$("div#sbg-leavegroup-data").css("height", h +'px') ;		
}
function clearLeaveGroup() {
	$('#txtLeaveGroupId').val("Auto") ;
	$('#txtLeaveGroupDesc').val("") ;
	$('#txtLeaveGroupRef').val("") ;
	$('#cobLeaveGroupSick').val("") ;
	$('#cobLeaveGroupAnnual').val("") ;
	for (var i = 1;i<7;i++) {
		$('cobLeaveGroupOther' + i).val("") ;
	}
	for (var i =1 ;i< 11;i++) {
		$('#txtLeaveGroupSickLen' + i).val("") ;
		$('#txtLeaveGroupSickDay' + i).val("") ;
		$('#txtLeaveGroupAnnualLen' + i).val("") ;
		$('#txtLeaveGroupAnnualDay' + i).val("") ;
	}
	$('#leaveGroup-tabs').tabs("option", "active", 0) ;
	$('#txtLeaveGroupDesc').focus() ;
	$('#btnLeaveGroupUpdate').prop('disabled', 'disabled');
	$('#leavegroup_err_mesg').text('') ;
	$('#leavegroup_err_desc').hide() ;
	leavegroup_obj = null;
}
function updateLeaveGroup() {
	saveLeaveGroup(C_UPDATE) ;
}
function addLeaveGroup() {
	saveLeaveGroup(C_ADD) ;
}
function saveLeaveGroup(type) {
	if (validateLeaveGroup()) {
		var data = { "type": type, "id": $('#txtLeaveGroupId').val(), "desc": $('#txtLeaveGroupDesc').val(),
			"ref": $('#txtLeaveGroupRef').val(), "grptype": "0", "options": getLeaveGroupOpts()};
		var url = "request.pzx?c=" + leavegroup_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onLeaveGroupResponse,leavegroup_obj) ;
	}
}
function printLeaveGroup() {
	var url = "report.pzx?c=" + leavegroup_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editLeaveGroup(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + leavegroup_url + "&d=" + new Date().getTime();		
	leavegroup_obj = obj ;
	callServer(url,"json",data,showLeaveGroup,obj) ;
}

function deleteLeaveGroup(id,obj) {
	if (confirm("Confirm you want to delete leave group id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + leavegroup_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onLeaveGroupResponse,obj) ;
	}
}
function showLeaveGroup(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		$('#txtLeaveGroupId').val(resp.data.id) ;
		$('#txtLeaveGroupDesc').val(resp.data.desc) ;
		$('#txtLeaveGroupRef').val(resp.data.ref) ;
		if (resp.data.options != "")
			setLeaveGroupOpts(resp.data.options) ;
		$('#txtLeaveGroupDesc').focus() ;
		$('#btnLeaveGroupUpdate').prop('disabled','');
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onLeaveGroupResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-leavegroup-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtLeaveGroupDesc').val() + "</td>" + 
				"<td>" + $('#txtLeaveGroupRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editLeaveGroup(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteLeaveGroup(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtLeaveGroupDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtLeaveGroupRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearLeaveGroup() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateLeaveGroup() {
	$('#leavegroup_err_mesg').text('') ;
	if ($('#txtLeaveGroupDesc').blank())
	{
		$('#leavegroup_err_desc').show() ;
		$('#txtLeaveGroupDesc').focus() ;
		$('#leavegroup_err_mesg').text("Leave Group description can not be blank.") ;
		return false ;
	}
	else 
		$('#leavegroup_err_desc').hide() ;

	return true ;
}
function getLeaveGroupOpts() { 
	var data = "" ;
	var sep = "" ;
	var fldsep = "" ;
	data = $('#cobLeaveGroupAnnual').val() + ":" ;
	for (var i = 1 ;i < 11;i++)
	{
		if ($('#txtLeaveGroupAnnualLen' + i).val() != "" && $('#txtLeaveGroupAnnualDay' +i).val() != "") {
			data = data + fldsep + $('#txtLeaveGroupAnnualLen' + i).val() + ">" + $('#txtLeaveGroupAnnualDay'+ i).val() ;
			fldsep = ",";
		}		
	}
	sep = "|";
	data = data + sep + $('#cobLeaveGroupSick').val() + ":";
	fldsep = "";
	for (var i = 1 ;i < 11;i++)
	{
		if ($('#txtLeaveGroupSickLen' + i).val() != "" && $('#txtLeaveGroupSickDay' +i).val() != "") {
			data = data + fldsep + $('#txtLeaveGroupSickLen' + i).val() + ">" + $('#txtLeaveGroupSickDay' + i).val() ;
			fldsep = ",";
		}		
	}
	for (var i = 1;i < 7;i++) {
		if ($('#cobLeaveGroupOther' + i).val() != "") {
			data = data + sep + $('#cobLeaveGroupOther' + i).val() + ":" ;
			//sep = "|";
		}
	}
	return data ;
}
function setLeaveGroupOpts(datas) {
	if (datas == undefined || datas == null || datas == "") return ;
	
	var data = datas.split('|') ;
	var fld = "" ;
	var idx = 1 ;
	var fldopt = "" ;
	var fldvalue = "" ;
	var fldidx = 1 ;
	for (var i = 0 ; i < data.length ; i++) {
		if (data[i] != "") {
			fld = data[i].split(':') ;
			if (i == 0) {
				$('#cobLeaveGroupAnnual').val(fld[0]) ;
				if (fld[1] != "") {
					fldopt = fld[1].split(',') ;
					fldidx = 1 ;
					for (var j = 0 ; j < fldopt.length ; j++) {
						if (fldopt[j] != "") {
							fldvalue = fldopt[j].split('>') ;
							$('#txtLeaveGroupAnnualLen' + fldidx).val(fldvalue[0]) ;
							$('#txtLeaveGroupAnnualDay' + fldidx).val(fldvalue[1]) ;
							fldidx++ ;
						}
					}
				}
			} else if (i == 1) {
				$('#cobLeaveGroupSick').val(fld[0]);
				if (fld[1] != "") {
					fldopt = fld[1].split(',');
					fldidx = 1 ;
					for (var j = 0 ; j < fldopt.length ; j++) {
						if (fldopt[j] != "") {
							fldvalue = fldopt[j].split('>') ;
							$('#txtLeaveGroupSickLen' + fldidx).val(fldvalue[0]) ;
							$('#txtLeaveGroupSickDay' + fldidx).val(fldvalue[1]) ;
							fldidx++ ;
						}
					}
				}
			}
			else {
				$('#cobLeaveGroupOther' + idx).val(fld[0]) ;
				//$('#txtEmpPayIncome' + idx).val(fld[1]) ;
				idx++ ;
			}
		}
	}
}