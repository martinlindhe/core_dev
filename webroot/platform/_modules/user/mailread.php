<?
	require('mail.fnc.php');
	require(CONFIG."secure.fnc.php");

	$page = '';

	if (!empty($_GET['id'])) {
		$res = getMail($_GET['id']);

		if (empty($res) || !count($res) || ($res['user_id'] == $l['id_id'] && $res['status_id'] != '1') || ($res['sender_id'] == $l['id_id'] && $res['sender_status'] != '1')) {
			popupACT('Felaktigt inlägg.');
		}
		$own_u = ($res['user_id'] == $l['id_id'])?true:false;
		$res_u = $user->getuser($own_u?$res['sender_id']:$res['user_id']);
	} else errorACT('Felaktigt inlägg.', l('user', 'mail'));

	if (isset($_GET['out'])) $page = '&out';
	if ($own_u && !$res['user_read']) {
		mailMarkAsRead($res['main_id']);
	}
	$s = $l;
	$own = true;

	$titel = secureOUT($res['sent_ttl']);
	if (!$titel) $titel = '<i>ingen titel</i>';

	require(DESIGN."head.php");
?>
	<div id="mainContent">

		<img src="/_gfx/ttl_mail.png" alt="Brev"/><br/><br/>

		<div class="centerMenuHeader">från: <?=($own_u)?(($res_u)?$user->getstring($res_u):'<b class="blk">[BORTTAGEN]</b>'):$user->getstring($l);?> till <?=($own_u)?$user->getstring($l):(($res_u)?$user->getstring($res_u):'<b class="blk">[BORTTAGEN]</b>');?> - skickat: <?=(@$res['sent_date'])?nicedate($res['sent_date'], 1, 1):nicedate(date("Y-m-d"), 1, 1);?></div>
			<div class="centerMenuBodyWhite" style="overflow: hidden;">
			<h3><?=$titel?></h3>

			<p class="no" id="formatText"><?=formatText($res['sent_cmt'])?></p>

			<div class="r"><input type="button" onclick="if(confirm('Säker ?')) goLoc('<?=l('user', 'mail').'&amp;'.$page.'&amp;del_msg='.$res['main_id']?>');" class="btn2_med" value="radera" />&nbsp;<input type="button" onclick="makeMail('<?='&amp;a='.$res['main_id'].'&amp;r'?>');" class="btn2_med" value="vidarebefordra" />&nbsp;<?=($own_u && $res_u)?'<input type="button" onclick="makeMail(\''.$res_u['id_id'].'/&amp;a='.$res['main_id'].'\');" class="btn2_med" value="svara" />':'';?></div>
			</div>
		</div>
	</div>
<?
	require(DESIGN.'foot.php');
?>