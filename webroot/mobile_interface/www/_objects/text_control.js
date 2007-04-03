_d=document;
_d=document;
_nv=navigator.appVersion.toLowerCase();
_f=false;_t=true;
isIE=(_nv.indexOf("msie")!=-1)?_t:_f;
TC_active = true;
TC_name = 'text';
TC_initialized = false;

function TC_Init() {
	editor = TC_GetIFrame();
	hiddenHTML = TC_GetHiddenFrame();
	editor.document.designMode = 'On';
	if(isIE) {
		editor.document.write("<html><head><style type=\"text/css\"> body { font-family: Arial; font-size: 12px; color: #000000; } p { margin: 0; } img { border: 0; } </style></head><body> </body></html>");
		editor.document.body.innerHTML = hiddenHTML.value;
		editor.document.onkeydown = function() { return TC_Event(); }
		editor.document.onkeypress = function() { return TC_Event(); }
		editor.document.onclick = function() { return TC_Event(); };	
		editor.document.onmousedown = function() { return TC_Event(); };
	} else {
		//if(hiddenHTML.value) editor.document.body.innerHTML = hiddenHTML.value;
		//editor.document.execCommand("useCSS", false, true);
		editor.document.write("<html><head><style type=\"text/css\"> body { cursor: text; font-family: Arial; font-size: 12px; color: #000000; height: 90%; } p { margin: 0; } img { border: 0; } </style></head><body> </body></html>");
		editor.document.body.innerHTML = ' ' + hiddenHTML.value;
		editor.addEventListener("keydown", function() {
			return TC_Event;	
		}, true);	
		editor.addEventListener("keypress", function() {
			return TC_Event;	
		}, true);
		editor.addEventListener("click", function() {
			return TC_Event;	
		}, true);			
		editor.addEventListener("mousedown", function() {
			return TC_Event;	
		}, true);
	}
	TC_initialized = true;
}
function TC_GetIFrame() {
	if(isIE) {
		return eval(TC_name + '_var');
	} else {
		return document.getElementById(TC_name + '_var').contentWindow;
	}
}
function TC_GetHiddenFrame() {
	return document.getElementById(TC_name + '_html');
}
function TC_VarToHidden() {
	if(TC_initialized) _d.getElementById(TC_name + '_html').value = editor.document.body.innerHTML;
	//_d.getElementById(TC_name + '_html').value = editor.document.body.innerHTML;
}
function TC_HiddenToVar() {
	editor.document.body.innerHTML = _d.getElementById(TC_name + '_html').value;
}
function TC_Switch() {
	if(!TC_initialized) return;
	if(TC_active) {
		_d.getElementById(TC_name + '_c_var').style.display = 'none';
		TC_VarToHidden();
		_d.getElementById(TC_name + '_c_html').style.display = '';
		_d.getElementById(TC_name + '_html').focus();
		TC_active = false;
	} else {
		_d.getElementById(TC_name + '_c_html').style.display = 'none';
		TC_HiddenToVar();
		_d.getElementById(TC_name + '_c_var').style.display = '';
		editor.focus();
		TC_active = true;
	}
}
function TC_Event(e) {
	if(!TC_initialized || !TC_active) return;
	editor = TC_GetIFrame();
	if(!e && editor.event) e = editor.event.keyCode;
	else e = window.event['keyCode'];
	var _TAB = 9;
	var _ENTER = 13;
	var _QUOTE = 222;
	/*if(e == _ENTER) {
		var sel = editor.document.selection;
		if(sel.type == 'Control') {
			return;
		}
		var r = sel.createRange();
		r.pasteHTML('<br>');
		editor.event.cancelBubble = true; 
		editor.event.returnValue = false; 
		r.select();
		r.collapse(false);
		return false;
	} else*/
	if (e == _TAB) {
		var sel = editor.document.selection;
		var r = sel.createRange();
		r.pasteHTML('&nbsp;&nbsp;&nbsp;');
		editor.event.cancelBubble = true; 
		editor.event.returnValue = false; 
		r.select();
		r.collapse(false);
		return false;
	}
	return;
}
function TC_Format(cmd, info, t) {
	if(!TC_initialized || !TC_active) return;
	if(!info) info = null;
	if(!t) t = false;
	editor.focus();
	editor.document.execCommand(cmd, t, info);
	return false;
}