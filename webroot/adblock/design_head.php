<?
	//<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
?>
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
	if ($_SESSION['isSuperAdmin']) {
		echo '<li><a href="newrule.php">New rule</a></li>';
		echo '<li><a href="ruleset.php">Browse rules</a></li>';
		echo '<li><a href="report_site.php">Report site</a></li>';
	}

	if ($_SESSION['loggedIn']) {
		echo '<li><a href="settings.php">Settings</a></li>';
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?logout">Logout</a></li>';
	}
?>
	</ul>
</div>

<?
if (!$_SESSION['loggedIn']) {
?>
<div id="loginmenu">
<?
	echo '<form name="login" method="post" action="'.$_SERVER['PHP_SELF'].'">';

	echo
			'<table>'.
			'<tr><td>Username:</td><td><input name="usr" type="text" value="'.$_SESSION['lastUserName'].'" size="9" onkeypress="return submitlogin(event);"/></td></tr>'.
			'<tr><td>Password:</td><td><input name="pwd" type="password" size="9" onkeypress="return submitlogin(event);"/></td></tr>'.
			'</table>'.
			'<a href="#" onclick="javascript:return sendlogin();">Log in</a><br/>'.
			'</form>';

	if ($_SESSION['lastError']) {
		echo '<a href="errorhelp.php?error='.urlencode($_SESSION['lastError']).'"><font color=red>Error: '.$_SESSION['lastError'].'</font></a><br/>';
		$_SESSION['lastError'] = '';
	}
?>
	<br/>
	<ul class="side-nav">
		<li><a href="newuser.php">New user</a></li>
	</ul>
</div>
<?
}
	if (!$_SESSION['loggedIn']) {
?>
<script type="text/javascript">
function submitlogin(event) {
	if ((event && event.which == 13) || (window.event && window.event.keyCode == 13))
	if (document.login.usr.value && document.login.pwd.value && (document.login.pwd.value.length > 1)) { sendlogin(); return false; }
	return true;
}
function sendlogin() {
	if (!document.login.usr.value || !document.login.pwd.value) { alert('Specify username and password'); return false; }
	document.login.submit();
	return false;
}
<?
	if ($_SESSION['lastUserName']) echo "document.login.pwd.focus();\n";
	else echo "document.login.usr.focus();\n";
?>
</script>
<?
	}
?>

<div id="middle">