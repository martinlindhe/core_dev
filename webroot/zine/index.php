<?
	include_once('include_all.php');

	include('design_head.php');
	include('inc/noscript.html');


	$content = '';
	$list = getBlogsNewest($db, 5);
	for ($i=0; $i<count($list); $i++) {
		$content .= formatShortDate($list[$i]['timeCreated']).' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a> ';
		$content .= 'av '.nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
		$content .= $list[$i]['blogBody'];
	}
	
	echo $content;


	include('design_foot.php');
?>