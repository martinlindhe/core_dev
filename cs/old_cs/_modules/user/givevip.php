<?
	require(DESIGN.'head_user.php');
	require_once('abuse.fnc.php');
?>

<div class="subHead">ge VIP</div><br class="clr"/>
<div class="bigHeader">Ge bort VIP-status till denna användare</div>
<div class="bigBody">

	För att ge 2 veckors VIP till denna användare, skicka följande SMS:<br/><br/>
	
	"<b>CITY VIP <?=$s['id_id']?></b>" till nummer <b>72777</b>.<br/><br/>

	SMS:et kostar 20 SEK.

</div>

<?
	require(DESIGN.'foot_user.php');
?>