<?
	require_once('config.php');

	require(DESIGN.'head.php');
?>

<div id="mainContent">
	
	<div class="subHead">uppgradera till vip</div><br class="clr"/>


	<div class="centerMenuBodyWhite">
		<?=stripslashes(gettxt('info-upgrade', 0, 0))?><br/><br/>
		<b><a href="settings_vipstatus.php">Klicka här för att se din aktuella VIP-status.</a></b>
		<br/><br/>
	</div>
</div>
<?
	require(DESIGN.'foot.php');
?>