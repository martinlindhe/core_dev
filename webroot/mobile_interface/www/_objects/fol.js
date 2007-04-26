var friendsC = 0, friendsMaximized = 0, friendObj = document.getElementById('friendsOnlineList');

function friendsToggle() {
	if (friendObj.style.display == 'none') {
		//expand
		friendsMaximized = 1;
		friendsC = 10;
		//friendObj.style.height = ((friendsC * 27) + 'px');
		friendObj.style.display = '';
	} else {
		//minimize
		friendsMaximized = 0;
		friendObj.style.display = 'none';
	}

	friendsSavePos();
	return false;
}
function friendsSavePos() {
	val = parseInt(friendsMaximized == 1?1:0);
	var expire = new Date(); expire.setTime(expire.getTime() + 3600000*24*1);
	document.cookie = "friendsOnline="+escape(val) + ";expires="+expire.toGMTString() + ';path=/';
}
