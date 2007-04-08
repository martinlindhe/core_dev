<?
	if ($session->isAdmin) {
		echo '<div id="footer">';
		//admin menu if logged in
		$reportedSites = getProblemSiteCount();
		if ($reportedSites) {
			$reportedSites = '<b>'.$reportedSites.'</b>';
		}
		echo '<a href="admin_events.php">admin</a>:: <a href="admin_reports.php">reported sites:'.$reportedSites.'</a> ';
		$db->showProfile($time_start);
		echo '</div>';
	}
?>
</div>
</body></html>