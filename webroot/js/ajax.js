/*
	AJAX implementation with class interface in JavaScript by Martin Lindhe, Feb 2007

	Example:
	
	var request = new AJAX();
	request.GET('/ajax/ajax_del_uservar.php?i='+id, ajax_delete_uservar_callback, id, 'GET');

*/

//constructor
function AJAX()
{
	var _request = false;
	this.GET = GET;
	this.ResultReady = ResultReady;
	this.EmptyResponse = EmptyResponse;

	if (window.XMLHttpRequest) { // Mozilla, Safari, Opera...
		this._request = new XMLHttpRequest();
		if (this._request.overrideMimeType) this._request.overrideMimeType('text/xml');
	} else if (window.ActiveXObject) { // IE
		try {
			this._request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				this._request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}

	if (!this._request) {
		alert('Giving up, Cannot create an XMLHTTP instance');
		return false;
	}
	
	// Performs an GET-request expected to return XML
	function GET(url, callback, callbackparam, params)
	{
		if (!this._request) return false;

		this._request.onreadystatechange = function() {
			callback(callbackparam);
		}
		this._request.open('GET', url, true);
		this._request.send(null);
	}
	
	function ResultReady()
	{
		if (!this._request || this._request.readyState != 4) return false;
		if (this._request.status == 200) return true;

		return false;
	}
	
	//Returns true if 'name' is the root tag of this xml object
	function EmptyResponse(name)
	{
		return this._request.responseXML.getElementsByTagName(name).item(0);
	}
}



//todo: anv�nd en timer s� att 'ajax_anim' inte visas f�rr�ns efter 20ms
var delete_request = null;
function perform_ajax_delete_uservar(id)
{
	show_element_by_name('ajax_anim');

	delete_request = new AJAX();
	delete_request.GET('/ajax/ajax_del_uservar.php?i='+id, ajax_delete_uservar_callback, id, 'GET');
}

function ajax_delete_uservar_callback(id)
{
	if (delete_request.ResultReady())
	{
		if (!delete_request.EmptyResponse('ok')) {
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