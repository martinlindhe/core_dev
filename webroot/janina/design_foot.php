<div id="footer">
<?
	//if (!$session->id) echo '<a href="login.php">login</a><br>';

	if ($session->username == "martin") $db->showProfile($time_start);

	if ($session->id) {
		echo '<br/>';
		echo '<a href="?logout">log out</a> - ';
		echo '<a href="files.php">files</a><br/>';
	}
?>
<a href="mailto:janina.m@home.se">e:janina.m@home.se</a>
</div>

</body></html>