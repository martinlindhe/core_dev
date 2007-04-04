<?
	if ($_SESSION['userMode'] == 1) {
		echo '<br/>';

		echo 'There are '.countNewAdditions($db).' new additions to moderate.<br/>';
		echo 'There are '.countPendingChanges($db).' pending changes to moderate.<br/>';
		echo '<a href="moderate.php">Go moderate</a>';
	}
?>
</body>
</html>