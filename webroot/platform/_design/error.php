<?
	require(DESIGN.'head.php');
?>
		<div id="mainContent">
			<div class="centerMenuHeader">citysurf informerar</div>
			<div class="centerMenuBodyWhite">
				<p><?=$msg?></p>
<? if(!empty($url)) echo '<input type="button" class="btn2_med r" onclick="goLoc(\''.$url.'\');" value="vidare >>" /><br class="clr" />'; ?>
			</div>
		</div>
<?
	require(DESIGN.'foot.php');
?>