<?
	$s = $l;
	$own = true;
	$page = 'in';
	if(isset($_GET['out'])) $page = 'out';
	if(!empty($_GET['del_msg']) && is_numeric($_GET['del_msg'])) {
		$res = $sql->queryLine("SELECT main_id, status_id, user_id, sender_id, user_read, sender_status FROM {$t}usermail WHERE main_id = '".secureINS($_GET['del_msg'])."' LIMIT 1");
		if(!empty($res) && count($res) && ($res[1] == '1' || $res[5] == '1')) {
			if($res[2] == $l['id_id'] || $res[3] == $l['id_id']) {
				if($res[2] == $l['id_id'] && $res[1] == '1') {
					$sql->queryUpdate("UPDATE {$t}usermail SET status_id = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
					if(!$res[4]) $user->notifyDecrease('mail', $res[2]);
					$user->counterDecrease('mail', $res[2]);
				}
				if($res[3] == $l['id_id'] && $res[5] == '1') {
					$sql->queryUpdate("UPDATE {$t}usermail SET sender_status = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
				}
			}
			if(isset($_GET['p'])) popupACT('Meddelandet raderat.', '', '500', l('user', 'mail', $page));
			if($page == 'out')
				reloadACT(l('user', 'mail').'&'.$page);
			else
				reloadACT(l('user', 'mail'));
		}
	} elseif(!empty($_POST['chg']) && is_array($_POST['chg']) && count($_POST['chg'])) {
		foreach($_POST['chg'] as $val) {
		$res = $sql->queryLine("SELECT main_id, status_id, user_id, sender_id, user_read, sender_status FROM {$t}usermail WHERE main_id = '".secureINS($val)."' LIMIT 1");
		if(!empty($res) && count($res) && ($res[1] == '1' || $res[5] == '1')) {
			if($isAdmin || $res[2] == $l['id_id'] || $res[3] == $l['id_id']) {
				if($res[2] == $l['id_id']) {
					if($res[1] == '1') $user->counterDecrease('mail', $l['id_id']);
					$sql->queryUpdate("UPDATE {$t}usermail SET status_id = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
				} elseif($res[3] == $l['id_id']) {
					$sql->queryUpdate("UPDATE {$t}usermail SET sender_status = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
				} else {
					if($res[1] == '1') $user->counterDecrease('mail', $res[2]);
					if($res[5] == '1') $user->counterDecrease('mail', $res[3]);
					$sql->queryUpdate("UPDATE {$t}usermail SET status_id = '2', sender_status = '2' WHERE main_id = '".secureINS($res[0])."' LIMIT 1");
				}
				#$user->setinfo($s['id_id'], 'mail_offset', 'content - 1');
				if(!$res[4]) $user->notifyDecrease('mail', $s['id_id']);
			}
		}
		}
		if($page == 'out')
			reloadACT(l('user', 'mail').'&'.$page);
		else
			reloadACT(l('user', 'mail'));

	}
	$menu = array('in' => array(l('user', 'mail'), 'inkorg'), 'out' => array(l('user', 'mail').'&out', 'utkorg'));
	$paging = paging(@$_GET['p'], 20);
	if($page == 'in') {
		$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usermail WHERE user_id = '".secureINS($l['id_id'])."' AND status_id = '1'");
		$res = $sql->query("SELECT m.main_id, m.sent_date, m.user_read, m.sent_ttl, u.id_id, u.u_alias, u.u_picid, u.u_picvalid, u.u_picd, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM {$t}usermail m LEFT JOIN {$t}user u ON u.id_id = m.sender_id AND u.status_id = '1' WHERE m.user_id = '".secureINS($l['id_id'])."' AND m.status_id = '1' ORDER BY m.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	} else {
		$paging['co'] = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usermail WHERE sender_id = '".secureINS($l['id_id'])."' AND sender_status = '1'");
		$res = $sql->query("SELECT m.main_id, m.sent_date, m.user_read, m.sent_ttl, u.id_id, u.u_alias, u.u_picid, u.u_picvalid, u.u_picd, u.account_date, u.status_id, u.u_sex, u.u_birth, u.level_id FROM {$t}usermail m LEFT JOIN {$t}user u ON u.id_id = m.user_id AND u.status_id = '1' WHERE m.sender_id = '".secureINS($l['id_id'])."' AND m.sender_status = '1' ORDER BY m.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	}
	require(DESIGN."head.php");
?>
	<div id="mainContent">
<script type="text/javascript">
function toggle(type) {
	type = (type.checked)?true:false;
	for(i = 0; i < document.m.length; i++) {

		var toggle = document.m.elements[i];
		if(toggle.type == 'checkbox') {
			toggle.checked = type;
		}
	}
}
function openMail(th, id) {
	th.className = th.className.replace('act_bg', '');
	makeBig(id);
}
</script>
			<div class="mainHeader2"><h4><?=makeMenu($page, $menu)?></h4></div>
			<div class="mainBoxed2">
<?dopaging($paging, l('user', 'mail', $s['id_id']).'&'.$page.'&p=', '', 'big', STATSTR);?>
<form name="m" action="<?=l('user', 'mail', $s['id_id']).'&'.$page?>" method="post">
<?
if(count($res) && !empty($res)) {
echo '<input type="checkbox" onclick="toggle(this);" class="chk" style="margin-bottom: 3px;"><input type="submit" value="radera mark." class="btn2_min" /><hr />';
}
?>
<table cellspacing="0" width="586">
<?
	if(count($res) && !empty($res)) {
	foreach($res as $row) {
		$c = ($page == 'in' && !$row['user_read'])?' act_bg':'';
/*echo '<tr'.(($c)?' class="'.$c.'"':' onmouseover="this.className = \'t1\';" onmouseout="this.className = \'\';"').'>
	<td class="spac pdg_l" style="padding-right: 0; width: 10px;"><input type="checkbox" class="chk" name="chg[]" value="'.$row['main_id'].'" /></td>
	<td class="cur spac pdg_l"><nobr><img src="'.OBJ.'icon_mail.gif" style="margin: 0 5px -2px 5px;" />
	<!--
	 onclick="openMail(this.parentNode, \'mail_read.php?id='.$row['main_id'].'\');"
	<a class="cur up bld" onclick="pop(this.parentNode.parentNode.parentNode, \'mail_read.php?id='.$row['main_id'].'\');">'.$user->getstring($row, array('noicon' => true, 'nolink' => true)).'</a>
	//-->
	<a class="cur up bld" href="mail_read.php?id='.$row['main_id'].'">'.$user->getstring($row, array('noicon' => true, 'nolink' => true)).'</a>&nbsp;</nobr></td>
	<td class="cur spac pdg_l" onclick="openMail(this.parentNode, \'mail_read.php?id='.$row['main_id'].'\');"><div style="overflow: hidden; height: 14px;">'.secureOUT($row['sent_ttl']).'&nbsp;</div></td>
	<td class="cur spac pdg_l rgt" onclick="openMail(this.parentNode, \'mail_read.php?id='.$row['main_id'].'\');"><nobr>'.nicedate($row['sent_date'], 1, 1).'</nobr></td>
	<td class="spac pdg_l rgt mid"><a href="mail.php?'.$page.'&del_msg='.$row['main_id'].'" onclick="return confirm(\'Säker ?\n\nMeddelandet kommer att raderas helt och hållet!\');"><img src="'.OBJ.'icon_del.gif" title="Radera" /></a></td>
</tr>
';*/
echo '<tr'.($c?' class="'.$c.'"':'').'>
	<td style="width: 10px; padding-right: 10px;"><input type="checkbox" class="chk" name="chg[]" value="'.$row['main_id'].'" /></td>
	<td class="cur" onclick="goLoc(\''.l('user','mailread', $row['main_id']).'&'.$page.'\');"><div style="overflow: hidden; height: 20px; width: 200px; padding-top: 4px;"><a href="'.l('user','mailread', $row['main_id']).'&'.$page.'">'.($row['sent_ttl']?secureOUT($row['sent_ttl']):'<em>Ingen titel</em>').'</a>&nbsp;</div></td>
	<td style="padding-top: 4px;">'.$user->getstring($row).'</td>
	<td class="rgt" style="padding-top: 4px;">'.nicedate($row['sent_date'], 1, 1).'</td>
	<td class="rgt mid"><a href="'.l('user', 'mail').'&'.$page.'&del_msg='.$row['main_id'].'" onclick="if(confirm(\'Säker ?\n\nMeddelandet kommer att raderas.\')) goLoc(\''.l('user', 'mail', $l['id_id']).'&'.$page.'&del_msg='.$row['main_id'].'\');"><img src="'.OBJ.'icon_del.gif" /></a></td>
</tr>
';
	} } else echo '<tr><td class="pdg cnt">Inga brev.</td></tr>';
?>
</table>
</form>
			</div>
	</div>
<?
	require(DESIGN.'foot.php');
?>
