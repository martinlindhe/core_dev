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
		'show_files.php' => 'Show uploaded files',
		'show_queue.php' => 'Show work queue',
		'show_events.php' => 'Show event log'
	);
	createMenu($menu);

	if ($session->isAdmin) {
		$menu = array(
			'process_queue.php' => 'FORCE process',
			$config['core_web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin'
		);
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'http_upload.php' => 'Upload file',
			'http_download.php' => 'Request a fetch',
			'?logout' => 'Logout');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);
?>
</div>

<div id="middle">
