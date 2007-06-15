<?
	if(!function_exists('notallowed') || notallowed()) {
		header("Location: ./");
		exit;
	}
	$thisid = 'gb';
	$thispage = 'obj.php?status=gb';

	if(!empty($_GET['del']) && is_numeric($_GET['del'])) {
		$res = $sql->queryLine("SELECT user_read, user_id FROM {$t}usergb WHERE main_id = '".$_GET['del']."' LIMIT 1");
		if(!empty($res) && count($res)) {
			if(!$res[0]) $sql->queryUpdate("UPDATE {$t}obj SET content = content - 1 WHERE content_type = '".$thisid."_count' AND owner_id = '".$res[1]."' LIMIT 1");
			$sql->queryUpdate("UPDATE {$t}usergb SET status_id = '2' WHERE main_id = '".$_GET['del']."' LIMIT 1");
			#$sql->queryUpdate("DELETE FROM {$t}usergbhistory WHERE msg_id = '".$_GET['del']."' LIMIT 1");
		}
		@header("Location: ".$thispage);
		exit;
	}


	$cmt_arr = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}usergb");

	$paging = paging(@$_GET['p'], 20);
	$list = $sql->query("SELECT a.*, u.id_id, u.u_alias, u.u_picd, u.u_picvalid, u.u_picid, u.u_sex, u2.id_id AS id_id2, u2.u_alias as u_alias2, u2.u_sex as u_sex2, u2.u_picd as u_picd2, u2.u_picvalid AS u_picvalid2, u2.u_picid as u_picid2 FROM {$t}usergb a LEFT JOIN {$t}user u ON u.id_id = a.sender_id AND u.status_id = '1' LEFT JOIN {$t}user u2 ON u2.id_id = a.user_id AND u2.status_id = '1' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	require("./_tpl/obj_head.php");
?>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';" checked><label for="view_0" class="txt_bld txt_look">Alla</label> [<?=$cmt_arr?>]

			<form name="upd" method="post" action="./<?=$thispage?>&all=<?=@$view_cmt?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">
<?
	if(isset($paging) && ($paging['p'] > 1 || $cmt_arr > $paging['slimit'] + $paging['limit'])) {
?>
			<hr /><div class="hr"></div>
					<table width="100%">
					<tr>
						<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
						<td align="right" valign="center">
<?
	$pm1 = $paging['p'] - 1;
	$pp1 = $paging['p'] + 1;
		if($paging['p'] > 1) {
			echo '<a href="'.$thispage.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($cmt_arr > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
						</td>
					</tr>
					</table>
<?	} else echo '<div>&nbsp;</div>';
?>
			<hr /><div class="hr"></div>
			<table width="100%">
<?
		foreach($list as $r) {
?>
			<tr><td>
			<table width="100%">
			<tr>
				<td colspan="2"<?=($r['status_id'] == '2')?' class="bg_blk"':'';?>>
				<table width="100%"<?=($r['status_id'] == '2')?' class="txt_wht"':'';?>>
				<tr><td colspan="2" style="padding-bottom: 5px;"><img src="./_img/status_<?=($r['status_id'] == '1')?'green':'none';?>.gif" style="margin: 0 1px -1px 2px;" id="1:<?=$r['main_id']?>" onclick="changeStatus('status', this.id);"><img src="./_img/status_<?=($r['status_id'] == '2')?'red':'none';?>.gif" style="margin: 0 8px -1px 1px;" id="2:<?=$r['main_id']?>" onclick="changeStatus('status', this.id);"><?=
	((!empty($r['id_id']))?'<a href="user.php?t&id='.$r['id_id'].'" class="txt_big user">'.secureOUT($r['u_alias']).'</a>':'[raderad]').
	' till '.((!empty($r['id_id2']))?'<a href="user.php?t&id='.$r['id_id2'].'" class="txt_big user">'.secureOUT($r['u_alias2']).'</a>':'[raderad]');?>
 - <em>inl&auml;gg skrivet <?=niceDate($r['sent_date'])?></em> (#<?=$r['main_id']?>)</span><?=(!$r['user_read'])?' <b>[oläst]</b>':'';?></td></tr>
				<tr>
					<td style="width: 100%;"><?='<table cellspacing="0"><tr><td>'.getadminimg($r['id_id'].$r['u_picid'].$r['u_picd'].$r['u_sex'], $r['u_picvalid']).'</td><td style="padding-left: 5px;"><div style="width: 470px; overflow: hidden;">'.secureOUT($r['sent_cmt']).'</div></td></tr></table>';?></td>
					<td><?=getadminimg($r['id_id2'].$r['u_picid2'].$r['u_picd2'].$r['u_sex2'], $r['u_picvalid2'])?></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="right" style="height: 12px; padding: 8px 0 0 0;"<?=($r['status_id'] == '2')?' class="bg_blk"':'';?>>
<input type="hidden" name="status_id:<?=$r['main_id']?>" id="status_id:<?=$r['main_id']?>" value="<?=$r['status_id']?>">
<a href="search.php?t&view=sss&s1=<?=$r['u_alias']?>&s2=<?=$r['u_alias2']?>">VISA</a> | 
<a href="<?=$thispage.'&del='.$r['main_id'];?>" onclick="return confirm('Säker ?');">RADERA</a>
				</td>
			</tr>
			<tr><td colspan="2" style="padding: 0 0 10px 0;"><hr /><div class="hr"></div></td></tr>
			</table>
			</td></tr>
<?
		}
?>
			</table>
<?
	if(isset($paging) && ($paging['p'] > 1 || $cmt_arr > $paging['slimit'] + $paging['limit'])) {
?>
			<table width="100%">
			<tr>
				<?=($paging['p'] > 1)?'<td><a href="'.$thispage.'" class="txt_look txt_bld">tillbaka</a></td>':'';?>
				<td align="right" height="20" valign="center">
<?
		if($paging['p'] > 1) {
			echo '<a href="'.$thispage.'&p='.$pm1.'" class="txt_look txt_bld">framåt</a>&nbsp;';
		}
		if($cmt_arr > $paging['slimit'] + $paging['limit']) {
			echo '<a href="'.$thispage.'&p='.$pp1.'" class="txt_look txt_bld">bakåt</a>&nbsp;';
		}
?>
				</td>
			</tr>
			</table>
<?	}
?>
			<hr /><div class="hr"></div>
			</form>
		</td>
	</tr>
	</table>
</body>
</html>