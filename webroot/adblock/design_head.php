<?createXHTMLHeader()?>
<div id="left-bg"></div>
<div id="left-sep"></div>
<div id="header">
	<div id="header-logo">
		<img src="<?=$session->web_root?>gfx/logo.png" alt="Filterset Database"/>
	</div>
	<div id="header-items">
		<a href="<?=$session->web_root?>wiki.php?View:Contribute">Contribute</a>
		<a href="<?=$session->web_root?>wiki.php?View:About">About</a>
		<a href="<?=$session->web_root?>wiki.php?View:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?
	$menu = array(
			'index.php' => 'Home',
			'news.php' => 'News',
			'wiki.php?View:Subscribe' => 'Subscribe',
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
