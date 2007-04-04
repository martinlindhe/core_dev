<?
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
	require(DESIGN."head.php");

?>
	<div id="mainContent">
<style type="text/css">
#text_container {
	background-color: #FFFFFF;
	font-family: Georgia, Verdana, Arial, sans-serif;
}
#formatText p { margin: 0; padding: 0; }
</style>
			<div class="mainHeader2"><h4>från: <span class="bg_wht"><?=($own_u)?(($res_u)?$user->getstring($res_u):'<b class="blk">[BORTTAGEN]</b>'):$user->getstring($l);?></span> till <span class="bg_wht"><?=($own_u)?$user->getstring($l):(($res_u)?$user->getstring($res_u):'<b class="blk">[BORTTAGEN]</b>');?></span> - skickat: <?=(@$res['sent_date'])?nicedate($res['sent_date'], 1, 1):nicedate(date("Y-m-d"), 1, 1);?> - <a href="<?=l('user', 'mail').$page?>">tillbaka</a></h4></div>
			<div class="mainBoxed2">
			<!--<div class="pdg l"><?=($s['id_id'] == $l['id_id'])?$user->getimg($res_u['id_id'].$res_u['u_picid'].$res_u['u_picd'].$res_u['u_sex'], $res_u['u_picvalid'], 1):$user->getimg($s['id_id'].$s['u_picid'].$s['u_picd'].$s['u_sex'], $s['u_picvalid'], 1);?></div>-->
			<div class="pdg l" style="width: 589px; overflow: hidden;">
			<h3><?=secureOUT($res['sent_ttl'])?></h3>
			<p class="no" id="formatText"><?=formatText($res['sent_cmt'])?></p>
			</div>
			<br class="clr" />
			<div class="r"><input type="button" onclick="if(confirm('Säker ?')) goLoc('<?=l('user', 'mail').'&'.$page.'&del_msg='.$res['main_id']?>');" class="btn2_med" value="radera" />&nbsp;<input type="button" onclick="makeMail('<?='&a='.$res['main_id'].'&r'?>');" class="btn2_med" value="vidarebefordra" />&nbsp;<?=($own_u && $res_u)?'<input type="button" onclick="makeMail(\''.$res_u['id_id'].'/&a='.$res['main_id'].'\');" class="btn2_med" value="svara" />':'';?></div>
			<br class="clr" />
			</div>
		</div>
<?
	require(DESIGN.'foot.php');
?>