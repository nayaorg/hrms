var employee_shift_obj = null ;


$('#btnEmployeeShiftClear').button().bind('click',clearEmployeeShift) ;
$('#btnEmployeeShiftUpdate').button().bind('click',updateEmployeeShift) ;
$('#btnEmployeeShiftPrint').button().bind('click',printEmployeeShift) ;
$('#employee-shift-tabs').tabs() ;

$('#rdoEmployeeShiftType').focus();

$(window).resize(resizeEmployeeShiftGrid) ;
resizeEmployeeShiftGrid() ;
clearEmployeeShift() ;

function resizeEmployeeShiftGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-employee-shift-entry").outerHeight() - 55;
	$("div#sbg-employee-shift-data").css("height", h +'px') ;		
}
function clearEmployeeShift() {
	$('#txtEmployeeShiftId').val("") ;
	$('input:radio[name=rdoEmployeeShiftType][value=0]').prop('checked',true);
	$('#rdoEmployeeShiftType').focus();
	$('#cobShiftGroup').val("") ;
	$('.cobShift').val("") ;
	$('#cobRateGroup').val("") ;
	$('#cobTimeCard').val("") ;
	$('#employee-shift-tabs').tabs("option", "active", 0) ;
	$('#btnEmployeeShiftUpdate').prop('disabled','disabled') ;
	
	$('#employee_shift_err_mesg').text('') ;
	$('#employee_shift_name').text('') ;
	employee_shift_obj = null;
}
function updateEmployeeShift() {
	saveEmployeeShift(C_UPDATE) ;
}
function saveEmployeeShift(type) {
	if (validateEmployeeShift()) {
		var shifttype=$('input:radio[name=rdoEmployeeShiftType]:checked').val();
		
		var data = { "type": type, "id": $('#txtEmployeeShiftId').val(), "shifttype": shifttype,
						"groupid": $('#cobShiftGroup').val(), "rateid": $('#cobRateGroup').val(), "timecardid":$('#cobTimeCard').val()};
		
		var url = "request.pzx?c=" + employee_shift_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onEmployeeShiftResponse,employee_shift_obj) ;
		
	}
}
function printEmployeeShift() {
	var url = "report.pzx?c=" + employee_shift_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editEmployeeShift(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + employee_shift_url + "&d=" + new Date().getTime() ;		
	employee_shift_obj = obj ;
	callServer(url,"json",data,showEmployeeShift,obj) ;
}
function showEmployeeShift(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtEmployeeShiftId').val(resp.data.id) ;
		$('input:radio[name=rdoEmployeeShiftType][value=' + resp.data.shifttype + ']').prop('checked',true);
		$('#cobShiftGroup').val(resp.data.groupid) ;
		$('#cobRateGroup').val(resp.data.rateid) ;
		$('#cobTimeCard').val(resp.data.timecardid) ;
		$('#employee_shift_name').text(resp.data.emp_name) ;
	
		$('#btnEmployeeShiftUpdate').prop('disabled','') ;
		
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onEmployeeShiftResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtEmployeeShiftDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtEmployeeShiftRef').val()) ;
			if($('#rdoEmployeeShiftType:checked').val()=='D')
				var shifttype='Daily';
			else if($('#rdoEmployeeShiftType:checked').val()=='W')
				var shifttype='Weekly';
				
			$($(obj).closest('tr')).children('td:eq(1)').text($('#employee_shift_name').text()) ;
			$($(obj).closest('tr')).children('td:eq(2)').html(shifttype) ;
			$($(obj).closest('tr')).children('td:eq(3)').text($('#cobShiftGroup :selected').text()) ;
			$($(obj).closest('tr')).children('td:eq(4)').text($('#cobRateGroup :selected').text()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearEmployeeShift() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateEmployeeShift() {
	$('#employee_shift_err_mesg').text('') ;
	
	if ($('input:radio[name=rdoEmployeeShiftType]:checked').blank())
	{
		$('#employee_shift_err_desc').show() ;
		$('#rdoEmployeeShiftType').focus() ;
		$('#employee_shift_err_mesg').text("shift type must be chosen.") ;
		return false ;
	}
	if ($('#cobShiftGroup').val() == 0 || $('#cobShiftGroup').val() == '')
	{
		$('#employee_shift_err_desc').show() ;
		$('#cobShiftGroup').focus() ;
		$('#employee_shift_err_mesg').text("shift group can not be blank.") ;
		return false ;
	}
	else 
		$('#employee_shift_err_desc').hide() ;
		
	if ($('#cobRateGroup').val() == 0 || $('#cobRateGroup').val() == '')
	{
		$('#employee_shift_err_desc').show() ;
		$('#cobRateGroup').focus() ;
		$('#employee_shift_err_mesg').text("Rate group can not be blank.") ;
		return false ;
	}
	else 
		$('#employee_shift_err_desc').hide() ;
		
	if ($('#cobTimeCard').val() == 0 || $('#cobTimeCard').val() == '')
	{
		$('#employee_shift_err_desc').show() ;
		$('#cobTimeCard').focus() ;
		$('#employee_shift_err_mesg').text("Default time card can not be blank.") ;
		return false ;
	}
	else 
		$('#employee_shift_err_desc').hide() ;
			
	return true ;
}