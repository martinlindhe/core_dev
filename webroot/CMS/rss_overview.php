<?
	include('include_all.php');

	include('design_head.php');

	echo getInfoField($db, 'page_rss_overview').'<br>';

?>

<a href="rss/news.php">News feed</a><br>	

<?
	include('design_foot.php');
?>