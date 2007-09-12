<?
	require_once('config.php');

	require('design_head.php');

	if ($session->error) {
		echo '<div class="critical"><img src="'.$config['core_web_root'].'gfx/icon_warning_big.png" alt="Error"/> '.$session->error.'</div>';
	} else {
		echo '<div class="okay">No errors to display</div>';
	}

	require('design_foot.php');
?>