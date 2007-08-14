<?
	include(DESIGN.'head.php');
?>

	<div class="bigHeader">citysurf informerar</div>
	<div class="bigBody">
		<?=$msg?>
		<? if(!empty($url)) echo '<input type="button" class="btn2_min r" onclick="goLoc(\''.$url.'\');" value="vidare >>" /><br class="clr" />'; ?>
	</div>

<?
	require(DESIGN.'foot.php');
?>
