<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Admin feedback<br/><br/>';

	$list = getFeedback();
	foreach ($list as $row) {
		echo 'From ',nameLink($row['userId'], $row['userName']).' at '.$row['timeCreated'].':<br/>';
		echo $row['text'];
		echo '<hr/>';
	}
	
	require($project.'design_foot.php');
?>