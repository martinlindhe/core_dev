<?
	$info = $user->getcontent($s['id_id'], 'user_head');
	require('head.php');
	$uarr = array();
	if($l) $uarr['top'] = 1;
	$uarr['noimg'] = 1;
?>

<div id="mainContent">
	<div id="userImage"><?=$user->getimg($s['id_id'].$s['u_picid'].$s['u_picd'].$s['u_sex'], $s['u_picvalid'], 1)?></div>
	<div id="userInfo">
		<h1><?=$user->getstring($s, '', @$uarr)?></h1>
		<div>
			• civilstånd: <?=(!empty($info['det_civil'][1]))?secureOUT($info['det_civil'][1]):@$det_type[$info['det_civil_type'][1]];?><br />
			• attityd: <?=@$info['det_attitude'][1]?><br />
			• vill ha: <?=@$info['det_wants'][1]?><br />
			<br />
			• alkohol: <?=@$info['det_alcohol'][1]?><br />
			• tobak: <?=@$info['det_tobacco'][1]?><br />
			• sexliv: <?=@$info['det_sex'][1]?><br />
		</div>
	</div>
	<div id="userDetail"><p><?=(($user->isonline($s['account_date']))?'<span class="on">online sedan<br />'.nicedate($s['lastlog_date'], 2).'</span>':'<span class="off">offline sedan<br />'.nicedate($s['lastonl_date'], 2).'</span>')?></p></div>
	<br class="clr" />

	<div id="userMenu">
		<div class="btnProfile" onclick="goLoc('<?=l('user', 'view', $s['id_id'])?>');"></div>
		<div class="btnGuestbook" onclick="goLoc('<?=l('user', 'gb', $s['id_id'])?>');"><?=@intval($info['gb_offset'][1])?>&nbsp;</div>
		<div class="btnBlog" onclick="goLoc('<?=l('user', 'blog', $s['id_id'])?>');"><?=@intval($info['blog_offset'][1])?>&nbsp;</div>
		<div class="btnGallery" onclick="goLoc('<?=l('user', 'gallery', $s['id_id'])?>');"><?=@intval($info['gal_offset'][1])?>&nbsp;</div>
<? if (!$own) { ?>
		<div class="btnMail"></div> <!-- skriv mail till denna person, fel ikon? -->
		<div class="btnBecomeFriend" onclick="makeRelation('<?=$s['id_id']?>');"></div> <!-- bli vän ikon -->
		<div class="btnBlock" onclick="makeBlock('<?=$s['id_id']?>');"></div> <!-- okänd ikon -->
		<div class="btnReport"></div> <!-- okänd ikon -->
<? } ?>
<!--
		..<input type="button" class="btn<?=($page == 'view'?'3':'2')?>_min" accesskey="1" onclick="goLoc('<?=l('user', 'view', $s['id_id'])?>');" value="profil" />
		..<input type="button" class="btn<?=($page == 'gb'?'3':'2')?>_min" accesskey="2" onclick="goLoc('<?=l('user', 'gb', $s['id_id'])?>');" value="gästbok <?=@intval($info['gb_offset'][1])?>" />
		..<input type="button" class="btn<?=($page == 'gallery'?'3':'2')?>_min" accesskey="3" onclick="goLoc('<?=l('user', 'gallery', $s['id_id'])?>');" value="galleri <?=@intval($info['gal_offset'][1])?>" />
		..<input type="button" class="btn<?=($page == 'blog'?'3':'2')?>_min" accesskey="4" onclick="goLoc('<?=l('user', 'blog', $s['id_id'])?>');" value="blogg <?=@intval($info['blog_offset'][1])?>" />
		<input type="button" class="btn<?=($page == 'relations'?'3':'2')?>_min" accesskey="5" onclick="goLoc('<?=l('user', 'relations', $s['id_id'])?>');" value="vänner <?=@intval($info['rel_offset'][1])?>" />
-->
<?
	if ($own) {
		/*
		if(@intval($info['gb_offset'][1]) != @intval($_SESSION['data']['offsets']['gb_offset'])) $_SESSION['data']['offsets']['gb_offset'] = @intval($info['gb_offset'][1]);
		if(@intval($info['gal_offset'][1]) != @intval($_SESSION['data']['offsets']['gal_offset'])) $_SESSION['data']['offsets']['gal_offset'] = @intval($info['gal_offset'][1]);
		if(@intval($info['blog_offset'][1]) != @intval($_SESSION['data']['offsets']['blog_offset'])) $_SESSION['data']['offsets']['blog_offset'] = @intval($info['blog_offset'][1]);
		if(@intval($info['rel_offset'][1]) != @intval($_SESSION['data']['offsets']['rel_offset'])) $_SESSION['data']['offsets']['rel_offset'] = @intval($info['rel_offset'][1]);
		if(@intval($info['mail_offset'][1]) != @intval($_SESSION['data']['offsets']['mail_offset'])) $_SESSION['data']['offsets']['mail_offset'] = @intval($info['mail_offset'][1]);
		*/
	} elseif($l) {
?>
<!--
		<input type="button" class="btnon btn2_midi" onclick="makeRelation('<?=$s['id_id']?>');" value="bli vän!" />
		<input type="button" class="btnon btn2_midi" onclick="makeBlock('<?=$s['id_id']?>');" value="blockera!" />
-->
<?
	}
?>
</div>
