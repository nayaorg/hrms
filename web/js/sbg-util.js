function showDate(id)
{
    var d = new Date() ;
    var dn = new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
    var mn = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $('#dte').html(dn[d.getDay()] + ', ' + mn[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear());
}
function getScrollbarWidth() {
	var sw = 0 ;
	var div = $('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div></div>'); 
	$('body').append(div); 
	var w1 = $('div', div).innerWidth(); 
	div.css('overflow-y', 'auto'); 
	var w2 = $('div', div).innerWidth(); 
	$(div).remove(); 
	sw = (w1 - w2);
	return sw ;
}
function openSite(site) {
	window.location.replace(site);
}
function showReport(site, w, h) {

    if (w == null || w == '')
        w = '800';

    if (h == null || h == '')
        h = '600';

    var l = (screen.width - w) / 2;
    if (l < 0) l = 0;

    var t = (screen.height - h) / 2;
    if (t < 0) t = 0;

    var winopt = 'menubar=0,toolbar=0,directories=0,status=0,scrollbars=1,location=0,resizable=1,width=' + w + ',height=' + h + ',left=' + l + ',top=' + t;

    var win = window.open(site, 'report', winopt);
    win.focus();
}
function showDialog(title,mesg) {
	$('#sbg-dialog-mesg').html(mesg) ;
	$('#sbg-dialog').dialog('option', 'title', title);
	$('#sbg-dialog').dialog('open') ;
}
function showConfirm(title,mesg) {
	$('#sbg-confirm-mesg').html(mesg) ;
	$('#sbg-confirm').dialog('option','title',title) ;
	$('#sbg-confirm').dialog('open') ;
}
function showProgress(mesg) {
	$('#sbg-progress-mesg').html(mesg) ;
	$('#sbg-progress').show() ;
}
function hideProgress() {
	$('#sbg-progress-mesg').html('') ;
	$('#sbg-progress').hide() ;
}
function callServer(url,type,data,callback,obj) {
	showProgress("") ;
	$.ajax({
		url: url,
		success: function(response) { hideProgress();callback(obj,response) ; },
		error: onError,
		data: data,
		dataType: type,
		cache: false,
		async: true,
		type: "POST",
		timeout: 30000
	});
}
function onError(handler,status,error) {
	hideProgress() ;
	alert(" Error : " + status + "," + error) ;
}
(function($) {
	$.fn.blank=function() {
		return $.trim($(this).val()).length==0;
	}
})(jQuery);

(function($) {
	$.fn.validDate = function() {
		if ($(this).val().length != 10) return false ;
		
		var re_date = /^\s*(\d{1,2})\/(\d{1,2})\/(\d{2,4})\s*$/;
		if (!re_date.exec($(this).val())) {
			return false;
		}
		var n_day = Number(re_date.$1),
			n_month = Number(re_date.$2),
			n_year = Number(re_date.$3);

		if (n_year < 100)
			n_year += 2000;
		if (n_month < 1 || n_month > 12) {
			return false;
		}
		var d_numdays = new Date(n_year, n_month, 0);
		if (n_day > d_numdays.getDate()) {
			return false;
		}
		return true;
	}
})(jQuery) ;

function dateInput(Separator) {
     var Key = event.keyCode;
     var KeyAscii = String.fromCharCode(Key);
     
     if (KeyAscii == Separator) {
         return;
     }
     
     if (KeyAscii < '0' || KeyAscii > '9')
         event.returnValue = false;
     return;
}
function numericInput(Type, DecimalSeparator,allowneg,decplace)
{
   var Obj = event.srcElement;
   var Val = Obj.value;
   var Key = event.keyCode;
   var KeyAscii = String.fromCharCode(Key);
   if (allowneg)
   {
      if(KeyAscii == "-")
      {
        if(Obj.value != "")
        {
           event.returnValue = false;
           return;
        }
        return;
      }
    }
    switch(Type)
    {
       case 0:
         if(KeyAscii < '0' || KeyAscii > '9')
         {
            event.returnValue = false;
            return;
         }
         return;
       case 1:
       case 2: 
         var DotPos = Val.indexOf(DecimalSeparator) ;
         if(KeyAscii == DecimalSeparator)
         {
            if (DotPos >= 0)
            {
               event.returnValue = false;
               return;
             }
             return;
         }
         if(KeyAscii < '0' || KeyAscii > '9')
         {
             event.returnValue = false;
             return;
         }
         else
         {
             //if (DotPos >= 0) 
             //{
                // var dectext = Val.slice(DotPos+1);
                 //if (dectext.length + 1 > decplace)
                 //{
                    // event.returnValue = false ;
                     //return ;
                 //}
             //}
         }
         return;
     }
 }
 function FormatCurrency(amount) {
    var amt = parseFloat(amount);
    if (isNaN(amt)) { amt = 0.00; }
    var minus = '';
    if (amt < 0) { minus = '-'; }
    amt = Math.abs(amt);
    amt = parseInt((amt + .005) * 100);
    amt = amt / 100;
    s = new String(amt);
    if (s.indexOf('.') < 0) { s += '.00'; }
    if (s.indexOf('.') == (s.length - 2)) { s += '0'; }
    s = minus + s;
    return FormatComma(s);
}
function FormatComma(amount) {
	if (isNaN(amount)) { return ''; }
    var delimiter = ","; // replace comma if desired
    var a = amount.split('.', 2) ;
    var d = a[1];
    var amt = parseInt(a[0]);
    if (isNaN(amt)) { return ''; }
    var minus = '';
    if (amt < 0) { minus = '-'; }
    amt = Math.abs(amt);
    var n = new String(amt);
    a = [];
    while (n.length > 3) {
        var nn = n.substr(n.length - 3);
        a.unshift(nn);
        n = n.substr(0, n.length - 3);
    }
    if (n.length > 0) { a.unshift(n); }
    n = a.join(delimiter);
    if (d.length < 1) { amount = n; }
    else { amount = n + '.' + d; }
    amount = minus + amount;
    return amount;
}
function bin2hex (s) {
    var i, f = 0,
        a = [];
     s += '';
    f = s.length;
 
    for (i = 0; i < f; i++) {
        a[i] = s.charCodeAt(i).toString(16).replace(/^([\da-f])$/, "0$1");    }
 
    return a.join('');
}
function strToHex(str) {
  var hex_tab = "0123456789abcdef";
  var output = "";
  var x;
  for(var i = 0; i < str.length; i++)
  {
    x = str.charCodeAt(i);
    output += hex_tab.charAt((x >>> 4) & 0x0F)
           +  hex_tab.charAt( x        & 0x0F);
  }
  return output;
}
function xxlong2str(v, w) { 
    var vl = v.length; 
    var n = (vl - 1) << 2; 
    if (w) { 
       var m = v[vl - 1]; 
       if ((m < n - 3) || (m > n)) return null; 
       n = m; 
    } 
    for (var i = 0; i < vl; i++) { 
        v[i] = String.fromCharCode(v[i] & 0xff, 
                                   v[i] >>> 8 & 0xff, 
                                   v[i] >>> 16 & 0xff, 
                                   v[i] >>> 24 & 0xff); 
    } 
    if (w) { 
        return v.join('').substring(0, n); 
    } 
    else { 
        return v.join(''); 
    } 
} 
   
function xxstr2long(s, w) { 
    var len = s.length; 
    var v = []; 
    for (var i = 0; i < len; i += 4) { 
        v[i >> 2] = s.charCodeAt(i) 
                  | s.charCodeAt(i + 1) << 8 
                  | s.charCodeAt(i + 2) << 16 
                  | s.charCodeAt(i + 3) << 24; 
    } 
    if (w) { 
        v[v.length] = len; 
    } 
    return v; 
} 
   
function xxtea_encrypt(str, key) { 
    if (str == "") { 
        return ""; 
    } 
    var v = xxstr2long(str, true); 
    var k = xxstr2long(key, false); 
    if (k.length < 4) { 
        k.length = 4; 
    } 
    var n = v.length - 1; 
  
    var z = v[n], y = v[0], delta = 0x9E3779B9; 
    var mx, e, p, q = Math.floor(6 + 52 / (n + 1)), sum = 0; 
    while (0 < q--) { 
        sum = sum + delta & 0xffffffff; 
        e = sum >>> 2 & 3; 
        for (p = 0; p < n; p++) { 
            y = v[p + 1]; 
            mx = (z >>> 5 ^ y << 2) + (y >>> 3 ^ z << 4) ^ (sum ^ y) + (k[p & 3 ^ e] ^ z); 
            z = v[p] = v[p] + mx & 0xffffffff; 
        } 
        y = v[0]; 
        mx = (z >>> 5 ^ y << 2) + (y >>> 3 ^ z << 4) ^ (sum ^ y) + (k[p & 3 ^ e] ^ z); 
        z = v[n] = v[n] + mx & 0xffffffff; 
    } 
   
    return xxlong2str(v, false); 
} 
   
function xxtea_decrypt(str, key) { 
    if (str == "") { 
        return ""; 
    } 
    var v = xxstr2long(str, false); 
    var k = xxstr2long(key, false); 
    if (k.length < 4) { 
        k.length = 4; 
    } 
    var n = v.length - 1; 
   
    var z = v[n - 1], y = v[0], delta = 0x9E3779B9; 
    var mx, e, p, q = Math.floor(6 + 52 / (n + 1)), sum = q * delta & 0xffffffff; 
    while (sum != 0) { 
        e = sum >>> 2 & 3; 
        for (p = n; p > 0; p--) { 
            z = v[p - 1]; 
            mx = (z >>> 5 ^ y << 2) + (y >>> 3 ^ z << 4) ^ (sum ^ y) + (k[p & 3 ^ e] ^ z); 
            y = v[p] = v[p] - mx & 0xffffffff; 
        } 
        z = v[n]; 
        mx = (z >>> 5 ^ y << 2) + (y >>> 3 ^ z << 4) ^ (sum ^ y) + (k[p & 3 ^ e] ^ z); 
        y = v[0] = v[0] - mx & 0xffffffff; 
        sum = sum - delta & 0xffffffff; 
    } 
   
     return xxlong2str(v, true); 
} 
function padLeft(number, length) {
    var str = '' + number;
    while (str.length < length) {
        str = '0' + str;
    }
    return str;
}

