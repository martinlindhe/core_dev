var gotFocus = new Boolean(true);
var items = 0;
var lastmsg = '';
var oc = '#330000';
var sc = '#1A805A';
var unique_num = (new Date).getTime();
var lim = 250;
var start = true;
try {
	xmlGet = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
	try {
		xmlGet = new ActiveXObject("Microsoft.XMLHTTP");
	} catch (e) {
		xmlGet = false;
	}
}
if(!xmlGet && typeof XMLHttpRequest != 'undefined') {
	try {
		xmlGet = new XMLHttpRequest();
	} catch (e) {
		xmlGet = false;
	}
}
window.onfocus = function() {
	textFocus();
	taskStop();
	gotFocus = new Boolean(true);
}
window.onblur = function() {
	gotFocus = new Boolean(false);
}
function htmle(str) {
	str = str.replace(/\&/gi, '&amp;').replace(/\%/gi, '&#37;').replace(/\</gi, '&lt;').replace(/\>/gi, '&gt;').replace(/\"/gi, '&quot;').replace(/\"/gi, '&quot;');
	return str;
}
function addZero(str) {
	str = str.toString();
	return (str.length == '1')?'0' + str:str;
}
function textFocus() {
	document.getElementById('msgTextbox').focus();
}
blink = null;
var gotmsgs = true;
function taskBlink() {
	if(document.title == usr + ' | privatchat')
		document.title = '### ' + usr + ' | privatchat';
	else
		document.title = usr + ' | privatchat';
	blink = window.setTimeout('taskBlink()', 300);
}
function taskStop() {
	items = 0;
	top.document.title = usr + ' | privatchat';
	if(blink != null)
	window.clearTimeout(blink);
}

function unique() {
	return unique_num++;
}
function reloadit() {
	reloadurl = window.setTimeout('reloadit()', 6100);
	if(gotmsgs) {
		getMSG();
		gotmsg = false;
	} else gotmsgs = true;
}
reloadurl = window.setTimeout('reloadit()', 6100);
function trim(str) {
	return str.replace(/^\s*|\s*$/g,"");
}
function DoCallback(url, type, parameters) {
	if (!xmlGet) return false;
	if(!parameters) parameters = '';

	if (type) {
		xmlGet.open("POST", url);
		xmlGet.onreadystatechange = processGet;
	} else {
		xmlGet.open("POST", url);
	}
	parameters += (parameters.length?'&':'') + 'rand=' + unique();
	xmlGet.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlGet.setRequestHeader("Content-length", parameters.length);
	xmlGet.setRequestHeader("Connection", "close");
	xmlGet.send(parameters);
}
function processGet() {
	if(xmlGet.readyState && xmlGet.readyState == 4 && xmlGet.status == 200) {
		outputMSG(xmlGet);
	}
}
function getMSG() {	
	if(start)
		DoCallback('/user/relay/' + id + '/1/', 1);
	else
		DoCallback('/user/relay/' + id, 1);
	start = false;
}
function doMSG() {
	today = new Date();
	if(trim(document.getElementById("msgTextbox").value).length > 0) {
		var msg = document.getElementById("msgTextbox").value.substr(0, 250);
		DoCallback('/user/relay/' + id, 0, 'msg=' + escape(msg.replace(/\+/gi, "%2b")));
		sendMSG(htmle(msg), 'kl '+addZero(today.getHours())+':'+addZero(today.getMinutes()), you, 0);
	}
	document.getElementById("msgTextbox").value = '';
}
function outputMSG(xmlGet) {
	gotmsgs = true;
	if(xmlGet && xmlGet.responseText && xmlGet.responseText.length > 30) {
		var md5 = xmlGet.responseText.substr(0, 32);
		var array = xmlGet.responseText.split(md5);
		//var array = xmlGet.responseText.split('.-.');
		if(xmlGet.responseText && trim(xmlGet.responseText).length > 0 && !gotFocus) {
			items++;
			if(items == '1') taskBlink();
		}
		for (i=1; i < array.length; i++) {
			if (trim(array[i]).length > 0) {
				var history = array[i].substr(0, 1);
				var aliasLength = new Number(array[i].substr(1, 2));
				var alias = array[i].substr(1 + 2, aliasLength);
				var dateLength = array[i].substr(aliasLength + 1 + 2, 2);
				var datetime = array[i].substr(aliasLength + 1 + 4, dateLength);
				var msg = array[i].substr(parseInt(aliasLength) + parseInt(dateLength) + 1 + 4, array[i].length - parseInt(aliasLength) - parseInt(dateLength));
				setMSG(msg, datetime, alias, history);
			}
		}
		if(gotFocus) document.getElementById('msgTextbox').focus();
	} else if(xmlGet && xmlGet.responseText) {
			if(xmlGet.responseText == '.') { logout();
		} else	if(xmlGet.responseText == ',') { turnoff(); alert('Privatchat-fönstret har inaktiverats eftersom användaren är raderad och inga meddelanden finns kvar.');
		} else	if(xmlGet.responseText == ':') { turnoff(); alert('Privatchat-fönstret har inaktiverats eftersom någon av er har blockerat den andra. Meddelandena som redan skickats har nu visats.');
		} else	if(xmlGet.responseText == ';1') { turnoff(); alert('Privatchat-fönstret har inaktiverats eftersom du har valt att bara chatta med dina vänner.');
		} else	if(xmlGet.responseText == ';2') { turnoff(); alert('Privatchat-fönstret har inaktiverats eftersom personen har valt att bara chatta med sina vänner.'); }
	}
}
function sendMSG(msg, datetime, alias, history) {
	setMSG(msg, datetime, alias, history);
	lastmsg = '';
	textFocus();
}
function setMSG(msg, datetime, alias, history) {
	if(msg != lastmsg) {
		window.frames["msgs"].document.getElementById("msgDiv").innerHTML += formatMSG(msg, datetime, alias, history);
		window.frames["msgs"].scrollTo(0, window.frames["msgs"].document.body.scrollHeight);
		lastmsg = msg;
	}
}
function formatMSG(msg, datetime, alias, history) {
	if(history == '1')
		var buffer = '<div style="margin: 5px; color: #999;"><b>' + unescape(alias) + '</b> - ' + unescape(datetime) + '<br>' + unescape(msg) + '</div>';
	else if(unescape(alias) == usr)
		var buffer = '<div style="margin: 5px; color: '+ sc + ';"><b>' + unescape(alias) + '</b> - ' + unescape(datetime) + '<br>' + unescape(msg) + '</div>';
	else
		var buffer = '<div style="margin: 5px; color: '+ oc + ';"><b>' + unescape(alias) + '</b> - ' + unescape(datetime) + '<br>' + unescape(msg) + '</div>';
	return buffer;
}
function logout() {
	window.opener = window.self;
	self.close();
}
function turnoff() {
	window.clearTimeout(reloadurl);
}
function ActivateByKey(e) {
	if(!e) var e=window.event;
	if (e['keyCode'] == 27) window.close();
	if(e.ctrlKey && e['keyCode'] == 13) {
		doMSG();
		return false;
	}
	if(e['keyCode'] == 13) {
		doMSG();
		return false;
	}		
}
function fixlimit(obj, id) {
	if(obj.value.length > lim) {
		obj.value = obj.value.substr(0, lim);
	}
	document.getElementById(id).innerHTML = lim - parseInt(obj.value.length);
}