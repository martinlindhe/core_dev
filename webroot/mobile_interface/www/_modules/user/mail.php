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
				reloadACT(l('user', 'mail').'&amp;'.$page);
			else
				reloadACT(l('user', 'mail'));
		}

	} else if (!empty($_POST['chg'])) {
		
		if (mailDeleteArray($_POST['chg'])) {		
			if($page == 'out')
				reloadACT(l('user', 'mail').'&amp;'.$page);
			else
				reloadACT(l('user', 'mail'));
		}

	}
	$menu = array('in' => array(l('user', 'mail'), 'inkorg'), 'out' => array(l('user', 'mail').'&amp;out', 'utkorg'));
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
<?dopaging($paging, l('user', 'mail', $s['id_id']).'&amp;'.$page.'&amp;p=', '', 'big', STATSTR);?>
<form name="m" action="<?=l('user', 'mail', $s['id_id']).'&amp;'.$page?>" method="post">
<?
if(count($res) && !empty($res)) {
echo '<input type="checkbox" onclick="toggle(this);" class="chk" style="margin-bottom: 3px;"/><input type="submit" value="radera mark." class="btn2_min" /><hr />';
}
?>
<table summary="" cellspacing="0" width="586">
<?
if(count($res) && !empty($res)) {
	foreach($res as $row) {
		$c = ($page == 'in' && !$row['user_read'])?' act_bg':'';
		echo '<tr'.($c?' class="'.$c.'"':'').'>
			<td style="width: 10px; padding-right: 10px;"><input type="checkbox" class="chk" name="chg[]" value="'.$row['main_id'].'" /></td>
			<td class="cur" onclick="goLoc(\''.l('user','mailread', $row['main_id']).'&amp;'.$page.'\');"><div style="overflow: hidden; height: 20px; width: 200px; padding-top: 4px;"><a href="'.l('user','mailread', $row['main_id']).'&amp;'.$page.'">'.($row['sent_ttl']?secureOUT($row['sent_ttl']):'<em>Ingen titel</em>').'</a>&nbsp;</div></td>
			<td style="padding-top: 4px;">'.$user->getstring($row).'</td>
			<td class="rgt" style="padding-top: 4px;">'.nicedate($row['sent_date'], 1, 1).'</td>
			<td class="rgt mid"><a href="'.l('user', 'mail').'&amp;'.$page.'&amp;del_msg='.$row['main_id'].'" onclick="if(confirm(\'Säker ?\n\nMeddelandet kommer att raderas.\')) goLoc(\''.l('user', 'mail', $l['id_id']).'&amp;'.$page.'&amp;del_msg='.$row['main_id'].'\');"><img src="'.OBJ.'icon_del.gif" alt="" /></a></td>
		</tr>';
	}
} else {
	echo '<tr><td class="pdg cnt">Inga brev.</td></tr>';
}
?>
</table>
</form>
			</div>
	</div>
<?
	require(DESIGN.'foot.php');
?>
