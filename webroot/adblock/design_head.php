<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Adblock Filterset Database</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<link rel="stylesheet" href="css/main.css" type="text/css"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<script type="text/javascript" src="/js/ajax.js"></script>
	<script type="text/javascript" src="/js/functions.js"></script>
	<script type="text/javascript" src="js/adblock.js"></script>
</head>
<body>
<div id="left-bg"></div>
<div id="left-sep"></div>
<div id="header">
	<div id="header-logo">
		<a href="index.php"><img src="gfx/logo.png" alt="Filterset Database"/></a>
	</div>
	<div id="header-items">
		<a href="wiki.php?View:Contribute">Contribute</a>
		<a href="wiki.php?View:About">About</a>
		<a href="wiki.php?View:Help">Help</a>
	</div>
</div>
<div id="leftmenu">
	<ul class="side-nav">
		<li><strong><a href="index.php">Home</a></strong></li>
		<li><a href="wiki.php?View:Subscribe">Subscribe</a></li>
		<li><a href="download.php">Download</a></li>
		<li><a href="recent.php">Recent changes</a></li>
<?
	if ($session->isAdmin) {
		echo '<li><a href="newrule.php">New rule</a></li>';
		echo '<li><a href="ruleset.php">Browse rules</a></li>';
		echo '<li><a href="report_site.php">Report site</a></li>';

		//admin menu if logged in
		echo '<li><a href="admin_events.php">Admin</a></li>';
		echo '<li><a href="admin_reports.php">Reported sites ('.getProblemSiteCount().')</a></li>';
	}

	if ($session->id) {
		echo '<li><a href="settings.php">Settings</a></li>';
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?logout">Logout</a></li>';
	}
?>
	</ul>
</div>

<div id="middle">
