<?
	$res = $sql->query("SELECT main_id, main_ttl FROM {$t}ftopic f WHERE f.status_id = '1' ORDER BY f.order_id", 0, 1);
	$last = $sql->query("SELECT f.main_id, f.topic_id, f.top_id, f.view_id, f.parent_id, CONCAT(SUBSTRING(f.sent_cmt, 1, 50), '...') as sent_cmt, f.sent_date, u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, p.sent_ttl as main_ttl FROM ({$t}f f, {$t}f p) RIGHT JOIN {$t}ftopic t ON t.main_id = f.topic_id  AND t.status_id = '1' LEFT JOIN {$t}user u ON u.id_id = f.sender_id AND u.status_id = '1' WHERE f.status_id = '1' AND f.view_id = '1' AND p.main_id = f.top_id AND p.status_id = '1' ORDER BY f.main_id DESC LIMIT 30", 0, 1);
	$own = $sql->query("SELECT o.main_id, o.top_id, o.sent_ttl, o.sent_date FROM {$t}f o INNER JOIN {$t}f p ON p.main_id = o.top_id AND p.status_id = '1' WHERE o.sender_id = '".secureINS($l['id_id'])."' AND o.status_id = '1' AND o.view_id = '1' ORDER BY o.main_id DESC LIMIT 5");
	require(DESIGN."head.php");
?>
		<div id="mainContent">
		<table cellspacing="0" summary="" style="margin-bottom: 20px;">
		<tr>
<?
	$i = 0;
	$r = 0;
	$nl = false;
	foreach($res as $row) {
		$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}f WHERE topic_id = '".secureINS($row['main_id'])."' AND parent_id = '0' AND status_id = '1'");
		$d = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}f WHERE topic_id = '".secureINS($row['main_id'])."' AND parent_id != '0' AND status_id = '1'");
		if($i && $nl) { echo '</tr><tr>'; $nl = false; $i = 0; $r++; }
		echo '
			<td>
			<table cellspacing="0" summary="" style="margin-left: '.(($i)?'62px':'0').';">
			<tr><td><a href="'.l('forum','list',$row['main_id']).'" class="bld"><img width="157" height="74" src="'.OBJ.$row['main_id'].'.jpg" alt="" onerror="this.src = \''.OBJ.'forum_nopic.jpg\';" /></a></td></tr>
			<tr><td><h4>'.secureOUT($row['main_ttl']).'</h4><b>'.$c.'</b> tråd'.(($c != '1')?'ar':'').'<br /><b>'.($d+$c).'</b> inlägg</td></tr>
			</table>
			</td>';
		if(++$i % 3 == 0) $nl = true;
	}
	$page = 'start';
	$menu = array('start' => array(l('forum', 'start'), 'start'));
?>
		</tr>
		</table>
		<script type="text/javascript">
		function openText(id, fid) {
			document.location.href = '<?=l('forum','read');?>' + id + (fid?'&item=' + fid + '#R' + fid:'');
		}
		</script>
		<div class="mainHeader2"><h4><?=makeMenu($page, $menu)?> - 50 senaste inläggen</h4></div>
		<div class="mainBoxed2">
<table cellspacing="0" summary="" width="589">
<?
if(count($last)) {
	foreach($last as $row) {
?>
<tr>
	<td class="cur pdg" onclick="openText('<?=$row['top_id']?>', '<?=$row['main_id']?>');"><div style="width: 100%; height: 14px; overflow: hidden;"><a href="<?=l('forum','read', $row['top_id']).'&item='.$row['main_id'].'#R'.$row['main_id']?>" class="up"><?=secureOUT($row['sent_cmt'])?></a>&nbsp;</div></td>
	<td class="cur pdg" onclick="openText('<?=$row['top_id']?>', '');"><div style="width: 100%; height: 14px; overflow: hidden;"><a href="<?=l('forum','read', $row['top_id'])?>" class="bld"><?=secureOUT($row['main_ttl'])?></a>&nbsp;</div></td>
	<td class="mid nobr"><?=$user->getstring($row)?></td>
	<td class="cur pdg rgt nobr" onclick="openText('<?=$row['top_id']?>', '<?=$row['main_id']?>');"><?=nicedate($row['sent_date'], 1, 1)?></td>
</tr>
<?
	}
} else {
	echo '<tr><td colspan="4" class="spac pdg cnt">Inga inlägg.</td></tr>';
}
?>
		</table>
		</div>
		</div>
<?
	require(DESIGN."foot.php");
?>