<br/>
<br/>
<?
	if (!$session->id) {
		echo '<div id="loginmenu">';
		$session->showLoginForm();
		echo '</div>';
	}

	//if ($session->isAdmin) 
	$db->showProfile($time_start);
?>
</div>
</body></html>
