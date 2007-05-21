<?
	$info = $user->getcontent($s['id_id'], 'user_head');
	require('head.php');
?>

<div id="mainContent">
	<div id="userImage"><?=$user->getimg($s['id_id'].$s['u_picid'].$s['u_picd'].$s['u_sex'], $s['u_picvalid'], 1)?></div>
	<div id="userInfo">
		<h1><?=$user->getstring($s, '')?></h1>
		<div>
			• civilstånd: <?=(!empty($info['det_civil'][1]))?secureOUT($info['det_civil'][1]):@$det_type[$info['det_civil_type'][1]];?><br />
			• attityd: <?=@$info['det_attitude'][1]?><br />
			• musik: <?=@$info['det_music'][1]?><br />
			• vill ha: <?=@$info['det_wants'][1]?><br />
			• alkohol: <?=@$info['det_alcohol'][1]?><br />
			• tobak: <?=@$info['det_tobacco'][1]?><br />
			• sexliv: <?=@$info['det_sex'][1]?><br />
			• barn: <?=@$info['det_children'][1]?><br />
			• längd: <?=@$info['det_length'][1]?><br />
			<br/>
		</div>
	</div>
	<div id="userDetail"><p><?=(($user->isonline($s['account_date']))?'<span class="on">online sedan<br />'.nicedate($s['lastlog_date'], 2).'</span>':'<span class="off">offline sedan<br />'.nicedate($s['lastonl_date'], 2).'</span>')?></p></div>
	<br class="clr" />

	<div id="userMenu">
		<? makeButton($action=='view',		'goLoc(\''.l('user', 'view', $s['id_id']).'\')',		'icon_profile.png',	'profil'); ?>
		<? makeButton($action=='gb',			'goLoc(\''.l('user', 'gb', $s['id_id']).'\')',			'icon_gb.png',			'gästbok', @intval($info['gb_offset'][1]) ); ?>
		<? makeButton($action=='blog',		'goLoc(\''.l('user', 'blog', $s['id_id']).'\')',		'icon_blog.png',		'blogg', @intval($info['blog_offset'][1]) ); ?>
		<? makeButton($action=='gallery',	'goLoc(\''.l('user', 'gallery', $s['id_id']).'\')',	'icon_gallery.png',	'galleri', @intval($info['gal_offset'][1]) ); ?>

<? if (!$own) { ?>
		<? makeButton(false,	'makeChat(\''.$s['id_id'].'\')',			'icon_qchat.png',	'chatta'); ?>
		<? makeButton(false,	'makeMail(\''.$s['id_id'].'\')',			'icon_mail_new.png',	'maila'); ?>
		<? makeButton(false,	'makeRelation(\''.$s['id_id'].'\')',	'icon_friends.png',	'bli vän'); ?>
		<? makeButton(false,	'makeBlock(\''.$s['id_id'].'\')',			'icon_block.png',	'blockera'); ?>
		<? //makeButton(false,	'',	'icon_abuse.png',	'rapportera'); ?>
<? } else {?>
		<? makeButton(false, 'goLoc(\''.l('member', 'settings', 'profile', '0').'&amp;go=1\')',	'icon_settings.png', 'ändra profil'); ?>
<? } ?>
	<br class="clr" />
<?
	
	if ($own) {
		if(@intval($info['gb_offset'][1]) != @intval($_SESSION['data']['offsets']['gb_offset'])) $_SESSION['data']['offsets']['gb_offset'] = @intval($info['gb_offset'][1]);
		if(@intval($info['gal_offset'][1]) != @intval($_SESSION['data']['offsets']['gal_offset'])) $_SESSION['data']['offsets']['gal_offset'] = @intval($info['gal_offset'][1]);
		if(@intval($info['blog_offset'][1]) != @intval($_SESSION['data']['offsets']['blog_offset'])) $_SESSION['data']['offsets']['blog_offset'] = @intval($info['blog_offset'][1]);
		if(@intval($info['rel_offset'][1]) != @intval($_SESSION['data']['offsets']['rel_offset'])) $_SESSION['data']['offsets']['rel_offset'] = @intval($info['rel_offset'][1]);
		if(@intval($info['mail_offset'][1]) != @intval($_SESSION['data']['offsets']['mail_offset'])) $_SESSION['data']['offsets']['mail_offset'] = @intval($info['mail_offset'][1]);
	}
	
?>
	</div>
