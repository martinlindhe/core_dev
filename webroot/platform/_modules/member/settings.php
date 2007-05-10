<?
	$menu = array(
						'settings_profile' => array(l('member', 'settings'), 'publika'),
						'settings_fact' => array(l('member', 'settings', 'fact'), 'fakta'),
						'settings_img' => array(l('member', 'settings', 'img'), 'bild'),
						'settings' => array(l('member', 'settings', 'personal'), 'personliga'),
						'settings_delete' => array(l('member', 'settings', 'delete'), 'radera konto'));

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
	} else {
		include('settings_profile.php');
		exit;
	}
?>