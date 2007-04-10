<?
	require_once('search_users.fnc.php');

	$result = performSearch();

#if($sexu)
	$menu = array('user' => array(l('list', 'users'), 'senast inloggade'), 'online' => array(l('list', 'users', '1'), 'visa online'), 'Fonline' => array(l('list', 'users', 'F'), 'tjejer online'), 'Monline' => array(l('list', 'users', 'M'), 'killar online'));
#else
#	$menu = array('user' => array(l('list', 'users'), 'senast inloggade'), 'online' => array(l('list', 'users', '1'), 'visa online'));


	require(DESIGN.'head.php');
?>
<script type="text/javascript">
function changePage(p) {
	document.search.p.value = p;
	document.search.submit();
}
</script>
<form name="search" action="<?=l('list', 'users', '2')?>" method="post">
		<input type="hidden" name="do" value="1" />
		<input type="hidden" name="p" value="0" />
		<div id="bigContent">
			<div class="bigHeader2"><h4>sök - <?=makeMenu($page, $menu)?></h4></div>
			<div class="bigBoxed2">
			<table cellspacing="0" class="mrg">
			<tr>
				<td style="padding-right: 30px;">alias:<br /><input type="text" class="txt" style="width: 170px;" name="alias" value="<?=secureOUT($result['alias'])?>" /></td>
				<td style="padding-right: 30px;">bor i:<br />
<?
	echo '<select class="txt" name="lan" onchange="this.form.submit();" style="width: 170px;">';
	echo '<option value="0">alla län</option>';
	optionLan($result['lan']);
	echo '</select><br />';

	echo '<select name="ort"'.(empty($result['lan'])?' disabled':'').' style="width: 170px;" class="txt" onchange="this.form.submit();">';
	echo '<option value="0">i alla orter</option>';
	optionOrt($result['lan'], $result['ort']);
	echo '</select>';
?>
				</td>
				<td style="padding-right: 30px;">alternativ:<br />
					<input type="checkbox" class="chk" value="1" name="pic" id="pic1" onclick="this.form.submit();"<?=($result['pic'])?' checked':'';?>><label for="pic1"> har bild</label><br />
					<input type="checkbox" class="chk" value="1" name="online" id="online1" onclick="this.form.submit();"<?=($result['online'])?' checked':'';?>><label for="online1"> är online</label><br />
					<input type="checkbox" class="chk" name="l" value="6" name="l_6" id="l_6" onclick="this.form.submit();"<?=($result['level'] == '6')?' checked':'';?>><label for="l_6"> VIP</label>
				</td>
				<td style="padding-right: 30px;">kön:<br />
					<input type="radio" class="chk" name="sex" value="0" id="s_0" onclick="this.form.submit();"<?=(!$result['sex'])?' checked':'';?>><label for="s_0"> alla</label><br />
					<input type="radio" class="chk" name="sex" value="M" id="s_m" onclick="this.form.submit();"<?=($result['sex'] == 'M')?' checked':'';?>><label for="s_m"> killar</label><br />
					<input type="radio" class="chk" name="sex" value="F" id="s_f" onclick="this.form.submit();"<?=($result['sex'] == 'F')?' checked':'';?>><label for="s_f"> tjejer</label>
				</td>
				<td>ålder:<br />
					<select name="age" class="txt" onchange="this.form.submit();">
					<option value="0"<?=(!$result['age'])?' selected':'';?>>alla åldrar</option>
					<option value="1"<?=($result['age'] == '1')?' selected':'';?>>mellan 0-20 år</option>
					<option value="2"<?=($result['age'] == '2')?' selected':'';?>>mellan 21-25 år</option>
					<option value="3"<?=($result['age'] == '3')?' selected':'';?>>mellan 26-30 år</option>
					<option value="4"<?=($result['age'] == '4')?' selected':'';?>>mellan 31-35 år</option>
					<option value="5"<?=($result['age'] == '5')?' selected':'';?>>mellan 36-40 år</option>
					<option value="6"<?=($result['age'] == '6')?' selected':'';?>>mellan 41-45 år</option>
					<option value="7"<?=($result['age'] == '7')?' selected':'';?>>mellan 46-50 år</option>
					<option value="8"<?=($result['age'] == '8')?' selected':'';?>>mellan 51-55 år</option>
					<option value="9"<?=($result['age'] == '9')?' selected':'';?>>56 år och äldre</option>
					</select>
				</td>
			</tr>
			</table>
			<input type="submit" class="btn2_sml r" value="sök" /><br class="clr" />
			</div>
			<div>
				<? if(count($result['res'])) dopaging($result['paging'], 'javascript:changePage(\'', '\');', 'biggest', STATSTR, 0); ?>
			</div>
			<table cellspacing="0"<?=($result['pic'])?'':' width="783"';?>>
<?
	if(empty($result['res']) || !count($result['res'])) {
		echo '<tr><td class="spac pdg cnt" width="786">Inga listade.</td></tr>';
	} else {
		$i = 0;
		$nl = true;
		if($result['pic']) {
			foreach($result['res'] as $row) {
				if($nl) echo (($i)?'</tr>':'').'<tr>';
				$i++;
				echo '<td style="padding: 0 0 6px '.((!$nl)?'5':'0').'px;">'.$user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid'], 0, array('text' => $row['u_alias'].' '.$sex[$row['u_sex']].$user->doage($row['u_birth'], 0))).'</td>';
				if($i % 16 == 0) $nl = true; else $nl = false;
			}
		} else {
			$i = 0;
			foreach($result['res'] as $row) {
				$i++;
				#$gotpic = ($row['u_picvalid'] == '1')?true:false;
				$gotpic = false;
				echo '
					<tr'.(($gotpic)?' onmouseover="this.className = \'t1\'; dumblemumble(\''.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'\', 1);" onmouseout="this.className = \'\'; mumbledumble(\''.$row['id_id'].$row['u_picid'].$row['u_picd'].$i.'\', 0, 1);"':' onmouseover="this.className = \'t1\';" onmouseout="this.className = \'\';"').'>
						<td class="cur pdg spac" width="250">'.$user->getstring($row, '', array('icons' => 1)).'</td>
						<td class="cur pdg spac" onclick="goUser(\''.$row['id_id'].'\');"><nobr>'.ucwords(strtolower($row['u_pstort'].($row['u_pstlan']?', ':'').$row['u_pstlan'])).'</nobr></td>
						<td class="cur pdg spac cnt" onclick="goUser(\''.$row['id_id'].'\');">'.(($gotpic)?'<img src="./_img/icon_gotpic.gif" alt="har bild" style="margin-top: 2px;" />':'&nbsp;').'</td>
						<td class="cur pdg spac rgt" onclick="goUser(\''.$row['id_id'].'\');"><nobr>'.(($user->isonline($row['account_date']))?'<span class="on">online ('.nicedate($row['lastlog_date'], 2).')</span>':'<span class="off">'.nicedate($row['lastonl_date'], 2).'</span>').'</nobr></td>
					</tr>';
				if($gotpic) echo '<tr id="pic:'.$i.'" style="display: none;"><td colspan="2">'.$user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid']).'</td></tr>';
			}
		}
	}
?>
</table>
<?
	if(count($result['res'])) dopaging($result['paging'], 'javascript:changePage(\'', '\');', 'biggest', '&nbsp;', 0);
?>
			</div>
		</div>
<?
	include(DESIGN.'foot.php');
?>