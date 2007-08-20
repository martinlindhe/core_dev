<?
	$rel = getset(0, 'r', 'm');

	if(!empty($paus) && count($paus)) {
?>
<div class="bigHeader">bli vän-förfrågningar</div>
<div class="bigBody">
	<table summary="" cellspacing="0" width="586">
<?
	$i = 0;
	foreach($paus as $row) {
		$i++;
		#$gotpic = ($row['u_picvalid'] == '1')?true:false;
		$gotpic = false;
		echo '
		<tr>
			<td class="spac pdg">'.$user->getstring($row).'</td>
			<td class="spac pdg">Väntar på dig som '.secureOUT($row['sent_cmt']).'</td>
			<td class="spac pdg rgt">'.nicedate($row['sent_date']).'</td>
			<td class="spac rgt pdg_tt"><a href="user_relations.php?id='.$row['id_id'].'&a='.$row['main_id'].'"><img src="'.$config['web_root'].'_gfx/icon_yes.gif" alt="" title="Godkänn" style="margin-bottom: -4px;" /></a> - <a href="user_relations.php?id='.$row['id_id'].'&amp;d='.$row['id_id'].'" onclick="return confirm(\'Säker ?\');"><img src="'.$config['web_root'].'_gfx/icon_no.gif" alt="" title="Neka" style="margin-bottom: -4px;" /></a></td>
		</tr>';
		if($gotpic) echo '<tr id="m_pic:'.$i.'" style="display: none;"><td colspan="2">'.$user->getphoto($row['id_id'].$row['u_picid'].$row['u_picd'], $row['u_picvalid'], 0, 0, '', ' ').'<span style="display: none;">'.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'</span></td></tr>';
	}
?>
	</table>
</div>
<?
		}

		if(!empty($wait) && count($wait)) {
?>
<div class="bigHeader">du väntar svar från</div>
<div class="bigBody">
	<table summary="" cellspacing="0" width="586">
<?
	if(!empty($_GET['c_w']) && is_numeric($_GET['c_w'])) $c = $_GET['c_w']; else $c = 0;
	$i = 0;
	foreach($wait as $row) {
		$i++;
		$gotpic = false;
		echo '
			<tr>
				<td class="spac pdg"><a name="w'.$row['main_id'].'"></a>'.$user->getstring($row).'</td>
				<td class="spac pdg">Du väntar på som '.secureOUT($row['sent_cmt']).'</td>
				<td class="spac pdg rgt">'.nicedate($row['sent_date']).'</td>
				<td class="spac rgt pdg_tt"><a href="user_relations.php?id='.$user->id.'&amp;c_w='.$row['main_id'].'#w'.$row['main_id'].'"><img src="'.$config['web_root'].'_gfx/icon_change.gif" alt="" title="Ändra" style="margin-bottom: -4px;" /></a> - <a href="user_relations.php?id='.$id.'&d='.$row['id_id'].'" onclick="return confirm(\'Säker ?\');"><img src="'.$config['web_root'].'_gfx/icon_no.gif" alt="" title="Sluta vänta" style="margin-bottom: -4px;" /></a></td>
			</tr>';
		if($c == $row['main_id']) {
?>
			<tr>
				<td colspan="4" class="pdg">
					<form action="<?=$_SERVER['PHP_SELF'].'?id='.$id.'&chg='.$row['id_id']?>" method="post"><input type="hidden" name="r" value="1"><select name="ins_rel" class="txt" style="width: 205px; margin-right: 10px;">
<?
						foreach ($rel as $val) {
							$selected = ($val['text_cmt'] == $row['sent_cmt'])?' selected':'';
							echo '<option value="'.$val['main_id'].'"'.$selected.'>'.secureOUT($val['text_cmt']).'</option>';
						}
?>
					</select>
					<input type="submit" class="b" value="spara" style="margin-left: 10px;">
					</form>
				</td>
			</tr>
<?
		}
		if($gotpic) echo '<tr id="m_pic:x'.$i.'" style="display: none;"><td colspan="2">'.$user->getphoto($row['id_id'].$row['u_picid'].$row['u_picd'], $row['u_picvalid'], 0, 0, '', ' ').'<span style="display: none;">'.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'</span></td></tr>';
	}
?>
	</table>
</div>
<?
		}
?>
