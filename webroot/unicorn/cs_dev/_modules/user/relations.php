<?
	if(isset($_GET['create'])) {
		include('relations_create.php');
		die;
	}
	
	//detta ändrar typ av relations-förfrågan för pågående förfrågningar (t.ex från "Granne" till "Sambo")
	//eller skickar en ny förfrågan vid ändring av relationstyp
	if(!empty($_POST['ins_rel'])) {
		$friend_id = $s['id_id'];
		if (!empty($_POST['friend_id'])) $friend_id = $_POST['friend_id'];
		$error = sendRelationRequest($friend_id, $_POST['ins_rel']);
		if ($error === true) {
			errorACT('Du har nu ändrat typ av förfrågan.', l('user', 'relations'));
			die;
		}
	}

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

	if ($own) {
		for ($i=1; $i<=200; $i++) {
			if (isset($_POST['allow_gallx_'.$i])) {
				setGallXStatus($l['id_id'], $_POST['allow_gallx_'.$i], 1);
			} else if (isset($_POST['deny_gallx_'.$i])) {
				setGallXStatus($l['id_id'], $_POST['deny_gallx_'.$i], 0);
			}
		}
	}


	$blocked = false;
	if($own && isset($_GET['blocked'])) {
		$blocked = true;
		if(isset($_GET['del'])) {
			unblockRelation($_GET['del']);
			errorACT('Nu har du slutat att blockera personen.', l('user', 'relations').'&amp;blocked');
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
?>
<div class="subHead">relationer</div><br class="clr"/>

<?
	if ($own && !$blocked) {		
		//paus är förfrågningar som andra skickat till dig
		$paus = getRelationRequestsToMe();

		//wait är förfrågningar du väntar på svar på
		$wait = getRelationRequestsFromMe();
		require("relations_user.php");
		echo '<br/>';
	}
	
	$page = 'friends';
	if ($blocked) $page = 'blocked';
	$menu = array('friends' => array(l('user', 'relations'), 'vänner'), 'blocked' => array(l('user', 'relations').'&amp;blocked', 'blockade'));
?>

<div class="bigHeader"><?=($own?makeMenu($page, $menu):'vänner')?></div>
<div class="bigBody">
<?
	if(!$blocked) dopaging($paging, l('user', 'relations', $s['id_id']).'p=', '&amp;ord='.$thisord, 'med', STATSTR);

	if ($own) echo '<form method="post" action="">';
?>	
	<table summary="" cellspacing="0" width="586">
	<?
	if(!empty($res) && count($res)) {
		if(!$blocked) {
			$i = 0;
			foreach($res as $row) {
				$i++;
				$gotpic = (@$row['u_picvalid'] == '1')?true:false;
				echo '<tr'.(($gotpic && $view != $row['main_id'])?' onmouseover="this.className = \'t1\'; dumblemumble(\''.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'\', 2);" onmouseout="this.className = \'\'; mumbledumble(\''.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'\', 0, 2);"':' onmouseover="this.className = \'t1\';" onmouseout="this.className = \'\';"').'>';
					echo '<td class="spac pdg"><a name="R'.$row['main_id'].'"></a>'.$user->getstring($row).'</td>';
					echo '<td class="cur spac pdg" onclick="goUser(\''.$row['id_id'].'\');">'.secureOUT($row['rel_id']).'</td>';
					echo '<td class="cur pdg spac cnt">'.((@$row['u_picvalid'] == '1')?'<img src="./_img/icon_gotpic.gif" alt="har bild" style="margin-top: 2px;" />':'&nbsp;').'</td>';
					echo '<td class="cur spac pdg rgt" onclick="goUser(\''.$row['id_id'].'\');">'.(($user->isonline($row['account_date']))?'<span class="on">online ('.nicedate($row['lastlog_date']).')</span>':'<span class="off">'.nicedate($row['lastonl_date']).'</span>').'</td>';
					if ($own) {
						echo '<td class="spac rgt pdg_tt">';
						echo '<input type="hidden" name="deny_gallx_'.$i.'" value="'.$row['id_id'].'"/>';
						echo '<input type="checkbox" name="allow_gallx_'.$i.'" value="'.$row['id_id'].'"'.($row['gallx']?' checked="checked"':'').' title="Visa Galleri X för den här personen"> ';
						echo '<a href="'.l('user', 'relations', $s['id_id'], $row['main_id']).'#R'.$row['main_id'].'"><img src="'.OBJ.'icon_change.gif" alt="" title="Ändra" style="margin-bottom: -4px;" /></a>';
						echo ' - <a class="cur" onclick="if(confirm(\'Säker ?\')) goLoc(\''.l('user', 'relations', $row['id_id'], '0').'&amp;d='.$row['id_id'].'\');"><img src="'.OBJ.'icon_del.gif" alt="" title="Radera" style="margin-bottom: -4px;" /></a>';
						echo '</td>';
					}
				echo '</tr>';
		
				if($view == $row['main_id']) {
					//Visar "Ändra typ av relation"
					echo '<tr><td colspan="5" class="pdg">';
					echo '<form name="do" action="" method="post">';
					echo '<input type="hidden" name="friend_id" value="'.$row['id_id'].'"/>';
					echo '<select name="ins_rel" class="txt">';
					foreach($rel as $r) {
						$sel = ($r[1] == $row['rel_id'])?' selected':'';
						echo '<option value="'.$r[0].'"'.$sel.'>'.secureOUT($r[1]).'</option>';
					}
					echo '</select>';
					echo '<input type="submit" value="spara" style="margin-left: 10px;"></form>';
					echo '</td></tr>';
				} else if($gotpic) {
					echo '<tr id="m_pic:'.$i.'" style="display: none;"><td colspan="2">'.$user->getphoto($row['id_id'].$row['u_picid'].$row['u_picd'], $row['u_picvalid'], 0, 0, '', ' ').'<span style="display: none;">'.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'</span></td></tr>';
				}
			}
		} else {
		  foreach($res as $row) {
				echo '
				<tr>
					<td class="spac pdg">'.$user->getstring($row, '', array('nolink' => 1)).'</td>
					<td class="spac pdg rgt">'.nicedate($row['activated_date']).'</td>
					<td class="spac pdg rgt"><a class="cur" onclick="return confirm(\'Säker ?\')" href="'.l('user', 'relations').'&amp;blocked&amp;del='.$row['id_id'].'"><img src="'.OBJ.'icon_del.gif" title="Avblockera" style="margin-bottom: -4px;" /></a></td>
				</tr>';
		  }
		}
		
	} else {
		echo '<tr><td class="spac pdg cnt">Inga '.($blocked?'blockade':'vänner').'.</td></tr>';
	}

	echo '</table>';

	if ($own) {
		echo '<input type="submit" value="Spara" class="btn2_min"/>';
		echo '</form>';
	}

?>

</div>

<?
	require(DESIGN.'foot_user.php');
?>
