<?
	$thispage = 'obj.php?status=img';
function imgOK($id, $picid, $flow) {
	$picid = intval($picid);
	$picid++;
	if($picid == '80') $picid = '01'; elseif(strlen($picid) == '1') $picid = '0'.$picid;
	rename('../_input/preimages/'.$id.'_'.$flow.'.jpg', '../'.USER_IMG.PD.'/'.$id.$picid.'.jpg');
	rename('../_input/preimages/'.$id.'_'.$flow.'_2.jpg', '../'.USER_IMG.PD.'/'.$id.$picid.'_2.jpg');
	return $picid;
}
	$reasons = array(
'A' => '.',
'G' => ' på grund av: <b>Solglasögon.</b>',
'S' => ' på grund av: <b>Oskärpa i bild.</b>',
'M' => ' på grund av: <b>Flera i personer i bild.</b>',
'R' => ' på grund av: <b>Reklambudskap i bild.</b>',
'F' => ' på grund av: <b>Felaktig bild.</b>',
'AB' => ' på grund av: <b>Stötande och/eller olämpligt material.</b>',
'TS' => ' på grund av: <b>För litet ansikte.</b>',
'TSB' => 'på grund av: <b>För litet ansikte, beskär annorlunda.</b>',
'TD' => 'på grund av: <b>Bilden är för mörk.</b>',
'TL' => 'på grund av: <b>Bilden är för ljus.</b>',
'NF' => 'på grund av: <b>Ej rakt framifrån.</b>');
	if(!empty($_POST['validate'])) {
		$doall = false;
		if(!empty($_POST['main_id:all']) && is_numeric($_POST['main_id:all'])) {
			$doall = true;
			$type = $_POST['main_id:all'];
		}
		foreach($_POST as $key => $val) {
			if(strpos($key, 'status_id') !== false) {
				$kid = explode(':', $key);
				$kid = $kid[1];
				$res = $db->getOneItem("SELECT flow_id FROM s_userpicvalid WHERE id_id = '".$kid."' LIMIT 1");
				if(!empty($res) && isset($_POST['status_id:' . $kid])) {
					if($doall && empty($_POST['status_id:' . $kid])) {
// alla-knapp
						if($type == '1') {
							if(!empty($res) && file_exists('../_input/preimages/'.$kid.'_'.$res.'.jpg')) {
								$line = $db->getOneItem("SELECT u_picid FROM s_user WHERE id_id = '".$kid."' LIMIT 1");
								$picid = imgOK($kid, $line, $res);
								$db->update("UPDATE s_user SET u_picdate = NOW(), u_picd = '".PD."', u_picvalid = '1', u_picid = '$picid' WHERE id_id = '".$kid."' LIMIT 1");
								$string = $db->getOneItem("SELECT level_id FROM s_userlevel WHERE id_id = '".$kid."' LIMIT 1");
								$string = str_replace('VALID', '', $string);
								$string = $string.' VALID';
								$db->update("UPDATE s_userlevel SET level_id = '$string' WHERE id_id = '".$kid."' LIMIT 1");
							}
							addALog(@$_SESSION['u_i'].' godkände profilbild '.$kid);
#							$user->spy($kid, 'ID', 'MSG', array('Din profilbild har godkänts!'));
							$db->delete("DELETE FROM s_userpicvalid WHERE id_id = '".$kid."' LIMIT 1");
						} elseif($type == '2') {
							@rename('../_input/preimages/'.$kid.'_'.$res.'.jpg', '../user_img_off84/'.$kid.'_'.microtime().'.jpg');
							@unlink('../_input/preimages/'.$kid.'_'.$res.'_2.jpg');
							if(!empty($_POST['reason_id:' . $kid])) {
								if(!empty($_POST['reasontext_id:' . $kid]) && $_POST['reason_id:' . $kid] == 'X') {
									$msg = 'Din nya profilbild har nekats på grund av: <b>'.$_POST['reasontext_id:' . $kid].'</b> Prova med en ny.';
									spyPostSend($kid, 'Nekad profilbild', $msg);
								}	else {
									$msg = 'Din nya profilbild har nekats'.$reasons[$_POST['reason_id:' . $kid]].' Prova med en ny.';
									spyPostSend($kid, 'Nekad profilbild', $msg);
								}
							} else {
								$msg = 'Din nya profilbild har nekats. Prova med en ny.';
								spyPostSend($kid, 'Nekad profilbild', $msg);
							}
							addALog(@$_SESSION['u_i'].' nekade profilbild '.$kid);
							$db->delete("DELETE FROM s_userpicvalid WHERE id_id = '".$kid."' LIMIT 1");
							$db->update("UPDATE s_user SET u_picdate = '' WHERE id_id = '".$kid."' LIMIT 1");
						}
					} else {
						if($_POST['status_id:' . $kid] == '1') {
							if(!empty($res) && file_exists('../_input/preimages/'.$kid.'_'.$res.'.jpg')) {
								$line = $db->getOneItem("SELECT u_picid FROM s_user WHERE id_id = '".$kid."' LIMIT 1");
								$picid = imgOK($kid, $line, $res);
								$db->update("UPDATE s_user SET u_picdate = NOW(), u_picd = '".PD."', u_picvalid = '1', u_picid = '$picid' WHERE id_id = '".$kid."' LIMIT 1");
								$string = $db->getOneItem("SELECT level_id FROM s_userlevel WHERE id_id = '".$kid."' LIMIT 1");
								$string = str_replace(' VALID', '', $string);
								$string = $string.' VALID';
								$db->update("UPDATE s_userlevel SET level_id = '$string' WHERE id_id = '".$kid."' LIMIT 1");
							}

							addALog(@$_SESSION['u_i'].' godkände profilbild '.$kid);
#							$user->spy($kid, 'ID', 'MSG', array('Din profilbild har godkänts!'));
							$db->delete("DELETE FROM s_userpicvalid WHERE id_id = '".$kid."' LIMIT 1");
						} elseif($_POST['status_id:' . $kid] == '2') {
							@rename('../_input/preimages/'.$kid.'_'.$res.'.jpg', '../user_img_off84/'.$kid.'_'.microtime().'.jpg');
							@unlink('../_input/preimages/'.$kid.'_'.$res.'_2.jpg');
							if(!empty($_POST['reason_id:' . $kid])) {
								if(!empty($_POST['reasontext_id:' . $kid]) && $_POST['reason_id:' . $kid] == 'X') {
									$msg = 'Din nya profilbild har nekats på grund av: <b>'.$_POST['reasontext_id:' . $kid].'</b> Prova med en ny.';
									spyPostSend($kid, 'Nekad profilbild', $msg);
								} else {
									$msg = 'Din nya profilbild har nekats'.$reasons[$_POST['reason_id:' . $kid]].' Prova med en ny.';
									spyPostSend($kid, 'Nekad profilbild', $msg);
								}
							} else {
								$msg = 'Din nya profilbild har nekats. Prova med en ny.';
								spyPostSend($kid, 'Nekad profilbild', $msg);
							}
							addALog(@$_SESSION['u_i'].' nekade profilbild '.$kid);
							$db->delete("DELETE FROM s_userpicvalid WHERE id_id = '".$kid."' LIMIT 1");
							$db->update("UPDATE s_user SET u_picdate = '' WHERE id_id = '".$kid."' LIMIT 1");
						}
					}
				}
			}
		}
		header('Location: '.$thispage);
		die;
	} elseif(!empty($_GET['del'])) {
		$res = $db->getOneRow("SELECT id_id, flow_id FROM s_userpicvalid WHERE id_id = '".secureINS($_GET['del'])."' LIMIT 1");
		if(!empty($res) && count($res)) {
			@rename('../_input/preimages/'.$res['id_id'].'_'.$res['flow_id'].'.jpg', '../user_img_off84/'.$res['id_id'].'_'.md5(microtime()).'.jpg');
			@unlink('../_input/preimages/'.$res['id_id'].'_'.$res['flow_id'].'_2.jpg');
			#$string = $sql->queryResult("SELECT level_id FROM s_userlevel WHERE id_id = '".$res[0]."' LIMIT 1");
			#$string = str_replace('VALID', '', $string);
			$db->update("UPDATE s_user SET u_picdate = '' WHERE id_id = '".$res['id_id']."' LIMIT 1");
			#$sql->queryUpdate("UPDATE s_userlevel SET level_id = '$string' WHERE id_id = '".$res[0]."' LIMIT 1");
			if(!empty($_GET['reason'])) {
				if(!empty($_GET['reasontext']) && $_GET['reason'] == 'X') {
					$msg = 'Din nya profilbild har nekats på grund av: <b>'.$_GET['reasontext'].'</b> Prova med en ny.';
					spyPostSend($res['id_id'], 'Nekad profilbild', $msg);
				} else {
					$msg = 'Din nya profilbild har nekats'.$reasons[$_GET['reason']].' Prova med en ny.';
					spyPostSend($res['id_id'], 'Nekad profilbild', $msg);
				}
			} else {
				$msg = 'Din nya profilbild har nekats. Prova med en ny.';
				spyPostSend($res['id_id'], 'Nekad profilbild', $msg);
			}
			$db->delete("DELETE FROM s_userpicvalid WHERE id_id = '".$db->escape($_GET['del'])."' LIMIT 1");
		}
		header("Location: ".$thispage);
		die;
	}
	$all = (!empty($_GET['all'])?'1':'0');
	require('obj_head.php');

	$count = array(
		$db->getOneItem("SELECT COUNT(*) FROM s_userpicvalid a INNER JOIN s_user u ON u.id_id = a.id_id AND u.status_id = '1' WHERE a.status_id = '1'")
	);

	if($all)
		$list = $db->getArray("SELECT u.id_id, u.u_picd, u.u_alias, u.level_id, u.u_sex, u.u_birth, u.u_picid FROM s_user u WHERE u.status_id = '1' AND u.u_picvalid = '1' ORDER BY u.u_picdate DESC LIMIT 48");
	else
		$list = $db->getArray("SELECT a.id_id, a.flow_id AS u_picd, u.u_alias, u.level_id, u.u_sex, u.u_birth FROM s_userpicvalid a INNER JOIN s_user u ON u.id_id = a.id_id AND u.status_id = '1' WHERE a.status_id = '1'");
?>
<script type="text/javascript">
function denyAns(val, id, extra) {
	if(!extra) extra = '';
	if(confirm('Säker ?'))
		document.location.href = '<?=$thispage?>&del=' + id + '&reason=' + val + '&reasontext=' + extra;
}
</script>
			<input type="radio" class="inp_chk" name="view" value="0" id="view_0" onclick="document.location.href = '<?=$thispage?>';"<?=(!$all?' checked':'')?>><label for="view_0" class="txt_bld txt_look">Icke granskade</label> [<?=$count[0]?>]
			<input type="radio" class="inp_chk" name="view" value="1" id="view_1" onclick="document.location.href = '<?=$thispage?>&all=1';"<?=($all?' checked':'')?>><label for="view_1" class="txt_bld txt_look">Senaste godkända</label>
			<form name="upd" method="post" action="./<?=$thispage?>&all=<?=$all?>">
			<input type="hidden" name="main_id:all" id="main_id" value="0">
			<input type="hidden" name="validate" value="1">
<?
	if(!$all) {
?>
			<input type="submit" class="inp_realbtn" value="Uppdatera" style="width: 70px; margin: 5px 2px 10px 0;">
			<input type="button" class="inp_realbtn" value="Neka blanka" style="width: 85px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '2'; this.form.submit();">
			<input type="button" class="inp_realbtn" value="Godkänn blanka" style="width: 100px; margin: 5px 2px 10px 0;" onclick="document.getElementById('main_id').value = '1'; this.form.submit();">
<?
	}
?>
		<br><br>
			<hr /><div class="hr"></div>
<?
	if(count($list) && !empty($list)) {
		echo '<table cellspacing="2">';
	#	echo '<tr><th>Bild</th><th>Användare</th><th>Mobilnummer</th><th>Bildnummer</th><th>Hämtningskod</th><th>Antal hämtningar</th><th>Datum</th></tr>';
		$nl = true;
		$i = 0;
		foreach($list as $row) {
			if($i % 8 == 0) $nl = true;
			if($i && $nl) echo '</tr>';
			if($nl) echo '<tr class="bg_gray">';
			if($nl) $nl = false;
			$i++;
			echo '<td class="pdg cnt">';
	if($all) {
echo '<a href="user.php?id='.$row['id_id'].'"><img style="margin-top: 3px;" src="../_input/images/'.$row[1].'/'.$row['id_id'].$row[6].'.jpg" /></a><br><a href="user.php?id='.$row['id_id'].'"><b>'.secureOUT($row[2]).'</b></a></td>';
	} else {
echo '
<input type="hidden" name="status_id:'.$row['id_id'].'" id="status_id:'.$row['id_id'].'" value="0"><img src="./_img/status_none.gif" style="margin: 0 1px -1px 2px;" id="1:'.$row['id_id'].'" onclick="changeStatus(\'status\', this.id);"><img src="./_img/status_none.gif" style="margin: 0 0 -1px 1px;" id="2:'.$row['id_id'].'" onclick="document.getElementById(\'re_re:'.$row['id_id'].'\').style.display = \'none\';  document.getElementById(\'reason_reason:'.$row['id_id'].'\').style.display = \'\'; changeStatus(\'status\', this.id);"> | <a href="javascript:void(0);" onclick="document.getElementById(\'re_re:'.$row['id_id'].'\').style.display = \'\';  document.getElementById(\'reason_reason:'.$row['id_id'].'\').style.display = \'none\';">NEKA DIREKT</a>
<br>
<div id="reason_reason:'.$row['id_id'].'" style="display: none;">
<input type="radio" name="reason_id:'.$row['id_id'].'" value="R" id="reason_id:'.$row['id_id'].':R"><label for="reason_id:'.$row['id_id'].':R">Reklam</label>
<input type="radio" name="reason_id:'.$row['id_id'].'" value="AB" id="reason_id:'.$row['id_id'].':AB"><label for="reason_id:'.$row['id_id'].':AB">Stötande</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" value="TSB" id="reason_id:'.$row['id_id'].':TSB"><label for="reason_id:'.$row['id_id'].':TSB">Litet ansikte, beskär</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" value="NF" id="reason_id:'.$row['id_id'].':NF"><label for="reason_id:'.$row['id_id'].':NF">Ej rakt framifrån</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" value="S" id="reason_id:'.$row['id_id'].':S"><label for="reason_id:'.$row['id_id'].':S">Oskärpa</label>
<input type="radio" name="reason_id:'.$row['id_id'].'" value="M" id="reason_id:'.$row['id_id'].':M"><label for="reason_id:'.$row['id_id'].':M">Flera i bild</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" value="TD" id="reason_id:'.$row['id_id'].':TD"><label for="reason_id:'.$row['id_id'].':TD">Mörk</label>
<input type="radio" name="reason_id:'.$row['id_id'].'" value="TL" id="reason_id:'.$row['id_id'].':TL"><label for="reason_id:'.$row['id_id'].':TL">Ljus</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" value="F" id="reason_id:'.$row['id_id'].':F"><label for="reason_id:'.$row['id_id'].':F">Fel</label><input type="radio" name="reason_id:'.$row['id_id'].'" value="G" id="reason_id:'.$row['id_id'].':G"><label for="reason_id:'.$row['id_id'].':G">Solglasögon</label><br />
<input type="radio" name="reason_id:'.$row['id_id'].'" value="X" onclick="if(this.checked) document.getElementById(\'reasontext_id:'.$row['id_id'].'\').style.display = \'\';" onchange="if(!this.checked) document.getElementById(\'reasontext_id:'.$row['id_id'].'\').style.display = \'none\';" id="reason_id:'.$row['id_id'].':X"><label for="reason_id:'.$row['id_id'].':X">Valfri</label><br />
<input type="text" name="reasontext_id:'.$row['id_id'].'" id="reasontext_id:'.$row['id_id'].'" style="width: 100px; display: none;" class="inp_nrm" value="">
</div>
<div id="re_re:'.$row['id_id'].'" style="display: none;">
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="R" id="re_id:'.$row['id_id'].':R"><label for="re_id:'.$row['id_id'].':R">Reklam</label>
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="AB" id="re_id:'.$row['id_id'].':AB"><label for="re_id:'.$row['id_id'].':AB">Stötande</label><br />
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TSB" id="re_id:'.$row['id_id'].':TSB"><label for="re_id:'.$row['id_id'].':TSB">Litet ansikte, beskär</label><br />
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="NF" id="re_id:'.$row['id_id'].':NF"><label for="re_id:'.$row['id_id'].':NF">Ej rakt framifrån</label><br />
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="S" id="re_id:'.$row['id_id'].':S"><label for="re_id:'.$row['id_id'].':S">Oskärpa</label>
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="M" id="re_id:'.$row['id_id'].':M"><label for="re_id:'.$row['id_id'].':M">Flera i bild</label><br />
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TD" id="re_id:'.$row['id_id'].':TD"><label for="re_id:'.$row['id_id'].':TD">Mörk</label>
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="TL" id="re_id:'.$row['id_id'].':TL"><label for="re_id:'.$row['id_id'].':TL">Ljus</label><br />
<input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="F" id="re_id:'.$row['id_id'].':F"><label for="re_id:'.$row['id_id'].':F">Fel</label><input type="radio" onclick="denyAns(this.value, \''.$row['id_id'].'\');" value="G" id="re_id:'.$row['id_id'].':G"><label for="re_id:'.$row['id_id'].':G">Solglasögon</label><br />
<input type="radio" name="retX'.$row['id_id'].'" value="X" onclick="if(this.checked) { document.getElementById(\'ret_id:'.$row['id_id'].'\').style.display = \'\'; document.getElementById(\'retb_id:'.$row['id_id'].'\').style.display = \'\'; }" onchange="if(!this.checked) { document.getElementById(\'ret_id:'.$row['id_id'].'\').style.display = \'none\'; document.getElementById(\'retb_id:'.$row['id_id'].'\').style.display = \'none\'; }" id="re_id:'.$row['id_id'].':X"><label for="re_id:'.$row['id_id'].':X">Valfri</label><br />
<input type="text" id="ret_id:'.$row['id_id'].'" style="width: 100px; display: none;" value="" class="inp_nrm">
<input type="button" class="inp_orgbtn" id="retb_id:'.$row['id_id'].'" style="margin: 0;" style="display: none;" value="skicka valfri" onclick="denyAns(\'X\', \''.$row['id_id'].'\', document.getElementById(\'ret_id:'.$row['id_id'].'\').value);" />
</div>
';
echo '<img style="margin-top: 3px;" src="../_input/preimages/'.$row['id_id'].'_'.$row['u_picd'].'.jpg" /><br><a href="user.php?id='.$row['id_id'].'"><b>'.secureOUT($row['u_alias']).'</b></a></td>';
	}

		}
		echo '</tr>';
		echo '</table>';
	}
?>
			</form>
		</td>
	</tr>
	</table>
</body>
</html>
