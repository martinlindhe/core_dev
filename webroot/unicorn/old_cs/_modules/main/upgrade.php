<?
	require(DESIGN.'head.php');
?>

<div id="mainContent">
	
	<div class="subHead">uppgradera till vip</div><br class="clr"/>


	<div class="centerMenuBodyWhite">
		<?=stripslashes(gettxt('info-upgrade', 0, 0))?><br/><br/>
		<a href="/member/settings/vipstatus/">Klicka här</a> för att se din aktuella VIP-status.
	</div>
</div>
<?
	require(DESIGN.'foot.php');
?>