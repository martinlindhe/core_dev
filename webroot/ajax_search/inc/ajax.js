//ajax implementation by Martin Lindhe 2006.08.17, based on MDC sample code

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
		alert('Giving up :( Cannot create an XMLHTTP instance');
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
	AJAX_XML_Request('ajax.xml', alertContents, 'GET');
	AJAX_XML_Request('ajax_test.php', alertContents2, 'POST', 'a=4');
*/
function AJAX_XML_Request(url, callback, method, params)
{
	if (!method) method = 'GET';

	var http_request = getXMLRequest();
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

var search_request = null;

function perform_ajax_search()
{
	//genomför sökningen
	search_request = AJAX_XML_Request('ajax_search.php?s='+document.ajax.txt.value, ajax_search_callback, 'GET');
}

function ajax_search_callback()
{
	if (!search_request || search_request.readyState != 4) return;
	if (search_request.status == 200)
	{
		//sökresultat mottaget
		empty_element('search_results');

		var e = document.getElementById('search_results');
		
		var root_node = search_request.responseXML.getElementsByTagName('x').item(0);
		if (!root_node) return;

		var items = root_node.childNodes;

		for (var i=0; i<items.length; i++)
		{
			var cur = items[i].childNodes;
			if (items[i].nodeName == 's') {		//<s> is search results
				add_link_node(e, items[i].firstChild.nodeValue, 'show_result.php?i='+items[i].getAttribute('id'), 'search_result_'+i%2);
			}
		}

		search_request = null;
	}
}
