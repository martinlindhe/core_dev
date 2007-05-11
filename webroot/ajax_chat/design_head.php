<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>ajax chat</title>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

<link rel="stylesheet" href="inc/site.css" type="text/css"/>
<link rel="stylesheet" href="inc/functions.css" type="text/css"/>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>

<script type="text/javascript" src="inc/formatDate.js"></script>
<script type="text/javascript" src="inc/functions.js"></script>
<script type="text/javascript" src="inc/ajax.js"></script>
<script type="text/javascript" src="/js/functions.js"></script>
</head>
<body>
<script type="text/javascript">
var _ext_ref='<?=getProjectPath()?>';
</script>

<div id="body_left">
	<ul>
		<li><a href="index.php">Start page</a></li>
<?
		if ($session->isAdmin) echo '<li><a href="admin_chat.php">Admin Chat</a></li>';
		if ($session->id) echo '<li><a href="?logout">Log out</a></li>';
		else echo '<li><a href="?login">Log in</a></li>';
?>
	</ul>
</div>

<div id="body_holder">
