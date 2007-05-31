<?createXHTMLHeader()?>
<div id="left_menu">
<?
	$menu = array(
		'index.php' => 'Home'
	);
	createMenu($menu);

	if ($session->isAdmin) {
		$menu = array(
			'perform_work.php' => 'Perform work',
			$config['core_web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin'
		);
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array('?logout' => 'Log out');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);
?>
</div>

<div id="main_body">
