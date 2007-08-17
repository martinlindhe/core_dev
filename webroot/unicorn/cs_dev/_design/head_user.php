<?
	$info = $user->getcontent($id, 'user_head');
	$s = $user->getuser($id);

	require('head.php');
?>

	<div id="userInfo" style="clear:both;">
		<div class="userName">
			<?
				echo $user->getstring($id, '');

				//todo: visa ej ge vip länken för vip-användare?
				$curr_vip = get_vip($id);
				//echo $curr_vip;
					
				if ($curr_vip == '2') echo ' <img src="/_gfx/icon_vip.png">';
				if ($curr_vip == '3') echo ' <img src="/_gfx/icon_vipd.png">';
				if ($curr_vip == '10') echo ' WEBMASTER';

				if ($id != $user->id) {
					echo ' <b>(<a href="'.l('user', 'givevip', $id).'">ge VIP</a>)</b>';
				}
			?>
		</div>
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

<? if ($user->id != $id) { ?>
	<div id="userMenu">
		<? makeButton($action=='view',		'goLoc(\''.l('user', 'view', $id).'\')',		'icon_profile.png',	'profil'); ?>
		<? makeButton($action=='gb',			'goLoc(\''.l('user', 'gb', $id).'\')',			'icon_gb.png',			'gästbok', @intval($info['gb_offset'][1]) ); ?>
		<? makeButton($action=='blog',		'goLoc(\''.l('user', 'blog', $id).'\')',		'icon_blog.png',		'blogg', @intval($info['blog_offset'][1]) ); ?>
		<? makeButton($action=='gallery',	'goLoc(\''.l('user', 'gallery', $id).'\')',	'icon_gallery.png',	'galleri', @intval($info['gal_offset'][1]) ); ?>

		<? makeButton(false,	'makeChat(\''.$id.'\')',			'icon_qchat.png',	'chatta'); ?>
		<? makeButton(false,	'makeMail(\''.$id.'\')',			'icon_mail_new.png',	'maila'); ?>
		<?
			if (!$user->isFriends($id, 1)) {
				makeButton(false,	'makeRelation(\''.$id.'\')',	'icon_friends.png',	'bli vän');
			}
		?>
		<? makeButton(false,	'goLoc(\''.l('user', 'abuse', $id).'\')',	'icon_abuse.png',	'abuse'); ?>
	</div>
	<br class="clr" />
<? } ?>

