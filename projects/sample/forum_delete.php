<?php

require_once('config.php');
$session->requireAdmin();

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;	//invalid request
$itemId = $_GET['id'];

require('design_head.php');

echo createMenu($forum_menu, 'blog_menu');

echo getForumDepthHTML(FORUM_FOLDER, $itemId);

$item = getForumItem($itemId);
if ($item) echo showForumPost($item, '', false);
	
if (confirmed('Are you sure you want to delete this forum post?', 'id', $itemId)) {

	deleteForumItemRecursive($itemId);
	echo 'The forum and all subforums has been deleted';
}

require('design_foot.php');

?>
