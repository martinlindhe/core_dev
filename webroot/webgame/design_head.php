<?createXHTMLHeader();?>
<div id="header">
	<div id="header-logo">
		webgame
	</div>
	<div id="header-items">
		<a href="<?=$config['web_root']?>wiki.php?Wiki:About">About</a>
		<a href="<?=$config['web_root']?>wiki.php?Wiki:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
		'index.php' => 'Home'
	);
	createMenu($menu);

	if ($session->isAdmin) {
		$menu = array(
			$config['core_web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin'
		);
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'?logout' => 'Logout'
		);
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);
?>
</div>

<div id="middle">
