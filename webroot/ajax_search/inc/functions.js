function add_link_node(e,t,url,s)
{
	var c=document.createElement('div');
	c.innerHTML='<a href="'+url+'" target="_blank">'+t+'</a>';
	c.className=s;
	e.appendChild(c);
	return c;
}

function empty_element(e)
{
	var x=document.getElementById(e);

	while (x.hasChildNodes())
		x.removeChild(x.firstChild);
}
