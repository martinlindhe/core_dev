function tryThis(e) {
	if(!e) var e=window.event;
	alert(e);
}
var gotFocus = false; var gotLet1 = ''; var gotLet2 = ''; var gotLet3 = '';
var numbers = new Array(96,97,98,99,100,101,102,103,104,105);
var keynumbers = new Array(48,49,50,51,52,53,54,55,56,57);
function changeselected(e) {
	if(!e) var e=window.event;
	gotAct = array_search(e['keyCode'], numbers);
	if(gotAct <= 0) gotAct = array_search(e['keyCode'], keynumbers);

	if(gotFocus == '1' && gotAct >= 0) {
		//gotAct = array_search(e['keyCode'], numbers).toString();
		gotLet1 = gotLet1 + gotAct.toString();
		if(gotLet1.length >= 4) {
			index = select_search(gotLet1, document.r.Y);
			if(index < 100)
				document.r.Y.selectedIndex = index;
			gotLet1 = '';
			if(index < 100)
				document.r.m.focus();
		}
		return false;
	} else if(gotFocus == '2' && gotAct >= 0) {
		gotLet2 = gotLet2 + gotAct.toString();
		if(gotLet2.length >= 2) {
			index = select_search(gotLet2, document.r.m);
			if(index < 100)
				document.r.m.selectedIndex = index;
			gotLet2 = '';
			if(index < 100)
				document.r.d.focus();
		}
		return false;
	} else if(gotFocus == '3' && gotAct >= 0) {
		gotLet3 = gotLet3 + gotAct.toString();
		if(gotLet3.length >= 2) {
			index = select_search(gotLet3, document.r.d);
			if(index < 100)
				document.r.d.selectedIndex = index;
			gotLet3 = '';
			if(index < 100)
				document.r.i.focus();
		}
		return false;
	}
}
function array_search(val, arr) {
	var i = arr.length;
	while(i--)
		if(arr[i] && arr[i] === val) break;
	return i;
}
function select_search(val, arr) {
	gotIt = false;
	for(i = 0; i < 100; i++) {
		if(!gotIt && arr[i] && arr[i].value.toString() === val.toString()) { gotIt = true; break; }
	}
	return i;
}
document.onkeydown = changeselected;