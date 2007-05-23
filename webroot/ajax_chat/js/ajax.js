//ajax implementation by Martin Lindhe 2006.08.17, based on MDC sample code

//Returns a XMLHttpRequest to be used by other AJAX functions
function getHttpRequest(format)
{
	var http_request = false;

	if (window.XMLHttpRequest) { // Mozilla, Safari, Opera...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType && format == 'XML') http_request.overrideMimeType('text/xml');
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
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}

	return http_request;
}

/*
	url - the file you want to run
	format - XML or TEXT
	callback - the function you want to handle the result with
	method - (optional, default: GET) HTTP request method: GET, POST, HEAD
	params - (optional, default: null) POST data, in this format: name=value&anothername=othervalue&so=on
		note: varje param måste ha ett värde annars skickas dom inte med, alltså "var=1" istället för bara "var"

	Returns:
	the XMLHttpRequest object for this AJAX call

	Examples:
	AJAX_Request('ajax.xml', 'XML', alertContents, 'GET');
	AJAX_Request('ajax_test.php', 'TEXT', alertContents2, 'POST', 'a=4');
*/
function AJAX_Request(url, format, callback, method, params)
{
	if (!method) method = 'GET';

	var http_request = getHttpRequest(format);
	if (!http_request) return false;

	http_request.onreadystatechange = callback;	//todo: möjligheter att skicka med parametrar, genom anonym funktionblabla, se MDC AJAX introduktion för exempel
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
