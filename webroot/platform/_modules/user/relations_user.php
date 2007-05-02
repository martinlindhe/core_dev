<?
	require_once('relations.fnc.php');

	$rel = getset('', 'r', 'm');
	if(!empty($paus) && count($paus)) {
?>
<div class="mainHeader2"><h4>bli vän-förfrågningar</h4></div>
<div class="mainBoxed2">
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
			<td class="spac rgt pdg_tt"><a href="'.l('user', 'relations', $row['id_id'], '0').'&amp;a='.$row['main_id'].'"><img src="'.OBJ.'icon_yes.gif" alt="" title="Godkänn" style="margin-bottom: -4px;" /></a> - <a href="'.l('user', 'relations', $row['id_id'], '0').'&amp;d='.$row['id_id'].'" onclick="return confirm(\'Säker ?\');"><img src="'.OBJ.'icon_no.gif" alt="" title="Neka" style="margin-bottom: -4px;" /></a></td>
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
<div class="mainHeader2"><h4>du väntar svar från</h4></div>
<div class="mainBoxed2">
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
				<td class="spac rgt pdg_tt"><a href="'.l('user', 'relations', $l['id_id'], '0').'&amp;c_w='.$row['main_id'].'#w'.$row['main_id'].'"><img src="'.OBJ.'icon_change.gif" alt="" title="Ändra" style="margin-bottom: -4px;" /></a> - <a href="'.l('user', 'relations', $row['id_id'], '0').'&amp;d='.$row['id_id'].'" onclick="return confirm(\'Säker ?\');"><img src="'.OBJ.'icon_no.gif" alt="" title="Sluta vänta" style="margin-bottom: -4px;" /></a></td>
			</tr>';
		if($c == $row['main_id']) {
?>
			<tr>
				<td colspan="4" class="pdg">
					<form action="<?=l('user', 'relations', $row['id_id'])?>" method="post"><input type="hidden" name="r" value="1"><select name="ins_rel" class="txt" style="width: 205px; margin-right: 10px;">
<?
						foreach ($rel as $val) {
							$selected = ($val[1] == $row['sent_cmt'])?' selected':'';
							echo '<option value="'.$val[0].'"'.$selected.'>'.secureOUT($val[1]).'</option>';
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