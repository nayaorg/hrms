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