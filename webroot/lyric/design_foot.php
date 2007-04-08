<?
	if ($session->isAdmin) {
		echo '<br/>';

		echo 'There are '.countNewAdditions().' new additions to moderate.<br/>';
		echo 'There are '.countPendingChanges().' pending changes to moderate.<br/>';
		echo '<a href="moderate.php">Go moderate</a>';
	}

	$db->showProfile($time_start);
?>
</div>
</body>
</html>