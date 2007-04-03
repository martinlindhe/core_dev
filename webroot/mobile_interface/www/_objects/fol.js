var friendsC = 0, friendsMaximized = '0', friendObj = document.getElementById('friendsOnline');
function friendsExpand(obj) {
	obj.style.height = (friendsC?(26 + (friendsC * 27)) + 3 + 'px':'27px');
}
function friendsClose(obj) {
	if(friendsMaximized == '1') return;
	obj.style.height = '27px';
}
function friendsToggle(auto) {
	btn = document.getElementById('friendsTog');
	if(auto || btn.innerHTML == 'öppna') {
		friendsMaximized = '1';
		friendsExpand(friendObj);
		btn.innerHTML = 'stäng';
	} else {
		friendsMaximized = '0';
		friendsClose(friendObj);
		btn.innerHTML = 'öppna';
	}
	if(!auto) {
		friendsSavePos();
		return false;
	}
}
function friendsSavePos() {
	val = parseInt(friendsMaximized == '1'?'1':'0');
	var expire = new Date(); expire.setTime(expire.getTime() + 3600000*24*1);
	document.cookie = "friendsOnline="+escape(val) + ";expires="+expire.toGMTString() + ';path=/';
}

function friendsSetPos(val) {
	if(val == '1')
		friendsToggle(1);
}
document.getElementById('friendsOnline').onselectstart = function () { return false; }