<?
	require_once('config.php');

	$res = $db->getArray('SELECT main_id, main_ttl FROM s_ftopic f WHERE f.status_id = "1" ORDER BY f.order_id');
	$last = $db->getArray('SELECT f.main_id, f.topic_id, f.top_id, f.view_id, f.parent_id, CONCAT(SUBSTRING(f.sent_cmt, 1, 50), "...") AS sent_cmt, f.sent_date, u.id_id, u.u_alias, u.account_date, u.u_sex, u.u_birth, u.level_id, p.sent_ttl AS main_ttl FROM (s_f f, s_f p) RIGHT JOIN s_ftopic t ON t.main_id = f.topic_id AND t.status_id = "1" LEFT JOIN s_user u ON u.id_id = f.sender_id AND u.status_id = "1" WHERE f.status_id = "1" AND f.view_id = "1" AND p.main_id = f.top_id AND p.status_id = "1" ORDER BY f.main_id DESC LIMIT 30');
	$own = $db->getArray('SELECT o.main_id, o.top_id, o.sent_ttl, o.sent_date FROM s_f o INNER JOIN s_f p ON p.main_id = o.top_id AND p.status_id = "1" WHERE o.sender_id = '.$user->id.' AND o.status_id = "1" AND o.view_id = "1" ORDER BY o.main_id DESC LIMIT 5');
	require(DESIGN."head.php");
?>

<script type="text/javascript">
function openText(id, fid) {
	document.location.href = '<?=l('forum','read');?>' + id + (fid?'&amp;item=' + fid + '#R' + fid:'');
}
</script>

	<div id="mainContent">

		<div class="subHead">forum</div><br class="clr"/>

		<table cellspacing="0" summary="" style="margin-bottom: 20px;">
		<tr>
<?
	$i = 0;
	$r = 0;
	$nl = false;
	foreach($res as $row) {
		$c = $db->getOneItem('SELECT COUNT(*) FROM s_f WHERE topic_id = '.$row['main_id'].' AND parent_id = "0" AND status_id = "1"');
		$d = $db->getOneItem('SELECT COUNT(*) FROM s_f WHERE topic_id = '.$row['main_id'].' AND parent_id != "0" AND status_id = "1"');
		if($i && $nl) { echo '</tr><tr>'; $nl = false; $i = 0; $r++; }
		echo '
			<td>
			<table cellspacing="0" summary="" style="margin-left: '.(($i)?'62px':'0').';">
			<tr><td><a href="forum_list.php?id='.$row['main_id'].'" class="bld"><img width="157" height="74" src="'.$config['web_root'].'_objects/'.$row['main_id'].'.jpg" alt="" onerror="this.src = \''.$config['web_root'].'_objects/forum_nopic.jpg\';" /></a></td></tr>
			<tr><td><h4>'.secureOUT($row['main_ttl']).'</h4><b>'.$c.'</b> tråd'.(($c != '1')?'ar':'').'<br /><b>'.($d+$c).'</b> inlägg</td></tr>
			</table>
			</td>';
		if(++$i % 3 == 0) $nl = true;
	}
	$page = 'start';
	$menu = array('start' => array('forum.php', 'start'));
?>
		</tr>
		</table>

		<div class="bigHeader"><?=makeMenu($page, $menu)?> - 50 senaste inläggen</div>
		<div class="bigBody">
<table cellspacing="0" summary="" width="589">
<?
if(count($last)) {
	foreach($last as $row) {
?>
<tr>
	<td class="cur pdg" onclick="openText('<?=$row['top_id']?>', '<?=$row['main_id']?>');">
		<div style="width: 100%; height: 14px; overflow: hidden;">
			<a href="forum_read.php?id=<?=$row['top_id']?>&amp;item=<?=$row['main_id'].'#R'.$row['main_id']?>" class="up">
				<?=secureOUT($row['sent_cmt'])?>
			</a>&nbsp;
		</div>
	</td>
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