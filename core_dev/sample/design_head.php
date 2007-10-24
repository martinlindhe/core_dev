<?createXHTMLHeader();?>
<div id="header">
	<div id="header-logo">
		<img src="<?=$config['web_root']?>gfx/logo.png" alt="Sample Site"/>
	</div>
	<div id="header-items">
		<a href="<?=$config['web_root']?>wiki.php?Wiki:About">About</a>
		<a href="<?=$config['web_root']?>wiki.php?Wiki:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
		'index.php' => 'Home',
		'news.php' => 'News',
		'faq.php' => 'FAQ',
		'feedback.php' => 'Feedback',
		'forum.php' => 'Forum',
		'blogs.php' => 'Blogs',
		'users.php' => 'Users',
		$config['core_web_root'].'process/' => 'PROCESS SERVER',
		$config['core_web_root'].'wiki/' => 'Wiki'
	);
	createMenu($menu);

	if ($session->isAdmin) {
		$menu = array(
			$config['core_web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin');
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
