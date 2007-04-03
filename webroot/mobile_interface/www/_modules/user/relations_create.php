<?
	if($own) popupACT('Du kan inte skapa en relation med dig själv.');
#	$user->blocked($s['id_id']);
	$isFriends = $user->isFriends($s['id_id'], 1);
	if($isFriends) {
		popupACT('Ni har redan en aktiv relation.');
	}

	if(!empty($_POST['ins_rel'])) {
		if($isAdmin) {
			$r = (is_numeric($_POST['ins_rel']))?getset($_POST['ins_rel'], 'r'):$_POST['ins_rel'];
		} else {
			$r = getset($_POST['ins_rel'], 'r');
			if(!$r) popupACT('Relationen finns inte.');
		}
		$c = $sql->queryResult("SELECT main_id FROM {$t}userrelation WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".secureINS($s['id_id'])."' LIMIT 1");
		if(!empty($c)) {
### ÄNDRA!
			$sql->queryUpdate("UPDATE {$t}userrelquest SET status_id = '2' WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($s['id_id'])."'");
			$sql->queryUpdate("UPDATE {$t}userrelquest SET status_id = '2' WHERE user_id = '".secureINS($s['id_id'])."' AND sender_id = '".secureINS($l['id_id'])."'");
			$sql->queryUpdate("DELETE FROM {$t}userrelation WHERE user_id = '".secureINS($l['id_id'])."' AND friend_id = '".secureINS($s['id_id'])."'");
			$sql->queryUpdate("DELETE FROM {$t}userrelation WHERE user_id = '".secureINS($s['id_id'])."' AND friend_id = '".secureINS($l['id_id'])."'");
			$res = $sql->queryInsert("INSERT {$t}userrelquest SET
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				sent_date = NOW(),
				user_id = '".secureINS($s['id_id'])."',
				sender_id = '".secureINS($l['id_id'])."'");
			$user->setRelCount($s['id_id']);
			$user->setRelCount($l['id_id']);
			popupACT('Nu har du skickat en förfrågan.');
		} else {
			$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userrelquest WHERE user_id = '".secureINS($l['id_id'])."' AND sender_id = '".secureINS($s['id_id'])."' AND status_id = '0'");
			if($c > 0) popupACT('Du har redan blivit tillfrågad.');

			$c = $sql->queryResult("SELECT COUNT(*) as count FROM {$t}userrelquest WHERE user_id = '".secureINS($s['id_id'])."' AND sender_id = '".secureINS($l['id_id'])."' AND status_id = '0'");
			if($c > 0) {
				@mysql_query("UPDATE {$t}userrelquest SET
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				sent_date = NOW()
				WHERE user_id = '".secureINS($s['id_id'])."' AND sender_id = '".secureINS($l['id_id'])."' AND status_id = '0'");
				$user->setRelCount($s['id_id']);
				$user->setRelCount($l['id_id']);
				popupACT('Nu har du skickat en förfrågan.');
			} else {
				$sql->queryInsert("INSERT INTO {$t}userrelquest SET
				user_id = '".secureINS($s['id_id'])."',
				sender_id = '".secureINS($l['id_id'])."',
				sent_cmt = '".secureINS($r)."',
				status_id = '0',
				sent_date = NOW()");
			$user->setRelCount($s['id_id']);
			$user->setRelCount($l['id_id']);
			popupACT('Nu har du skickat en förfrågan.');
			}
		}
	} 

	$rel = getset('', 'r', 'mo', 'text_cmt ASC');

	require(DESIGN.'head_popup.php');
?>
<body>
<form name="msg" action="<?=l('user', 'relations', $s['id_id'], '0')?>create=1" method="post">
		<div class="popupWholeContent cnti mrg">
			<div class="smallHeader1 lft"><h4>bli vän</h4></div>
			<div class="smallFilled2 cnt pdg_t wht">
			<table cellspacing="0" style="height: 150px;"><tr><td style="height: 150px; vertical-align: middle;">
bli vän med:<br /><span class="bg_wht"><?=$user->getstring($s, '', array('nolink' => true))?></span>
<br /><br /><b>relationstyp:</b><br /><select name="ins_rel" class="txt">
<?
	foreach($rel as $row) {
echo '<option value="'.$row[0].'">'.secureOUT($row[1]).'</option>';
	}
?>
</select>
			</td></tr></table>
				<input type="submit" class="btn2_sml r" value="spara!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>
</body>
</html>
</div>
</body>
</html>