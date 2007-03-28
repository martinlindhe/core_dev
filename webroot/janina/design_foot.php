<div id="footer">
<?
	if (!$session->id) {
		echo '<a href="login.php">login</a><br>';
	}

	$db->showProfile($time_start);
	echo '<br>';

	if ($session->id) {
		echo '<br>';
		echo '<a href="?logout">log out</a> - ';
		echo '<a href="files.php">files</a><br>';
	}
?>
<a href="mailto:janina.m@home.se">e:janina.m@home.se</a>
</div>

</body></html>