<?
	require('mail.fnc.php');

	$s = $l;
	$own = true;
	$page = 'in';

	if(isset($_GET['out'])) $page = 'out';
	if(!empty($_GET['del_msg'])) {
		if (mailDelete($_GET['del_msg'])) {
			if (isset($_GET['p'])) popupACT('Meddelandet raderat.', '', '500', l('user', 'mail', $page));

			if($page == 'out')
				reloadACT(l('user', 'mail').'&'.$page);
			else
				reloadACT(l('user', 'mail'));
		}

	} else if (!empty($_POST['chg'])) {
		
		if (mailDeleteArray($_POST['chg'])) {		
			if($page == 'out')
				reloadACT(l('user', 'mail').'&'.$page);
			else
				reloadACT(l('user', 'mail'));
		}

	}
	$menu = array('in' => array(l('user', 'mail'), 'inkorg'), 'out' => array(l('user', 'mail').'&out', 'utkorg'));
	$paging = paging(@$_GET['p'], 20);
	if($page == 'in') {
		$paging['co'] = mailInboxCount();
		$res = mailInboxContent($paging['slimit'], $paging['limit']);
	} else {
		$paging['co'] = mailOutboxCount();
		$res = mailOutboxContent();
	}
	require(DESIGN."head.php");
?>
	<div id="mainContent">
<script type="text/javascript">
function toggle(type) {
	type = (type.checked)?true:false;
	for(i = 0; i < document.m.length; i++) {

		var toggle = document.m.elements[i];
		if(toggle.type == 'checkbox') {
			toggle.checked = type;
		}
	}
}
function openMail(th, id) {
	th.className = th.className.replace('act_bg', '');
	makeBig(id);
}
</script>
			<div class="mainHeader2"><h4><?=makeMenu($page, $menu)?></h4></div>
			<div class="mainBoxed2">
<?dopaging($paging, l('user', 'mail', $s['id_id']).'&'.$page.'&p=', '', 'big', STATSTR);?>
<form name="m" action="<?=l('user', 'mail', $s['id_id']).'&'.$page?>" method="post">
<?
if(count($res) && !empty($res)) {
echo '<input type="checkbox" onclick="toggle(this);" class="chk" style="margin-bottom: 3px;"><input type="submit" value="radera mark." class="btn2_min" /><hr />';
}
?>
<table cellspacing="0" width="586">
<?
	if(count($res) && !empty($res)) {
	foreach($res as $row) {
		$c = ($page == 'in' && !$row['user_read'])?' act_bg':'';
/*echo '<tr'.(($c)?' class="'.$c.'"':' onmouseover="this.className = \'t1\';" onmouseout="this.className = \'\';"').'>
	<td class="spac pdg_l" style="padding-right: 0; width: 10px;"><input type="checkbox" class="chk" name="chg[]" value="'.$row['main_id'].'" /></td>
	<td class="cur spac pdg_l"><nobr><img src="'.OBJ.'icon_mail.gif" style="margin: 0 5px -2px 5px;" />
	<!--
	 onclick="openMail(this.parentNode, \'mail_read.php?id='.$row['main_id'].'\');"
	<a class="cur up bld" onclick="pop(this.parentNode.parentNode.parentNode, \'mail_read.php?id='.$row['main_id'].'\');">'.$user->getstring($row, array('noicon' => true, 'nolink' => true)).'</a>
	//-->
	<a class="cur up bld" href="mail_read.php?id='.$row['main_id'].'">'.$user->getstring($row, array('noicon' => true, 'nolink' => true)).'</a>&nbsp;</nobr></td>
	<td class="cur spac pdg_l" onclick="openMail(this.parentNode, \'mail_read.php?id='.$row['main_id'].'\');"><div style="overflow: hidden; height: 14px;">'.secureOUT($row['sent_ttl']).'&nbsp;</div></td>
	<td class="cur spac pdg_l rgt" onclick="openMail(this.parentNode, \'mail_read.php?id='.$row['main_id'].'\');"><nobr>'.nicedate($row['sent_date'], 1, 1).'</nobr></td>
	<td class="spac pdg_l rgt mid"><a href="mail.php?'.$page.'&del_msg='.$row['main_id'].'" onclick="return confirm(\'Säker ?\n\nMeddelandet kommer att raderas helt och hållet!\');"><img src="'.OBJ.'icon_del.gif" title="Radera" /></a></td>
</tr>
';*/
echo '<tr'.($c?' class="'.$c.'"':'').'>
	<td style="width: 10px; padding-right: 10px;"><input type="checkbox" class="chk" name="chg[]" value="'.$row['main_id'].'" /></td>
	<td class="cur" onclick="goLoc(\''.l('user','mailread', $row['main_id']).'&'.$page.'\');"><div style="overflow: hidden; height: 20px; width: 200px; padding-top: 4px;"><a href="'.l('user','mailread', $row['main_id']).'&'.$page.'">'.($row['sent_ttl']?secureOUT($row['sent_ttl']):'<em>Ingen titel</em>').'</a>&nbsp;</div></td>
	<td style="padding-top: 4px;">'.$user->getstring($row).'</td>
	<td class="rgt" style="padding-top: 4px;">'.nicedate($row['sent_date'], 1, 1).'</td>
	<td class="rgt mid"><a href="'.l('user', 'mail').'&'.$page.'&del_msg='.$row['main_id'].'" onclick="if(confirm(\'Säker ?\n\nMeddelandet kommer att raderas.\')) goLoc(\''.l('user', 'mail', $l['id_id']).'&'.$page.'&del_msg='.$row['main_id'].'\');"><img src="'.OBJ.'icon_del.gif" /></a></td>
</tr>
';
	} } else echo '<tr><td class="pdg cnt">Inga brev.</td></tr>';
?>
</table>
</form>
			</div>
	</div>
<?
	require(DESIGN.'foot.php');
?>
