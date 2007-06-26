<?
	require(CONFIG.'cut.fnc.php');
	require(CONFIG.'secure.fnc.php');

	require_once('settings.fnc.php');

	require(DESIGN.'head.php');
	
	$vip_levels = array(
		1 => 'Normal användare',
		2 => 'VIP',
		3 => 'VIP Deluxe'
	);
?>
<div id="mainContent">

	<div class="subHead">inställningar - vip status</div><br class="clr"/>

	<? makeButton(false, 'goLoc(\''.l('member', 'settings').'\')', 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'fact').'\')', 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'theme').'\')', 'icon_settings.png', 'tema'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'img').'\')', 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'personal').'\')', 'icon_settings.png', 'personliga'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'subscription').'\')', 'icon_settings.png', 'span'); ?>
	<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'delete').'\')', 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(true, 'goLoc(\''.l('member', 'settings', 'vipstatus').'\')', 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/>

	<div class="bigHeader">Din aktuella VIP-nivå</div>
	<div class="bigBody">
<?
		$current_vip = getCurrentVIPLevel($l['id_id']);
		echo $vip_levels[ $current_vip ].'<br/><br/>';
?>
		<a href="/main/upgrade/">Klicka här</a> för att läsa mer om VIP-nivåer.
	</div>
	<br/>

	<div class="bigHeader">Tillgängliga VIP-nivåer</div>
	<div class="bigBody">
<?
		$list = getVIPLevels($l['id_id']);

		if (!$list) echo 'Inga VIP-nivåer tillgängliga för dig!';
		
		foreach ($list as $row) {
			echo '<div'.($current_vip==$row['level']?' style="font-weight: bold;"':'').'>';
			echo $vip_levels[ $row['level'] ];
			echo ' - '.$row['days'].' dagar återstår. Betalades senast '.$row['timeSet'];
			echo '</div>';
		}
?>
	</div>

</div>

<?
	include(DESIGN.'foot.php');
?>