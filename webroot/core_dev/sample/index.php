<?
	require_once('config.php');

	$meta_rss[] = array("title" => "All RSS News feeds", "name" => "news", "category" => 0);	//all news from all categories
	require('design_head.php');

	wiki('Home');

?>
<br/>
<a href="dragdrop.php">dragdrop sample</a>


<?
	require('design_foot.php');
?>