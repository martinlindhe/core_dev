<?
	require_once('config.php');

	$meta_rss[] = array("title" => "All RSS News feeds", "name" => "news", "category" => 0);	//all news from all categories
	require('design_head.php');

	//echo '<a href="javascript:installSearchPlugin(\'http://87.227.76.225/adblock/searchplugin/filterset_search\')">click to install search plugin</a>';

	wiki('Home');
?>
<br/>
<a href="http://validator.w3.org/check?uri=referer" target="_blank">
<img src="/gfx/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" />
</a>

<?
	require('design_foot.php');
?>