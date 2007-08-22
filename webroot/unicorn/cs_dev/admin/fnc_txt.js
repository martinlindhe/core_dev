prjtags = new Array('<b>','</b>', '[bild]', '[/bild]');
var isfocused = false;



function addText(nmb) {

	var msg = document.change.ins_msg;
	theSelection = false;

	// Om någonting är markerat, lägg taggar runt om

	if (window.getSelection) {
		theSelection = window.getSelection();
	} else if (document.getSelection) {
		theSelection = document.getSelection();
	} else if (document.selection) {
		theSelection = document.selection.createRange().text;

	}
	if(theSelection && theSelection != '' && msg.value.indexOf(theSelection) != -1) {
		ieWrap(msg, theSelection, prjtags[nmb], prjtags[nmb+1]);
	} else if(msg.selectionEnd && (msg.selectionEnd - msg.selectionStart > 0)) {
		mozWrap(msg, prjtags[nmb], prjtags[nmb+1]);
	} else {
	// Lägg in båda i slutet
		msg.value += prjtags[nmb] + prjtags[nmb+1];
		msg.blur();
		return;
	}
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
	msg.blur();
	return;
}

function ieWrap(msg, theSelection, open, close) {
	document.selection.createRange().text = open + theSelection + close;
	theSelection = '';
	msg.blur();
	return;
}
