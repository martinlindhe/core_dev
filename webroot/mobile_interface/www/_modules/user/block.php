<?
	if($own) popupACT('Du kan inte blocka dig själv.');
	if(!empty($_POST['do'])) {
		$sql->queryInsert("INSERT INTO {$t}userblock SET rel_id = 'u', user_id = '".secureINS($l['id_id'])."', friend_id = '".secureINS($s['id_id'])."', activated_date = NOW()");
		$sql->queryInsert("INSERT INTO {$t}userblock SET rel_id = 'f', user_id = '".secureINS($s['id_id'])."', friend_id = '".secureINS($l['id_id'])."', activated_date = NOW()");
		popupACT('Nu har du blockerat personen.', '', l('user', 'block', $s['id_id']));
	}
	require(DESIGN.'head_popup.php');
?>
<body>
<form name="msg" action="<?=l('user', 'block', $s['id_id'])?>" method="post">
<input type="hidden" name="do" value="1" />
		<div class="popupWholeContent cnti mrg">
			<div class="smallHeader1 lft"><h4>blockera</h4></div>
			<div class="smallFilled2 cnt pdg_t wht">
			<table cellspacing="0" style="height: 150px;"><tr><td style="height: 150px; vertical-align: middle;">
blockera:<br /><span class="bg_wht"><?=$user->getstring($s, '', array('nolink' => true))?></span>
<br /><br /><p class="lft">varken du eller personen kommer att kunna kontakta varandra här på sidan. du kan när som helst ta bort din blockering under vänner / ovänner.</p><br /><b>fortsätt?</b><br /><br />
			</td></tr></table>
				<input type="submit" class="btn2_med r" value="blockera!" style="margin-top: 5px;" /><br class="clr" />
			</div>
		</div>
</form>
</body>
</html>