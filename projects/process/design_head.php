<?createXHTMLHeader();?>
<div id="header">
	<div id="header-logo">
		process site
	</div>
	<div id="header-items">
		<a href="<?=$config['app']['web_root']?>wiki.php?Wiki:HelpProcess">Help</a>
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
			$config['core']['web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin'
		);
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'http_upload.php' => 'Upload file',
			'http_download.php' => 'Request a fetch',
			'http_monitor.php' => 'Monitor server',
			'?logout' => 'Logout');
		createMenu($menu);
	}
?>
</div>

<div id="middle">
