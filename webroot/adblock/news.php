<?
	require_once('config.php');

	$meta_rss[] = array("title" => "RSS News feed", "url" => "/core/rss_news.php".getProjectPath(false));
	require('design_head.php');

	snowNews();

	require('design_foot.php');
?>