<?
	require_once('config.php');

	require('design_head.php');
	if (!$session->id) {
		//echo '<div id="loginmenu">';
		$session->showLoginForm();
		//echo '</div>';
	}
	require('design_foot.php');
?>