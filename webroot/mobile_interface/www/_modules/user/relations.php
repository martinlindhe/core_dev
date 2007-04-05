<?
	require_once('relations.fnc.php');

	if(isset($_GET['create'])) {
		include('relations_create.php');
		exit;
	}

	/*
	//'ins_rel' är typen av relation
	//martin kommenterade ut detta. samma sak hanteras redan i relations_create.php
	//todo: eller var detta för att acceptera relation!?
	if(!empty($_POST['ins_rel']) && !$own) {
		$error = sendRelationRequest($_POST['ins_rel']);
		if ($error) {
			errorACT($error, l('user', 'relations', $l['id_id']));
			die;
		}
	}
	*/

	if (!empty($_POST['d']) && is_numeric($_POST['d']) || !empty($_GET['d']) && is_numeric($_GET['d'])) {
		$d = (!empty($_POST['d'])?$_POST['d']:$_GET['d']);
		removeRelation($d, $s['id_id']);
	}

	if(!empty($_GET['a'])) {
		$error = acceptRelationRequest($_GET['a'], $s);
		if ($error !== true) {
			errorACT($error, l('user', 'relations'));
		} else {
			reloadACT(l('user', 'relations'));
		}
	}

	
	//Detta är möjligheter att välja hur kompislistan ska sorteras. Funktionen exponeras för stunden inte på citysurf
	$thisord = 'A';
	if(!empty($_POST['ord']) && ($_POST['ord'] == 'A' || $_POST['ord'] == 'L' || $_POST['ord'] == 'R' || $_POST['ord'] == 'O')) {
		$thisord = $_POST['ord'];
	}
	if($thisord == 'L') {
		$page = 'login';
		$ord = 'u.lastonl_date DESC';
	} elseif($thisord == 'R') {
		$page = 'rel';
		$ord = 'rel.rel_id ASC';
	} elseif(!$thisord || $thisord == 'O') {
		$page = 'onl';
		$ord = 'isonline DESC';
	} else {
		$page = 'alpha';
		$ord = 'u.u_alias ASC';
	}
	$view = false;
	if(!empty($_GET['key']) && is_numeric($_GET['key']) && $own) {
		$view = $_GET['key'];
	}

	$blocked = false;
	if($own && isset($_GET['blocked'])) {
		$blocked = true;
		if(isset($_GET['del'])) {
			$check = $sql->queryResult("SELECT friend_id FROM {$t}userblock WHERE main_id = '".secureINS($_GET['del'])."' AND user_id = '".$l['id_id']."' LIMIT 1");
			if($check) {
				$sql->queryUpdate("DELETE FROM {$t}userblock WHERE user_id = '".$l['id_id']."' AND friend_id = '".$check."' AND rel_id = 'u' LIMIT 1");
				$sql->queryUpdate("DELETE FROM {$t}userblock WHERE friend_id = '".$l['id_id']."' AND user_id = '".$check."' AND rel_id = 'f' LIMIT 1");
			}
			errorACT('Nu har du slutat att blockera personen.', l('user', 'relations').'&blocked');
		}
		$res = $sql->query("SELECT ".CH." b.main_id, b.friend_id, b.activated_date, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth, u.level_id FROM {$t}userblock b INNER JOIN {$t}user u ON b.friend_id = u.id_id AND u.status_id = '1' WHERE b.user_id = '".secureINS($l['id_id'])."' AND rel_id = 'u'", 0, 1);
	} else { 
		$paging = paging(@$_GET['p'], 50);
		$paging['co'] = $sql->queryResult("SELECT ".CH." COUNT(*) as count FROM {$t}userrelation rel INNER JOIN {$t}user u ON u.id_id = rel.friend_id AND u.status_id = '1' WHERE rel.user_id = '".secureINS($s['id_id'])."'");
		$res = $sql->query("SELECT ".CH." rel.main_id, rel.user_id, rel.rel_id, u.id_id, u.u_alias, u.account_date, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.lastlog_date, u.u_sex, u.u_birth, u.level_id FROM {$t}userrelation rel INNER JOIN {$t}user u ON u.id_id = rel.friend_id AND u.status_id = '1' WHERE rel.user_id = '".secureINS($s['id_id'])."' ORDER BY $ord LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	}
	$is_blocked = $blocked;
	$page = 'relations';

	require(DESIGN.'head_user.php');
	if($own && !$blocked) {
		$paus = $sql->query("SELECT q.main_id, q.sent_cmt, q.sent_date, u.id_id, u.u_alias, u.account_date, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth, u.level_id FROM {$t}userrelquest q INNER JOIN {$t}user u ON u.id_id = q.sender_id AND u.status_id = '1' WHERE q.user_id = '".secureINS($l['id_id'])."' AND q.status_id = '0' ORDER BY q.main_id DESC", 0, 1);
		$wait = $sql->query("SELECT q.main_id, q.sent_cmt, q.sent_date, u.id_id, u.u_alias, u.account_date, u.u_picid, u.u_picd, u.status_id, u.lastonl_date, u.u_sex, u.u_birth, u.level_id FROM {$t}userrelquest q INNER JOIN {$t}user u ON u.id_id = q.user_id AND u.status_id = '1' WHERE q.sender_id = '".secureINS($l['id_id'])."' AND q.status_id = '0' ORDER BY q.main_id DESC", 0, 1);
		require("relations_user.php");
	}
	$page = 'friends';
	$blocked = $is_blocked;
	if($blocked) $page = 'blocked';
	$menu = array('friends' => array(l('user', 'relations'), 'vänner'), 'blocked' => array(l('user', 'relations').'&blocked', 'ovänner'));

?>
			<?=($own?'<div class="mainHeader2"><h4>'.makeMenu($page, $menu).'</h4></div>':'<div class="mainHeader2"><h4>vänner</h4></div>')?>
			<div class="mainBoxed2">
<? if(!$blocked) dopaging($paging, l('user', 'relations', $s['id_id']).'p=', '&ord='.$thisord, 'med', STATSTR); ?>
<table cellspacing="0" width="586">
<?
	if(!empty($res) && count($res)) {
	if(!$blocked) {
	$i = 0;
	foreach($res as $row) {
		$i++;
		$gotpic = ($row['u_picvalid'] == '1')?true:false;
echo '
<tr'.(($gotpic && $view != $row['main_id'])?' onmouseover="this.className = \'t1\'; dumblemumble(\''.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'\', 2);" onmouseout="this.className = \'\'; mumbledumble(\''.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'\', 0, 2);"':' onmouseover="this.className = \'t1\';" onmouseout="this.className = \'\';"').'>
	<td class="spac pdg"><a name="R'.$row['main_id'].'"></a>'.$user->getstring($row).'</td>
	<td class="cur spac pdg" onclick="goUser(\''.$row['id_id'].'\');">'.secureOUT($row['rel_id']).'</td>
	<td class="cur pdg spac cnt">'.(($row['u_picvalid'] == '1')?'<img src="./_img/icon_gotpic.gif" alt="har bild" style="margin-top: 2px;" />':'&nbsp;').'</td>
	<td class="cur spac pdg rgt" onclick="goUser(\''.$row['id_id'].'\');">'.(($user->isonline($row['account_date']))?'<span class="on">online ('.nicedate($row['lastlog_date']).')</span>':'<span class="off">'.nicedate($row['lastonl_date']).'</span>').'</td>
	'.(($own)?'<td class="spac rgt pdg_tt"><a href="'.l('user', 'relations', $s['id_id'], $row['main_id']).'#R'.$row['main_id'].'"><img src="'.OBJ.'icon_change.gif" title="Ändra" style="margin-bottom: -4px;" /></a> - <a class="cur" onclick="if(confirm(\'Säker ?\')) goLoc(\''.l('user', 'relations', $row['id_id'], '0').'&d='.$row['main_id'].'\');"><img src="'.OBJ.'icon_del.gif" title="Radera" style="margin-bottom: -4px;" /></a></td>':'').'
</tr>
';
if($view == $row['main_id']) {
echo '
	<tr>
		<td colspan="5" class="pdg">
		<form name="do" action="'.l('user', 'relations', $row['id_id']).'" method="post">
';
	if($isAdmin)
		echo '<input type="text" class="txt" name="ins_rel" onfocus="this.select();" value="'.secureOUT($row['rel_id']).'" style="width: 205px; margin-right: 10px;">';
	else {
		echo '<select name="ins_rel" class="txt">';
		foreach($rel as $r) {
			$sel = ($r[1] == $row['rel_id'])?' selected':'';
			echo '<option value="'.$r[0].'"'.$sel.'>'.secureOUT($r[1]).'</option>';
		}
		echo '</select>';
	}
echo'
		<input type="submit" class="br" value="spara" style="margin-left: 10px;"></form>
		</td>
	</tr>
';
} elseif($gotpic) echo '<tr id="m_pic:'.$i.'" style="display: none;"><td colspan="2">'.$user->getphoto($row['id_id'].$row['u_picid'].$row['u_picd'], $row['u_picvalid'], 0, 0, '', ' ').'<span style="display: none;">'.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'</span></td></tr>';
	}
} else {
	  foreach($res as $row){
echo '
<tr>
	<td class="spac pdg">'.$user->getstring($row, '', array('nolink' => 1)).'</td>
	<td class="spac pdg rgt">'.nicedate($row['activated_date']).'</td>
	<td class="spac pdg rgt"><a class="cur" onclick="return confirm(\'Säker ?\')" href="'.l('user', 'relations').'&blocked&del='.$row['main_id'].'"><img src="'.OBJ.'icon_del.gif" title="Avblockera" style="margin-bottom: -4px;" /></a></td>
</tr>';
	  }
	}

	} else echo '<tr><td class="spac pdg cnt">Inga '.($blocked?'ovänner':'vänner').'.</td></tr>';
?>
</table>
<? if(!$blocked) dopaging($paging, l('user', 'relations', $s['id_id']).'p=', '&ord='.$thisord, 'medmin'); ?>
		</div>
	</div>
<?
	require(DESIGN.'foot_user.php');
	require(DESIGN.'foot.php');
?>
