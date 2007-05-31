<?
	$info = $user->getcontent($s['id_id'], 'user_head');
	require('head.php');
?>

	<div id="userInfo" style="clear:both;">
		<div class="userName"><?=$user->getstring($s, '')?></div>
		<div id="userImage">
			<?=$user->getimg($s['id_id'].$s['u_picid'].$s['u_picd'].$s['u_sex'], $s['u_picvalid'], 1)?>
			<br class="clr"/>
		</div>

		<div id="userDetail">
			<b>inloggningar:</b> <?=@intval($info['login_offset'][1])?><br/>
			<b>besökare:</b> <?=@intval($info['visit_cnt'][1])?><br/>
			<?=(($user->isonline($s['account_date']))?'<span class="on">online sedan '.nicedate($s['lastlog_date'], 2).'</span>':'<span class="off">offline sedan '.nicedate($s['lastonl_date'], 2).'</span>')?><br/>

			<? 
			if (!defined('U_NOINFO')) {
				if ($s['u_pstlan_id']) {
					echo '<table summary="" border=0><tr>';
					echo '<td><br/><br/><br/><br/><br/><br/><br/>'.str_replace(', ', '<br />', $user->tagline($s['u_pst'])).'</td>';
					echo '<td><img alt="'.$user->tagline($s['u_pst']).'" src="'.OBJ.'loc1_'.$s['u_pstlan_id'].'.gif" /></td>';
					echo '</tr></table>';
				}
			}
			?>
			<br/>
		</div>

	</div><!-- userInfo -->
	<br class="clr"/>

<? if (!$own) { ?>
	<div id="userMenu">
		<? makeButton($action=='view',		'goLoc(\''.l('user', 'view', $s['id_id']).'\')',		'icon_profile.png',	'profil'); ?>
		<? makeButton($action=='gb',			'goLoc(\''.l('user', 'gb', $s['id_id']).'\')',			'icon_gb.png',			'gästbok', @intval($info['gb_offset'][1]) ); ?>
		<? makeButton($action=='blog',		'goLoc(\''.l('user', 'blog', $s['id_id']).'\')',		'icon_blog.png',		'blogg', @intval($info['blog_offset'][1]) ); ?>
		<? makeButton($action=='gallery',	'goLoc(\''.l('user', 'gallery', $s['id_id']).'\')',	'icon_gallery.png',	'galleri', @intval($info['gal_offset'][1]) ); ?>

		<? makeButton(false,	'makeChat(\''.$s['id_id'].'\')',			'icon_qchat.png',	'chatta'); ?>
		<? makeButton(false,	'makeMail(\''.$s['id_id'].'\')',			'icon_mail_new.png',	'maila'); ?>
		<?
			if (!$user->isFriends($s['id_id'], 1)) {
				makeButton(false,	'makeRelation(\''.$s['id_id'].'\')',	'icon_friends.png',	'bli vän');
			}
		?>
		<? makeButton(false,	'goLoc(\''.l('user', 'abuse', $s['id_id']).'\')',	'icon_abuse.png',	'abuse'); ?>
	</div>
	<br class="clr" />
<? } ?>

