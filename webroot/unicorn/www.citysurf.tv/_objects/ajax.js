//Makes element with name "n" invisible in browser
function hide_element_by_name(n)
{
	var e = document.getElementById(n);
	e.style.display = 'none';
}

//Makes element with name "n" visible in browser
function show_element_by_name(n)
{
	var e = document.getElementById(n);
	e.style.display = '';
}

//Toggles element with name "n" between visible and hidden
function toggle_element_by_name(n)
{
	var e = document.getElementById(n);
	e.style.display = (e.style.display?'':'none');
}

var interval, friendsC = 0, url = '/member/retrieveajax/', xmlObj = false, isDone = true;
try {
	xmlObj = new ActiveXObject("Msxml2.XMLHTTP");
} catch (E) {
	try {
		xmlObj = new ActiveXObject("Microsoft.XMLHTTP");
	} catch (E) {
		xmlObj = false;
	}
}
if(!xmlObj && typeof(XMLHttpRequest) != 'undefined') {
	try {
		xmlObj = new XMLHttpRequest();
	} catch (E) {
		xmlObj = false;
	}
}

function timeoutData() {
	retrieveData();
	executeTimeout();
}
function executeTimeout() {
	interval = window.setTimeout('timeoutData()', 9000);
}

function retrieveData() {
	if(xmlObj) {
		if(isDone) {
			callbackData(url);
			isDone = false;
		} else isDone = true;
	} else {
		window.clearInterval(interval);
	}
}
function callbackData(url) {
	if(xmlObj) {
		xmlObj.open("GET", url);
		xmlObj.onreadystatechange = processData;
		xmlObj.send(null);
	} else return false;
}
function processData() {
	if(xmlObj.readyState && xmlObj.readyState == 4 && xmlObj.status == 200) {
		isDone = true;
		parseData();
	}
}
function parseData() {
	if(xmlObj) {
		executeData(trim(xmlObj.responseText));
	}
}
function logout() {
	document.logout.submit();
}
function executeData(text) {
	if(text) {
		if (text == 'NAK') { logout(); return; }
		text = text.split('#');
		str = '';
		for (var i = 0; i < text.length; i++) {
			text[i] = text[i].split(':');
			if(text[i][0] == 'f') { executeFriendsOnline(text[i][1]); continue; }
			if(text[i][1] > 0) {
				str += (str?' ':'') + text[i][0] + '' + text[i][1];
				activateData(text[i]);
			} else {
				deactivateData(text[i]);
			}
		}
		setTitle(str);
	} else {
		//silentmode
	}
}
function executeFriendsOnline(text) {
	if(text.length > 1) text = text.split(';');
	document.getElementById('friendsOnlineCount').innerHTML = (text.length-1);
	friendsC = (text.length-1);
	obj = document.getElementById('friendsOnlineList');
	ret = '';
	if(text.length > 1) {
		ret = '<ul class="friends_list">';
		for(i = 0; i < text.length-1; i++) {
			text[i] = text[i].split('|');
			ret += '<li>';
			ret += '<table width="100%" cellpadding="0" cellspacing="0"><tr><td>';
			ret += '<span onmouseover="launchHover(event, \'' + text[i][0] + '\');" onmouseout="clearHover();">';
			ret += '<a href="/user/view/' + text[i][0] + '">' + unescape(text[i][1]) + ' <b>' + text[i][2] + '</b></a>';
			ret += '</span></td><td width="36">';
			ret += '<img align="absmiddle" onclick="makeGb(\'' + text[i][0] + '\')" title="Skriv i gästboken" alt="Skriv i gästboken" src="/_gfx/icon_gb.png" />';
			ret += '<img align="absmiddle" onclick="makeChat(\'' + text[i][0] + '\')" title="Chatta" alt="Chatta" src="/_gfx/icon_qchat.png" />';
			ret += '</td></tr></table>';
			ret += '</li>';
		}
		ret += '</ul>';
	}
	obj.innerHTML = ret;
	delete ret;
	delete text;
	if (friendsMaximized == '1') obj.style.display = '';
}

function activateData(info) {
	switch(info[0]) {
		case 'g': obj = 'Xg'; break;
		case 'm': obj = 'Xm'; break;
		case 'v': obj = 'Xr'; break;
		case 'c':
			//quickchat
			document.getElementById('quickchat_indicator').onclick = function() {
				makeChat(info[2]);
				hide_element_by_name('quickchat_indicator');
			};
			show_element_by_name('quickchat_indicator');
			return;

		default: return;
		break;
	}
	e = document.getElementById(obj);
	if (!e) return;
	e.style.color = '#832e30';
	e.innerHTML = ' (' + info[1] + ')';
}
function deactivateData(info) {
	switch(info[0]) {
		case 'g': obj = 'Xg'; break;
		case 'm': obj = 'Xm'; break;
		case 'v': obj = 'Xr'; break;
		default: return;
		break;
	}
	e = document.getElementById(obj);
	if (!e) return;
	e.innerHTML = '';
	e.style.color = '';
}
function emptyChat() {
	check = parseInt(document.getElementById('Xc').childNodes[1].innerHTML);
	if(check > 0) document.getElementById('Xc').childNodes[1].innerHTML = '(' + check + ')';
	else {
		document.getElementById('XcL').innerHTML = '<a id="Xc">privatchat<span></span></a>';
		document.getElementById('Xc').style.color = '#d9c7a7';
	}
}
