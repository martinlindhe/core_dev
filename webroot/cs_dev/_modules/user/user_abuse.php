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
	Vill du bara blockera denna användare från din sida, <a href="javascript:makeBlock('<?=$s['id_id']?>');">klicka här</a>.<br/><br/>
	
	Vill du anmäla användaren, ange orsak i rutan här under.andlar ärendet så snabbt vi kan.<br/>
	Missbruk kan lea till avstängning av dig själv!

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
