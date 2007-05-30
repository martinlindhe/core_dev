<?createXHTMLHeader()?>
<div id="left-sep"></div>
<div id="header">
	<div id="header-logo">
		<img src="<?=$config['web_root']?>gfx/logo.png" alt="Filterset Database"/>
	</div>
	<div id="header-items">
		<a href="<?=$config['web_root']?>wiki.php?Wiki:Contribute">Contribute</a>
		<a href="<?=$config['web_root']?>wiki.php?Wiki:About">About</a>
		<a href="<?=$config['web_root']?>wiki.php?Wiki:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
			'index.php' => 'Home',
			'news.php' => 'News',
			'wiki.php?Wiki:Subscribe' => 'Subscribe',
			'download.php' => 'Download',
			'report_site.php' => 'Report site',
			'recent.php' => 'Recent changes');
	createMenu($menu);

	if ($session->isAdmin) {
		$menu = array(
			'newrule.php' => 'New rule',
			'ruleset.php' => 'Browse ruleset',
			'/admin/admin.php'.getProjectPath(0) => 'Admin',
			'admin_reports.php' => 'Reported sites');
		createMenu($menu);
	}

	if ($session->id) {
		$menu = array('?logout' => 'Logout');
	} else {
		$menu = array('?login' => 'Log in');
	}
	createMenu($menu);
?>
</div>

<div id="middle">
