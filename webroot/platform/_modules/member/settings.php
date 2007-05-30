<?
	if($id == 'img') {
		include('settings_img.php');
		exit;
	} elseif($id == 'delete') {
		include('settings_delete.php');
		exit;
	} elseif($id == 'fact') {
		include('settings_fact.php');
		exit;
	} elseif($id == 'personal') {
		include('settings_personal.php');
		exit;
	} elseif($id == 'subscription') {
		include('settings_subscription.php');
		exit;
	} elseif($id == 'theme') {
		include('settings_theme.php');
		exit;
	} else {
		include('settings_profile.php');
		exit;
	}
?>