<?
	require_once('config.php');

	$id = $user->id;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id'];

	if(isset($_GET['create'])) {
		require('user_relations_create.php');
		die;
	}
	
	//detta ändrar typ av relations-förfrågan för pågående förfrågningar (t.ex från "Granne" till "Sambo")
	//eller skickar en ny förfrågan vid ändring av relationstyp

	//userid of existing friend to change relation type for
	$change_id = 0;
	if (!empty($_GET['chg']) && is_numeric($_GET['chg']) && $user->id == $id) $change_id = $_GET['chg'];

	if (!empty($_POST['ins_rel']) && $change_id) {
		$error = sendRelationRequest($change_id, $_POST['ins_rel']);
		if ($error === true) {
			errorACT('Du har nu ändrat typ av förfrågan.', 'user_relations.php');
		}
	}

	//ta bort relation	
	if (!empty($_POST['d']) || !empty($_GET['d']))
	{
		$d = !empty($_POST['d']) ? $_POST['d'] : $_GET['d'];

		removeRelation($d);
	}
	else if(!empty($_GET['a']))
	{
		$error = acceptRelationRequest($_GET['a']);
		if ($error != true) errorACT($error, 'user_relations.php');
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

	//ge eller ta bort kompisars möjlighet att se Galleri X
	if ($user->id == $id) {
		for ($i=1; $i<=200; $i++) {
			if (isset($_POST['allow_gallx_'.$i])) {
				setGallXStatus($user->id, $_POST['allow_gallx_'.$i], 1);
			} else if (isset($_POST['deny_gallx_'.$i])) {
				setGallXStatus($user->id, $_POST['deny_gallx_'.$i], 0);
			}
		}
	}


	$blocked = false;
	if ($user->id == $id && isset($_GET['blocked'])) {
		$blocked = true;
		if(isset($_GET['del'])) {
			unblockRelation($_GET['del']);
			errorACT('Nu har du slutat att blockera personen.', 'user_relations.php?blocked');
		}
		$res = getBlockedRelations();
	} else { 
		$paging = paging(@$_GET['p'], 50);
		$paging['co'] = getRelationsCount($id);
		$res = getRelations($id, $ord, $paging['slimit'], $paging['limit']);
	}
	$is_blocked = $blocked;

	$action = 'relations';
	require(DESIGN.'head_user.php');
?>
<div class="subHead">relationer</div><br class="clr"/>

<?
	if ($user->id == $id && !$blocked) {		
		//paus är förfrågningar som andra skickat till dig
		$paus = getRelationRequestsToMe();

		//wait är förfrågningar du väntar på svar på
		$wait = getRelationRequestsFromMe();
		require("relations_user.php");
		echo '<br/>';
	}
	
	$page = 'friends';
	if ($blocked) $page = 'blocked';
	$menu = array(
		'friends' => array('user_relations.php', 'vänner'),
		'blocked' => array('user_relations.php?blocked', 'blockade')
	);
?>

<div class="bigHeader"><?=($user->id == $id ? makeMenu($page, $menu):'vänner')?></div>
<div class="bigBody">
<?
	if (!$blocked) dopaging($paging, 'user_relations.php?p=', '&amp;ord='.$thisord, 'med', STATSTR);

	if ($user->id == $id && !$change_id) echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
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
					echo '<td class="cur spac pdg">'.secureOUT($row['rel_id']).'</td>';
					echo '<td class="cur pdg spac cnt">'.((@$row['u_picvalid'] == '1')?'<img src="./_img/icon_gotpic.gif" alt="har bild" style="margin-top: 2px;" />':'&nbsp;').'</td>';
					echo '<td class="cur spac pdg rgt">'.(($user->isonline($row['account_date']))?'<span class="on">online ('.nicedate($row['lastlog_date']).')</span>':'<span class="off">'.nicedate($row['lastonl_date']).'</span>').'</td>';
					if ($user->id == $id) {
						echo '<td class="spac rgt pdg_tt">';
						echo '<input type="hidden" name="deny_gallx_'.$i.'" value="'.$row['id_id'].'"/>';
						echo '<input type="checkbox" name="allow_gallx_'.$i.'" value="'.$row['id_id'].'"'.($row['gallx']?' checked="checked"':'').' title="Visa Galleri X för den här personen"> ';
						echo '<a href="user_relations.php?id='.$id.'&chg='.$row['id_id'].'#R'.$row['main_id'].'"><img src="'.$config['web_root'].'_gfx/icon_change.gif" alt="" title="Ändra" style="margin-bottom: -4px;" /></a>';
						echo ' - <a class="cur" onclick="if(confirm(\'Säker ?\')) goLoc(\'user_relations.php?id='.$row['id_id'].'&amp;d='.$row['id_id'].'\');"><img src="'.$config['web_root'].'_gfx/icon_del.gif" alt="" title="Radera" style="margin-bottom: -4px;" /></a>';
						echo '</td>';
					}
				echo '</tr>';
		
				if ($change_id == $row['id_id']) {
					$rel = getset(0, 'r', 'm');

					//Visar "Ändra typ av relation"
					echo '<tr><td colspan="5" class="pdg">';
						echo '<form name="do" action="'.$_SERVER['PHP_SELF'].'?id='.$id.'&chg='.$row['id_id'].'" method="post">';
					echo '<select name="ins_rel" class="txt">';
					foreach ($rel as $r) {
						$sel = ($r['text_cmt'] == $row['rel_id'])?' selected':'';
						echo '<option value="'.$r['main_id'].'"'.$sel.'>'.secureOUT($r['text_cmt']).'</option>';
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

	if ($user->id == $id && !$change_id) {
		echo '<input type="submit" value="Spara" class="btn2_min"/>';
		echo '</form>';
	}

?>

</div>

<?
	require(DESIGN.'foot_user.php');
?>
