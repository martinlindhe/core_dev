<?
	require_once('relations.fnc.php');

	if(isset($_GET['create'])) {
		include('relations_create.php');
		die;
	}

	/*
	//'ins_rel' är typen av relation
	//martin kommenterade ut detta. samma sak hanteras redan i relations_create.php
	//todo: eller var detta för att acceptera relation!?
	if(!empty($_POST['ins_rel']) && !$own) {
		$error = sendRelationRequest($s['id_id'], $_POST['ins_rel']);
		if ($error) {
			errorACT($error, l('user', 'relations', $l['id_id']));
			die;
		}
	}
	*/

	if (!empty($_POST['d']) || !empty($_GET['d']))
	{
		$d = !empty($_POST['d']) ? $_POST['d'] : $_GET['d'];

		if (removeRelation($d) === true) reloadACT(l('user', 'relations'));
	}
	else if(!empty($_GET['a']))
	{
		$error = acceptRelationRequest($_GET['a']);
		if ($error === true) reloadACT(l('user', 'relations'));
		errorACT($error, l('user', 'relations'));
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
			unblockRelation($_GET['del']);
			errorACT('Nu har du slutat att blockera personen.', l('user', 'relations').'&blocked');
		}
		$res = getBlockedRelations();
	} else { 
		$paging = paging(@$_GET['p'], 50);
		$paging['co'] = getRelationsCount($s['id_id']);
		$res = getRelations($s['id_id'], $ord, $paging['slimit'], $paging['limit']);
	}
	$is_blocked = $blocked;
	$page = 'relations';

	require(DESIGN.'head_user.php');
	if ($own && !$blocked) {
		
		//paus är förfrågningar som andra skickat till dig
		$paus = getRelationRequestsToMe();
		
		//wait är förfrågningar du väntar på svar på
		$wait = getRelationRequestsFromMe();
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
