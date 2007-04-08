<?
	if (!$session->id) {
		echo '<div id="loginmenu">';
		$session->showLoginForm();
		echo '</div>';
	}

	if ($session->isAdmin) {
		echo '<div id="footer">';
		$db->showProfile($time_start);
		echo '</div>';
	}
?>
</div>
</body></html>