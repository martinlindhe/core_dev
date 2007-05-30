<?
	require(DESIGN.'head_user.php');
	require_once('abuse.fnc.php');
?>

<div class="subHead">anmäl användare</div><br class="clr"/>
<div class="bigHeader">anmälan</div>
<div class="bigBody">

<?
	if (!empty($_POST['abuse']) ) {
		abuseReport($s['id_id'], $_POST['abuse']);
?>
		Din anmälan har mottagits!
<?
	} else {
?>

	Om du istället vill blockera användaren, <a href="javascript:makeBlock('<?=$s['id_id']?>');">klicka här</a>.<br/><br/>
	Motivera din anmälan.<br/>
	<form method="post" action="">
	<textarea name="abuse" rows="7" cols="40"></textarea><br/>
	<input type="submit" class="btn2_sml" value="anmäl!"/>
	</form>
<?
	}
?>

</div>

<?
	require(DESIGN.'foot_user.php');
?>
