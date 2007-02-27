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