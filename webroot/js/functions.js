function trace(s)
{
	console.debug(s);
}

//Toggles element with name "n" between visible and hidden
function toggle_element_by_name(n)
{
	var e = document.getElementById(n);
	if (e.style.display != 'none') e.style.display = 'none';
	else e.style.display = '';
}

//Makes element with name "n" invisible in browser
function hide_element_by_name(n)
{
	var e = document.getElementById(n);
	e.style.display = 'none';
}

//Makes element with name "n" visible in browser
function show_element_by_name(n)
{
	var e = document.getElementById(n);
	e.style.display = '';
}

function add_div(e, idname, style)
{
	var c=document.createElement('div');

	c.setAttribute('id', idname);
	c.className = style;
	e.appendChild(c);

	return c;
}

function empty_element_by_name(n)
{
	var e=document.getElementById(n);

	while (e.hasChildNodes())
		e.removeChild(e.firstChild);
}

function empty_element(e)
{
	while (e.hasChildNodes()) e.removeChild(e.firstChild);
}


var zoomed_id = 0;
//closeup view of image file
function zoomImage(id)
{
	var e = document.getElementById('zoom_image');
	e.setAttribute('src', 'file.php?id='+id);

	zoomed_id = id;

	show_element_by_name('zoom_image_layer');
}

//closeup view of audio file
function zoomAudio(id,name)
{
	zoomed_id = id;

	//requires ext_flashobject.js
	var fo = new FlashObject('/flash/mp3_player.swf?n='+name+'&s=/janina/file.php?id='+id, 'animationName', '180', '45', '8', '#FFFFFF');
	fo.addParam('allowScriptAccess', 'sameDomain');
	fo.addParam('quality', 'high');
	fo.addParam('scale', 'noscale');
	fo.write('zoom_audio');

	show_element_by_name('zoom_audio_layer');
}


//used by image zoomer
function delete_selected_file()
{
	//Send AJAX call for file delete
	ajax_delete_file(zoomed_id);

	//Hide selected file
	hide_element_by_name('zoom_image_layer');
	hide_element_by_name('zoom_audio_layer');

	//remove zoomed_id thumbnail from file gadget
	hide_element_by_name('file_' + zoomed_id);

	zoomed_id = 0;
}


//Loads image id into holder-div
function loadImage(id, holder)
{
	var e = document.getElementById(holder);
	empty_element(e);

	var i = document.createElement('img');
	i.setAttribute('src', 'file.php?id='+id);
	e.appendChild(i);
}


function scroll_up(e, step, offs)
{
	e.scrollTop -= step;
	offs += step;

	if (offs<0) setTimeout(function() {scroll_up(e,step,offs)}, 1);
}

function scroll_down(e, step, offs)
{
	e.scrollTop += step;
	offs -= step;

	if (offs>0) setTimeout(function() {scroll_down(e,step,offs)}, 1);
}

//scroll the content of element name "n" by offset pixels. use negative value of offset to scroll up, positive to scroll down
function scroll_element_content(n,offs)
{
	var e = document.getElementById(n);

	if (offs>0) {
		setTimeout(function() {scroll_down(e,6,offs)}, 1);
	} else {
		setTimeout(function() {scroll_up(e,6,offs)}, 1);
	}
}