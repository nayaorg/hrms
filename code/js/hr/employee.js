var emp_obj = null ;

$(document).ready(function() 
{ 
	$('input').css('marginTop', 0); 
	$('input').css('marginBottom',0);
	$('select').css('marginTop',0) ;
	$('select').css('marginBottom',0) ;	
	
	var dteopt = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};

	$('#txtEmpDob').datepicker(dteopt) ;
	$('#txtEmpJoin').datepicker(dteopt) ;
	$('#txtEmpResign').datepicker(dteopt);
	$('#txtEmpPostal').keypress(function() { numericInput(0,'',0,0) ; }) ;
	$('#btnEmpClear').button().bind('click',clearEmp) ;
	$('#btnEmpAdd').button().bind('click',addEmp) ;
	$('#btnEmpUpdate').button().bind('click',updateEmp) ;
	$('#btnEmpPrint').button().bind('click',printEmp) ;
	$('#txtEmpDob').keypress(function() { dateInput('/') ; }) ;
	$('#txtEmpJoin').keypress(function() { dateInput('/') ; }) ;
	$('#txtEmpResign').keypress(function() { dateInput('/') ; }) ;
	$('#emp-tabs').tabs() ;
	$('#txtEmpName').focus() ;
	$(window).resize(resizeEmpGrid) ;
	resizeEmpGrid() ;
	clearEmp();
}) ;
function resizeEmpGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-emp-entry").outerHeight() - 55;
	$("div#sbg-emp-data").css("height", h +'px') ;		
}
function clearEmp() {
	$('#txtEmpId').val("Auto") ;
	$('#txtCode').val("");
	$('#txtEmpName').val("") ;
	$('#txtEmpNric').val("") ;
	$('#cobEmpIdType').val("1") ;
	$('#txtEmpEmail').val("") ;
	$('#txtEmpDob').val("");
	$('#txtEmpJoin').val("") ;
	$('#txtEmpResign').val("") ;
	$('#txtEmpHouseNo').val("");
	$('#txtEmpStreet').val("");
	$('#txtEmpLevel').val("");
	$('#txtEmpUnitNo').val("");
	$('#txtEmpPostal').val("") ;
	$('#txtEmpTel').val("");
	$('#txtEmpMobile').val("");
	//$('#chkEmpBlock').prop('checked',false) ;
	$('#cobEmpDept').val("") ;
	$('#cobEmpType').val("") ;
	$('#cobEmpGender').val("M") ;
	$('#cobEmpMarital').val("0");
	$('#cobEmpNat').val("") ;
	$('#cobEmpJob').val("") ;
	$('#cobEmpCoy').val("");
	$('#cobEmpRace').val("");
	$('#cobEmpPermit').val("");
	$('#txtEmpRmks').val("");
	$('#emp-tabs').tabs("option", "active", 0) ;
	$('#txtEmpName').focus() ;
	$('#btnEmpUpdate').prop('disabled','disabled') ;
	
	$('#emp_err_dept').hide() ;
	$('#emp_err_nric').hide() ;
	$('#emp_err_name').hide() ;
	$('#emp_err_code').hide() ;
	$('#emp_err_coy').hide() ;
	$('#emp_err_race').hide() ;
	$('#emp_err_dob').hide();
	$('#emp_err_nat').hide() ;
	$('#emp_err_job').hide() ;
	$('#emp_err_type').hide() ;
	$('#emp_err_join').hide() ;
	$('#emp_err_resign').hide() ;
	
	$('#emp_err_mesg').text("") ;
	emp_obj = null;
}
function updateEmp() {
	saveEmp(C_UPDATE) ;
}
function addEmp() {
	saveEmp(C_ADD) ;
}
function saveEmp(type) {
	if (validateEmp()) {
		var block = "0" ;
		//if ($('#chkEmpBlock').is(':checked'))
			//block = "1" ;
		var data = { "type": type, "id": $('#txtEmpId').val(), "name": $('#txtEmpName').val(), "code": $('#txtCode').val(),
			"coy": $('#cobEmpCoy').val(), "dept": $('#cobEmpDept').val(), "emptype": $('#cobEmpType').val(),
			"job": $('#cobEmpJob').val(), "join": $('#txtEmpJoin').val(), "resign": $('#txtEmpResign').val(),
			"nric": $('#txtEmpNric').val(), "gender": $('#cobEmpGender').val(), "marital": $('#cobEmpMarital').val(),
			"race": $('#cobEmpRace').val(), "nat": $('#cobEmpNat').val(), "dob": $('#txtEmpDob').val(),
			"house": $('#txtEmpHouseNo').val(), "street": $('#txtEmpStreet').val(), "level": $('#txtEmpLevel').val(),
			"unitno": $('#txtEmpUnitNo').val(),"postal": $('#txtEmpPostal').val(), "tel": $('#txtEmpTel').val(), 
			"mobile": $('#txtEmpMobile').val(),	"email": $('#txtEmpEmail').val(), "rmks": $('#txtEmpRmks').val(), 
			"permit": $('#cobEmpPermit').val(),	"idtype": $('#cobEmpIdType').val(), "refno": "", "block": block };
		var url = "request.pzx?c=" + emp_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onEmpResponse,emp_obj) ;
	}
}
function printEmp() {
	var url = "report.pzx?c=" + emp_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editEmp(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + emp_url + "&d=" + new Date().getTime();		
	emp_obj = obj ;
	callServer(url,"json",data,showEmp,obj) ;
}

function deleteEmp(id,obj) {
	if (confirm("Confirm you want to delete employee id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + emp_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onEmpResponse,obj) ;
	}
}
function showEmp(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtEmpId').val(resp.data.id) ;
		$('#txtCode').val(resp.data.code);
		$('#txtEmpName').val(resp.data.name) ;
		$('#txtEmpNric').val(resp.data.nric) ;
		$('#cobEmpIdType').val(resp.data.idtype) ;
		$('#txtEmpEmail').val(resp.data.email) ;
		$('#txtEmpDob').val(resp.data.dob) ;
		$('#txtEmpJoin').val(resp.data.join) ;
		$('#txtEmpResign').val(resp.data.resign);
		$('#cobEmpDept').val(resp.data.dept) ;
		$('#cobEmpType').val(resp.data.emptype) ;
		$('#cobEmpCoy').val(resp.data.coy) ;
		$('#cobEmpRace').val(resp.data.race) ;
		$('#cobEmpNat').val(resp.data.nat) ;
		$('#cobEmpGender').val(resp.data.gender);
		$('#cobEmpMarital').val(resp.data.marital);
		$('#cobEmpJob').val(resp.data.job);
		$('#txtEmpHouseNo').val(resp.data.house);
		$('#txtEmpStreet').val(resp.data.street);
		$('#txtEmpLevel').val(resp.data.level);
		$('#txtEmpUnitNo').val(resp.data.unitno) ;
		$('#txtEmpPostal').val(resp.data.postal) ;
		$('#txtEmpTel').val(resp.data.tel);
		$('#txtEmpMobile').val(resp.data.mobile);
		$('#txtEmpRmks').val(resp.data.rmks);
		$('#cobEmpPermit').val(resp.data.permit) ;
		//if (resp.data.block == "1")
			//$('#chkEmpBlock').prop("checked", true); 
		
		//ery gave the comment so the button can be disabled.
		//$('#emp-tabs').tabs('select',0) ;
		
		$('#btnEmpUpdate').prop('disabled','') ;
		$('#txtEmpName').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onEmpResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-emp-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtCode').val() + "</td>" + 
				"<td>" + $('#txtEmpName').val() + "</td>" + 
				"<td>" + $('#cobEmpCoy :selected').text() + "</td>" + 
				"<td>" + $('#cobEmpDept :selected').text() + "</td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='editEmp(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteEmp(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').text($('#txtCode').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtEmpName').val()) ;
			$($(obj).closest('tr')).children('td:eq(3)').text($('#cobEmpCoy :selected').text()) ;
			$($(obj).closest('tr')).children('td:eq(4)').text($('#cobEmpDept :selected').text()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearEmp() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateEmp() {
	$('#emp_err_mesg').text('') ;
	if ($('#txtEmpName').blank()) {
		$('#emp_err_name').show() ;
		$('#txtEmpName').focus() ;
		$('#emp_err_mesg').text("Employee name can not be blank.") ;
		return false ;
	} else {
		$('#emp_err_name').hide() ;
	}
	if ($('#txtCode').blank()) {
		$('#emp_err_code').show() ;
		$('#txtCode').focus() ;
		$('#emp_err_mesg').text("Employee code can not be blank.") ;
		return false ;
	} else {
		$('#emp_err_code').hide() ;
	}
	if ($('#cobEmpCoy').val() == "") {
		$('#emp_err_coy').show() ;
		$('#cobEmpCoy').focus() ;
		$('#emp_err_mesg').text("You must assign a Company to this employee") ;
		return false 
	} else {
		$('#emp_err_coy').hide() ;
	}
	if ($('#txtEmpNric').blank()) {
		$('#emp_err_nric').show() ;
		$('#txtEmpNric').focus() ;
		$('#emp_err_mesg').text("ID no can not be blank.") ;
		return false ;
	} else {
		$('#emp_err_nric').hide() ;
	}
	if ($('#cobEmpDept').val() == "") {
		$('#emp_err_dept').show() ;
		$('#cobEmpDept').focus() ;
		$('#emp_err_mesg').text("You must assign a Department to this employee") ;
		return false 
	} else {
		$('#emp_err_dept').hide() ;
	}
	if ($('#cobEmpRace').val() == "") {
		$('#emp_err_race').show() ;
		$('#cobEmpRace').focus() ;
		$('#emp_err_mesg').text("You must assign a Race to this employee") ;
		return false 
	} else {
		$('#emp_err_race').hide() ;
	}
	if ($('#cobEmpNat').val() == "") {
		$('#emp_err_nat').show() ;
		$('#cobEmpNat').focus() ;
		$('#emp_err_mesg').text("You must assign a Nationality to this employee") ;
		return false 
	} else {
		$('#emp_err_nat').hide() ;
	}
	if ($('#cobEmpJob').val() == "") {
		$('#emp_err_job').show() ;
		$('#cobEmpJob').focus() ;
		$('#emp_err_mesg').text("You must assign a Job Title to this employee") ;
		return false 
	} else {
		$('#emp_err_job').hide() ;
	}
	if ($('#cobEmpType').val() == "") {
		$('#emp_err_type').show() ;
		$('#cobEmpType').focus() ;
		$('#emp_err_mesg').text("You must assign a Employee Type to this employee") ;
		return false 
	} else {
		$('#emp_err_type').hide() ;
	}
	if ($('#txtEmpDob').blank()) {
		$('#emp_err_dob').show() ;
		$('#txtEmpDob').focus() ;
		$('#emp_err_mesg').text("Date of Birth can not be blank.") ;
		return false ;
	} else {
		$('#emp_err_dob').hide() ;
	}
	if ($('#txtEmpDob').validDate())
		$('#emp_err_dob').hide() ;
	else {
		$('#emp_err_dob').show() ;
		$('#txtEmpDob').focus() ;
		$('#emp_err_mesg').text("Invalid date entry for Date of Birth") ;
		return false ;
	}
	if ($('#txtEmpJoin').val() != ""){
		if ($('#txtEmpJoin').validDate())
			$('#emp_err_join').hide() ;
		else {
			$('#emp_err_join').show() ;
			$('#txtEmpJoin').focus() ;
			$('#emp_err_mesg').text("Invalid date entry for Join Date") ;
			return false ;
		}
	}
	
	if ($('#txtEmpResign').val() != "")	{
		if ($('#txtEmpResign').validDate())
			$('#emp_err_resign').hide() ;
		else {
			$('#emp_err_resign').show() ;
			$('#txtEmpResign').focus() ;
			$('#emp_err_mesg').text("Invalid date entry for Resign Date") ;
			return false ;
		}
	}
	return true ;
}