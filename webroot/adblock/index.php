<?
	require_once('config.php');

	$meta_rss[] = array("title" => "All RSS News feeds", "name" => "news", "category" => 0);	//all news from all categories
	require('design_head.php');

	//echo '<a href="javascript:installSearchPlugin(\'http://87.227.76.225/adblock/searchplugin/filterset_search\')">click to install search plugin</a>';

	wiki('Home');

	require('design_foot.php');
?>