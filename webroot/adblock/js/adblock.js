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

