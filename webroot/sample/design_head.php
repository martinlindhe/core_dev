<?createXHTMLHeader();?>
<div id="left-bg"></div>
<div id="header">
	<div id="header-logo">
		<img src="<?=$session->web_root?>gfx/logo.png" alt="Filterset Database"/>
	</div>
	<div id="header-items">
		<a href="<?=$session->web_root?>wiki.php?View:About">About</a>
		<a href="<?=$session->web_root?>wiki.php?View:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
			'index.php' => 'Home',
			'news.php' => 'News',
			'users.php' => 'Users');
	createMenu($menu);
	
	if ($session->isAdmin) {
		$menu = array(
			'/admin/admin.php'.getProjectPath(0) => 'Admin');
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array(
			'user.php' => 'My profile',
			'?logout' => 'Logout');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);

?>
</div>

<div id="middle">
