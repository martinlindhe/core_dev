<link rel="stylesheet" href="inc/style.css" type="text/css">

<script type="text/javascript" src="inc/functions.js"></script>
<script type="text/javascript" src="inc/ajax.js"></script>

<script type="text/javascript">
var search_request = null;

function trace(s)
{
	console.debug(s);
}

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
				//add_node(e, items[i].firstChild.nodeValue, 'search_result_'+i%2);
				
				add_link_node(e, items[i].firstChild.nodeValue, 'show_result.php?i='+items[i].getAttribute('id'), 'search_result_'+i%2);
				
				//visa value för id parametern, <s id="value">
				//trace();
			}
		}

		search_request = null;
	}
}
</script>

<form name="ajax">
Search:<br>
<input type="text" id="search_box" name="txt" onKeyUp="perform_ajax_search()">
<div id="search_results"></div>
</form>

