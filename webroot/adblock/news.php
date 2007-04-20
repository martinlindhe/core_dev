<?
	require_once('config.php');

	$meta_rss[] = array("title" => "RSS News feed", "url" => "/rss/news.php");
	require('design_head.php');

	snowNews();

	require('design_foot.php');
?>