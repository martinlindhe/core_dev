<?
	require_once('config.php');

	require('design_head.php');

	if (!$session->id) $session->showLoginForm();

	$list = getLatestBlogs(5);
	foreach ($list as $row) {
		echo $row['timeCreated'].' - <a href="blog_show.php?id='.$row['blogId'].'">'.$row['blogTitle'].'</a> ';
		echo 'by '.nameLink($row['userId'], $row['userName']).'<br>';
		echo $row['blogBody'];
	}

	require('design_foot.php');
?>