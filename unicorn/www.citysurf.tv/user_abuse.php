<?
	require_once('config.php');
	$user->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('ingen mottagare');
	$id = $_GET['id'];

	$action = 'abuse';
	require(DESIGN.'head_user.php');
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
	Vill du bara <b>blockera</b> denna användare från din sida, <b><a href="javascript:makeBlock('<?=$id?>');">klicka här</a></b>.<br/><br/>
	
	Vill du anmäla användaren, ange orsak i rutan här under. Vi behandlar ärendet så snabbt vi kan.<br/>
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
