		<form name="addpic" method="post" action="pics_generate.php" onsubmit="genA();" target="generate">
		<input type="hidden" name="doload" value="1">
			Visar:<br>
			<input type="radio" class="inp_chk" name="upl" value="0" id="upl_0" onclick="document.location.href = 'pics.php?<?=$lnk_str?>';" checked><label for="upl_0" class="txt_bld txt_look">Postload</label> [<a href="javascript:popup('help.php?id=postload', 'help', 316, 355);">Hjälp</a>]
			<input type="radio" class="inp_chk" name="upl" value="1" id="upl_1" onclick="document.location.href = 'pics.php?singlefile&<?=$lnk_str?>';"><label for="upl_1" class="txt_bld txt_look">Uppladdning</label>
			<input type="radio" class="inp_chk" name="upl" value="2" id="upl_2" onclick="document.location.href = 'pics.php?movie&<?=$lnk_str?>';"><label for="upl_2" class="txt_bld txt_look">Film</label> [<a href="javascript:popup('help.php?id=film', 'help', 316, 355);">Hjälp</a>]

		<table cellspacing="0" width="350" style="margin: 0 0 10px 0;">
		<tr>
			<td colspan="2" align="right"><nobr>Antal filer: <b>Alla</b></nobr></td>
		</tr>
		<tr>
			<td colspan="2" style="padding: 18px 0 5px 0;">
			<select name="p_session" size="1" class="inp_nrm" style="margin: 0; width: 307px;">
<?
	if(mysql_num_rows($list) > 0) mysql_data_seek($list, 0);

	if(mysql_num_rows($list) == '0') {
		echo '<option value="0">Det finns inga bildsessioner.</option>';
	} else {
		while($frow = mysql_fetch_assoc($list)) {
			if($change && $row['main_id'] == $frow['main_id']) {
				echo '<option value="'.$frow['main_id'].'" id="'.$frow['main_id'].'" selected>'.$cities[$frow['p_city']].' '.stripslashes($frow['p_name']).' - '.specialDate($frow['p_date'], $frow['p_dday']).' ['.(($frow['p_pics'] == '1')?'1 BILD':$frow['p_pics'].' BILDER').']</option>';
			} else {
				echo '<option value="'.$frow['main_id'].'" id="'.$frow['main_id'].'">'.$cities[$frow['p_city']].' '.stripslashes($frow['p_name']).' - '.specialDate($frow['p_date'], $frow['p_dday']).' ['.(($frow['p_pics'] == '1')?'1 BILD':$frow['p_pics'].' BILDER').']</option>';
			}
		}
	}
?>
			</select></td>
		</tr>
		<tr><td colspan="2" width="350" style="padding: 5px 0 0 0;">ZIP-filer listas ifrån mappen: <b>DOLD</b></tr>
		</table>
		<table cellspacing="0" style="margin-bottom: 10px;">
<?
	$p_dir = listDir(substr($in_dir, 0, -1));
	$t = 0;
	if(count($p_dir) > 0) { 
		echo '		<tr>
			<td>ZIP-Fil</td>
			<td colspan"2">Fotograf</td>
		</tr>';
	foreach($p_dir as $val) {
	$t++;
?>
		<tr>
			<td nowrap><input type="text" name="file_<?=$t?>" class="inp_nrm" style="width: 131px;" value="<?=$val?>" readonly></td>
<td><select name="owner_<?=$t?>" id="owner_id:Y<?=$t?>" style="width: 130px; margin: 4px 0 0 2px;">
<option value="0">Välj</option>
<?=$photopt[1]?>
</select>
<script type="text/javascript">
<? $pos = 0; ?>
<?=($change)?'changeOwner(\'Y'.$t.'\', \''.(($pos = array_search($row['owner_id'], $photopt[0]))?$pos:0).'\');':'';?>
</script></td>
			<td align="right"><input type="button" class="inp_orgbtn" value="Generera" style="width: 80px; margin: 4px 0 0 2px;" onclick="if(document.addpic.p_session.options[document.addpic.p_session.selectedIndex].value != '0') popup('pics_generate.php?do_gen=1&id=' + document.addpic.p_session.options[document.addpic.p_session.selectedIndex].value + '&file=' + document.addpic.file_<?=$t?>.value + '&owner=' + document.addpic.owner_<?=$t?>.value, 'gen', 316, 355); else alert('Välj en bildsession!');"></td>
		</tr>
<?
	} } else {
?>
		<tr>
			<td colspan="3" align="center" height="50" style="vertical-align: middle;"><strong>Det finns inga ZIP-filer uppladdade.</strong></td>
		</tr>
<?	} ?>
		<tr><td colspan="3" style="padding: 5px 0 5px 0;"><hr /><div class="hr"></div></td></tr>
		</table>
		</form>