<?php
$head = new xhtml_header();
$head->addCss('css/site.css');
echo $head->render();

?>
<div id="header">
	<div id="header-logo">
		process site
	</div>
	<div id="header-items">
		<a href="<?php echo $config['app']['web_root']?>wiki.php?Wiki:HelpProcess">Help</a>
	</div>
</div>
<div id="leftmenu">
<?php

$menu = array(
	'index.php' => 'Home'
);
echo xhtmlMenu($menu);

if ($h->session->id) {
	$menu = array(
		'show_files.php' => 'Show uploaded files',
		'show_queue.php' => 'Show work queue',
		'http_upload.php' => 'Upload file',
		'http_download.php' => 'Request a fetch',
		'?logout' => 'Logout');
	echo xhtmlMenu($menu);
}

if ($h->session->isAdmin) {
	$menu = array(
		'process_queue.php' => 'FORCE process',
		$config['core']['web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin'
	);
	echo xhtmlMenu($menu);
}
?>
</div>

<div id="middle">
