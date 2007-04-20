<?
	require_once('config.php');

	$meta_rss[] = array("title" => "RSS News feed", "name" => "news", "channel" => 1);
	require('design_head.php');

	snowNews();

	require('design_foot.php');
?>