var d = document;
function changeStatus(mn, fn) {
	fn = fn.split(":");
	id = fn[1];
	fn = fn[0];

	d.getElementById(mn + '_id:' + id).value = fn;

	changePic(id, fn);
	return false;
}
function popup(url, name, w, h) {
	if(!w) w = 687;
	if(!h) h = 680;
	window.open(url, name, 'toolbars=0, scrollbars=1, location=0, statusbars=0, menubars=0, resizable=0, width='+w+', height='+h);
}
function vimmel(id, w, h) {
	if(!w) w = 707;
	if(!h) h = 680;
	window.open('pics_single.php?id=' + id, 'admpic', 'toolbars=0, scrollbars=1, location=0, statusbars=0, menubars=0, resizable=1, width='+w+', height='+h+', left = 100, top = 18');
}
function makePop(url, name, w, h, c, opt) {
	if(!c) c = false;
	if(!opt) opt = 'resizable=0, status=no, ';
	if(!w || w == '') w = 300;
	if(!h || h == '') h = 380;
	if(c) {
		l = (screen.availWidth - w)/2 - w/2;
		t = (screen.availHeight - h)/2 - h;
	}
		p = window.open(url, name, opt + ((c)?'top='+l+', left='+l+', ':'') + 'width='+w+', height='+h);
		p.focus();
}
function changeStatus2(mn, fn) {
	fn = fn.split(":");
	dif = fn[2];
	id = fn[1];
	fn = fn[0];

	d.getElementById(mn + '_id:' + id + ':' + dif).value = fn;

	changePic(id + ':' + dif, fn);
	return false;
}

function changePic(id, fn) {
	fn = (fn == 2)?false:true;
	if(fn) {
		d.getElementById('2:' + id).src = '_img/status_none.gif';
		d.getElementById('1:' + id).src = '_img/status_green.gif';
	} else {
		d.getElementById('1:' + id).src = '_img/status_none.gif';
		d.getElementById('2:' + id).src = '_img/status_red.gif';
	}
}
function openWin(url) {
	window.open(url, 'answer', 'width=420, height=370, location=no, menubar=no, scrollbars=no, resizable=no, status=no, toolbar=no');
}
function openSWin(url) {
	window.open(url, 'total', 'width=420, height=350, location=no, menubar=no, scrollbars=yes, resizable=no, status=no, toolbar=no');
}
