var C_ADD = "a" ;
var C_CHANGE = "c" ;
var C_DELETE = "d" ;
var C_EXPORT = "e" ;
var C_GET = "g";
var C_LIST = "l" ;
var C_NEW = "n";
var C_QUERY = "q" ;
var C_REPORT = "r" ;
var C_UPDATE = "u" ;
var C_VIEW = "v";

var	C_OK = "0";
var C_INFO = "6" ;
var C_INVALID = "7";
var C_CONFIRM = "8" ;
var C_ERROR = "9" ;

$(document).ready(function () {            
	showDate('dte');
	setInterval("showDate('dte')", 5000);
	$("input:button").button() ;
	
	var accordopt = {
		active:false ,
		collapsible: true,
		heightStyle: "content"
	} ;
	
	$('#sbg-menu-box').accordion(accordopt) ;
	//$('#sbg-mesg-box').accordion(accordopt) ;
	//$('#sbg-mesg-box').accordion('activate',1) ;
	
	$("#mainmenu div").bind({
		click: function() { 
			$(this).effect('bounce',{direction:'up',distance:20,mode:'effect',times:3},200);
		}
	}) ;
	
	$("#sidemenu div").bind({
		click: function() { 
			$(this).effect('bounce',{direction:'up',distance:20,mode:'effect',times:3},200);
		}
	}) ;
	$("#mainmenu div").hover(function() {
			$("#sbg-tips").text($(this).children('a').attr('title')) ;
			$("#sbg-tips").css("top", ($(this).offset().top-35) + "px") ;
			$("#sbg-tips").css("left", ($(this).offset().left) + "px");
			$("#sbg-tips").show() ;
		},function(){
		$("#sbg-tips").text('').hide() ;
	});
		
	$("#sidemenu div").hover(function() {
			$("#sbg-tips").text($(this).children('a').attr('title')) ;
			$("#sbg-tips").css("top", ($(this).offset().top-35) + "px") ;
			$("#sbg-tips").css("left", ($(this).offset().left) + "px");
			$("#sbg-tips").show() ;
		},function(){
			$("#sbg-tips").text('').hide() ;
	});
		
	$("#sbg-dialog" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: { ok: function () { $(this).dialog("close") ; } }
	});
	$('#sbg-confirm').dialog({
		autoOpen: false,
		modal: true,
		closeOnEscape: false 
	});
}); 

function showPage(page,desc) {
	showProgress("loading " + desc) ;
	var data = { "type": "v"} ;
	var url = "index.pzx?c=" + page + "&d=" + new Date().getTime() ;
	//callServer(url,"html",data,onMainResponse) ;
	$("#sbg-center-panel").load(url,data,function() {hideProgress();}) ;
}
function onMainResponse(data) {
	hideProgress() ;
	$("#sbg-center-panel").html(data) ;
}
function onLogout() {
	var data = { "type": "o"} ;
	var url = "request.pzx?c=" + login_url  +"&d=" + new Date().getTime() ;		
	callServer(url,"json",data,onLogoutResponse) ;
}
function onLogoutResponse(data) {
	openSite("index.pzx");
}