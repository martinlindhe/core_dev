<?
	require_once('config.php');

	$page = 'in';
	if (isset($_GET['out'])) $page = 'out';

	if (!empty($_GET['id'])) {
		$res = getMail($_GET['id']);

		if (empty($res) || !count($res) || ($res['user_id'] == $user->id && $res['status_id'] != '1') || ($res['sender_id'] == $user->id && $res['sender_status'] != '1')) {
			popupACT('Felaktigt inlägg.');
		}
		$own_u = ($res['user_id'] == $user->id) ? true : false;
		$res_u = $user->getuser($own_u ? $res['sender_id'] : $res['user_id']);
	} else errorACT('Felaktigt inlägg.', l('user', 'mail'));

	if (isset($_GET['out'])) $page = '&out';
	if ($own_u && !$res['user_read']) {
		mailMarkAsRead($res['main_id']);
	}

	$own = true;

	$titel = secureOUT($res['sent_ttl']);
	if (!$titel) $titel = '<i>ingen titel</i>';

	$id = $user->id;
	require(DESIGN."head_user.php");
?>
	<div class="subHead">brev</div><br class="clr"/>

	<div class="bigHeader">
		från: <?=($own_u)?(($res_u)?$user->getstring($res_u):($res['sender_id']?'<b class="blk">[BORTTAGEN]</b>':'SYSTEM')):$user->getstring($_SESSION['data']);?>
		till <?=($own_u)?$user->getstring($_SESSION['data']):(($res_u)?$user->getstring($res_u):'<b class="blk">[BORTTAGEN]</b>');?>
		- skickat: <?=($res['sent_date'])?nicedate($res['sent_date'], 1, 1):nicedate(date("Y-m-d"), 1, 1);?>
	</div>
	<div class="bigBody">
		<h3><?=$titel?></h3>

		<p class="no" id="formatText">
<?
		$text = $res['sent_cmt'];
		//if ($res['sender_id']) $text = strip_tags($text);	//strip tags om avsändaren _INTE_ är system user
		$text = nl2br($text);
		echo $text;
?>
		</p>

		<div class="r">
			<input type="button" onclick="if(confirm('Säker ?')) goLoc('user_mail.php?del_msg=<?=$res['main_id'].'&'.$page?>');" class="btn2_min" value="radera" />
			<input type="button" onclick="makeMail('<?=$res_u['id_id'].'&amp;a='.$res['main_id'].'&amp;r'?>');" class="btn2_min" value="skicka vidare" />&nbsp;
			<? if ($own_u && $res_u) echo '<input type="button" onclick="makeMail(\''.$res_u['id_id'].'&amp;a='.$res['main_id'].'\');" class="btn2_min" value="svara" />'; ?>
		</div>
		<br class="clr"/>
	</div>
<?
	require(DESIGN.'foot_user.php');
?>
