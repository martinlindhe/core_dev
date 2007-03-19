<?
	$db->showProfile($time_start);

	if ($session->id) {
		echo '<br>';
		echo '<a href="?logout">log out</a><br>';
		echo '<a href="settings.php">settings</a><br>';
		echo '<a href="files.php">files</a><br>';
	}
	
?>
</body></html>