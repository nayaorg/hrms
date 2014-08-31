
$(document).ready(function() 
{ 
	//if ($.browser.webkit) { 
		$('input').css('marginTop', 0); 
		$('input').css('marginBottom',0);
		$('select').css('marginTop',0) ;
		$('select').css('marginBottom',0) ;
	//} 
	
	$('#setting-tabs').tabs() ;
	$('#txtSettingName1').focus() ;
	$('#btnSettingUpdate').button().bind('click',updateSetting) ;
	$('#btnSettingUpload').button().bind('click',uploadSetting) ;
	$('#btnSettingRemove').button().bind('click',removeSetting) ;
		
	if ($('#imgLogo').attr('src') == "") {
		$('#btnSettingRemove').prop('disabled','disabled') ;
	}
}) ;
function updateSetting() {
	saveSetting(C_UPDATE) ;
}
function saveSetting(type) {
	if (validateSetting()) {
		var data = { "type": type, "name1": $('#txtSettingName1').val(), "code": $('#txtSettingCode').val(),
			"addr1": $('#txtSettingAddr1').val(), "addr2": $('#txtSettingAddr2').val(), 
			"addr3": $('#txtSettingAddr3').val(), "addr4": $('#txtSettingAddr4').val(), 
			"addr5": $('#txtSettingAddr5').val(), "refno": $('#txtSettingRefNo').val(),
			"telno": $('#txtSettingTelNo').val(), "faxno": $('#txtSettingFaxNo').val(),
			"failcount": "3" } ;
		var url = "request.pzx?c=" + setting_url + "&d=" + new Date().getTime() ;
		//alert(JSON.stringify(data)) ;
		clearSetting() ;
		callServer(url,"json",data,onSettingResponse,null) ;
	}
}
function clearSetting() {
	$('#setting_err_mesg').text("") ;
}
function onSettingResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_DELETE) {
			$('#imgLogo').prop('src','') ;
			$('#btnSettingRemove').prop('disabled','disabled') ;
		}
		showDialog("System Message",resp.mesg) ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateSetting() {
	$('#setting_err_mesg').text('') ;
	if ($('#txtSettingName1').blank())
	{
		$('#setting_err_name1').show() ;
		$('#txtSettingName1').focus() ;
		$('#setting_err_mesg').text("Organization name can not be blank.") ;
		return false ;
	}
	else 
		$('#setting_err_name1').hide() ;
	
	if ($('#txtSettingCode').blank())
	{
		$('#setting_err_code').show() ;
		$('#txtSettingCode').focus() ;
		$('#setting_err_mesg').text("Organization code can not be blank.") ;
		return false ;
	}
	else
		$('#setting_err_code').hide() ;
		
	return true ;
}
function onUploadEnd(resp) {
	//alert(resp) ;
	//if (uploaded) return ;
	//uploaded = true ;
	$('#setting_upload').hide() ;
	//alert(resp) ;
	$('#btnSettingUpload').prop('disabled','')
	var s = resp.split("|") ;
	if (s[0]==C_OK) {
		$('#imgLogo').prop('src',s[1]) ;
		$('#btnSettingRemove').prop('disabled','') ;
	}
	else
		alert(s[1]) ;
	
}
function uploadSetting() {
	var fn = $('#fileLogo').val() ;
	if (fn == "") {
		showDialog('System Message','You must select a logo image file for upload.') ;
		return ;
	}
	if (validImageExt(fn)) {
		$('#btnSettingUpload').prop('disabled','disabled') ;
		var url = "upload.pzx?t=l&n=fileLogo&d=" + new Date().getTime() ;
		$('#setting_upload').show() ;
		uploaded = false ;
		ajaxUpload(this.form,url,onUploadEnd);
	}
}
function removeSetting() {
	if (confirm("Confirm you want to remove the logo?")) {
		var data = {"type": C_DELETE} ;
		var url = "request.pzx?c=" + setting_url + "&d=" + new Date().getTime() ;
		//alert(JSON.stringify(data)) ;
		callServer(url,"json",data,onSettingResponse,null) ;
	}
}
function validImageExt(file) 
{
	var extArray = new Array(".png", ".jpg", ".jpeg"); 
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
	if (validext) 
		return true ;
	else {
		alert("You can only upload files in .png, .jpg, .jpeg format") ;
		return false ;
	}
}