var ie = document.all, nn6 = document.getElementById && !document.all, friendsIsDrag = false;
var fX,fY,oX,oY,friendsObj;
function friendsExpand(obj) {
	//if(friendsMaximized) return;
	obj.style.height = (21 + (friendsC * 27)) + 3 + 'px';
	//obj.style.overflow = 'visible';
}
function friendsClose(obj) {
	if(friendsMaximized == '1') return;
	obj.style.height = '23px';
	//obj.style.overflow = 'hidden';
}
function friendsToggle(auto) {
	btn = document.getElementById('friendsTog');
	if(btn.innerHTML == 'öppna') {
		friendsMaximized = '1';
		friendsExpand(btn.parentNode);
		btn.innerHTML = 'stäng';
	} else {
		friendsMaximized = '0';
		friendsClose(btn.parentNode);
		btn.innerHTML = 'öppna';
	}
	if(!auto) {
		friendsSavePos();
		return false;
	}
}
function friendsSavePos() {
	val = parseInt(document.getElementById('friendsOnline').style.left) + 'x' + parseInt(document.getElementById('friendsOnline').style.top) + 'x' + (friendsMaximized == '1'?'1':'0');
	var expire = new Date(); expire.setTime(expire.getTime() + 3600000*24*1);
	document.cookie = "friendsOnlinePos="+escape(val) + ";expires="+expire.toGMTString() + ';path=/';
}

function friendsDrag(e) {
	if(friendsIsDrag) {
		friendsObj.style.left = (nn6 ? oX + e.clientX - fX : oX + event.clientX - fX) + 'px';
		friendsObj.style.top  = (nn6 ? oY + e.clientY - fY : oY + event.clientY - fY) + 'px';
		return false;
	}
}
function friendsActivate(e, obj) {
	friendsIsDrag = true;
	friendsObj = obj;
	oX = parseInt(obj.style.left) + 0;
	oY = parseInt(obj.style.top) + 0;
	fX = nn6 ? e.clientX : event.clientX;
	fY = nn6 ? e.clientY : event.clientY;
	document.onmousemove = friendsDrag;
	return false;
}
document.onmouseup = function() {
	if(friendsIsDrag) { window.setTimeout('friendsSavePos()', 200); friendsIsDrag = false; }
}
function friendsSetPos(val) {
	if(val.length <= 0) return;
	val = val.split('x');
	document.getElementById('friendsOnline').style.left = val[0] + 'px';
	document.getElementById('friendsOnline').style.top = val[1] + 'px';
	if(val[2] == '1')
		friendsToggle(1);
}
	document.getElementById('friendsOnline').onselectstart = function () { return false; }