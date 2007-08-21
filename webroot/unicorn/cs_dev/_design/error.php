<?
	require(DESIGN.'head.php');
?>
		<div id="mainContent">
			<div class="bigHeader">citysurf informerar</div>
			<div class="bigBody">
				<p><?=$msg?></p>
<? if(!empty($url)) echo '<input type="button" class="btn2_min r" onclick="goLoc(\''.$url.'\');" value="vidare >>" /><br class="clr" />'; ?>
			</div>
		</div>
<?
	require(DESIGN.'foot.php');
?>
