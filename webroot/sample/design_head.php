<?createXHTMLHeader();?>
<div id="header">
	<div id="header-logo">
		<img src="<?=$session->web_root?>gfx/logo.png" alt="Filterset Database"/>
	</div>
	<div id="header-items">
		<a href="<?=$session->web_root?>wiki.php?Wiki:About">About</a>
		<a href="<?=$session->web_root?>wiki.php?Wiki:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
		'index.php' => 'Home',
		'news.php' => 'News',
		'faq.php' => 'FAQ',
		'feedback.php' => 'Feedback',
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
