<?php
	require_once('find_config.php');
	$session->requireSuperAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	echo createMenu($super_admin_menu, 'blog_menu');
	echo createMenu($super_admin_tools_menu, 'blog_menu');

	ob_start();
	phpinfo();
	$info = ob_get_contents();
	ob_end_clean();

	//hack to remove phpinfo()'s own CSS rules
	$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
	echo $info;

	require($project.'design_foot.php');
?>
