var C_ADD 		= "a" ;
var C_CHANGE 	= "c" ;
var C_DELETE 	= "d" ;
var C_EXPORT 	= "e" ;
var C_GET 		= "g" ;
var C_LIST 		= "l" ;
var C_NEW 		= "n" ;
var C_QUERY		= "q" ;
var C_REPORT 	= "r" ;
var C_UPDATE 	= "u" ;
var C_VIEW 		= "v" ;

var	C_OK = "0";
var C_INFO = "6" ;
var C_INVALID = "7";
var C_CONFIRM = "8" ;
var C_ERROR = "9" ;

$('#example1').datepicker({
    format: "dd/mm/yyyy"
}); 

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

$(function() {
    $('.navbar-btn').click(function() {
    }); 
});

function showPage(page,desc,type) {
	showProgress("loading " + desc) ;
	var data = { "type": type} ;
	var url = "index.pzx?c=" + page + "&t=" + type  + "&d=" + new Date().getTime() ;
	$(".content").load(url,data,function() {hideProgress();}) ;
}

function loadContentInMaster() {
	$(".content").load(url,data,function() {hideProgress();}) ;
}

/****
 * Add collapse and remove events to boxes
 */
$("[data-widget='collapse']").click(function() {   
    var box = $(this).parents(".box").first();
    var bf = box.find(".box-body, .box-footer");
    if (!box.hasClass("collapsed-box")) {
        box.addClass("collapsed-box");
        bf.slideUp();
    } else {
        box.removeClass("collapsed-box");
        bf.slideDown();
    }
});

$("[data-widget='remove']").click(function() {       
    var box = $(this).parents(".box").first();
    box.slideUp();
});

$('a').click(function() {
	var box = $(this).parents(".li").first();
	 if (!box.hasClass("active")) 
		 box.addClass("active");
});

$(document).ready(function () {
	$('[data-toggle=offcanvas]').click(function () {
	    $('.row-offcanvas').toggleClass('active');   
	  });
});

/****
 * Change BreadCrumb and header title when click to change page
 * @param arrNaviName 			All of navigation element name
 * @param arrNaviURL 			url corresponding with element name
 * @param navMenuActiveLArr 	menu name corresponding with the element name
 */
function changeMainHeader(arrNaviName, arrNaviURL, navMenuActiveLArr) {
	var mainHeader = document.getElementById('main-header');
	
	var lenArr = arrNaviName.length;
	var breadcrumb = "";
	var classHome  = "fa fa-home";
	for(var i = 0; i < lenArr-1; i++) {
		if(i != 0)
			classHome = "fa";
		
		breadcrumb = breadcrumb + "<li><a onclick=\"loadContentToMaster('"+arrNaviURL[i]+"', '"+ navMenuActiveLArr[i]+"')\" " +
								        "href='#'><i class='"+classHome+"'></i> "+ arrNaviName[i] +"</a></li>";
	}
	
	breadcrumb = breadcrumb + "<li class='active'>"+arrNaviName[lenArr-1]+"</li>";
	
	var contentInside = "<h1>Dashboard<small>My Views</small>" +
    					"</h1>" +
    					"<ol class='breadcrumb'>" + breadcrumb + "</ol>";
	mainHeader.innerHTML = contentInside;
}

/*****
 * Load Content to master view and activate corresponding menu
 * @param url
 * @param menuActive : active menu element when changing page
 * Remain one step: set active menu
 */
function loadContentToMaster(url, menuActive) {
	var data = {} ;
	$(".content").load(url,data,function() {hideProgress();}) ;
}
