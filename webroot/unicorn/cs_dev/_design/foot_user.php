</div>	<!-- end mainContent -->

<div id="rightMenu">
	
<?
	if (!empty($own)) {
		if(@$l['id_id'] == @$s['id_id']) {
			if(@intval($info['gb_offset'][1]) != @intval($_SESSION['data']['offsets']['gb_offset'])) $_SESSION['data']['offsets']['gb_offset'] = @intval($info['gb_offset'][1]);
			if(@intval($info['gal_offset'][1]) != @intval($_SESSION['data']['offsets']['gal_offset'])) $_SESSION['data']['offsets']['gal_offset'] = @intval($info['gal_offset'][1]);
			if(@intval($info['blog_offset'][1]) != @intval($_SESSION['data']['offsets']['blog_offset'])) $_SESSION['data']['offsets']['blog_offset'] = @intval($info['blog_offset'][1]);
			if(@intval($info['rel_offset'][1]) != @intval($_SESSION['data']['offsets']['rel_offset'])) $_SESSION['data']['offsets']['rel_offset'] = @intval($info['rel_offset'][1]);
			if(@intval($info['mail_offset'][1]) != @intval($_SESSION['data']['offsets']['mail_offset'])) $_SESSION['data']['offsets']['mail_offset'] = @intval($info['mail_offset'][1]);
		}
	}
	
	if (!empty($s) && $s['id_id']) {
		echo '<div class="smallHeader">profil</div>';
		echo '<div class="smallBody">';
			echo '• civilstånd: '.(!empty($info['det_civil'][1])?secureOUT($info['det_civil'][1]):@$det_type[$info['det_civil_type'][1]]).'<br />';
			echo '• attityd: '.@$info['det_attitude'][1].'<br />';
			echo '• musik: '.@$info['det_music'][1].'<br />';
			echo '• vill ha: '.@$info['det_wants'][1].'<br />';
			echo '• alkohol: '.@$info['det_alcohol'][1].'<br />';
			echo '• tobak: '.@$info['det_tobacco'][1].'<br />';
			echo '• sexliv: '.@$info['det_sex'][1].'<br />';
			echo '• barn: '.@$info['det_children'][1].'<br />';
			echo '• längd: '.@$info['det_length'][1].'<br />';
			echo '• vikt: '.@$info['det_weight'][1].'<br />';
		echo '</div><br/>';
	}

	if ($l['id_id'] && empty($own) && !empty($s) && $s['id_id']) {
		$txt = array('age' => 'Ålder', 'sex' => 'Sexliv', 'children' => 'Barn', 'music' => 'Musiksmak', 'tobacco' => 'Tobak', 'alcohol' => 'Alkohol', 'wants' => 'Vill ha', 'civil' => 'Civilstatus', 'attitude' => 'Attityd', 'weight' => 'Vikt');
		$myinfo = $user->getcontent($l['id_id'], 'user_head');
		
		echo '<div class="smallHeader">matchmaking</div>';
		echo '<div class="smallBody">';
		echo '<table summary="" cellspacing="0" id="diverse">';
			echo '<tr>';
				echo '<td class="cnt" colspan="3"><div class="usr">jag vs. '.$user->getstring($s, '', array('noage' => 1)).'</div></td>';
			echo '</tr>';
			echo '<tr title="'.$txt['age'].'">';
				echo '<td class="rgt"><div>'.$user->doage($l['u_birth']).' år</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.$user->doage($s['u_birth']).' år</div></td>';
			echo '</tr>';

			if (@$det_type[$info['det_civil_type'][1]] && @$det_type[$myinfo['det_civil_type'][1]]) {
				echo '<tr title="'.$txt['civil'].'">';
				echo '<td class="rgt"><div>'.(!empty($myinfo['det_civil'][1])?secureOUT($myinfo['det_civil'][1]):(@$det_type[$myinfo['det_civil_type'][1]]?@$det_type[$myinfo['det_civil_type'][1]]:'-')).'</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.(!empty($info['det_civil'][1])?secureOUT($info['det_civil'][1]):(@$det_type[$info['det_civil_type'][1]]?@$det_type[$info['det_civil_type'][1]]:'-')).'</div></td>';
				echo '</tr>';
			}
			if (@$info['det_attitude'][1] && @$myinfo['det_attitude'][1]) {
				echo '<tr title="'.$txt['attitude'].'">';
				echo '<td class="rgt"><div>'.(@$myinfo['det_attitude'][1]?@$myinfo['det_attitude'][1]:'-').'</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.(@$info['det_attitude'][1]?@$info['det_attitude'][1]:'-').'</div></td>';
				echo '</tr>';
			}
			if (@$info['det_wants'][1] && @$myinfo['det_wants'][1]) {
				echo '<tr title="'.$txt['wants'].'">';
				echo '<td class="rgt"><div>'.(@$myinfo['det_wants'][1]?@$myinfo['det_wants'][1]:'-').'</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.(@$info['det_wants'][1]?@$info['det_wants'][1]:'-').'</div></td>';
				echo '</tr>';
			}
			if (@$info['det_alcohol'][1] && @$myinfo['det_alcohol'][1]) {
				echo '<tr title="'.$txt['alcohol'].'">';
				echo '<td class="rgt"><div>'.(@$myinfo['det_alcohol'][1]?@$myinfo['det_alcohol'][1]:'-').'</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.(@$info['det_alcohol'][1]?@$info['det_alcohol'][1]:'-').'</div></td>';
				echo '</tr>';
			}
			if (@$myinfo['det_tobacco'][1] && @$info['det_tobacco'][1]) {
				echo '<tr title="'.$txt['tobacco'].'">';
				echo '<td class="rgt"><div>'.(@$myinfo['det_tobacco'][1]?@$myinfo['det_tobacco'][1]:'-').'</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.(@$info['det_tobacco'][1]?@$info['det_tobacco'][1]:'-').'</div></td>';
				echo '</tr>';
			}
			if (@$myinfo['det_children'][1] && @$info['det_children'][1]) {
				echo '<tr title="'.$txt['children'].'">';
				echo '<td class="rgt"><div>'.(@$myinfo['det_children'][1]?@$myinfo['det_children'][1]:'-').'</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.(@$info['det_children'][1]?@$info['det_children'][1]:'-').'</div></td>';
				echo '</tr>';
			}
			if (@$myinfo['det_music'][1] && @$info['det_music'][1]) {
				echo '<tr title="'.$txt['music'].'">';
				echo '<td class="rgt"><div>'.(@$myinfo['det_music'][1]?@$myinfo['det_music'][1]:'-').'</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.(@$info['det_music'][1]?@$info['det_music'][1]:'-').'</div></td>';
				echo '</tr>';
			}
			if (@$info['det_sex'][1] && @$myinfo['det_sex'][1]) {
				echo '<tr title="'.$txt['sex'].'">';
				echo '<td class="rgt"><div>'.(@$myinfo['det_sex'][1]?@$myinfo['det_sex'][1]:'-').'</div></td>';
				echo '<td class="cnt">•</td>';
				echo '<td class="lft"><div>'.(@$info['det_sex'][1]?@$info['det_sex'][1]:'-').'</div></td>';
				echo '</tr>';
			}
			echo '</table>';
		echo '</div><br/>';
	}

	if(defined('U_VISIT')) {
		$res = $sql->query("SELECT o.visit_date, u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picvalid, u.u_picid, u.u_picd FROM s_uservisit o INNER JOIN s_user u ON u.id_id = o.visitor_id AND u.status_id = '1' WHERE o.user_id = '".$s['id_id']."' ORDER BY o.main_id DESC LIMIT ".(isset($_GET['more'])?'10':'5'), 0, 1);
		echo '<a name="visit"></a>';
		if ($own && $user->vip_check(VIP_LEVEL2)) {
			echo '<div class="smallHeader">besökare (<a href="'.l('user', 'view', $s['id_id']).(!isset($_GET['more'])?'&amp;more#visit">fler':'#visit">färre').'</a>)</div>';
		} else {
			echo '<div class="smallHeader">besökare</div>';
		}
		echo '<div class="smallBody">';
		echo '<ul class="friends_list">';
		if(!empty($res) && count($res)) {
			$i = 0;
			$nl = true;
			if (isset($_GET['more'])) {
				foreach($res as $row) {
					echo $user->getstring($row).'besökte: '.nicedate($row['visit_date']).'<br/><br/>';
					$i++;
				}
			} else {
				foreach($res as $row) {
					echo '<li>'.$user->getstring($row).'</li>';
					$i++;
				}
			}
		} else {
			echo '<li>Inga besökare.</li>';
		}
		echo '</ul></div><br/>';
	}

	require('foot.php');
?>
</div>	<!-- end rightMenu -->
