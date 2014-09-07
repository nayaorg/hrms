function $m(theVar){
	return document.getElementById(theVar)
}
function remove(theVar){
	if (theVar==null) return ;
	var theParent = theVar.parentNode;
	theParent.removeChild(theVar);
}
function addEvent(obj, evType, fn){
	if(obj.addEventListener)
	    obj.addEventListener(evType, fn, true)
	if(obj.attachEvent)
	    obj.attachEvent("on"+evType, fn)
}
function removeEvent(obj, type, fn){
	if(obj.detachEvent){
		obj.detachEvent('on'+type, fn);
	}else{
		obj.removeEventListener(type, fn, false);
	}
}
function isWebKit(){
	return RegExp(" AppleWebKit/").test(navigator.userAgent);
}

function ajaxUpload(form,url_action,callback){
	var detectWebKit = isWebKit();
	var responded = false ;
	form = typeof(form)=="string"?$m(form):form;
	var erro="";
	if(form==null || typeof(form)=="undefined"){
		erro += "The form of 1st parameter does not exists.\n";
	}else if(form.nodeName.toLowerCase()!="form"){
		erro += "The form of 1st parameter its not a form.\n";
	}
	if(erro.length>0){
		alert("Error in call file upload:\n" + erro);
		return;
	}
	var iframe = document.createElement("iframe");
	iframe.setAttribute("id","ajax-temp");
	iframe.setAttribute("name","ajax-temp");
	iframe.setAttribute("width","0");
	iframe.setAttribute("height","0");
	iframe.setAttribute("border","0");
	iframe.setAttribute("style","width: 0; height: 0; border: none;");
	form.parentNode.appendChild(iframe);
	window.frames['ajax-temp'].name="ajax-temp";
	
	var doUpload = function(){
		removeEvent($m('ajax-temp'),"load", doUpload);
		if (!responded) {
			var o = document.getElementById('ajax-temp');
			if (o.contentWindow.document.body.innerHTML != null && o.contentWindow.document.body.innerHTML != "undefined")
				callback(o.contentWindow.document.body.innerHTML) ;
		}
		responded = true ;
		//var cross = "javascript: ";
		//cross += "window.parent.$m('setting_err_mesg').innerHTML = document.body.innerHTML; parent.onSettingUpload(document.body.innerHTML); void(0);";
		//cross += "parent.onend(document.body.innerHTML);" ;
		//$m('ajax-temp').src = cross;
		if(detectWebKit){
        	remove($m('ajax-temp'));
        }else{
        	setTimeout(function(){ remove($m('ajax-temp'))}, 250);
        }
    }
	addEvent($m('ajax-temp'),"load", doUpload);
	form.setAttribute("target","ajax-temp");
	form.setAttribute("action",url_action);
	form.setAttribute("method","post");
	form.setAttribute("enctype","multipart/form-data");
	form.setAttribute("encoding","multipart/form-data");
	form.submit();
}