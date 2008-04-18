<?createXHTMLHeader();?>
<div id="header">
	<div id="header-logo">
		dump
	</div>
	<div id="header-items">
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
		'index.php' => 'Home',
		'files.php' => 'File dump',
		'pastebin.php' => 'Pastebin'
		
	);
	createMenu($menu);

	if ($session->isAdmin) {
		$menu = array(
			$config['core']['web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin');
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'?logout' => 'Logout');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);
?>
</div>

<div id="middle">
