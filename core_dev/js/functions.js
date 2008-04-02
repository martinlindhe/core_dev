function trace(s)
{
	console.debug(s);
}

function urlencode(str)	//function borrowed from http://www.albionresearch.com/misc/urlencode.php
{
	var SAFECHARS = "0123456789" +					// Numeric
					"ABCDEFGHIJKLMNOPQRSTUVWXYZ" +	// Alphabetic
					"abcdefghijklmnopqrstuvwxyz" +
					"-_.!~*'()";					// RFC2396 Mark characters
	var HEX = "0123456789ABCDEF";

	var encoded = "";
	for (var i = 0; i < str.length; i++ ) {
		var ch = str.charAt(i);
	    if (ch == " ") {
		    encoded += "+";				// x-www-urlencoded, rather than %20
		} else if (SAFECHARS.indexOf(ch) != -1) {
		    encoded += ch;
		} else {
		    var charCode = ch.charCodeAt(0);
			if (charCode > 255) {
			    alert( "Unicode Character '"
                        + ch
                        + "' cannot be encoded using standard URL encoding.\n" +
				          "(URL encoding only supports 8-bit characters.)\n" +
						  "A space (+) will be substituted." );
				encoded += "+";
			} else {
				encoded += "%";
				encoded += HEX.charAt((charCode >> 4) & 0xF);
				encoded += HEX.charAt(charCode & 0xF);
			}
		}
	}

	return encoded;
};

//Toggles element with name "n" between visible and hidden
function toggle_element_by_name(n)
{
	var e = document.getElementById(n);
	e.style.display = (e.style.display?'':'none');
}

//Makes element with name "n" invisible in browser
function hide_element_by_name(n)
{
	var e = document.getElementById(n);
	if (!e) {
		alert('fixme: element ' + n + ' not found');
		return;
	}
	e.style.display = 'none';
}

//Makes element with name "n" visible in browser
function show_element_by_name(n)
{
	var e = document.getElementById(n);
	if (!e) {
		alert('fixme: element ' + n + ' not found');
		return;
	}
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

function fill_element_by_name(n,txt)
{
	var e = document.getElementById(n);
	empty_element(e);

	e.innerHTML = txt;
}

function empty_element(e)
{
	while (e.hasChildNodes()) e.removeChild(e.firstChild);
}

//checks if image n has been loaded, waits until it is done otherwise
function image_loaded(n)
{
	var e = document.getElementById(n);

	trace('image_loaded('+n+') w:'+e.width+',h:'+e.height);

	//IE. fixme:untested
	if (!e.complete) {
		return false;
	}

	//Firefox
	if (typeof e.naturalWidth != "undefined" && e.naturalWidth == 0) {
		return false;
	}

	return true;
}

//resizes current window to the same size as image n
function resize_wnd_to_img(n)
{
	var e = document.getElementById(n);
	trace('resize_wnd_to_img('+n+') w:'+e.width+',h:'+e.height);
	var NS = (navigator.appName=="Netscape")?true:false; 

	iWidth = (NS)?window.innerWidth:document.body.clientWidth;
	iHeight = (NS)?window.innerHeight:document.body.clientHeight;
	iWidth = e.width - iWidth;
	iHeight = e.height - iHeight;
	window.resizeBy(iWidth, iHeight);
	self.focus();
}

function pause(ms)
{
	var now = new Date();
	var exitTime = now.getTime() + ms;

	while(true) {
		now = new Date();
		if(now.getTime() > exitTime) return;
	}
}

/* sends a ajax poll submit */
function submit_poll(id,opt)
{
	//alert('fixme: submit_poll() add ajax submit. id ' + id + ', opt ' + opt);
	ajax_poll(id,opt);

	hide_element_by_name('poll'+id);
	show_element_by_name('poll_voted'+id);
}

function get_poll_csv(id)
{
	var w = window.open(_ext_core+'csv_poll.php?id='+id+_ext_ref, '_blank');
	w.focus();
}

//Loads image id into holder-div
function loadImage(id, holder)
{
	var e = document.getElementById(holder);
	empty_element(e);

	var i = document.createElement('img');
	i.setAttribute('src', _ext_core+'file.php?id='+id+_ext_ref);
	e.appendChild(i);


	var j = document.getElementById('image_comments_iframe');
	j.setAttribute('src', _ext_core+'html_imgcomments.php?i='+id+_ext_ref);
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

//expands size of <input type="text" id="n">
function expand_input(e)
{
	var x=document.getElementById(e);
	if (x.size < 120) x.size += 20;
	//fixme: set focus to start of inputted text after resizing
}

function urlOpen(u)
{
	document.location = u;
}

function in_arr(arr,val)
{
	for (var i=0; i<arr.length; i++) {
		if (arr[i] == val) return true;
	}
	return false;
}

function arr_del(arr,val)
{
	for (var i=0; i<arr.length; i++) {
		if (arr[i] == val) delete arr[i];	//fixme: this will leave arr[i] undefined, arr.length will not be changed
	}
}

function set_class(e,c){
	var x=document.getElementById(e);
	x.className=c;
}

function add_node(e,t,s) {
	var c=document.createElement('div');
	e.appendChild(c);
	var tx=document.createTextNode(t);
	c.appendChild(tx);
	c.className=s;
	return c;
}

function add_node_and_focus(e,t,s) {
	var c=add_node(e,t,s);
	c.scrollIntoView(false);
}

/* focuses on the faq item #i */
function faq_focus(n) {
	e = document.getElementById('faq_'+n);
	if (!e) return;
	e.style.display = '';	//show

	for (i=0;i<=100;i++) {
		if (i==n) continue;
		e = document.getElementById('faq_'+i);
		if (!e) return;
		e.style.display = 'none';	//hide

		hide_element_by_name('faq_edit_'+i);
		show_element_by_name('faq_holder_'+i);
	}
}

//used with $files->showThumbnails(). displays comments div and shrinks currently displayed image
//n=image id currently displayed
var comments_shown = false;
var org_w, org_h;
function show_image_comments(n)
{
	e = document.getElementById('img_'+n);

	if (!comments_shown) {
		show_element_by_name('image_comments_content');
		comments_shown = true;
		org_w = e.width;
		org_h = e.height;
		//fixme: shrinking image here dont work properly, first time it resizes it gets wrong then it gets correct
		//e.width *= 0.5;
		//e.height *= 0.5;
		return;
	}

	hide_element_by_name('image_comments_content');
	comments_shown = false;
	e.width = org_w;
	e.height = org_h;
}

//shows fileId i in a new window, displayed as a image
function popup_imgview(i,w,h)
{
	if(!w)w=300;
	if(!h)h=200;
	p = window.open(_ext_core+'popup_imgview.php?id='+i+_ext_ref, 'name', 'width='+w+',height='+h);
	if (window.focus) p.focus();
}

//sets current homepage,works for ie only
function set_homepage(u)
{
	this.style.behavior='url(#default#homepage)';
	this.setHomePage(u);
}

//sets element name "n" to the html content of "txt"
function set_div_content(n, txt)
{
	var e = document.getElementById(n);
	empty_element(e);

	e.innerHTML = txt;
	show_element_by_name(n);
}











/**
 * Format selection
 *
 * Apply tagOpen/tagClose to selection in textarea, use sampleText instead
 * of selection if there is none. Copied and adapted from phpBB
 *
 * @author phpBB development team
 * @author MediaWiki development team
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Jim Raynor <jim_raynor@web.de>
 */
function insertTags(edid,tagOpen, tagClose, sampleText) {
  var txtarea = document.getElementById(edid);
  // IE
  if(document.selection  && !is_gecko) {
    var theSelection = document.selection.createRange().text;
    var replaced = true;
    if(!theSelection){
      replaced = false;
      theSelection=sampleText;
    }
    txtarea.focus();

    // This has change
    var text = theSelection;
    if(theSelection.charAt(theSelection.length - 1) == " "){// exclude ending space char, if any
      theSelection = theSelection.substring(0, theSelection.length - 1);
      r = document.selection.createRange();
      r.text = tagOpen + theSelection + tagClose + " ";
    } else {
      r = document.selection.createRange();
      r.text = tagOpen + theSelection + tagClose;
    }
    if(!replaced){
      r.moveStart('character',-text.length-tagClose.length);
      r.moveEnd('character',-tagClose.length);
    }
    r.select();
  // Mozilla
  } else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
    replaced = false;
    var startPos = txtarea.selectionStart;
    var endPos   = txtarea.selectionEnd;
    if(endPos - startPos){ replaced = true; }
    var scrollTop=txtarea.scrollTop;
    var myText = (txtarea.value).substring(startPos, endPos);
    if(!myText) { myText=sampleText;}
    if(myText.charAt(myText.length - 1) == " "){ // exclude ending space char, if any
      subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + " ";
    } else {
      subst = tagOpen + myText + tagClose;
    }
    txtarea.value = txtarea.value.substring(0, startPos) + subst +
                    txtarea.value.substring(endPos, txtarea.value.length);
    txtarea.focus();

    //set new selection
    if(replaced){
      var cPos=startPos+(tagOpen.length+myText.length+tagClose.length);
      txtarea.selectionStart=cPos;
      txtarea.selectionEnd=cPos;
    }else{
      txtarea.selectionStart=startPos+tagOpen.length;
      txtarea.selectionEnd=startPos+tagOpen.length+myText.length;
    }
    txtarea.scrollTop=scrollTop;
  // All others
  } else {
    var copy_alertText=alertText;
    var re1=new RegExp("\\$1","g");
    var re2=new RegExp("\\$2","g");
    copy_alertText=copy_alertText.replace(re1,sampleText);
    copy_alertText=copy_alertText.replace(re2,tagOpen+sampleText+tagClose);

    if (sampleText) {
      text=prompt(copy_alertText);
    } else {
      text="";
    }
    if(!text) { text=sampleText;}
    text=tagOpen+text+tagClose;
    //append to the end
    txtarea.value += "\n"+text;

    // in Safari this causes scrolling
    if(!is_safari) {
      txtarea.focus();
    }

  }
  // reposition cursor if possible
  if (txtarea.createTextRange){
    txtarea.caretPos = document.selection.createRange().duplicate();
  }
}
