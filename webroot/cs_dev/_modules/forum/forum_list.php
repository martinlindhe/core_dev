<?
	if(empty($_GET['id']) || !is_numeric($_GET['id'])) {
		errorACT('Rubriken existerar inte.', l('forum', 'start'));
	}
	$r = $db->getOneRow('SELECT main_id, main_ttl, main_cmt, subjects FROM {$t}ftopic f WHERE f.status_id = "1" AND f.main_id = '.@secureINS($_GET['id']).' LIMIT 1');
	if(empty($r) || !count($r)) {
		errorACT('Rubriken existerar inte.', l('forum', 'start'));
	}

	if(!empty($_POST['do']) && !empty($_POST['ins_ttl']) && !empty($_POST['ins_cmt'])) {

		$sub = (!empty($_POST['sub']) && is_numeric($_POST['sub']) && $_POST['sub'] >= 1 && $_POST['sub'] <= 3)?intval($_POST['sub']):1;

		$id = $sql->queryInsert("INSERT INTO {$t}f SET
			topic_id = '{$r['main_id']}',
			parent_id = '0',
			sender_id = '$s->id',
			status_id = '1',
			subject_id = '".secureINS($sub)."',
			sent_html = '".(($s->isAdmin)?'1':'0')."',
			sent_ttl = '".secureINS(substr($_POST['ins_ttl'], 0, 50))."',
			sent_cmt = '".secureINS($_POST['ins_cmt'])."',
			change_date = NOW(),
			sent_date = NOW()");
		if(!$id) {
			reloadACT(l('forum', 'list', $r['main_id']));
		} else {
			$sql->queryUpdate("UPDATE {$t}f SET top_id = main_id WHERE main_id = '".secureINS($id)."' LIMIT 1");
			reloadACT(l('forum', 'read', $id));
		}
	}

	if($isAdmin) {
		if(!empty($_GET['deltree']) && is_numeric($_GET['deltree'])) {
			@$sql->queryUpdate("UPDATE {$t}f SET status_id = '2' WHERE top_id = '".secureINS($_GET['deltree'])."'");
			reloadACT('fList.php?id='.$r['main_id']);
		}
	}
	$subs = explode(';', $r['subjects']);
	$gotsub = (!empty($_GET['sub']) && is_numeric($_GET['sub']) && $_GET['sub'] >= 1 && $_GET['sub'] <= 3)?intval($_GET['sub']):0;

	$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}f WHERE topic_id = '".secureINS($r['main_id'])."' AND parent_id = '0' AND status_id = '1'");
	$d = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}f WHERE topic_id = '".secureINS($r['main_id'])."' AND parent_id != '0' AND status_id = '1'");

	$paging = paging(@$_GET['p'], 20);

	if($isAdmin)
		$res = $sql->query("SELECT f.main_id, f.topic_id, f.change_date, f.parent_id, f.subject_id, f.status_id, f.sender_id, f.sent_ttl, f.sent_date, CONCAT(SUBSTRING(f.sent_cmt, 1, 50), '...') as sent_cmt, u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, COUNT(t.main_id) - 1 as count FROM {$t}f f LEFT JOIN {$t}user u ON u.id_id = f.sender_id AND u.status_id = '1' LEFT JOIN {$t}f t ON t.topic_id = f.topic_id AND t.top_id = f.main_id AND t.status_id = '1' WHERE f.parent_id = '0' AND f.topic_id = '".$r['main_id']."' AND f.status_id = '1' GROUP BY f.main_id ORDER BY f.change_date DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	else
		$res = $sql->query("SELECT f.main_id, f.topic_id, f.change_date, f.parent_id, f.subject_id, f.status_id, f.sender_id, f.sent_ttl, f.sent_date, CONCAT(SUBSTRING(f.sent_cmt, 1, 50), '...') as sent_cmt, u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, COUNT(t.main_id) - 1 as count FROM {$t}f f LEFT JOIN {$t}user u ON u.id_id = f.sender_id AND u.status_id = '1' LEFT JOIN {$t}f t ON t.topic_id = f.topic_id AND t.top_id = f.main_id AND t.status_id = '1' WHERE f.parent_id = '0' AND f.topic_id = '".$r['main_id']."' AND f.status_id = '1' GROUP BY f.main_id ORDER BY f.change_date DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);

	$own = $sql->query("SELECT o.main_id, o.top_id, o.sent_ttl, o.sent_date FROM {$t}f o INNER JOIN {$t}f p ON p.main_id = o.top_id AND p.status_id = '1' WHERE o.sender_id = '".secureINS($l['id_id'])."' AND o.status_id = '1' AND o.view_id = '1' ORDER BY o.main_id DESC LIMIT 5");
	$page = 'list';
	$menu = array('start' => array(l('forum', 'start'), 'start'), 'list' => array(l('forum', 'list', $r['main_id']), secureOUT($r['main_ttl'])), 'write' => array('javascript:makeForum('.$r['main_id'].');', 'skriv ny tråd'));
	require(DESIGN."head.php");
?>
<script type="text/javascript">
var clickonover = false;
function openText(id) {
	if(!clickonover)
		document.location.href = '<?=l('forum','read')?>' + id;
}
</script>

	<div id="mainContent">
		
		<div class="subHead">forum</div><br class="clr"/>
		
		<table summary="" cellspacing="0" style="margin-bottom: 20px;">
			<tr>
				<td style="width: 157px;"><a href="<?=l('forum','list',$r['main_id'])?>" class="bld"><img src="<?=OBJ.$r['main_id']?>.jpg" alt="" onerror="this.src = '<?=OBJ?>forum_nopic.jpg';" width="157" height="74" /></a></td>
				<td class="pdg"><?=safeOUT($r['main_cmt'])?></td>
			</tr>
			<tr><td><?='<h4>'.secureOUT($r['main_ttl']).'</h4><b>'.$c.'</b> tråd'.(($c != '1')?'ar':'').'<br /><b>'.($d+$c).'</b> inlägg'?></td></tr>
		</table>

		<div class="bigHeader"><?=makeMenu($page, $menu)?></div>

			<div class="bigBody">
				<table summary="" cellspacing="0" width="589">
<?
	if(count($res)) { foreach($res as $row) {
?>
<tr onclick="openText('<?=$row['main_id']?>');" title="<?=secureOUT($row['sent_cmt'])?>">
	<td class="cur pdg"><div style="width: 100%; height: 14px; padding-left: 1px; overflow: hidden;"><a href="<?=l('forum','read', $row['main_id'])?>" class="bld"><?=secureOUT($row['sent_ttl'])?></a>&nbsp;</div></td>
	<td class="cur pdg rgt nobr"><?=($row['count'])?'<b>'.$row['count'].'</b>':'0';?> svar</td>
	<td class="cur mid rgt nobr"><?=$user->getstring($row)?></td>
	<td class="cur pdg rgt nobr"><?=nicedate($row['change_date'])?> - (<a href="<?=l('forum','read', $row['main_id'])?>&amp;showlast=1" onclick="clickonover = true;">senaste</a>)</td>
</tr>
<?
	} } else echo '<tr><td colspan="4" class="spac pdg cnt">Inga inlägg.</td></tr>';
?>
		</table>
			</div>
		</div>
<?
	require(DESIGN."foot.php");
?>