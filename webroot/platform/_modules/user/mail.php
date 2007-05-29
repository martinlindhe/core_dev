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
				reloadACT(l('user', 'mail').'&amp;'.$page);
			else
				reloadACT(l('user', 'mail'));
		}

	}
	$paging = paging(@$_GET['p'], 20);
	if($page == 'in') {
		$paging['co'] = mailInboxCount();
		$res = mailInboxContent($paging['slimit'], $paging['limit']);
	} else {
		$paging['co'] = mailOutboxCount();
		$res = mailOutboxContent();
	}
	require(DESIGN."head_user.php");
?>

	<div class="subHead">brev</div><br class="clr"/>
	<? makeButton(!isset($_GET['out']), 	'goLoc(\''.l('user', 'mail').'\')',	'icon_mail.png', 'inkorg'); ?>
	<? makeButton(isset($_GET['out']), 	'goLoc(\''.l('user', 'mail').'&amp;out\')',	'icon_mail.png', 'utkorg'); ?>
	<br/><br/><br/>

	<div class="centerMenuBodyWhite">
<?dopaging($paging, l('user', 'mail', $s['id_id']).'&amp;'.$page.'&amp;p=', '', 'big', STATSTR);?>
<form name="m" action="<?=l('user', 'mail', $s['id_id']).'&amp;'.$page?>" method="post">
<?
if(count($res) && !empty($res)) {
echo '<input type="checkbox" onclick="toggle2(this);" class="chk" style="margin-bottom: 3px;"/><input type="submit" value="radera mark." class="btn2_min" /><hr />';
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
			<td style="padding-top: 4px;">'.($row['sender_id']?$user->getstring($row):'SYSTEM').'</td>
			<td class="rgt" style="padding-top: 4px;">'.nicedate($row['sent_date'], 1, 1).'&nbsp;</td>';
		
		echo '<td width="66">';
		makeButton(false, 'if(confirm(\'Säker ?\n\nMeddelandet kommer att raderas.\')) goLoc(\''.l('user', 'mail', $l['id_id']).'&amp;'.$page.'&amp;del_msg='.$row['main_id'].'\');', 'icon_delete.png', 'radera');
		echo '</td>';

		echo '</tr>';
	}
} else {
	echo '<tr><td class="pdg cnt">Inga brev.</td></tr>';
}
?>
</table>
</form>
	</div>
<?
	require(DESIGN.'foot_user.php');
?>
