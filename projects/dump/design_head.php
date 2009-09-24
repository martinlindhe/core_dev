<?php

$header = new xhtml_header();
echo $header->render();

?>
<div id="header">
	<div id="header-logo">
		dump
	</div>
	<div id="header-items">
	</div>
</div>
<div id="leftmenu">
<?php

$menu = array(
	'index.php' => 'Home',
	'files.php' => 'File dump',
	'pastebin.php' => 'Pastebin'
);
echo xhtmlMenu($menu);

if ($session->isAdmin) {
	$menu = array(
		$config['core']['web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin');
	echo xhtmlMenu($menu);
}

if ($session->id) {
	$menu = array(
		'?logout' => 'Logout');
} else {
	$menu = array('?login' => 'Log in');
}
echo xhtmlMenu($menu);
?>
</div>

<div id="middle">
