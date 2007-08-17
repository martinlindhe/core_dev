<?
	require_once('config.php');

	$page = 'settings_delete';
	if (!empty($_POST['css_theme'])) {
		$user->setinfo($user->id, 'det_tema', $_POST['css_theme']);
	}

	require(DESIGN.'head.php');
?>
<div id="mainContent">
	
	<div class="subHead">inställningar - ändra tema</div><br class="clr"/>

	<? makeButton(false, "document.location='settings_presentation.php'", 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, "document.location='settings_fact.php'", 'icon_settings.png', 'fakta'); ?>
	<? makeButton(true, "document.location='settings_theme.php'", 'icon_settings.png', 'tema'); ?>
	<? makeButton(false, "document.location='settings_img.php'", 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, "document.location='settings_personal.php'", 'icon_settings.png', 'personliga'); ?>
	<? makeButton(false, "document.location='settings_subscription.php'", 'icon_settings.png', 'span'); ?>
	<? makeButton(false, "document.location='settings_delete.php'", 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(false, "document.location='settings_vipstatus.php'", 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/>

	<div class="centerMenuBodyWhite">
<?
	if (!empty($_POST['css_theme'])) {
		echo 'Ditt val av tema är sparat!';
		echo '</div>';
		include(DESIGN.'foot.php');
		die;
	}

	$themes = array('default.css' => 'Default-tema', 'jord.css' => 'Jord', 'vatten.css' => 'Vatten', 'luft.css' => 'Luft', 'eld.css' => 'Eld');
	
	echo 'Här kan du välja hur du vill att CitySurf ska visas när du är inloggad.<br/><br/>';
	
	$current = $user->getinfo($user->id, 'det_tema');
	if (!$current) $current = 'default.css';

	echo '<form method="post" action="">';
	foreach ($themes as $css => $namn) {
		echo '<input type="radio" value="'.secureOUT($css).'" id="css_'.secureOUT($css).'" name="css_theme"'.($current==$css?' checked="checked"':'').'/>';
		echo ' <label for="css_'.secureOUT($css).'">'.$namn.'</label><br/>';
	}
	echo '<br/>';
	echo '<input type="submit" class="btn2_min" value="ändra tema!"/>';
	echo '</form>';
?>
	</div>
</div>
<?
	include(DESIGN.'foot.php');
?>