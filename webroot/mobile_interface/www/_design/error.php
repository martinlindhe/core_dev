<?
	require(DESIGN.'head.php');
?>
		<div id="mainContent">
			<div class="mainHeader2"><h4>citysurf informerar</h4></div>
			<div class="mainBoxed2">
				<p><?=$msg?></p>
<? if(!empty($url)) echo '<input type="button" class="btn2_med r" onclick="goLoc(\''.$url.'\');" value="vidare >>" /><br class="clr" />'; ?>
			</div>
		</div>
<?
	require(DESIGN.'foot.php');
?>