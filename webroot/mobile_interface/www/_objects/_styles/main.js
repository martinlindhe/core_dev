function makeGb(id, more, w) {
	if(!more) more = '';
	if(!w) w = '476';
	ref = window.open('/user/gbwrite/' + id + '/' + more, 'GB_' + id, 'left='+((screen.availWidth - w)/2)+',top='+((screen.availHeight - 310)/2)+', resizable=0, status=no, width=' + w + ', height=310');
	ref.focus();
}
function makeChat(id) {
	ref = (window.opener)?window.opener.parent.comhead:parent.window.comhead;
	if(ref.popupArr['C_' + id] == null || ref.popupArr['C_' + id].closed) {
		ref.popupArr['C_' + id] = window.open('user_chat.php?id=' + id, 'C_' + id, 'left='+((screen.availWidth - 476)/2)+',top='+((screen.availHeight - 425)/2)+', resizable=0, status=no, width=476, height=425, location=yes');
		ref.popupArr['C_' + id].focus();
	} else
		ref.popupArr['C_' + id].focus();
}
function goLoc(url) { document.location.href = url; return true; }