<?
	require(DESIGN.'head_start.php');
?>
			<div class="wholeHeader2"><h4>citysurf informerar</h4></div>
			<div class="wholeBoxed2">
				<p><?=$msg?></p>
<? if(!empty($url)) echo '<input type="button" class="btn2_med r" onclick="goLoc(\''.$url.'\');" value="vidare >>" /><br class="clr" />'; ?>
			</div>
<?
	require(DESIGN.'foot_start.php');
?>