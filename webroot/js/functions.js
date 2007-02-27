//Toggles element with name "n" between visible and hidden
function toggle_element_by_name(n)
{
	var e = document.getElementById(n);
	if (e.style.display != 'none') e.style.display = 'none';
	else e.style.display = '';
}

function hide_element_by_name(n)
{
	var e = document.getElementById(n);
	e.style.display = 'none';
}