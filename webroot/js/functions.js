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


function zoomImage(id)
{
	var e = document.getElementById('zoom_image');
	e.setAttribute('src', 'file.php?id='+id);
	//e.setAttribute('src', '/gfx/ajax_loading.gif');

	show_element_by_name('zoom_image_layer');
}

//Loads image id into targetdiv
function loadImage(id, targetdiv)
{
	var e = document.getElementById(targetdiv);
	
	empty_element(e);

	var i = document.createElement('img');
	i.setAttribute('src', 'file.php?id='+id);
	e.appendChild(i);

	trace('loading image ' + id + ' into ' + targetdiv);
	
}