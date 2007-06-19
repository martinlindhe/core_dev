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
	document.eventTarget = what;
        isDrag = what;
	if(size) {
		doi('imSize').innerHTML = 'DRA MED MUSEN';
		isSize = true;
	}
}

function stopDrag() {
	document.eventTarget = null;
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
	doi('imBorder').style.display = '';
	doi('imDrag').style.width = inWidth;
	doi('imDrag').style.height = inHeight;
	doi('theSpace').style.width = inWidth;
	doi('theSpace').style.height = inHeight;
	doi('submitbtn').disabled = false;
}

function shutdown() {
	doi('everything').style.display = 'none';
	doi('imDrag').style.display = 'none';
	doi('imBorder').style.display = 'none';
	doi('theSpace').style.display = 'none';
	doi('imDiv').style.display = 'none';
	doi('theImage').style.display = 'none';
	doi('submitbtn').disabled = true;
}

function dofix() {

	doi('imBorder').style.width = inWidth;
	doi('imBorder').style.height = inHeight;
	doi('imDrag').style.width = inWidth;
	doi('imDrag').style.height = inHeight;
	doi('theSpace').style.width = inWidth;
	doi('theSpace').style.height = inHeight;
	doi('UserImageW').value = inWidth;
	doi('UserImageH').value = inHeight;
}

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

function ifDrag(e) {
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
		nowX = thisX + e.clientX - jX;
		nowY = thisY + e.clientY - jY;
		nowW = Math.round(di(l.style.width) + e.clientX - jX);
		nowH = Math.round(di(l.style.height) + e.clientY - jY);
		if(isSize) {
// &&  || nowW > di(inWidth)
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
				doi('UserImageX').value = di(l.style.left) - di(ld.style.left);
			}
			if(nowY >= minY && nowY < maxY) {
				l.style.top = nowY;
				doi('UserImageY').value = di(l.style.top) - di(ld.style.top);
			}
		}
	}
	jX = e.clientX;
	jY = e.clientY;
	document.eventTarget = null;
	return false;
}

// Ökar storleken på div:en
function IncDIV(val) {
	var l = doi('imDrag');
	var ld = doi('imDiv');
	var ii = doi('theImage');
	var li = doi('theSpace');
	thisLW = di(l.style.width) + di(l.style.left);
	thisTH = di(l.style.height) + di(l.style.top);
	picLW = di(ii.width) + di(ld.style.left);
	picTH = di(ii.height) + di(ld.style.top);
	if((thisLW + di(val)) < picLW && (thisTH + di(val)) < picTH) {
		inWidth += di(val);
		inHeight += di(val);
		l.style.width = inWidth;
		l.style.height = inHeight;
		li.style.width = l.style.width;
		li.style.height = l.style.height;
		doi('UserImageW').value = di(inWidth);
		doi('UserImageH').value = di(inHeight);
	}
//alert(li.style.width +':::'+ thisTH +':::'+ picLW +':::'+ picTH);
	return false;
}

// Minskar storleken på div:en
function DecDIV(val) {
	var l = doi('imDrag');
	var ld = doi('imDiv');
	var ii = doi('theImage');
	var li = doi('theSpace');
	thisW = di(l.style.width);
	thisH = di(l.style.height);
	if((thisW - di(val)) >= defWidth && (thisH - di(val)) >= defHeight) {
		inWidth -= di(val);
		inHeight -= di(val);
		l.style.width = inWidth;
		l.style.height = inHeight;
		li.style.width = l.style.width;
		li.style.height = l.style.height;
		doi('UserImageW').value = di(inWidth);
		doi('UserImageH').value = di(inHeight);
	}
	return false;
}