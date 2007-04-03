var vtimes = 0;
var atimes = 0;
var ptimes = 0;
var pcolor1 = 0;
var pcolor2 = 0;
function checkColor() {
	if(pcolor1 < 1) {
		if(confirm("Nu kommer du att använda färgen som du valt i färgpaletten,\nskriv texten som ska färgar emellan <färg=FÄRGKOD> och </färg>\n\nKom ihåg att du kan markera text som ska ändras i förväg och sedan\ntrycka på den här knappen!")) {
			pcolor1++;
			return true;
		}
	} else return true;
	return false;
}
function checkColor2() {
	if(pcolor2 < 1) {
		if(confirm("Du har inte valt någon färg i färgpaletten, och kommer därför att få\ngul som textfärg. <färg=yellow>\n\nByt ut yellow till en färgkod från färgpaletten, eller skriv in en annan färg.\n\nKom ihåg att du kan markera text som ska ändras i förväg och sedan\ntrycka på den här knappen!")) {
			pcolor1++;
			return true;
		}
	} else return true;
	return false;
}
function checkPhoto() {
	if(ptimes < 1) {
		if(confirm("För att lägga in ett foto, skriv in bildnumret som står i ditt fotoalbum,\nutan #-tecken, mellan <foto> och </foto>\n\nKom ihåg att du endast kan använda bilder från ditt egna fotoalbum!")) {
			ptimes++;
			return true;
		}
	} else return true;
	return false;
}
function checkVimmel() {
	if(vtimes < 1) {
		if(confirm("För att lägga in en vimmelbild, skriv in bildnumret,\nutan #-tecken, mellan <vimmel> och </vimmel>")) {
			vtimes++;
			return true;
		}
	} else return true;
	return false;
}
function checkAlias() {
	if(atimes < 1) {
		if(confirm("För att lägga in en bildlänk till en vän, skriv in alias, mellan <alias> och </alias>")) {
			atimes++;
			return true;
		}
	} else return true;
	return false;
}
function fixHeight() {
	chg_txt = document.getElementById('chg_txt');
	height = 12;
	str = chg_txt.value.split("\n");
	lines = (str.length*12);
	if(lines > 24) {
		lines = lines + 100;
		chg_txt.style.height = lines;
	} else
		chg_txt.style.height = '250';
}
function matchFields() {
	var exp=/<\S[^>]*>/g;
	var exp2 = unescape("%0D%0A");
	var exp2 = new RegExp(exp2, "g");
	str = document.getElementById('chg_txt').value;
	if(trim(str).length > 0) {
		str = str.replace(exp,'');
		str = str.replace(exp2, "<br>");
		document.getElementById('chg').innerHTML = str;
	} else document.getElementById('chg').innerHTML = 'ingen text.';
}
function matchColor() {
	fnt = escape(document.getElementById('ins_fnt').value).toLowerCase();
	fnt = fnt.replace("%27", '');
	fnt = fnt.replace('%22', '');
	fnt = fnt.replace('%3b', '');
	fnt = fnt.replace("%2c", '');
	fnt = fnt.replace('%23', '');
	if(fnt) document.getElementById('color_pre').style.color = unescape(fnt);
	bg = escape(document.getElementById('ins_bg').value).toLowerCase();
	bg = bg.replace("%27", '');
	bg = bg.replace('%22', '');
	bg = bg.replace('%3b', '');
	bg = bg.replace('%2c', '');
	bg = bg.replace('%23', '');
	if(bg) document.getElementById('color_pre').style.background = unescape(bg);
}

prjtags = new Array('<b>','</b>','<i>','</i>','<center>','</center>', '<u>', '</u>', "<privat>", "</privat>", '<vimmel>', '</vimmel>', '<alias>', '</alias>', '<foto>', '</foto>', '<färg=yellow>', '</färg>', '<font=Arial>', '</font>', '<size=20>', '</size>');
var isfocused = false;

function addText(nmb) {

	var msg = document.pres.chg_txt;
	theSelection = false;

// SPECIAL
	if(nmb == 16 && document.getElementById('ins_clr').value != '') {
		prjtags[16] = '<färg=' + document.getElementById('ins_clr').value + '>';
	} else if(nmb == 18 && document.getElementById('ins_font').value != '0') {
		prjtags[18] = '<font=' + document.getElementById('ins_font').value + '>';
	} else if(nmb == 20 && document.getElementById('ins_size').value != '0') {
		prjtags[20] = '<size=' + document.getElementById('ins_size').value + '>';
	}

	if (window.getSelection) {
		theSelection = window.getSelection();
	} else if (document.getSelection) {
		theSelection = document.getSelection();
	} else if (document.selection) {
		theSelection = document.selection.createRange().text;

	}

	if(theSelection && theSelection != '') {
		if(msg.value.indexOf(theSelection) != -1) {
			ieWrap(msg, theSelection, prjtags[nmb], prjtags[nmb+1]);
			return;
		}
	} else if(msg.selectionEnd && (msg.selectionEnd - msg.selectionStart > 0)) {
		mozWrap(msg, prjtags[nmb], prjtags[nmb+1]);
		return;
	}


	msg.value += prjtags[nmb] + prjtags[nmb+1];
	msg.focus();
	return;


}

function mozWrap(msg, open, close) {

	var selLength = msg.textLength;
	var selStart = msg.selectionStart;
	var selEnd = msg.selectionEnd;

	if (selEnd == 1 || selEnd == 2)
		selEnd = selLength;

	var s1 = (msg.value).substring(0, selStart);
	var s2 = (msg.value).substring(selStart, selEnd)
	var s3 = (msg.value).substring(selEnd, selLength);

	msg.value = s1 + open + s2 + close + s3;
	return;
}

function ieWrap(msg, theSelection, open, close) {
	document.selection.createRange().text = open + theSelection + close;
	msg.focus();
	theSelection = '';
	return;
}