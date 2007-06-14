<?
	require('config.php');

	require('design_head.php');

	echo 'De fem siste innlegg i debattforumet:<br><br>';

	$list = getLastForumPosts(5);

	for ($i=0; $i<count($list); $i++) {
		echo getForumFolderDepthHTML($list[$i]['itemId']);
		echo showForumPost($list[$i], '#'.($i+1));
	}

	require('design_foot.php');
?>