<?php

$meta_search[] = array('url' => $config['app']['web_root'].'opensearch.xml', 'name' => 'Adblock Ruleset Search');
createXHTMLHeader();
?>
<div id="left-sep"></div>
<div id="header">
	<div id="header-logo">
		<img src="<?=$config['app']['web_root']?>gfx/logo.png" alt="Filterset Database"/>
	</div>
	<div id="header-items">
		<a href="<?=$config['app']['web_root']?>wiki.php?Wiki:Contribute">Contribute</a>
		<a href="<?=$config['app']['web_root']?>wiki.php?Wiki:About">About</a>
		<a href="<?=$config['app']['web_root']?>wiki.php?Wiki:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
<?php

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
		$config['core']['web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin');
	createMenu($menu);
}

if ($session->id) {
	$menu = array('index.php?logout' => 'Logout');
} else {
	$menu = array('login.php' => 'Log in');
}
createMenu($menu);
?>
<br/>
<a href="http://www.nosoftwarepatents.com/" target="_blank"><img src="gfx/nswpat80x15.gif"/></a>
</div>

<div id="middle">
