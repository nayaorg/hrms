var user_obj = null ;

$(document).ready(function() 
{ 
	//if ($.browser.webkit) { 
		$('input').css('marginTop', 0); 
		$('input').css('marginBottom',0);
		$('select').css('marginTop',0) ;
		$('select').css('marginBottom',0) ;
	//} 
	var dteopt = {
		dateFormat: "dd/mm/yy",
		appendText: "  dd/mm/yyyy",
		showOn: "button",
		buttonImage: "image/calendar.gif",
		buttonImageOnly: true
	};

	$("#txtUserStart").datepicker(dteopt) ;
	$("#txtUserExpiry").datepicker(dteopt) ;
	$('#btnUserClear').button().bind('click',clearUser) ;
	$('#btnUserUpdate').button().bind('click',updateUser) ;
	$('#btnUserAdd').button().bind('click',addUser) ;
	$('#btnUserPrint').button().bind('click',printUser) ;
	$('#user-tabs').tabs() ;
	$('#txtUserName').focus() ;
	$(window).resize(resizeUserGrid) ;
	resizeUserGrid() ;
	clearUser() ;
}) ;
function resizeUserGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-user-entry").outerHeight() - 55;
	$("div#sbg-user-data").css("height", h +'px') ;		
}
function clearUser() {
	$('#txtUserId').val("Auto") ;
	$('#txtUserName').val("") ;
	$('#txtUserFull').val("") ;
	$('#txtUserEmail').val("") ;
	$('#txtUserStart').val("") ;
	$('#txtUserExpiry').val("") ;
	//$('#chkUserBlock').prop('checked',false) ;
	$('#cobUserGroup').val("") ;
	$('#user-tabs').tabs("option", "active", 0) ;
	$('#txtUserName').focus() ;
	$('#btnUserUpdate').prop('disabled','disabled') ;
	
	$('#user_err_name').hide() ;
	$('#user_err_full').hide() ;
	$('#user_err_group').hide() ;
	$('#user_err_mesg').text('') ;
	user_obj = null;
}
function updateUser() {
	saveUser(C_UPDATE) ;
}
function addUser() {
	saveUser(C_ADD) ;
}
function saveUser(type) {
	if (validateUser()) {
		var block = "0" ;
		//if ($('#chkUserBlock').is(':checked'))
			//block = "1" ;
		var data = { "type": type, "id": $('#txtUserId').val(), "name": $('#txtUserName').val(),
			"email": $('#txtUserEmail').val(), "start": $('#txtUserStart').val(), "expiry": $('#txtUserExpiry').val(),
			"block": block,	"fullname": $('#txtUserFull').val(), "group": $('#cobUserGroup').val() };
		var url = "request.pzx?c=" + user_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onUserResponse,user_obj) ;
	}
}
function printUser() {
	var url = "report.pzx?c=" + user_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editUser(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + user_url + "&d=" + new Date().getTime();		
	user_obj = obj ;
	callServer(url,"json",data,showUser,obj) ;
}

function deleteUser(id,obj) {
	if (confirm("Confirm you want to delete user id : " + id + "?")) {
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + user_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onUserResponse,obj) ;
	}
}
function showUser(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtUserId').val(resp.data.id) ;
		$('#txtUserName').val(resp.data.name) ;
		$('#txtUserFull').val(resp.data.fullname) ;
		$('#txtUserEmail').val(resp.data.email) ;
		$('#txtUserStart').val(resp.data.start) ;
		$('#txtUserExpiry').val(resp.data.expiry) ;
		$('#cobUserGroup').val(resp.data.group) ;
		//if (resp.data.block == "1")
			//$('#chkBlock').prop("checked", true); 
		$('#btnUserUpdate').prop('disabled','') ;
		$('#txtUserName').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onUserResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	//alert(resp) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-user-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtUserName').val() + "</td>" + 
				"<td>" + $('#txtUserFull').val() + "</td>" + 
				"<td>" + $('#cobUserGroup :selected').text() + "</td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='editUser(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteUser(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			$($(obj).closest('tr')).children('td:eq(1)').text($('#txtUserName').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtUserFull').val()) ;
			$($(obj).closest('tr')).children('td:eq(3)').text($('#cobUserGroup :selected').text()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(5000) ;
		clearUser() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateUser() {
	$('#user_err_mesg').text('') ;
	if ($('#txtUserName').blank())
	{
		$('#user_err_name').show() ;
		$('#txtUserName').focus() ;
		$('#user_err_mesg').text("User signin name can not be blank.") ;
		return false ;
	}
	else 
		$('#user_err_name').hide() ;
		
	if ($('#txtUserFull').blank())
	{
		$('#user_err_full').show() ;
		$('#txtUserFull').focus() ;
		$('#user_err_mesg').text("You must enter the name of this user.") ;
		return false ;
	}
	else 
		$('#user_err_full').hide() ;
		
	if ($('#cobUserGroup').val() == "") {
		$('#user_err_group').show() ;
		$('#cobUserGroup').focus() ;
		$('#user_err_mesg').text("You must assign a User group to this user") ;
		return false 
	}
	else 
		$('#user_err_group').hide() ;
		
	return true ;
}