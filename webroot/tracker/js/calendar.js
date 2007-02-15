/* time selector functions  */

var cal_current_id, cal_current_time, cal_current_mode;

function TS_SelectPredefined()
{
	enable_element_by_name('timespan');
	set_style_by_name('cal_ts_object_predefined', 'cal_ts_object_active');
	set_style_by_name('cal_ts_object_freeform', 'cal_ts_object_grayed');

	e = document.getElementById('timetype_freeform');
	e.checked = false;

	e = document.getElementById('timetype_timespan');
	e.checked = true;

	disable_element_by_name('from_year');
	disable_element_by_name('from_month');
	disable_element_by_name('from_day');

	disable_element_by_name('to_year');
	disable_element_by_name('to_month');
	disable_element_by_name('to_day');
}

function TS_SelectFreeform(id)
{
	e = document.getElementById('timetype_freeform');
	if (e.checked == true) return;
	e.checked = true;

	e = document.getElementById('timetype_timespan');
	e.checked = false;

	disable_element_by_name('timespan');
	set_style_by_name('cal_ts_object_freeform', 'cal_ts_object_active');
	set_style_by_name('cal_ts_object_predefined', 'cal_ts_object_grayed');

	enable_element_by_name('from_year');
	enable_element_by_name('from_month');
	enable_element_by_name('from_day');

	enable_element_by_name('to_year');
	enable_element_by_name('to_month');
	enable_element_by_name('to_day');
	
	//Submits initial selection
	Cal_FreeSelect(id);
}

function Cal_FreeSelect(id)
{
	var time_from = document.getElementById('from_year').value + '-' + document.getElementById('from_month').value + '-' + document.getElementById('from_day').value;
	var time_to 	= document.getElementById('to_year').value + '-' + document.getElementById('to_month').value + '-' + document.getElementById('to_day').value;

	var time = time_from + '_' + time_to;
	Cal_Select(id, time, 'f');
}


/* väljer en tidsperiod i kalendern, läser in data för tidsperioden med ajax & visar i cal_selection_body */
//time: timestamp start
//mode: "d" (day), "w" (week) or "m" (month)
function Cal_Select(id,time,mode)
{
	if (cal_current_id) {
		trace('Another request is already in progress');
		return;
	}

	var body = 'cal_selection_body';
	var e = document.getElementById(body);

	show_element( document.getElementById('cal_selection_holder') );

	set_style_by_name('cal_selection_expander', 'cal_piece_shrink');

	//Clears the element from childs and shows a "loading" animated gif
	empty_element(e);
	add_div(e, 'Cal_AJAX_content', 'ajax_content_progress');
	show_element(e);


	trace('Loading data for track point #' + id + ' for ' + time + ', mode ' + mode);

	//Set global variables to hold id and timespan for Cal_Update_SelectionInfo() function
	cal_current_id = id;
	cal_current_time = time;
	cal_current_mode = mode;

	var request = XMLRequest();
	
	var filename = 'ajax_selection_info.php?id=' + id + '&' + mode + '=' + time;

	request.onreadystatechange = function() { Cal_Update_SelectionInfo(request); };
	request.open('GET', filename, true);
	request.send(null);
}

function Cal_Update_SelectionInfo(handle)
{
	if (handle.readyState != 4) {
		//Requesten är ännu inte komplett
		return;
	}
	
	if (handle.status && handle.status != 200) {
		//Något gick snett på server-sidan
		alert('Servern returnerade felkod ' + handle.status);
		return;
	}


	//Vi visar resultatet
	var e = document.getElementById('cal_selection_body');
	

	var root_node = handle.responseXML.getElementsByTagName('d').item(0);
	if (!root_node) return;

	var total, unique;
	
	var items = root_node.childNodes;
	for (var i=0; i<items.length; i++)
	{
		if (items[i].nodeName == 't') total = items[i].firstChild.nodeValue;
		if (items[i].nodeName == 'u') unique = items[i].firstChild.nodeValue;
	}

	var x = document.getElementById('Cal_AJAX_content');	//skapades när ajax-anropet utfördes
	
	set_style(x, 'ajax_content_loaded');
	
	switch (cal_current_mode)
	{
		case 'd': //Show day selection
			add_text_node(x, 'Selected ' + display_date(cal_current_time) + ':');
			break;

		case 'w':	//Show week selection
			//fixme: +10000 eftersom week returnerar fel veckonummer, utgår från söndagar? eller bara nån bugg i formatDate('W')?
			add_text_node(x, 'Selected Week ' + display_week(cal_current_time+10000) + ':');
			break;

		case 'm':	//Show month selection
			add_text_node(x, 'Selected ' + display_month(cal_current_time) + ':');
			break;
			
		case 'f':	//Show free form selection
			add_text_node(x, 'Selected freeform ' + cal_current_time + ':');
			break;
	}
	
	add_br_node(x);

	add_text_node(x, 'Total ' + total + ' views registered');
	add_text_node(x, 'From ' + unique + ' unique users');

	add_br_node(x);

	var param = '?id=' + cal_current_id + '&' + cal_current_mode + '=' + cal_current_time;
	add_image_link_node(x, 'admin_show_trackpoint_data.php' + param, 'design/placeholder.png');								//Details
	add_image_link_node(x, 'admin_show_trackpoint_referrers.php' + param, 'design/placeholder.png');					//Referrers
	add_image_link_node(x, 'admin_show_trackpoint_location_details.php' + param, 'design/placeholder.png');		//Locations
	add_image_link_node(x, 'admin_show_trackpoint_browserstats.php' + param, 'design/placeholder.png');				//Browser stats
	add_image_link_node(x, 'admin_show_trackpoint_unique_visitors.php' + param, 'design/placeholder.png');		//Unique visitors

	cal_current_id = cal_current_time = cal_current_mode = 0;
}

function display_timestamp(stamp)
{
	if (stamp == undefined) return 'invalid timestamp';

	var curr = new Date(stamp*1000); // from timestamp

	return curr.formatDate('D jS M H:i');
}

function display_date(stamp)
{
	if (stamp == undefined) return 'invalid timestamp';

	var curr = new Date(stamp*1000); // from timestamp

	return curr.formatDate('D j:S M');
}

function display_week(stamp)
{
	if (stamp == undefined) return 'invalid timestamp';

	var curr = new Date(stamp*1000); // from timestamp

	return curr.formatDate('W');
}

function display_month(stamp)
{
	if (stamp == undefined) return 'invalid timestamp';

	var curr = new Date(stamp*1000); // from timestamp

	return curr.formatDate('F Y');
}

//highlighhts all div's named weekXX_1 - weekXX_7
function HighlightWeek(week)
{
	//trace('highlighting week ' + week);
	for (var i = 1; i<=7; i++) {
		var e = document.getElementById('week' + week + '_' + i);
		if (e) {
			e.orgClassName = e.className;
			e.className = 'cal_week_hover';
		}
	}
}

//restores div classes to orginal state
function UnHighlightWeek(week)
{
	//trace('restoring week ' + week);
	for (var i = 1; i<=7; i++) {
		var e = document.getElementById('week' + week + '_' + i);
		if (e) e.className = e.orgClassName;
	}
}