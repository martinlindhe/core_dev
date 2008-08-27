<?php
/**
 * $Id$
 */

//FIXME: kunna se nyheter som ska publiceras i framtiden

require_once('find_config.php');
require_core('functions_news.php');

$session->requireAdmin();

require('design_admin_head.php');

echo '<h1>Manage news</h1> (<a href="admin_news_add.php">Add news</a>)';

echo '<br/><br/><h2>Unpublished news</h2>';
$list = getUnpublishedNews();
if ($list) {
	echo '<table><tr><td><b>Title</b></td><td><b>Time published</b></td></tr>';
	foreach ($list as $row)
	{
		echo '<tr><td>'.$row['timeToPublish'].':</td><td>';
		echo '<a href="'.$config['app']['web_root'].'news.php?News:'.$row['newsId'].'">'.$row['title'].'</a></td></tr>';
	}
	echo '</table>';
}

echo '<h2>Published news</h2>';
$list = getPublishedNews();
if ($list) {
	echo '<table><tr><td><b>Title</b></td><td><b>Time published</b></td></tr>';
	foreach ($list as $row)
	{
		echo '<tr><td>'.$row['timeToPublish'].':</td><td>';
		echo '<a href="'.$config['app']['web_root'].'news.php?News:'.$row['newsId'].'">'.$row['title'].'</a></td></tr>';
	}
	echo '</table>';
}

require('design_admin_foot.php');
?>
