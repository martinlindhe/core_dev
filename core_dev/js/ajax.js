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
	var _busy = false;

	this.GET = GET;
	this.GET_raw = GET_raw;
	this.POST = POST;
	this.POST_raw = POST_raw;
	this.ResultReady = ResultReady;
	this.EmptyResponse = EmptyResponse;

	if (window.XMLHttpRequest) { // Mozilla, Safari, Opera...
		this._request = new XMLHttpRequest();
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
		if (this._request.overrideMimeType) this._request.overrideMimeType('text/xml');
		if (callback) this._request.onreadystatechange = function() { callback(callbackparam); }
		try {
			this._busy = true;
			this._request.open('GET', url, true);
			this._request.send(null);
		} catch (e) {
			alert('failed to open AJAX call. adblock software might be the cause');
		}
	}

	// Performs an GET-request expected to return anything, like raw text or html
	function GET_raw(url, callback, callbackparam, params)
	{
		if (!this._request) return false;
		if (callback) {
			this._request.onreadystatechange = function() { callback(callbackparam); }
		}
		try {
			this._busy = true;
			this._request.open('GET', url, true);
			this._request.send(null);
		} catch (e) {
			alert('failed to open AJAX call. adblock software might be the cause');
		}
	}

	// Performs an POST-request expected to return XML
	function POST(url, callback, callbackparam, params)
	{
		if (!this._request) return false;
		if (this._request.overrideMimeType) this._request.overrideMimeType('text/xml');
		if (callback) this._request.onreadystatechange = function() { callback(callbackparam); }
		try {
			this._busy = true;
			this._request.open('POST', url, true);
			this._request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			this._request.send(params);
		} catch (e) {
			alert('failed to open AJAX call. adblock software might be the cause');
		}
	}

	// Performs an POST-request expected to return anything, like raw text or html
	function POST_raw(url, callback, callbackparam, params)
	{
		if (!this._request) return false;
		if (callback) this._request.onreadystatechange = function() { callback(callbackparam); }
		try {
			this._busy = true;
			this._request.open('POST', url, true);
			this._request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
			this._request.send(params);
		} catch (e) {
			alert('failed to open AJAX call. adblock software might be the cause');
		}
	}

	function ResultReady()
	{
		if (!this._request || this._request.readyState != 4) return false;
		if (this._request.status == 200) {
			this._busy = false;
			return true;
		}

		return false;
	}

	//Returns true if 'name' is the root tag of this xml object
	function EmptyResponse(name)
	{
		return this._request.responseXML.getElementsByTagName(name).item(0);
	}
}
//todo: i show_ajax_anim kolla om den fortfarande behövs visas!
var ajax_anim_abort = 0;
function show_ajax_anim() { if (!ajax_anim_abort) show_element_by_name('ajax_anim'); ajax_anim_abort = 0; }
function hide_ajax_anim() { hide_element_by_name('ajax_anim'); }

//todo: försök kom på ett mer standardiserat interface till GET-funktionen så att mindre sådan här init&callback kod behövs


//Sends an AJAX call to submit someones vote for a site poll
var poll_request = null;
function ajax_poll(id,opt)
{
	poll_request = new AJAX();
	poll_request.GET(_ext_core+'ajax_poll.php?i='+id+'&o='+opt+_ext_ref, null);
}


//Sends an AJAX call to delete specified file
var delete_file_request = null;
function ajax_delete_file(id)
{
	delete_file_request = new AJAX();
	delete_file_request.GET(_ext_core+'ajax_del_file.php?i='+id+_ext_ref, null);
}

var fileinfo_request = null;
function ajax_get_fileinfo(id)
{
	fileinfo_request = new AJAX();
	fileinfo_request.GET_raw(_ext_core+'ajax_fileinfo.php?i='+id+_ext_ref, ajax_get_fileinfo_callback);

	setTimeout("show_ajax_anim()", 20);
}

function ajax_get_fileinfo_callback()
{
	if (!fileinfo_request.ResultReady()) return;

	zoomShowFileInfo(fileinfo_request._request.responseText);
	ajax_anim_abort = true;
	hide_ajax_anim();

	fileinfo_request = null;
}

var rategadget_request = null;
function ajax_get_rategadget(id,type)
{
	rategadget_request = new AJAX();
	rategadget_request.GET_raw(_ext_core+'ajax_rategadget.php?i='+id+'&t='+type+_ext_ref, ajax_get_rategadget_callback);

	setTimeout("show_ajax_anim()", 20);
}

function ajax_get_rategadget_callback()
{
	if (!rategadget_request.ResultReady()) return;

	fill_element_by_name('rate_file', rategadget_request._request.responseText);
	ajax_anim_abort = true;
	hide_ajax_anim();

	rategadget_request = null;
}

var rate_request = null;
function ajax_rate(type,id,val)
{
	rate_request = new AJAX();
	rate_request.GET_raw(_ext_core+'ajax_rate.php?i='+id+'&t='+type+'&v='+val+_ext_ref, ajax_rate_callback);
}

function ajax_rate_callback()
{
	if (!rate_request.ResultReady()) return;

	fill_element_by_name('rate_file', rate_request._request.responseText);
	ajax_anim_abort = true;

	rate_request = null;
}

function submit_apc_upload(id)
{
	//submit form
	document.ajax_file_upload.submit();
	show_element_by_name('file_gadget_apc_progress');

	ajax_get_upload_progress(id,_ext_ref);

	return false;
}

var upload_progress_request = null;
function ajax_get_upload_progress(id)
{
//	if (upload_progress_request == null) {
		upload_progress_request = new AJAX();
		upload_progress_request.GET_raw(_ext_core+'ajax_upload_progress.php?s='+id+_ext_ref, ajax_get_upload_progress_callback);
//	}
	setTimeout("ajax_get_upload_progress("+id+",'"+_ext_ref+"')", 500);
}

function ajax_get_upload_progress_callback()
{
	if (!upload_progress_request.ResultReady()) return;

	var e = document.getElementById('file_gadget_apc_progress');
	empty_element(e);

	e.innerHTML = upload_progress_request._request.responseText;

	upload_progress_request = null;
}


//sets search cities according to region id "id"
var searchcities_request = null;
function ajax_set_search_cities(id)
{
	searchcities_request = new AJAX();
	searchcities_request.GET_raw(_ext_core+'ajax_search_cities.php?i='+id+_ext_ref, ajax_set_search_cities_callback);
}

function ajax_set_search_cities_callback()
{
	if (!searchcities_request.ResultReady()) return;

	set_div_content('ajax_cities', searchcities_request._request.responseText);

	searchcities_request = null;
}
