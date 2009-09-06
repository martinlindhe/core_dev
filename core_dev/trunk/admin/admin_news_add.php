<?php
/**
 * $Id$
 */

require_once('find_config.php');

$h->session->requireAdmin();

require('design_admin_head.php');

if (!empty($_POST['title']) && !empty($_POST['body']) && !empty($_POST['publish']) ) {
	$check = addNews($_POST['title'], $_POST['body'], $_POST['publish'], $_POST['rss'], $_POST['news_cat']);

	if ($check) {
		echo 'News article successfully added!';
	} else {
		echo 'There were problems adding news article. Most likley you pressed submit more than once.';
	}

	require('design_admin_foot.php');
	die;
}

echo '<h1>Add news</h1>';

echo '<form name="add_news" method="post" action="'.$_SERVER['PHP_SELF'].'">';
echo '<input type="hidden" name="rss" value="0"/>';
echo 'Title:<br/>';
echo '<input type="text" name="title" size="50"/><br/>';
echo 'Text:<br/>';
echo '<textarea name="body" cols="60" rows="16"></textarea><br/>';

echo 'Choose category: ';
echo xhtmlSelectCategory(CATEGORY_NEWS, 0, 'news_cat').'<br/>';

echo '<input name="rss" id="rss_check" type="checkbox" class="checkbox" value="1" checked="checked"/>';
echo '<label for="rss_check">';
echo '<img src="'.$config['core']['web_root'].'gfx/icon_rss.png" width="16" height="16" alt="RSS enabled" title="RSS enabled"/>';
echo 'Include this news in the RSS feed</label><br/><br/>';

echo 'Time for publication:<br/>';
echo '<input type="text" name="publish" value="NOW" onclick="document.forms.add_news.publish.value=\''.date('Y-m-d H:i').'\';"/> ';
echo '<input type="submit" class="button" value="Store news"/><br/>';
echo '</form>';

require('design_admin_foot.php');
?>
