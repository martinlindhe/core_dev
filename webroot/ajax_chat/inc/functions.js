function wnd_url(u,x,y){w=window.open(u,'','width='+x+',height='+y+',toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no');w.focus;}
function toggle_div(o){var e=document.getElementById(o);if(e.style.display!="none")e.style.display="none"; else e.style.display="";}
function show_div(o){var e=document.getElementById(o);e.style.display="";}
function hide_div(o){var e=document.getElementById(o);e.style.display="none";}
function SetCookie(n,v){document.cookie=n+"="+escape(v);}

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

function empty_element(e) {
	var x=document.getElementById(e);

	while (x.hasChildNodes())
		x.removeChild(x.firstChild);
}


function display_timestamp(stamp)
{	
	//todo: timezone conversion stuff
	if (stamp == undefined) var curr = new Date();
	else var curr = new Date(stamp*1000) // from timestamp
	//return curr.toString();
	return curr.formatDate('D jS M H:i');
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