<?php

$meta_css[] = 'css/site.css';
createXHTMLHeader();
?>
<div id="top">
	<div id="top-logo"></div>
	<div id="top-items">
		<a href="xxx">fffd</a>
	</div>
</div>


<div id="leftmenu">
<?php

$menu = array(
	'index.php' => 'Home'
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
