<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Adblock Filterset Database</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="stylesheet" href="<?=$session->web_root?>css/site.css" type="text/css"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
<?linkRSSfeeds()?>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>
<script type="text/javascript">
var _ext_ref='<?=getProjectPath()?>';
</script>
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
			'/admin/admin.php'.getProjectPath(false) => 'Admin',
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
