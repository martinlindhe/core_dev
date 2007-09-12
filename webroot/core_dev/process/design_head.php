<?createXHTMLHeader();?>
<div id="header">
	<div id="header-logo">
		process site
	</div>
	<div id="header-items">
		<a href="<?=$config['web_root']?>wiki.php?Wiki:AboutProcess">About</a>
		<a href="<?=$config['web_root']?>wiki.php?Wiki:HelpProcess">Help</a>
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
		'index.php' => 'Home',
		'show_queue.php' => 'Show work queue');
	createMenu($menu);

	if ($session->isAdmin) {
		$menu = array(
			$config['core_web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin');
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'add_order_http.php' => 'Add order',
			'?logout' => 'Logout');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);
?>
</div>

<div id="middle">
