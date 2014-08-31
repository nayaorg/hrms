var project_obj = null ;


$('#btnProjectClear').button().bind('click',clearProject) ;
$('#btnProjectUpdate').button().bind('click',updateProject) ;
$('#btnProjectAdd').button().bind('click',addProject) ;
$('#btnProjectPrint').button().bind('click',printProject) ;
$('#project-tabs').tabs() ;
$('#txtProjectDesc').focus() ;
$(window).resize(resizeProjectGrid) ;
resizeProjectGrid() ;
clearProject() ;

function resizeProjectGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-project-entry").outerHeight() - 55;
	$("div#sbg-project-data").css("height", h +'px') ;		
}
function clearProject() {
	$('#txtProjectId').val("Auto") ;
	$('#txtProjectDesc').val("") ;
	$('#txtProjectRef').val("") ;
	$('#project-tabs').tabs("option", "active", 0) ;
	$('#txtProjectDesc').focus() ;
	$('#btnProjectUpdate').prop('disabled','disabled') ;
	
	$('#project_err_mesg').text('') ;
	$('#project_err_desc').hide() ;
	project_obj = null;
}
function updateProject() {
	saveProject(C_UPDATE) ;
}
function addProject() {
	saveProject(C_ADD) ;
}
function saveProject(type) {
	if (validateProject()) {
		var data = { "type": type, "id": $('#txtProjectId').val(), "desc": $('#txtProjectDesc').val(),"refno": $('#txtProjectRef').val() };
		var url = "request.pzx?c=" + project_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onProjectResponse,project_obj) ;
	}
}
function printProject() {
	var url = "report.pzx?c=" + project_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editProject(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + project_url + "&d=" + new Date().getTime() ;		
	project_obj = obj ;
	callServer(url,"json",data,showProject,obj) ;
}

function deleteProject(id,obj) {
	if (confirm("Confirm you want to delete department id : " + id + "?")) {
		//$($(obj).closest("tr")).remove() ;
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + project_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onProjectResponse,obj) ;
	}
}
function showProject(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtProjectId').val(resp.data.id) ;
		$('#txtProjectDesc').val(resp.data.desc) ;
		$('#txtProjectRef').val(resp.data.refno) ;
		$('#btnProjectUpdate').prop('disabled','') ;
		$('#txtProjectDesc').focus() ;
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onProjectResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			$('#sbg-project-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + $('#txtProjectDesc').val() + "</td>" + 
				"<td>" + $('#txtProjectRef').val() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editProject(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteProject(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtProjectDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtProjectRef').val()) ;
			$($(obj).closest('tr')).children('td:eq(1)').html($('#txtProjectDesc').val()) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#txtProjectRef').val()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearProject() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateProject() {
	$('#project_err_mesg').text('') ;
	if ($('#txtProjectDesc').blank())
	{
		$('#project_err_desc').show() ;
		$('#txtProjectDesc').focus() ;
		$('#project_err_mesg').text("Project description can not be blank.") ;
		return false ;
	}
	else 
		$('#project_err_desc').hide() ;
			
	return true ;
}