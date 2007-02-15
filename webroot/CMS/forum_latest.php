<?
	include('include_all.php');

	include('design_head.php');
	include('design_forum_head.php');

	$content = 'De fem siste innlegg i debattforumet:<br><br>';

	$list = getLastForumPosts($db, 5);

	for ($i=0; $i<count($list); $i++) {
		$content .= getForumFolderDepthHTML($db, $list[$i]['itemId']);
		$content .= showForumPost($db, $list[$i], '#'.($i+1));
	}

	echo '<div id="user_forum_content">';
	echo MakeBox('<a href="forum.php">Forum</a>|Siste innlegg', $content, 500);
	echo '</div>';

	include('design_forum_foot.php');
	include('design_foot.php');
?>