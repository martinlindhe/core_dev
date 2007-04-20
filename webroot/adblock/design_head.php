<?
	$r = $config['site']['web_root'];

	$rss_tags = '';
	if (!empty($meta_rss)) {
		foreach ($meta_rss as $feed) {
			$rss_tags .= 	"\t".'<link rel="alternate" type="application/rss+xml" title="'.$feed['title'].'" href="'.$feed['url'].'"/>'."\n";
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Adblock Filterset Database</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="stylesheet" href="<?=$r?>css/main.css" type="text/css"/>
<?=$rss_tags?>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>
<div id="left-bg"></div>
<div id="left-sep"></div>
<div id="header">
	<div id="header-logo">
		<a href="<?=$r?>index.php"><img src="<?=$r?>gfx/logo.png" alt="Filterset Database"/></a>
	</div>
	<div id="header-items">
		<a href="<?=$r?>wiki.php?View:Contribute">Contribute</a>
		<a href="<?=$r?>wiki.php?View:About">About</a>
		<a href="<?=$r?>wiki.php?View:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
	<ul class="side-nav">
		<? $cur = basename($_SERVER['SCRIPT_NAME']); ?>
		<li><?=($cur=='index.php'?'<strong>':'');?><a href="<?=$r?>index.php">Home</a><?=($cur=='index.php'?'</strong>':'')?></li>
		<li><?=($cur=='news.php'?'<strong>':'')?><a href="<?=$r?>news.php">News</a><?=($cur=='news.php'?'</strong>':'')?></li>
		<li><?=($cur=='wiki.php'?'<strong>':'')?><a href="<?=$r?>wiki.php?View:Subscribe">Subscribe</a><?=($cur=='wiki.php'?'</strong>':'')?></li>
		<li><?=($cur=='download.php'?'<strong>':'')?><a href="<?=$r?>download.php">Download</a><?=($cur=='download.php'?'</strong>':'')?></li>
		<li><?=($cur=='recent.php'?'<strong>':'')?><a href="<?=$r?>recent.php">Recent changes</a><?=($cur=='recent.php'?'</strong>':'')?></li>
<?
	if ($session->isAdmin) {
		echo '<li>'.($cur=='newrule.php'?'<strong>':'').'<a href="'.$r.'newrule.php">New rule</a>'.($cur=='newrule.php'?'</strong>':'').'</li>';
		echo '<li>'.($cur=='ruleset.php'?'<strong>':'').'<a href="'.$r.'ruleset.php">Browse rules</a>'.($cur=='ruleset.php'?'</strong>':'').'</li>';
		echo '<li>'.($cur=='report_site.php'?'<strong>':'').'<a href="'.$r.'report_site.php">Report site</a>'.($cur=='report_site.php'?'</strong>':'').'</li>';

		//admin menu if logged in
		echo '<li>'.($cur=='admin.php'?'<strong>':'').'<a href="/admin/admin.php'.getProjectPath(false).'">Admin</a>'.($cur=='admin.php'?'</strong>':'').'</li>';
		echo '<li>'.($cur=='admin_reports.php'?'<strong>':'').'<a href="'.$r.'admin_reports.php">Reported sites ('.getProblemSiteCount().')</a>'.($cur=='admin_reports.php'?'</strong>':'').'</li>';
	}

	if ($session->id) {
		echo '<li>'.($cur=='settings.php'?'<strong>':'').'<a href="'.$r.'settings.php">Settings</a>'.($cur=='settings.php'?'</strong>':'').'</li>';
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?logout">Logout</a></li>';
	}
?>
	</ul>
</div>

<div id="middle">
