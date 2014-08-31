var shift_update_obj = null ;

$('#btnShiftUpdateClear').button().bind('click',clearShiftUpdate) ;
$('#btnShiftUpdateUpdate').button().bind('click',updateShiftUpdate) ;
$('#btnShiftUpdateAdd').button().bind('click',addShiftUpdate) ;
$('#shift-update-tabs').tabs() ;

$('#shiftID').hide();
$('#shiftDaily').hide();
$('#shiftWeekly').hide();
$('.shiftCollective').hide();
$('.shiftIndividual').hide();

$('#rdoShiftUpdateType').focus();

$(window).resize(resizeShiftUpdateGrid) ;
resizeShiftUpdateGrid() ;
clearShiftUpdate() ;

$('#cobDept').change(populateEmployee) ;

function rdoTypeChange(){
	if ($('#rdoType:checked').val() == 'C'){
		$('#shiftID').show('fast');
		$('.shiftIndividual').hide();
		$('.shiftCollective').show();
		$('#shiftDaily').hide();
		$('#shiftWeekly').hide();
	}else if ($('#rdoType:checked').val() == 'I'){
		$('#shiftID').show('fast');
		$('.shiftCollective').hide();
		$('.shiftIndividual').show();
		
		$('#shiftDaily').show('fast');
		$('#shiftWeekly').hide();
		
	}	
}

$("input:radio[name=rdoType]").change(function (){ rdoTypeChange(); });


$("input:radio[name=rdoType][value=C]").prop('checked',true);
rdoTypeChange();

$("input:radio[name=rdoShiftUpdateType]").change(function(){
	if ($('#rdoShiftUpdateType:checked').val() == 0){
		$('#shiftWeekly').hide();
		$('#shiftDaily').show('fast');
		
	}else if ($('#rdoShiftUpdateType:checked').val() == 1){
		$('#shiftDaily').hide();
		$('#shiftWeekly').show();
	}	
	if($("#cobShiftGroup").val()!="")
		showShiftDetail();	
});

$("#cobShiftGroup").change(function(){
	if($('#rdoShiftUpdateType:checked').val()!="")
		showShiftDetail();	
});

function resizeShiftUpdateGrid() {
	var h = $("#sbg-center-panel").outerHeight() - $("#sbg-shift-update-entry").outerHeight() - 55;
	$("div#sbg-shift-update-data").css("height", h +'px') ;		
}
function clearShiftUpdate() {
	$('#txtShiftUpdateId').val("Auto") ;
	$('input:radio[name=rdoShiftUpdateType]:checked').prop('checked',false);
	$('#rdoShiftUpdateType').focus();
	$('#cobShiftGroup').val("") ;
	$('.cobShift').val("") ;
	$('#cobDept').val(0);
	$('#cobEmpId').val("");
	$('#shift-update-tabs').tabs("option", "active", 0) ;
	$('#txtShiftUpdateDesc').focus() ;
	//$('#btnShiftUpdateUpdate').prop('disabled','disabled') ;
	
	$('#shift_update_err_mesg').text('') ;
	$('#shift_update_err_desc').hide() ;
	shift_update_obj = null;
}
function updateShiftUpdate() {
	saveShiftUpdate(C_UPDATE) ;
}
function addShiftUpdate() {
	saveShiftUpdate(C_ADD) ;
}
function saveShiftUpdate(type) {
	if (validateShiftUpdate()) {
		var updatetype=$('input:radio[name=rdoType]:checked').val();
		var shifttype;
		var emp_id;
		
		if(updatetype == 'C'){
			emp_id = "";
			group_id = $('#cobShiftGroup').val();
			shifttype = $('input:radio[name=rdoShiftUpdateType]:checked').val();
		} else {
			emp_id = $('#cobEmpId').val();
			group_id = "";
			shifttype = "";
		}
		
		var data = { "type": type, "id": $('#txtShiftUpdateId').val(), "shifttype": shifttype,
						"month":$('#cobMonth').val(), "year":$('#cobYear').val(), 
						"updatetype": updatetype, "emp_id":emp_id,
						"groupid": group_id, "shift01": $('#cobShift01').val(), 
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
		
		var url = "request.pzx?c=" + shift_update_url + "&d=" + new Date().getTime() ;
		callServer(url,"json",data,onShiftUpdateResponse,shift_update_obj) ;
		
	}
}

function editShiftUpdate(id,obj) {
	var data = { "type": C_GET,"id": id} ;
	var url = "request.pzx?c=" + shift_update_url + "&d=" + new Date().getTime() ;		
	shift_update_obj = obj ;
	callServer(url,"json",data,showShiftUpdate,obj) ;
}

function showShiftDetail(){
	var groupType=$('#rdoType:checked').val();
	var shiftType=$('#rdoShiftUpdateType:checked').val();
	var groupId=$('#cobShiftGroup').val();
	var empId=$('#cobEmpId').val();
	var month=$('#cobMonth').val();
	var year=$('#cobYear').val();
	
	var data = { "type": C_GET, "groupType":groupType ,"shiftType": shiftType, "groupId": groupId, "empId":empId, "month":month, "year":year } ;
	var url = "request.pzx?c=" + shift_update_url + "&d=" + new Date().getTime() ;		
	callServer(url,"json",data,showShiftUpdate,shift_update_obj) ;
}

function showShiftUpdate(obj,resp) {
	if (resp.status == C_OK) {
		//$('#txtShiftUpdateId').val(resp.data.id) ;
		//$('input:radio[name=rdoShiftUpdateType][value=' + resp.data.shifttype + ']').prop('checked',true);
		//$('#cobShiftGroup').val(resp.data.groupid) ;
	
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
		
		
		$('#btnShiftUpdateUpdate').prop('disabled','') ;
		$('#txtShiftUpdateDesc').focus() ;
		
		
	}
	else {
		showDialog("System Message",resp.mesg) ;
	}
}
function onShiftUpdateResponse(obj,resp) {
	if (resp.status == C_OK) {
		if (resp.type == C_ADD) {
			 
		} else if (resp.type == C_UPDATE) {
			//$($(obj).closest("tr")).$('td:eq(1)').text($('#txtShiftUpdateDesc').val()) ;
			//$($(obj).closest("tr")).$('td:eq(2)').text($('#txtShiftUpdateRef').val()) ;
			if($('#rdoShiftUpdateType:checked').val()==0)
				var shifttype='Daily';
			else if($('#rdoShiftUpdateType:checked').val()==1)
				var shifttype='Weekly';
				
			$($(obj).closest('tr')).children('td:eq(1)').html(shifttype) ;
			$($(obj).closest('tr')).children('td:eq(2)').text($('#cobShiftGroup :selected').text()) ;
			
			$('#sbg-popup-box').html(resp.mesg) ;
			$('#sbg-popup-box').show() ;
			$('#sbg-popup-box').fadeOut(10000) ;
			clearShiftUpdate() ;
		} else if (resp.type == C_GET + '_EMP'){
			var lines = resp.data.empList;
			var line = lines.split('|') ;
			var fld = "" ;
			var opt = "";
			
			for (var i = 0 ; i < line.length ; i++) {
				fld = line[i].split(':') ;
				opt += "<option value='"+ fld[0] +"'>"+  fld[1] +"</option>";
			}
			$("#cobEmpId").html(opt);
		}
	}
	else 
		showDialog("Error",resp.mesg) ;
}
function validateShiftUpdate() {
	$('#shift_update_err_mesg').text('') ;
	
	if ($('#rdoType:checked').val() == 'C') {
		if ($('input:radio[name=rdoShiftUpdateType]:checked').blank())
		{
			$('#shift_update_err_desc').show() ;
			$('#rdoShiftUpdateType').focus() ;
			$('#shift_update_err_mesg').text("shift type must be chosen.") ;
			return false ;
		}
		if ($('#cobShiftGroup').blank())
		{
			$('#shift_update_err_desc').show() ;
			$('#cobShiftGroup').focus() ;
			$('#shift_update_err_mesg').text("shift group can not be blank.") ;
			return false ;
		}
		else 
			$('#shift_update_err_desc').hide() ;
	} else if ($('#rdoType:checked').val() == 'I') {
	
	}
			
	return true ;
}

function populateEmployee(){
	$dept_id = $('#cobDept').val();	
	var data = { "type": C_GET + "_EMP","id": $dept_id} ;
	var url = "request.pzx?c=" + shift_update_url + "&d=" + new Date().getTime() ;
	callServer(url,"json",data,onShiftUpdateResponse,shift_update_obj) ;
	
}