//sends debug output to firebug, if installed
function trace(s)
{
	//if (this.firebug || IEerBug) console.debug(s);
	if (this.firebug) console.debug(s);
}


/********************************************
	Javascript functions for DOM manipulation

********************************************/

//Toggles element with name "n" between visible and hidden
function toggle_element_by_name(n)
{
	var e = document.getElementById(n);
	if (e.style.display != 'none') e.style.display = 'none';
	else e.style.display = '';
}

//Shows element specified by name in "n"
function show_element_by_name(n)
{
	var e = document.getElementById(n);
	e.style.display = '';
}

function hide_element_by_name(n)
{
	var e=document.getElementById(n);
	e.style.display = 'none';
}

function enable_element_by_name(e)
{
	var o = document.getElementById(e);
	o.removeAttribute('disabled');
}

function disable_element_by_name(e)
{
	var o = document.getElementById(e);
	o.setAttribute('disabled', true);
}

//Shows element specified by id in "e", get the id with document.getElementById()
function show_element(e)
{
	e.style.display = '';
}

function hide_element(e)
{
	e.style.display = 'none';
}

// Deletes element id specified in "e" and makes a empty copy of it.
// This will break code relying on previous set-up pointers to "e", but is alot faster. Use with care
function empty_element_fast(e)
{
	// perform a shallow clone on the element
	nObj = e.cloneNode(false);
	// insert the cloned element into the DOM before the original one
	e.parentNode.insertBefore(nObj, e);
	// remove the original element
	e.parentNode.removeChild(e);
}

// Empties element id specified in "e"
//But this is considerable slower than empty_element_fast(), see http://slayeroffice.com/test/clone_vs_firstchild.html
function empty_element(e)
{
	while (e.hasChildNodes())
		e.removeChild(e.firstChild);
}


function add_div(e, idname, style)
{
	var c=document.createElement('div');

	c.setAttribute('id', idname);
	c.className = style;
	e.appendChild(c);

	return c;
}




//creates a div element, adds a text node to the div element and attaches the result to the element specified in "e"
function add_text_node(e, t)
{
	var c = document.createElement('div');
	e.appendChild(c);

	var txt=document.createTextNode(t);
	c.appendChild(txt);
}

//Adds a <br> element to "e"
function add_br_node(e)
{
	var c = document.createElement('br');
	e.appendChild(c);
}


//Creates a <a href> node
//  url - länken
//	t - texten
function add_link_node(e, url, t)
{
	var a = document.createElement('a');
	a.setAttribute('href', url);
	a.appendChild( document.createTextNode(t) );

	e.appendChild(a);
}

//url - länken
//i - bildlänken
function add_image_link_node(e, url, i)
{
	var a = document.createElement('a');
	a.setAttribute('href', url);
	
	var img = document.createElement('img');
	img.src = i;
	a.appendChild( img );

	e.appendChild(a);
}

//Adds a image element to the element "e"
// - i is the filename of the image to show
// - t is the text that is displayed on hover
function add_image_node(e, i, t)
{
	var img=document.createElement('img');

	img.src = i;
	img.title = t;
	e.appendChild(img);
}








/// RESTERANDE FUNKTIONER:::::::


function select_text(n){t=eval('document.'+n);t.focus();t.select();}
function anon_popup(u)
{
	w=window.open(u,'','width=600,height=500,toolbar=yes,menubar=no,location=yes,scrollbars=no,resizable=no,directories=no,status=no');
	w.focus;
	return false;
}


function set_style(e, style)
{
	e.className = style;
}

function set_style_by_name(n, c)
{
	var o = document.getElementById(n);
	o.className=c;
}

function toggle_class(e,c1,c2)
{
	var o=document.getElementById(e);
	if (o.className==c1) {
		o.className=c2;
	} else {
		o.className=c1;
	}
}


function MM_swapImgRestore(){var i,x,a=document.MM_sr;for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;}
function MM_findObj(n,d){var p,i,x;if(!d) d=document;if((p=n.indexOf("?"))>0&&parent.frames.length){d=parent.frames[n.substring(p+1)].document;n=n.substring(0,p);}if(!(x=d[n])&&d.all) x=d.all[n];for(i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);if(!x && d.getElementById) x=d.getElementById(n);return x;}
function MM_swapImage(){var i,j=0,x,a=MM_swapImage.arguments;document.MM_sr=new Array;for(i=0;i<(a.length-2);i+=3)if((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x;if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}}






//Returnerar ett XMLHttpRequest objekt
function XMLRequest()
{
	var handle = false;

	if (window.XMLHttpRequest) {
		// Firefox, Opera, Safari
		handle = new XMLHttpRequest();
		if (handle.overrideMimeType) {
			handle.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) {
		// Internet Explorer
		try {
			handle = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				handle = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}

	if (!handle) {
		alert('Ger upp! Kan inte skapa ett XMLHTTP-objekt');
		return false;
	}

	return handle;
}


//todo: fixa funktionsnamnet
function toggle_div_w_img(o,i,n) {
	var e=document.getElementById(o);
	if (e.style.display!="none") {
		e.style.display="none";
		MM_swapImgRestore();
	} else{
		e.style.display="";
		MM_swapImage(i,'',n,1);
	}
}


preload = new Array(
	'design/ajax_loading.gif',
	'design/arrow_left.png',
	'design/arrow_right.png',
	'design/arrow_left_gray.png',
	'design/arrow_right_gray.png',
	'design/arrow_down.png',
	'design/arrow_up.png',
	'design/header_bg.jpg',
	'design/placeholder.png'
);

function ImagePreload()
{
	if (!document.images) return;

	trace('ImagePreload() called');

	preImages = new Array();
	for (i=0; i<preload.length; i++)
	{
		preImages[i] = new Image();
		trace('Preloading ' + preload[i]);
		preImages[i].src=preload[i];
	}

	trace('ImagePreload() completed');
}