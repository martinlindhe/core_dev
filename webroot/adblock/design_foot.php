</div>
<?
	if ($_SESSION['isAdmin']) {
		echo '<div id="footer">';
		//admin menu if logged in
		$reportedSites = getProblemSiteCount($db);
		if ($reportedSites) {
			$reportedSites = '<b>'.$reportedSites.'</b>';
		}
		echo '<a href="admin_events.php">admin</a>:: <a href="admin_reports.php">reported sites:'.$reportedSites.'</a> ';
		if ($config['debug']) debugFooter($pageload_start);
		echo '</div>';
	}
?>
</body></html>