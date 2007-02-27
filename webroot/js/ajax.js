//ajax implementation by Martin Lindhe

//Returns a XMLHttpRequest to be used by other AJAX functions
function getXMLRequest()
{
	var http_request = false;

	if (window.XMLHttpRequest) { // Mozilla, Safari, Opera...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) http_request.overrideMimeType('text/xml');
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}

	if (!http_request) {
		alert('Giving up, Cannot create an XMLHTTP instance');
		return false;
	}

	return http_request;
}

/*
	url - the file you want to run
	callback - the function you want to handle the result with
	method - (optional, default: GET) HTTP request method: GET, POST, HEAD
	params - (optional, default: null) POST data, in this format: name=value&anothername=othervalue&so=on
		note: varje param måste ha ett värde annars skickas dom inte med, alltså "var=1" istället för bara "var"
	
	Returns:
	the XMLHttpRequest object for this AJAX call
	
	Examples:
	AJAX_XML_Request('ajax.xml', alertContents, 0, 'GET');
	AJAX_XML_Request('ajax_test.php', alertContents2, 0, 'POST', 'a=4');
*/
function AJAX_XML_Request(url, callback, callbackparam, method, params)
{
	if (!method) method = 'GET';

	var http_request = getXMLRequest();
	if (!http_request) return false;

	http_request.onreadystatechange = function() {
		callback(callbackparam);
	}
	http_request.open(method, url, true);
	if (method == 'POST') {
		http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		http_request.send(params);
	}
	else {
		http_request.send(null);
	}

	return http_request;
}


//todo: använd en timer så att 'ajax_anim' inte visas förräns efter 20ms
var delete_request = null;
function perform_ajax_delete_uservar(id)
{
	show_element_by_name('ajax_anim');
	delete_request = AJAX_XML_Request('/ajax/ajax_del_uservar.php?i='+id, ajax_delete_uservar_callback, id, 'GET');
}

function ajax_delete_uservar_callback(id)
{
	if (!delete_request || delete_request.readyState != 4) return;
	if (delete_request.status == 200)
	{
		var root_node = delete_request.responseXML.getElementsByTagName('ok').item(0);
		if (!root_node) {
			var e = document.getElementById('ajax_anim_pic');
			e.setAttribute('src', '/gfx/icon_warning_big.png');
			e.setAttribute('title', 'Database error');
			return;
		}

		hide_element_by_name('edit_setting_div_'+id);
		hide_element_by_name('ajax_anim');
		delete_request = null;
	}
}