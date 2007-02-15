//todo: fixa för firefox 2
function addFilterSearchEngine()
{
	if ((typeof window.sidebar=='object') && (typeof window.sidebar.addSearchEngine=='function')) {
		window.sidebar.addSearchEngine(
			'http://martin2/adblock/searchplugin/filterset_search.src',
			'http://martin2/adblock/searchplugin/filterset_search.png',
			'Filterset Search',
			'0'
		);
	} else {
		alert("Sorry, you need a Mozilla-based browser to install a search plugin.");
	}
}


//Toggles element with name "n" between visible and hidden
function toggle_element_by_name(n)
{
	var e = document.getElementById(n);
	if (e.style.display != 'none') e.style.display = 'none';
	else e.style.display = '';
}