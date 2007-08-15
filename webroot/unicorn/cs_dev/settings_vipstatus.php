<?
	require_once('config.php');

	require(DESIGN.'head.php');
	
	$vip_levels = array(
		1 => 'Normal användare',
		2 => 'VIP',
		3 => 'VIP Deluxe'
	);
?>
<div id="mainContent">

	<div class="subHead">inställningar - vip status</div><br class="clr"/>

	<? makeButton(false, "document.location='settings_presentation.php'", 'icon_settings.png', 'publika'); ?>
	<? makeButton(false, "document.location='settings_fact.php'", 'icon_settings.png', 'fakta'); ?>
	<? makeButton(false, "document.location='settings_theme.php'", 'icon_settings.png', 'tema'); ?>
	<? makeButton(false, "document.location='settings_img.php'", 'icon_settings.png', 'bild'); ?>
	<? makeButton(false, "document.location='settings_personal.php'", 'icon_settings.png', 'personliga'); ?>
	<? makeButton(false, "document.location='settings_subscription.php'", 'icon_settings.png', 'span'); ?>
	<? makeButton(false, "document.location='settings_delete.php'", 'icon_settings.png', 'radera konto'); ?>
	<? makeButton(true, "document.location='settings_vipstatus.php'", 'icon_settings.png', 'VIP'); ?>
	<br class="clr"/><br/>
	
	<div class="bigHeader">Fyll på VIP</div>
	<div class="bigBody">
	
		För att köpa VIP, skicka följande SMS:<br/><br/>
		
		"<b>CITY VIPD <?=$user->id?></b>" till nummer <b>72777</b> för 10 dagars VIP Delux (SMS:et kostar 20 SEK).<br/><br/>
		
		"<b>CITY VIP <?=$user->id?></b>" till nummer <b>72777</b> för 14 dagars VIP (SMS:et kostar 20 SEK).<br/><br/>
	
		<b><a href="/main/upgrade/">Klicka här</a></b> för att läsa mer om VIP-nivåer.<br/><br/>
	</div>
	<br/>

	<div class="bigHeader">Din aktuella VIP-nivå</div>
	<div class="bigBody">
<?
		$current_vip = getCurrentVIPLevel($user->id);
		echo $vip_levels[ $current_vip ].'<br/><br/>';
?>
	</div>
	<br/>

	<div class="bigHeader">Tillgängliga VIP-nivåer</div>
	<div class="bigBody">
<?
		$list = getVIPLevels($user->id);

		if (!$list) echo 'Inga VIP-nivåer tillgängliga för dig!<br/>';
		
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