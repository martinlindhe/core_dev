var MAIN_TITLE = null;
var oldstr = '';
function launchHover(e, id) {
	if(!e) e = window.event;
	ob = document.getElementById('hoverCraft');
	ob.style.display = '';
	str = '<img src="/user/image/' + id + '.jpg" class="bbrd" />';
	if(oldstr != str) ob.innerHTML = str;
	oldstr = str;
	oXY = [0, 0];
	try {
		if(document.documentElement.scrollTop || document.documentElement.scrollLeft) oXY = [document.documentElement.scrollLeft, document.documentElement.scrollTop];
		if(document.body.scrollTop || document.body.scrollLeft) oXY = [document.body.scrollLeft, document.body.scrollTop];
		if(window.pageXOffset || window.pageYOffset) oXY = [window.pageXOffset, window.pageYOffset];
	} catch(Exception) {
	}
	ob.style.top = parseInt(e.clientY + 5 + oXY[1]) + 'px';
	ob.style.left = parseInt(e.clientX + 5 + oXY[0]) + 'px';
}
function clearHover() {
	document.getElementById('hoverCraft').style.display = 'none';
}

function makeGb(id, more, w) {
	if(!more) more = '';
	if(!w) w = '200';
	h = 280;
	ref = window.open('user_gbwrite.php?id=' + id + more, 'GB_' + id, 'left='+((screen.availWidth - w)/2)+',top='+((screen.availHeight - h)/2)+', resizable=0, status=no, width=' + w + ', height='+h);
	ref.focus();
}
function makePop(url, name, w, h, c, extra, opt) {
	if(!c) c = false;
	if(!extra) opt = 'resizable=0, status=no, ';
	if(!extra) extra = '';
	if(!w) w = 500;
	if(!h) h = 400;
	if(c) {
		l = (screen.availWidth - w)/2;
		t = (screen.availHeight - h)/2;
		opt = opt + 'left='+l+',top='+t+', ';
	}
	ref = window.open(url, name, opt + 'width='+w+', height='+h+extra);
	ref.focus();
}
function makeSmall(url) {
	makePop(url, '', 476, 310, 1, ',scrollbars=0', 'resizable=0, status=0, ');
	return false;
}
function makeBig(url) {
	makePop(url, '', 666, 520, 1, ',scrollbars=0', 'resizable=1, status=1, ');
	return true;
}
function makeTiny(url) {
	makePop(url, '', 200, 280, ',scrollbars=0', 'resizable=0, status=0, ');
	return false;
}

function makeMail(id) {
	if(!id) id = '';
	window.open('user_mailwrite.php?id=' + id, '', 'width=650, height=560, scrollbars=0, resizable=1, status=no, location=0');
}
function makeChat(id) {
	win = window.open('user_chat.php?id=' + id, 'C_' + id, 'left='+((screen.availWidth - 535)/2)+',top='+((screen.availHeight - 465)/2)+', resizable=0, status=no, width=570, height=470, location=yes');
	win.focus();
}
function makeText(url, type) {
	if(!type)
		win = window.open(url, 'text', 'left='+((screen.availWidth - 535)/2)+',top='+((screen.availHeight - 465)/2)+', resizable=1, scrollbars=1, status=no, width=535, height=465, location=yes');
	else
		win = window.open(url, 'text', 'left='+((screen.availWidth - 630)/2)+',top='+((screen.availHeight - 465)/2)+', resizable=1, scrollbars=1, status=no, width=630, height=465, location=yes');
	win.focus();
}
function makeContribution() { 
	win = window.open('/main/speakerscorner/create/', '', 'left='+((screen.availWidth - 200)/2)+',top='+((screen.availHeight - 280)/2)+', resizable=0, scrollbars=0, status=no, width=200, height=280, location=yes');
	win.focus();
}
function makeRelation(id) { 
	ref = window.open('user_relations_create.php?id=' + id, '',  'left='+((screen.availWidth - 200)/2)+',top='+((screen.availHeight - 280)/2)+', resizable=0, status=no, width=' + 200 + ', height='+280);
	ref.focus();
}
function makeUpload(doit) { 
	if(!doit) doit = '';
	win = window.open('/user/galleryupload/&' + doit, 'upload', 'left='+((screen.availWidth - 200)/2)+',top='+((screen.availHeight - 280)/2)+', resizable=0, scrollbars=0, status=no, width=200, height=280, location=yes');
	win.focus();
}
function makeForum(id) {
	ref = window.open('forum_write.php?id=' + id, '', 'left='+((screen.availWidth - 610)/2)+',top='+((screen.availHeight - 265)/2)+', resizable=0, status=no, width=610, height=265');
	ref.focus();
}
function makeForumAns(id) {
	ref = window.open('forum_answer.php?id=' + id, '', 'left='+((screen.availWidth - 610)/2)+',top='+((screen.availHeight - 265)/2)+', resizable=0, status=no, width=610 height=265');
	ref.focus();
}
function makeBlog() { 
	win = window.open('user_blog_write.php', '', 'left='+((screen.availWidth - 570)/2)+',top='+((screen.availHeight - 650)/2)+', resizable=0, scrollbars=0, status=no, width=570, height=650, location=yes');
	win.focus();
}
function makeBlogComment(uid, id) {
	if(!id) id = '';
	ref = window.open('/user/blogcomment/' + uid + '/' + id + '/', '',  'left='+((screen.availWidth - 200)/2)+',top='+((screen.availHeight - 280)/2)+', resizable=0, status=no, width=' + 200 + ', height='+280);
	ref.focus();
}
function makePhotoComment(uid, id) {
	if(!id) id = '';
	ref = window.open('/user/gallerycomment/' + uid + '/' + id + '/', '',  'left='+((screen.availWidth - 200)/2)+',top='+((screen.availHeight - 280)/2)+', resizable=0, status=no, width=' + 200 + ', height='+280);
	ref.focus();
}
function trim(str) {
	return str.replace(/^\s*|\s*$/g,"");
}
function makeBlock(id) {
	ref = window.open('/user/block/' + id + '/', '',  'left='+((screen.availWidth - 200)/2)+',top='+((screen.availHeight - 280)/2)+', resizable=0, status=no, width=' + 200 + ', height='+280);
	ref.focus();
}
<!-- function makeMail() { alert('Kommer alldeles snart!'); } //-->
function makeSms() { alert('Kommer alldeles snart!'); }
function goLoc(url) { document.location.href = url; return true; }
function goUser(id) { document.location.href = '/user/view/' + id; return true; }
function postData(action, arr) {
	dcm = document.getElementById('pd');
	dcm.innerHTML = '<form name="pdForm" action="' + action + '" method="post">';
	for(i = 0; i < arr.length; i++) {
		dcm.innerHTML += '<input type="hidden" name="' + arr[i][0] + '" value="' + arr[i][1] + '" />';
	}
	dcm.innerHTML += '</form>';
	document.pdForm.submit();
}
function setTitle(str) {
	if(MAIN_TITLE == null) MAIN_TITLE = top.document.title;
	top.document.title = str + (str.length > 0?' | ':'') + MAIN_TITLE;
}
<!-- Forum functions //-->
function printtoggle(){
	if(document.getElementById) {
		document.write('&nbsp;<a href="javascript:toggleAll();" class="wht">visa/göm alla</a> - ');
	}
}

var last = false;
var last_id = 0;

function toggleLast() {
	if(last) {
		toggle('head:' + last_id, last_id, 1);
		document.location.hash = 'R' + last_id;
	}
}

function toggleAll(val) {
	var e = document.all.length;
	var obj;
	var objH;
	var objI;
	var objArr = new Array();
	var headArr = new Array();
	var infoArr = new Array();
	var o = 0;
	var displaymode = 'none';

	for(i=0; i < e; i++) {
		obj = document.all[i];
		if(obj.id.indexOf('head:') != -1) {
			headArr[o] = obj;
		} else if(obj.id.indexOf('content:') != -1) {
			if (val != 1) {
				if(obj.style.display == 'none') {
					displaymode = '';
				}
			}
			objArr[o] = obj;
			o++;
		}
	}

	for (i = 0; i < objArr.length; i++) {
		objArr[i].style.display = displaymode;
		if(displaymode == 'none')
			headArr[i].className = 'spac';
		else
			headArr[i].className = '';
	}
}

function toggle(obj, id, on) {
	var objC;
	objC = document.getElementById('content:' + id);
	obj = document.getElementById(obj);
	if(on) {
		objC.style.display = '';
		obj.className = '';
//alert('3');
	} else if(objC.style.display == 'none') {
		objC.style.display = '';
		obj.className = '';
//alert('2');
	} else {
		objC.style.display = 'none';
		obj.className = 'spac0';
//alert('1');
	}
}

function toggleInp(obj, text, blur_text) {
	if(text.length > 0 && obj.value == text) obj.value = '';
	else if(blur_text.length > 0 && obj.value == text) obj.value = blur_text;
}

function trace(s) { console.debug(s); }
<!-- End of forum functions //-->

function toggle2(type) {
	type = (type.checked)?true:false;
	for(i = 0; i < document.m.length; i++) {

		var toggle = document.m.elements[i];
		if(toggle.type == 'checkbox') {
			toggle.checked = type;
		}
	}
}
function openMail(th, id) {
	th.className = th.className.replace('act_bg', '');
	makeBig(id);
}

var faq_oldsel = 0;
function selectFAQ(sel) {
	if(faq_oldsel) {
		document.getElementById('F' + faq_oldsel).className = '';
	}
	document.getElementById('F' + sel).className = 'wht';
	faq_oldsel = sel;
}
function changePage(p) {
	document.search.p.value = p;
	document.search.submit();
}

