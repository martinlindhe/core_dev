<?
	require('config.php');

	require('design_head.php');

	echo createMenu($forum_menu, 'blog_menu');

	echo '<div class="forum_overview_group">';

	echo 'The 5 last posts in the forum:<br/><br/>';

	$list = getLastForumPosts(5);

	for ($i=0; $i<count($list); $i++) {
		echo getForumDepthHTML(FORUM_FOLDER, $list[$i]['itemId']);
		echo showForumPost($list[$i], '#'.($i+1));
	}

	echo '</div>';

	require('design_foot.php');
?>