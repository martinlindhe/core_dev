isDrag = false;
isSize = false;
jX = '';
jY = '';
inWidth = init_w;
inHeight = init_h;
dSpeed = 0;
defWidth = inWidth;
defHeight = inHeight;
defTop = 0;
defLeft = 0;

function doi(obj){
	return document.getElementById(obj);
}

function di(v){
	return parseInt(v);
}
function doDrag(what, size) {
	isDrag = what;
	if(size) {
		doi('imSize').innerHTML = 'DRA MED MUSEN';
		isSize = true;
	}
}

function stopDrag() {
        if(isDrag) isDrag = false;
	isSize = false;
	doi('imSize').innerHTML = 'STORLEK»»';
}

// Startar script
function initiate() {
	document.onmousemove = ifDrag;
	document.onmouseup = stopDrag;
	inWidth = doi('UserImageW').value;
	inHeight = doi('UserImageH').value;
	doi('everything').style.display = '';
	doi('imDrag').style.display = '';
	doi('theSpace').style.display = '';
	doi('imDiv').style.display = '';
	doi('theImage').style.display = '';
	doi('imDrag').style.width = inWidth;
	doi('imDrag').style.height = inHeight;
	doi('theSpace').style.width = inWidth;
	doi('theSpace').style.height = inHeight;
	doi('submitbtn').disabled = false;
}

function shutdown() {
	doi('everything').style.display = 'none';
	doi('imDrag').style.display = 'none';
	doi('theSpace').style.display = 'none';
	doi('imDiv').style.display = 'none';
	doi('theImage').style.display = 'none';
	doi('submitbtn').disabled = true;
}

function dofix() {
	doi('imDrag').style.width = inWidth;
	doi('imDrag').style.height = inHeight;
	doi('theSpace').style.width = inWidth;
	doi('theSpace').style.height = inHeight;
	doi('UserImageW').value = inWidth;
	doi('UserImageH').value = inHeight;
}

// Återställer
function doStart() {
	var l = doi('imDrag');
	var li = doi('theSpace');
	l.style.width = defWidth;
	l.style.height = defHeight;
	l.style.top = defTop;
	l.style.left = defLeft;
	li.style.width = l.style.width;
	li.style.height = l.style.height;
	doi('UserImageW').value = di(defWidth);
	doi('UserImageH').value = di(defHeight);
	inWidth = defWidth;
	inHeight = defHeight;
	return false;
}
// Flyttar div:en
function ifDrag(e) {
	if(!e) var e=window.event;
	if(isDrag) {
		var l = doi('imDrag');
		var ld = doi('imDiv');
		var ii = doi('theImage');
		minX = di(ld.style.left);
		minY = di(ld.style.top);
		maxX = minX + di(ii.width) - inWidth;
		maxY = minY + di(ii.height) - inHeight;
		maxW = minX + di(ii.width);
		maxH = minY + di(ii.height);
		thisX = di(l.style.left);
		thisY = di(l.style.top);
		nowX = thisX + window.event.clientX - jX;
		nowY = thisY + window.event.clientY - jY;
		nowW = Math.round(di(l.style.width) + window.event.clientX - jX);
		nowH = Math.round(di(l.style.height) + window.event.clientY - jY);
		if(isSize) {
			if(nowH > di(inHeight) || nowW > di(inWidth)) {
				if(((di(inWidth)+nowX) < maxW) && ((di(inHeight)+nowY) < maxH)) {
					inHeight = nowH;
					inWidth = Math.round(nowH*1);
					dofix();
				}
			} else if(nowH < di(inHeight) && di(inHeight) >= 100) {
				inHeight = nowH;
				inWidth = Math.round(nowH*1);
				dofix();
			}
		} else {
			if(nowX >= minX && nowX < maxX){
				l.style.left = nowX;
				doi('UserImageX').value = l.style.pixelLeft - ld.style.pixelLeft;
			}
			if(nowY >= minY && nowY < maxY) {
				l.style.top = nowY;
				doi('UserImageY').value = l.style.pixelTop - ld.style.pixelTop;
			}
		}
	}
	jX = window.event.clientX;
	jY = window.event.clientY;
	return false;
}