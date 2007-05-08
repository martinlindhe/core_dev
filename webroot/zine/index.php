<?
	require_once('config.php');

	require('design_head.php');

	if (!$session->id) $session->showLoginForm();

	$content = '';
	$list = getLatestBlogs(5);
	for ($i=0; $i<count($list); $i++) {
		$content .= $list[$i]['timeCreated'].' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a> ';
		$content .= 'av '.nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
		$content .= $list[$i]['blogBody'];
	}
	
	echo $content;

	require('design_foot.php');
?>