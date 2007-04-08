<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Adblock Filterset Database</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="css/main.css" type="text/css"/>
	<link rel="stylesheet" href="css/functions.css" type="text/css"/>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
	<script type="text/javascript" src="js/functions.js"></script>
</head>
<body>
<div id="left-bg"></div>
<div id="left-sep"></div>
<div id="header">
	<div id="header-logo">
		<a href="index.php"><img src="gfx/logo.png" alt="Filterset Database"/></a>
	</div>
	<div id="header-items">
		<a href="contribute.php">Contribute</a>
		<a href="about.php">About</a>
		<a href="help.php">Help</a>
	</div>
</div>
<div id="leftmenu">
	<ul class="side-nav">
		<li><strong><a href="index.php">Home</a></strong></li>
		<li><a href="subscribe.php">Subscribe</a></li>
		<li><a href="download.php">Download</a></li>
		<li><a href="recent.php">Recent changes</a></li>
<?
	if ($session->isAdmin) {
		echo '<li><a href="newrule.php">New rule</a></li>';
		echo '<li><a href="ruleset.php">Browse rules</a></li>';
		echo '<li><a href="report_site.php">Report site</a></li>';
	}

	if ($session->id) {
		echo '<li><a href="settings.php">Settings</a></li>';
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?logout">Logout</a></li>';
	}
?>
	</ul>
</div>

<?
if (!$session->id) {
?>
<div id="loginmenu">
<?
	$session->showLoginForm();
?>
</div>
<?
	}
?>

<div id="middle">