<?
	require_once('config.php');
	
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('ingen mottagare');
	$id = $_GET['id'];

	$action = 'givevip';
	require(DESIGN.'head_user.php');
?>

<div class="subHead">ge VIP</div><br class="clr"/>
<div class="bigHeader">Ge bort VIP-status till denna användare</div>
<div class="bigBody">

	För att ge 2 veckors VIP till denna användare, skicka följande SMS:<br/><br/>
	
	"<b>CITY VIP <?=$id?></b>" till nummer <b>72777</b>.<br/><br/>

	SMS:et kostar 20 SEK.

</div>

<?
	require(DESIGN.'foot_user.php');
?>
