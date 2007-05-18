<?createXHTMLHeader()?>
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
