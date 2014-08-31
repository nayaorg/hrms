var shift_detail_obj = null ;

$('#btnShiftDetailClear').button().bind('click',clearShiftDetail) ;
$('#btnShiftDetailUpdate').button().bind('click',updateShiftDetail) ;
$('#btnShiftDetailAdd').button().bind('click',addShiftDetail) ;
$('#btnShiftDetailPrint').button().bind('click',printShiftDetail) ;
$('#shift-detail-tabs').tabs() ;

$('#rdoShiftDetailType').focus();
$('#shiftDaily').hide();
$('#shiftWeekly').hide();

$(window).resize(resizeShiftDetailGrid) ;
resizeShiftDetailGrid() ;
clearShiftDetail() ;

$("input:radio[name=rdoShiftDetailType]").change(function(){
	val=$("input:radio[name=rdoShiftDetailType]:checked").val();
	
	if(val==0){
		$('#shiftDaily').show();
		$('#shiftWeekly').hide();
		
	}else if (val==1){
		$('#shiftDaily').hide();
		$('#shiftWeekly').show();
		
	}
		
});
function resizeShiftDetailGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-shift-detail-entry").outerHeight() - 55;
	$("div#sbg-shift-detail-data").css("height", h +'px') ;		
}
function clearShiftDetail() {
	$('#txtShiftDetailId').val("Auto") ;
	$('input:radio[name=rdoShiftDetailType]:checked').prop('checked',false);
	$('#rdoShiftDetailType').focus();
	$('#cobShiftGroup').val("") ;
	$('.cobShift').val("") ;
	$('#shiftDaily').hide();
	$('#shiftWeekly').hide();
	$('#shift-detail-tabs').tabs("option", "active", 0) ;
	$('#txtShiftDetailDesc').focus() ;
	$('#btnShiftDetailUpdate').prop('disabled','disabled') ;
	
	$('#shift_detail_err_mesg').text('') ;
	$('#shift_detail_err_desc').hide() ;
	shift_detail_obj = null;
}
function updateShiftDetail() {
	saveShiftDetail(C_UPDATE) ;
}
function addShiftDetail() {
	saveShiftDetail(C_ADD) ;
}
function saveShiftDetail(type) {
	if (validateShiftDetail()) {
		var shifttype=$('input:radio[name=rdoShiftDetailType]:checked').val();
		
		var data = { "type": type, "id": $('#txtShiftDetailId').val(), "shifttype": shifttype,
						"groupid": $('#cobShiftGroup').val(), "shift01": $('#cobShift01').val(), 
						"shift02": $('#cobShift02').val(), "shift03": $('#cobShift03').val(), 
						"shift04": $('#cobShift04').val(), "shift05": $('#cobShift05').val(), 
						"shift06": $('#cobShift06').val(), "shift07": $('#cobShift07').val(), 
						"shift08": $('#cobShift08').val(), "shift09": $('#cobShift09').val(), 
						"shift10": $('#cobShift10').val(), "shift11": $('#cobShift11').val(), 
						"shift12": $('#cobShift12').val(), "shift13": $('#cobShift13').val(), 
						"shift14": $('#cobShift14').val(), "shift15": $('#cobShift15').val(), 
						"shift16": $('#cobShift16').val(), "shift17": $('#cobShift17').val(), 
						"shift18": $('#cobShift18').val(), "shift19": $('#cobShift19').val(), 
						"shift20": $('#cobShift20').val(), "shift21": $('#cobShift21').val(), 
						"shift22": $('#cobShift22').val(), "shift23": $('#cobShift23').val(), 
						"shift24": $('#cobShift24').val(), "shift25": $('#cobShift25').val(), 
						"shift26": $('#cobShift26').val(), "shift27": $('#cobShift27').val(), 
						"shift28": $('#cobShift28').val(), "shift29": $('#cobShift29').val(), 
						"shift30": $('#cobShift30').val(), "shift31": $('#cobShift31').val(),
						"shiftMon": $('#cobShiftMon').val(), "shiftTue": $('#cobShiftTue').val(),
						"shiftWed": $('#cobShiftWed').val(), "shiftThu": $('#cobShiftThu').val(),
						"shiftFri": $('#cobShiftFri').val(), "shiftSat": $('#cobShiftSat').val(),
						"shiftSun": $('#cobShiftSun').val()
						};
		
		var url = "request.pzx?c=" + shift_detail_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onShiftDetailResponse,shift_detail_obj) ;
		
	}
}
function printShiftDetail() {
	var url = "report.pzx?c=" + shift_detail_url + "&d=" + new Date().getTime();
	showReport(url) ;
}
function editShiftDetail(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + shift_detail_url + "&d=" + new Date().getTime() ;		
	shift_detail_obj = obj ;
	callServer(url,"json",data,showShiftDetail,obj) ;
}

function deleteShiftDetail(id,obj) {
	if (confirm("Confirm you want to delete shift detail id : " + id + "?")) {
		//$($(obj).closest("tr")).remove() ;
		var data = {"type": C_DELETE,"id": id} ;
		var url = "request.pzx?c=" + shift_detail_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onShiftDetailResponse,obj) ;
	}
}
function showShiftDetail(obj,resp) {
	if (resp.status == C_OK) {
		$('#txtShiftDetailId').val(resp.data.id) ;
		$('input:radio[name=rdoShiftDetailType][value=' + resp.data.shifttype + ']').prop('checked',true);
		$('#cobShiftGroup').val(resp.data.groupid) ;
	
		if(resp.data.shifttype==0){
			$('#shiftDaily').show();
			$('#shiftWeekly').hide();
			$('#cobShift01').val(resp.data.shift01) ;
			$('#cobShift02').val(resp.data.shift02) ;
			$('#cobShift03').val(resp.data.shift03) ;
			$('#cobShift04').val(resp.data.shift04) ;
			$('#cobShift05').val(resp.data.shift05) ;
			$('#cobShift06').val(resp.data.shift06) ;
			$('#cobShift07').val(resp.data.shift07) ;
			$('#cobShift08').val(resp.data.shift08) ;
			$('#cobShift09').val(resp.data.shift09) ;
			$('#cobShift10').val(resp.data.shift10) ;
			$('#cobShift11').val(resp.data.shift11) ;
			$('#cobShift12').val(resp.data.shift12) ;
			$('#cobShift13').val(resp.data.shift13) ;
			$('#cobShift14').val(resp.data.shift14) ;
			$('#cobShift15').val(resp.data.shift15) ;
			$('#cobShift16').val(resp.data.shift16) ;
			$('#cobShift17').val(resp.data.shift17) ;
			$('#cobShift18').val(resp.data.shift18) ;
			$('#cobShift19').val(resp.data.shift19) ;
			$('#cobShift20').val(resp.data.shift20) ;
			$('#cobShift21').val(resp.data.shift21) ;
			$('#cobShift22').val(resp.data.shift22) ;
			$('#cobShift23').val(resp.data.shift23) ;
			$('#cobShift24').val(resp.data.shift24) ;
			$('#cobShift25').val(resp.data.shift25) ;
			$('#cobShift26').val(resp.data.shift26) ;
			$('#cobShift27').val(resp.data.shift27) ;
			$('#cobShift28').val(resp.data.shift28) ;
			$('#cobShift29').val(resp.data.shift29) ;
			$('#cobShift30').val(resp.data.shift30) ;
			$('#cobShift31').val(resp.data.shift31) ;
		}else if(resp.data.shifttype==1){
			
			$('#shiftDaily').hide();
			$('#shiftWeekly').show();
			$('#cobShiftMon').val(resp.data.shiftMon) ;
			$('#cobShiftTue').val(resp.data.shiftTue) ;
			$('#cobShiftWed').val(resp.data.shiftWed) ;
			$('#cobShiftThu').val(resp.data.shiftThu) ;
			$('#cobShiftFri').val(resp.data.shiftFri) ;
			$('#cobShiftSat').val(resp.data.shiftSat) ;
			$('#cobShiftSun').val(resp.data.shiftSun) ;
		}
		
		
		$('#btnShiftDetailUpdate').prop('disabled','') ;
		$('#txtShiftDetailDesc').focus() ;
		
		
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onShiftDetailResponse(obj,resp) {
	//alert("response : " + JSON.stringify(resp)) ;
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			if($('#rdoShiftDetailType:checked').val()==0)
				var shifttype='Daily';
			else if($('#rdoShiftDetailType:checked').val()==1)
				var shifttype='Weekly';
			
				
			$('#sbg-shift-detail-table tr:first').after("<tr><td>" + resp.data + "</td>" + 
				"<td>" + shifttype + "</td>" + 
				"<td>" + $('#cobShiftGroup :selected').text() + "</td>" + 
				"<td style='text-align:center'><a href='javascript:' onclick='editShiftDetail(" + resp.data + ",this)'><img src='image/edit_16.png' title='Edit'></img></a></td>" +
				"<td style='text-align:center'><a href='javascript:' onclick='deleteShiftDetail(" + resp.data + ",this)'><img src='image/delete_16.png' title='Delete'></img></a></td>" +
				"</tr>"); 
		} else if (resp.type == C_DELETE) {
			$($(obj).closest("tr")).remove() ;
		} else if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtShiftDetailDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtShiftDetailRef').val()) ;
			if($('#rdoShiftDetailType:checked').val()==0)
				var shifttype='Daily';
			else if($('#rdoShiftDetailType:checked').val()==1)
				var shifttype='Weekly';
				
			$($(obj).closest('tr')).children('td:eq(1)').html(shifttype) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#cobShiftGroup :selected').text()) ;
		}
		$('#sbg-popup-box').html(resp.mesg) ;
		$('#sbg-popup-box').show() ;
		$('#sbg-popup-box').fadeOut(10000) ;
		clearShiftDetail() ;
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateShiftDetail() {
	$('#shift_detail_err_mesg').text('') ;
	
	if ($('input:radio[name=rdoShiftDetailType]:checked').blank())
	{
		$('#shift_detail_err_desc').show() ;
		$('#rdoShiftDetailType').focus() ;
		$('#shift_detail_err_mesg').text("shift type must be chosen.") ;
		return false ;
	}
	if ($('#cobShiftGroup').blank())
	{
		$('#shift_detail_err_desc').show() ;
		$('#cobShiftGroup').focus() ;
		$('#shift_detail_err_mesg').text("shift group can not be blank.") ;
		return false ;
	}
	else 
		$('#shift_detail_err_desc').hide() ;
			
	return true ;
}