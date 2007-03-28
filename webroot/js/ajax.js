/*
	AJAX implementation with class interface in JavaScript by Martin Lindhe, Feb 2007

	Example:
	
	var request = new AJAX();
	request.GET('/ajax/url.php?i='+id, function_callback, id);
*/

//class definition of AJAX
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

		if (callback) this._request.onreadystatechange = function() { callback(callbackparam); }
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


//todo: försök kom på ett mer standardiserat interface till GET-funktionen så att mindre sådan här init&callback kod behövs
var delete_request = null;

function show_ajax_anim()
{
	if (delete_request && !delete_request.ResultReady()) show_element_by_name('ajax_anim');
}

function perform_ajax_delete_uservar(id)
{
	delete_request = new AJAX();
	delete_request.GET('/ajax/ajax_del_uservar.php?i='+id, ajax_delete_uservar_callback, id);

	//uses callback function to only display animated icon if request isnt already completed in 20ms
	//this removes the flickering display of animated image if you have very low response times
	setTimeout("show_ajax_anim()", 20);
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


//Sends an AJAX call to delete specified file
var delete_file_request = null;
function ajax_delete_file(id)
{
	delete_file_request = new AJAX();
	delete_file_request.GET('/ajax/ajax_del_file.php?i='+id, null);
}