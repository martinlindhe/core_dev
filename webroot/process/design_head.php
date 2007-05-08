<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>process server</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" href="/css/functions.css" type="text/css"/>
	<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>

<div id="leftmenu">
	<ul class="side-nav">
		<? $cur = basename($_SERVER['SCRIPT_NAME']); ?>
		<li><a href="index.php">Home</a></li>
<?
	if ($session->isAdmin) {
		//admin menu if logged in
		echo '<li>'.($cur=='admin.php'?'<strong>':'').'<a href="/admin/admin.php'.getProjectPath(false).'">Admin</a>'.($cur=='admin.php'?'</strong>':'').'</li>';
	}

	if ($session->id) {
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?logout">Logout</a></li>';
	}
?>
	</ul>
</div>

<div id="middle">
