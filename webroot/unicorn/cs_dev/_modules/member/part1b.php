<?
	$topic = 'register';
		include(DESIGN.'head.php');
?>
	<form action="<?=$url?>" method="post">
	<div style="margin: 15px 0 25px 0;"><?=gettxt('register-part1b')?></div>
	<input type="submit" class="btn2_min" value="nästa" /><br class="clr" />
	</form>
<?
	include(DESIGN.'foot.php');
?>