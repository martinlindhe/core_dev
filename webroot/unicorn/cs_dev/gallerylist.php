<div class="centerMenuBodyWhite">
<?
	dopaging($paging, 'user_gallery.php?id='.$id.'&p=', '', 'med', STATSTR);

	$showall = $user->vip_check(VIP_LEVEL1);	//visa alla bilderna istället för klickbara rubriker (VIP Delux enbart)

	$view = 0;	//aktuelll bild, för showall=true
	$change = false;//redigera bilden?

	echo '<table summary="" cellspacing="0" width="100%">';
	$ti = 0;
	if (!empty($res) && count($res)) {
		if ($all) {
			foreach($res as $row) {
				if(!$row['hidden_id'])
					$file = '/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.'.$row['pht_name'];
				else
					//$file = '/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'_'.$row['hidden_value'].'.'.$row['pht_name'];
					$file = '/'.USER_GALLERY.$row['picd'].'/'.$row['main_id'].'-tmb.'.$row['pht_name'];
				$ti++;
				$cls = ($showall)?'':' spac';
				echo '
				<tr>
				<td class="cur'.$cls.' pdg"><div style="width: 100%; overflow: hidden;"><a href="gallery_view.php?id='.$id.'&n='.$row['main_id'].'&p='.$paging['p'].'#view" id="lnk'.$row['main_id'].'" class="bld '.(($view && $row['main_id'] == $res[0])?'on up':'up').'">'.secureOUT($row['pht_cmt']).'</a></div></td>
				<td class="cur'.$cls.' pdg cnt nobr">'.round($row['pht_size']/1024, 1).'kb</td>
				<td class="cur'.$cls.' pdg">'.$row['pht_cmts'].' kommentarer</td>
				<td class="cur'.$cls.' pdg">'.secureOUT($row['pht_click']).' visningar</td>
				<td class="cur'.$cls.' pdg rgt nobr">'.nicedate($row['pht_date'], 2).'</td>';

				if ($user->id == $id) {
					echo '<td class="'.$cls.' rgt pdg_tt nobr" width="150">';
					makeButton(false, 'goLoc(\'gallery_view.php?id='.$id.'&n='.$row['main_id'].'&c=1#view'.$row['main_id'].'\')', 'icon_gallery.png', 'ändra');
					makeButton(false, 'if(confirm(\'Säker ?\')) goLoc(\'user_gallery.php?id='.$id.'&amp;d='.$row['main_id'].'\');', 'icon_delete.png', 'radera');
					echo '</td>';
				}

				echo '</tr>';
				if ($user->id == $id && $change && $change == $row['main_id']) {
					echo '<tr>';
					echo '<td colspan="8" class="pdg wht com_bg">';
					echo '<form name="do" action="'.l('user', 'gallery').'" method="post"><input type="hidden" name="c_id" value="'.$row['main_id'].'"><input type="text" class="txt" name="ins_cmt" onfocus="this.select();" value="'.secureOUT($row['pht_cmt']).'" maxlength="40" style="width: 205px; margin-right: 10px;"><input type="checkbox" class="chk" id="ins_priv" name="ins_priv" value="1"'.(($row['hidden_id'])?' checked':'').'><label for="ins_priv"> Privat foto [endast för vänner]</label> <input type="submit" class="br" value="spara" style="margin-left: 10px;"></form>';
					echo '</td></tr>';
				}
				if ($showall || $view == $row['main_id']) {
					echo '<tr>';
						echo '<td colspan="6" style="padding-bottom: 6px;">';
						echo '<div class="cnt" style="width: 586px; overflow: hidden;"><a href="'.l('user','gallery',$s['id_id'],$row['main_id']).'"><img src="'.$file.'" alt="" onload="if(this.width > 510) this.width = 510;" /></a></div>';
						echo '</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<td align="right" colspan="8" class="pdg wht com_bg">';
						makeButton(false, 'makePhotoComment('.$s['id_id'].','.$row['main_id'].')', 'icon_blog.png', 'skriv kommentar');
						echo '</td>';
					echo '</tr>';
					
				}

			}
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
