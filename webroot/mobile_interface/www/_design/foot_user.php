		<div class="smallContent mll" style="">
<?
if(!defined('U_NOINFO')) echo '
			<div class="smallHeader1"><h4>personliga fakta</h4></div>
			<div class="smallBoxed1">
			<div class="l mrg">'.str_replace(', ', '<br />', $user->tagline($s['u_pst'])).'</div>
			'.($s['u_pstlan_id']?'<div class="pdg r"><img alt="'.$user->tagline($s['u_pst']).'" src="'.OBJ.'loc1_'.$s['u_pstlan_id'].'.gif" /></div>':'').'
			<br class="clr" />
			<div class="pdg">
				<b>• inloggningar:</b> '.@intval($info['login_offset'][1]).'<br />
				<b>• besökare:</b> '.@intval($info['visit_cnt'][1]).'<br />
			</div>
			</div>
';
/*<!--			<b>registrerad:</b> '.(nicedate($s['u_regdate'], 2)).'<br />-->*/
if(@!$own && $l) {
	$txt = array('age' => 'Ålder', 'sex' => 'Sexliv', 'children' => 'Barn', 'music' => 'Musiksmak', 'tobacco' => 'Tobak', 'alcohol' => 'Alkohol', 'wants' => 'Vill ha', 'civil' => 'Civilstatus', 'attitude' => 'Attityd');
	$myinfo = $user->getcontent($l['id_id'], 'user_head');
	echo '
			<div class="smallHeader3"><h4>matchmaking</h4></div>
			<div class="smallFilled3">
<style type="text/css">
#diverse { width: 176px; }
#diverse td { padding: 2px 2px 3px 2px; table-layout: fixed; }
#diverse .rgt, #diverse .lft, #diverse .rgt div, #diverse .lft div { overflow: hidden; width: 80px; }
#diverse .rgt { color: #974b4b; }
#diverse .lft { color: #4c5195; }
#diverse .rgt, #diverse .lft, #diverse .cnt { vertical-align: middle; }
#diverse .cnt { padding: 2px 3px 3px 3px; }
#diverse .usr, #diverse .usr { height: 32px; }
</style>
<table cellspacing="0" id="diverse">
<tr>
	<td class="rgt"><div class="usr">'.$user->getstring($s, '', array('noage' => 1)).'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div class="usr">'.$user->getstring($l, '', array('noage' => 1)).'</div></td>
</tr>
<tr title="'.$txt['age'].'">
	<td class="rgt"><div>'.$user->doage($s['u_birth']).' år</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.$user->doage($l['u_birth']).' år</div></td>
</tr>
'.(@$det_type[$info['det_civil_type'][1]] || @$det_type[$myinfo['det_civil_type'][1]]?'<tr title="'.$txt['civil'].'">
	<td class="rgt"><div>'.(!empty($info['det_civil'][1])?secureOUT($info['det_civil'][1]):(@$det_type[$info['det_civil_type'][1]]?@$det_type[$info['det_civil_type'][1]]:'-')).'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.(!empty($myinfo['det_civil'][1])?secureOUT($myinfo['det_civil'][1]):(@$det_type[$myinfo['det_civil_type'][1]]?@$det_type[$myinfo['det_civil_type'][1]]:'-')).'</div></td>
</tr>':'').'
'.(@$info['det_attitude'][1] || @$myinfo['det_attitude'][1]?'<tr title="'.$txt['attitude'].'">
	<td class="rgt"><div>'.(@$info['det_attitude'][1]?@$info['det_attitude'][1]:'-').'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.(@$myinfo['det_attitude'][1]?@$myinfo['det_attitude'][1]:'-').'</div></td>
</tr>':'').'
'.(@$info['det_wants'][1] || @$myinfo['det_wants'][1]?'<tr title="'.$txt['wants'].'">
	<td class="rgt"><div>'.(@$info['det_wants'][1]?@$info['det_wants'][1]:'-').'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.(@$myinfo['det_wants'][1]?@$myinfo['det_wants'][1]:'-').'</div></td>
</tr>':'').'
'.(@$info['det_alcohol'][1] || @$myinfo['det_alcohol'][1]?'<tr title="'.$txt['alcohol'].'">
	<td class="rgt"><div>'.(@$info['det_alcohol'][1]?@$info['det_alcohol'][1]:'-').'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.(@$myinfo['det_alcohol'][1]?@$myinfo['det_alcohol'][1]:'-').'</div></td>
</tr>':'').'
'.(@$myinfo['det_tobacco'][1] || @$info['det_tobacco'][1]?'<tr title="'.$txt['tobacco'].'">
	<td class="rgt"><div>'.(@$info['det_tobacco'][1]?@$info['det_tobacco'][1]:'-').'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.(@$myinfo['det_tobacco'][1]?@$myinfo['det_tobacco'][1]:'-').'</div></td>
</tr>':'').'
'.(@$myinfo['det_children'][1] || @$info['det_children'][1]?'<tr title="'.$txt['children'].'">
	<td class="rgt"><div>'.(@$info['det_children'][1]?@$info['det_children'][1]:'-').'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.(@$myinfo['det_children'][1]?@$myinfo['det_children'][1]:'-').'</div></td>
</tr>':'').'
'.(@$myinfo['det_music'][1] || @$info['det_music'][1]?'<tr title="'.$txt['music'].'">
	<td class="rgt"><div>'.(@$info['det_music'][1]?@$info['det_music'][1]:'-').'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.(@$myinfo['det_music'][1]?@$myinfo['det_music'][1]:'-').'</div></td>
</tr>':'').'
'.(@$info['det_sex'][1] || @$myinfo['det_sex'][1]?'<tr title="'.$txt['sex'].'">
	<td class="rgt"><div>'.(@$info['det_sex'][1]?@$info['det_sex'][1]:'-').'</div></td>
	<td class="cnt">•</td>
	<td class="lft"><div>'.(@$myinfo['det_sex'][1]?@$myinfo['det_sex'][1]:'-').'</div></td>
</tr>':'').'
</table>
			</div>
';
}
if(defined('U_VISIT')) {
	$res = $sql->query("SELECT o.visit_date, u.id_id, u.u_alias, u.u_sex, u.u_birth, u.u_picvalid, u.u_picid, u.u_picd FROM {$t}uservisit o INNER JOIN {$t}user u ON u.id_id = o.visitor_id AND u.status_id = '1' WHERE o.user_id = '".$s['id_id']."' ORDER BY o.main_id DESC LIMIT ".(isset($_GET['more'])?'10':'5'), 0, 1);
	echo '<a name="visit"></a>
	<div class="smallHeader2"><h4>senaste besökare - <a href="'.l('user', 'view', $s['id_id']).(!isset($_GET['more'])?'&more#visit">fler':'#visit">färre').'</a></h4></div>
	<div class="smallBoxed2">
	<ul class="userlist">
';
	if(!empty($res) && count($res)) {
		$i = 0;
		$nl = true;
		/*foreach($res as $row) {
			if($nl) echo (($i)?'</tr>':'').'<tr>';
			$extra=array();
			$extra['text']=secureOUT($row['u_alias'].' '.$row['u_sex'].$user->doage($row['u_birth']));
			//secureOUT($row['u_alias'].' '.$row['u_sex'].$user->doage($row['u_birth']));
			echo '<td style="padding: 0 0 6px '.((!$nl)?'8':'1').'px;">'.$user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, 1, $user->getstring($s, '', @$uarr)).'</td>';
			if($i % 8 == 0) $nl = true; else $nl = false;
		}*/
		if(isset($_GET['more'])) {
		foreach($res as $row) {
			echo '<li'.(!$i?' style="border: 0;"':'').'>'.$user->getstring($row).'<br /><br />besökte: '.nicedate($row['visit_date']).'</li>';
			$i++;
		} } else {
		foreach($res as $row) {
			echo '<li'.(!$i?' style="border: 0;"':'').'>'.$user->getstring($row).'</li>';
			$i++;
		} }
	} else {
echo '
	<li>Inga besökare.</li>
';
	}
echo '
	</ul>
	</div>
';
}
?>

		</div>
