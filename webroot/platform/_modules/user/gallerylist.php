<div class="centerMenuBodyWhite">
<?		
	dopaging($paging, l('user', 'gallery', $s['id_id'], '0').'p=', '', 'med', STATSTR);

	$showall = false;//visa bilderna istället för thumbs
	$view = 0;	//aktuelll bild, för showall=true
	$change = false;//redigera bilden?

	echo '<table summary="" cellspacing="0" width="100%">';
	$ti = 0;
	if (!empty($res) && count($res)) {
		if($all) {

			foreach($res as $row) {
				if(!$row['hidden_id'])
					$file = '/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'.'.$row['pht_name'];
				else
					$file = '/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'_'.$row['hidden_value'].'.'.$row['pht_name'];
				$ti++;
				$cls = ($showall)?'':' spac';
				$url = 'goLoc(first + \''.l('user', 'gallery', $s['id_id'], $row['main_id']).'p='.$paging['p'].'#view'.$row['main_id'].'\');';
				echo '
				<tr>
				<td class="cur'.$cls.' pdg" onclick="'.$url.'"><div style="width: 100%; overflow: hidden;"><a href="'.l('user', 'gallery', $s['id_id'], $row['main_id']).'p='.$paging['p'].'#view" id="lnk'.$row['main_id'].'" class="bld '.(($view && $row['main_id'] == $res[0])?'on up':'up').'">'.secureOUT($row['pht_cmt']).'</a></div></td>
				<td class="cur'.$cls.' pdg cnt nobr" onclick="'.$url.'">'.round($row['pht_size']/1024, 1).'kb</td>
				<td class="cur'.$cls.' pdg" onclick="'.$url.'">'.$row['pht_cmts'].' kommentarer</td>
				<td class="cur'.$cls.' pdg" onclick="'.$url.'">'.secureOUT($row['pht_click']).' visningar</td>
				<td class="cur'.$cls.' pdg rgt nobr" onclick="'.$url.'">'.nicedate($row['pht_date'], 2).'</td>';

				if ($own) {
					echo '<td class="'.$cls.' rgt pdg_tt nobr" width="150">';
					makeButton(false, 'goLoc(\''.l('user', 'gallery', $s['id_id'], $row['main_id']).'c=1#view'.$row['main_id'].'\')', 'icon_gallery.png', 'ändra');
					makeButton(false, 'if(confirm(\'Säker ?\')) goLoc(\''.l('user', 'gallery', $s['id_id'], '0').'&amp;d='.$row['main_id'].'\');', 'icon_delete.png', 'radera');
					echo '</td>';
				}

				echo '
				</tr>
				'.(($own && $change && $change == $row['main_id'] && $l)?'
				<tr>
				<td colspan="8" class="pdg wht com_bg">
				<form name="do" action="'.l('user', 'gallery').'" method="post"><input type="hidden" name="c_id" value="'.$row['main_id'].'"><input type="text" class="txt" name="ins_cmt" onfocus="this.select();" value="'.secureOUT($row['pht_cmt']).'" maxlength="40" style="width: 205px; margin-right: 10px;"><input type="checkbox" class="chk" id="ins_priv" name="ins_priv" value="1"'.(($row['hidden_id'])?' checked':'').'><label for="ins_priv"> Privat foto [endast för vänner]</label> <input type="submit" class="br" value="spara" style="margin-left: 10px;"></form>
				</td></tr>
				':'').
				(($showall || $view == $row['main_id'])?'
				<tr>
					<td colspan="6" style="padding-bottom: 6px;"><div class="cnt" style="width: 586px; overflow: hidden;"><img src="'.$file.'" alt="" onload="if(this.width > 510) this.width = 510;" /></a></div></td>
				</tr>
				<tr>
				<td align="right" colspan="8" class="pdg wht com_bg">
				<a href="javascript:makePhotoComment(\''.l('user', 'gallerycmt', $s['id_id'],$row['main_id']).'cmt=1#view\');">
				<img src="'.OBJ.'icon_comments.gif" alt="Kommentera" style="margin-bottom: -4px;" /></a>
				</td>
				</tr>':'');
			}
			/*
			$res_cmts=$sql->query("SELECT p.main_id, p.user_id, p.c_msg, p.c_date, p.status_id, u.id_id, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.u_birth, u.u_sex, u.account_date, u.level_id FROM {$t}userphotocmt p LEFT JOIN {$t}user u ON p.user_id = u.id_id AND u.status_id = '1' WHERE p.photo_id = '".$_GET['key']."' ORDER BY main_id DESC", 0, 1);
			foreach($res_cmts as $line) {
				echo '<tr><td class="spac pdg"><div style="width: 100%; overflow: hidden;">'.$line['u_alias'].'<br />'.$line['u_picd'].'<br />'.$line['u_birth'].'<br />'.$line['u_sex'].'<br />'.$line['c_msg'].'</div></td></tr>';
			}*/
		} else {
			foreach($res as $row) {
				echo '<tr><td class="spac pdg"><div style="width: 100%; overflow: hidden;"><b>[privat]</b></div></td></tr>';
			}
		}
	} else {
		echo '<tr><td class="cnt">Inga foton uppladdade.</td></tr>';
	}
	echo '</table>';
?>
</div><br/>