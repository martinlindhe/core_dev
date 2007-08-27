var allowedext = Array('jpg', 'jpeg', 'gif', 'png');
var error = false;
var oldval = '';
var init_w = 150;
var init_h = 150;
var prevent = false;

function checkSize(obj) {
	if(obj.src.indexOf('1x1.gif') == -1) {
		if(obj.width > 900) {
			document.getElementById('theImage').style.width = '900px';
			document.getElementById('theImage').width = '900';
			if(document.getElementById('theImage').height < 200) {
				init_h = document.getElementById('theImage').height;
				init_w = init_h*1;
				doi('UserImageH').value = init_h;
				doi('UserImageW').value = init_w;
				initiate();
			}
		} else {
			if(obj.width < 75 || obj.height < 100) {
				alert('Den valda bilden är för liten!');
				shutdown();
				return;
			} else if(obj.width < 150 || obj.height < 200) {
				init_h = obj.height;
				init_w = init_h*1;
				doi('UserImageH').value = init_h;
				doi('UserImageW').value = init_w;
				initiate();
			}
			document.getElementById('theImage').style.width = obj.width;
		}
		document.getElementById('ActImageW').value = document.getElementById('theImage').width;
		document.getElementById('ActImageH').value = document.getElementById('theImage').height;
	} else shutdown();
}

function checkSrc(obj) {
	if(obj.src.indexOf('1x1.gif') == -1) initiate(); else shutdown();
}

function validateUpl(tForm) {
	if(tForm.ins_img.value.length <= 0) {
		alert('Felaktigt fält: Sökväg');
		return false;
	}

	if(tForm.ins_msg.value.length <= 0) {
		alert('Felaktigt fält: Beskrivning');
		tForm.ins_msg.focus();
		return false;
	}
	return true;
}

function validateForm(tForm) {
	if(prevent) { return false; }
	tForm.submitbtn.disabled = true;
	tForm.submitbtn.value = 'Vänta...';
	return true;
}

function checkKey(e) {
	if(!e) var e=window.event;
	if(e['keyCode'] == '13') prevent = true;
	return true;
}

function imgError(obj) {
	error = true;
	hideAll(obj);
	return;
}
function scriptError(obj) {
	hideAll(obj);
	return;
}
function hideAll(obj) {
	obj.src = '1x1.gif';
	obj.style.display = 'none';
}
function showPre(val) {
	var picpre = document.getElementById('theImage');
	var picsize = document.getElementById('theSize');
	if(val != '') {
		var showimg = false;
		ext = val.split(".");
		ext = ext[ext.length - 1].toLowerCase();
		for(var i = 0; i <= allowedext.length; i++)
			if(allowedext[i] == ext) 
				showimg = true;
		if(showimg) {
			previewpic = val;
			error = false;
			picpre.src = 'file://' + val.replace(/\\/g,'/');
			picsize.src = 'file://' + val.replace(/\\/g,'/');

			if(!error) {
				picpre.style.display = '';
			}
		} else {
			scriptError(picpre);
		}
	} else {
			scriptError(picpre);
	}
	oldval = val;
}

function intern_get(obj) {
	if((obj.value.match(/^[0-9]{1,7}$/) == null && obj.value != '') || obj.value == '') {
		alert('Felaktigt bildnummer.');
		obj.focus();
		return false;
	}
	document.intern.get.value = obj.value;
	document.intern.submit();
}

function delete_go() {
	if(confirm('Säker ?')) { 
		document.del.del.value = '1';
		document.del.submit();
	} else return false;
}

function fffix() {
	document.getElementById('submitbtn').disabled = false;
	document.getElementById('submitbtn').value = 'ladda upp';
}

function fffno() {
	document.getElementById('submitbtn').disabled = true;
	document.getElementById('submitbtn').value = 'beskär bild';
}

function intern_go() {
	document.location.href = 'showList.php?intern=1';
}
