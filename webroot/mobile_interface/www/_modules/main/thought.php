<?
	$str = '';
	$page = 'start';
	$menu = array('start' => array(l('main','thought'),'start'));
	if($l) {
		if(!empty($_POST['ins_cmt'])) {
			checkBan(1);
			if($l) {
				$ins = $sql->queryInsert("INSERT INTO {$t}thought SET
				gb_name = '".secureINS($l['u_alias'])."',
				logged_in = '".secureINS($l['id_id'])."',
				sess_ip = '".secureINS($_SERVER["REMOTE_ADDR"])."',
				sess_id = '".secureINS($sql->gc())."',
				gb_html = '".(($isAdmin)?'1':'0')."',
				gb_msg = '".secureINS($_POST['ins_cmt'])."',
				gb_date = NOW()");
			}
			if($ins) $sql->logADD('', $ins, 'GB_SEND');
				$msg = "Tack! Meddelandet kommer att publiceras n�r det granskats!";
				errorACT($msg, l('main', 'thought'));
		} elseif(!empty($_GET['del']) && is_numeric($_GET['del'])) {
			$do = false;
			if(!$isAdmin) {
				$id_id = $sql->queryLine("SELECT status_id, logged_in FROM {$t}thought WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
				if($id_id[0] == '1' && $id_id[1] == $l['id_id']) $do = true;
			} else $do = true;
			if($do) {
				$sql->queryUpdate("UPDATE {$t}thought SET status_id = '2', view_id = '1' WHERE main_id = '".secureINS($_GET['del'])."' LIMIT 1");
			}
			reloadACT(l('main', 'thought', $paging['p']));
		}
	}

	$search = false;
	if(!empty($_POST['s'])) $search = true;
	if(!$search) {
		$paging = paging(@$_GET['id'], 20);
		$gb = $sql->query("SELECT ".CH." a.gb_msg, a.gb_date, a.gb_html, a.answer_msg, a.answer_date, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.id_id, u.u_sex, u.u_birth, u.level_id, u.account_date, u2.u_alias as u_alias2, u2.u_picid as u_picid2, u2.u_picd as u_picd2, u2.u_picvalid as u_picvalid2, u2.id_id as id_id2, u2.u_sex as u_sex2, u2.u_birth as u_birth2, u2.level_id as level_id2, u2.account_date as account_date2 FROM ({$t}thought a, {$t}user u) INNER JOIN {$t}user u2 ON u2.id_id = a.answer_id AND u2.status_id = '1'  WHERE u.id_id = a.logged_in AND u.status_id = '1' AND a.status_id = '1' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
#		$gb = $sql->query("SELECT ".CH." a.*, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.id_id, u.u_sex, u.u_birth, u.level_id, u.account_date FROM {$t}thought a INNER JOIN {$t}user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.status_id = '1' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
		$paging['co'] = $sql->queryResult("SELECT ".CH." COUNT(*) as count FROM {$t}thought a INNER JOIN {$t}user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.status_id = '1'");
	} else {
		$paging = paging(1, 20);
		$str = $_POST['s'];
		if(substr($str, 0, 1) == 't' && is_numeric(substr($str, 1))) {
			 $gb = $sql->query("SELECT ".CH." a.gb_msg, a.gb_date, a.gb_html, a.answer_msg, a.answer_date, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.id_id, u.u_sex, u.u_birth, u.level_id, u.account_date, u2.u_alias as u_alias2, u2.u_picid as u_picid2, u2.u_picd as u_picd2, u2.u_picvalid as u_picvalid2, u2.id_id as id_id2, u2.u_sex as u_sex2, u2.u_birth as u_birth2, u2.level_id as level_id2, u2.account_date as account_date2 FROM ({$t}thought a, {$t}user u) INNER JOIN {$t}user u2 ON u2.id_id = a.answer_id AND u2.status_id = '1'  WHERE u.id_id = a.logged_in AND u.status_id = '1' AND a.status_id = '1' AND a.main_id = '".substr($str, 1)."' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
			#$gb = $sql->query("SELECT ".CH." a.*, u.u_alias, u.u_picid, u.u_picd, u.id_id, u.u_sex, u.u_birth, u.level_id, u.account_date FROM {$t}thought a INNER JOIN {$t}user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.status_id = '1' AND a.main_id = '".substr($str, 1)."' ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
			$paging['co'] = count($gb); //$sql->queryResult("SELECT ".CH." COUNT(*) as count FROM {$t}thought a INNER JOIN {$t}user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.status_id = '1' AND a.main_id = '".substr($str, 1)."'");
			#if(!empty($gb) && count($gb) == '1' && !empty($_GET['spy'])) { $user->cleanspy($l['id_id'], substr($str, 1), 'THO'); }
		} else {
			$gb = $sql->query("SELECT ".CH." a.gb_msg, a.gb_date, a.gb_html, a.answer_msg, a.answer_date, u.u_alias, u.u_picid, u.u_picd, u.u_picvalid, u.id_id, u.u_sex, u.u_birth, u.level_id, u.account_date, u2.u_alias as u_alias2, u2.u_picid as u_picid2, u2.u_picd as u_picd2, u2.u_picvalid as u_picvalid2, u2.id_id as id_id2, u2.u_sex as u_sex2, u2.u_birth as u_birth2, u2.level_id as level_id2, u2.account_date as account_date2 FROM ({$t}thought a, {$t}user u) INNER JOIN {$t}user u2 ON u2.id_id = a.answer_id AND u2.status_id = '1'  WHERE u.id_id = a.logged_in AND u.status_id = '1' AND a.status_id = '1' AND (a.gb_name LIKE '%".secureINS($str)."%' OR a.gb_email LIKE '%".secureINS($str)."%' OR a.gb_msg LIKE '%".secureINS($str)."%' OR a.answer_msg LIKE '%".secureINS($str)."%') ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
			#$gb = $sql->query("SELECT ".CH." a.*, u.u_alias, u.u_picid, u.u_picd, u.id_id, u.u_sex, u.u_birth, u.level_id, u.account_date FROM {$t}thought a INNER JOIN {$t}user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.status_id = '1' AND (a.gb_name LIKE '%".secureINS($str)."%' OR a.gb_email LIKE '%".secureINS($str)."%' OR a.gb_msg LIKE '%".secureINS($str)."%' OR a.answer_msg LIKE '%".secureINS($str)."%') ORDER BY a.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
			$paging['co'] = count($gb); //$sql->queryResult("SELECT ".CH." COUNT(*) as count FROM {$t}thought a INNER JOIN {$t}user u ON u.id_id = a.logged_in AND u.status_id = '1' WHERE a.status_id = '1' AND (a.gb_name LIKE '%".secureINS($str)."%' OR a.gb_email LIKE '%".secureINS($str)."%' OR a.gb_msg LIKE '%".secureINS($str)."%' OR a.answer_msg LIKE '%".secureINS($str)."%')");
		}
	}

	require(DESIGN.'head.php');
?>
		<div class="mainContent" width="100%">
		<table cellspacing="0"><tr><td>
<form action="<?=l('main', 'thought')?>" method="post">
		<table cellspacing="0" cellpadding="0px" class="pdg" style="width: 400px;">
	<tr><td><img alt="" src="<?=OBJ?>_heads/head_thought.png" /></td></tr>
	<tr><td style="padding-bottom: 8px;"><?=gettxt('top-thought')?></td></tr>
	<?=($l?'<tr><td><textarea class="txt" name="ins_cmt" style="width: 400px; height: 80px;"></textarea></td></tr>
	':'<tr><td>Du m�ste vara inloggad f�r att kunna skriva.</td></tr>')?>
	<tr><td align="right" style="padding-bottom: 8px;"><input type="image" src="<?=OBJ?>_heads/btn2_send.png" /></td></tr>
	</table>
</form>
</td><td>
<form action="<?=l('main', 'thought')?>" method="post">
	
	<table align="right" cellspacing="0" class="cnti" style="width: 120px;">
	<tr><td class="cnt"><img alt="" src="<?=OBJ?>_heads/head_search_2.png" style="position: absolute; top: -10px; left: -10px;" /></td></tr>
	<tr><td class="rgt"><input class="txt" name="s" value="<?=secureOUT($str)?>" style="width: 150px;" /></td></tr>
	<tr><td class="rgt"><input type="image" src="<?=OBJ?>_heads/btn2_search.png" /></td></tr>
	</table>

</form>
</td></tr>
</table>

<!--		</div> -->
			<div class="mainHeader2"><h4><?=makeMenu($page, $menu)?></h4></div>
			<div class="mainBoxed2">
<?

//($search?secureOUT($_POST['s']):'')).($search?'p=':'')
		dopaging($paging, l('main', 'thought'), '', 'med', STATSTR);
	if(count($gb) && !empty($gb)) {
		$i = 0;
		foreach($gb as $row) {
			#if($i)  else $brd = false;
			$brd = true;
			#$i++;
			$own = ($l && ($row['logged_in'] == $l['id_id'] || $isAdmin))?true:false;
echo '
<div class="pdg">
	<div class="pdg">
	<table cellspacing="0" class="cnti lft" style="margin-top: 5px; width: 450px;">
	<tr><td style="width: 50px; padding: 0 5px 10px 0;">'.$user->getimg($row['id_id'].$row['u_picid'].$row['u_picd'].$row['u_sex'], $row['u_picvalid']).'</td><td style="padding-bottom: 10px;"><div style="width: 440px; overflow: hidden;"><h5>'.$user->getstring($row).' - '.nicedate($row['gb_date']).'</h5><span class="">'.secureOUT($row['gb_msg'], 1).'</span></div></td></tr>
	'.(!empty($row['answer_msg'])?'<tr><td style="width: 50px; padding: 0 5px 10px 0;">'.$user->getimg($row['id_id2'].$row['u_picid2'].$row['u_picd2'].$row['u_sex2'], $row['u_picvalid2']).'</td><td style="padding-bottom: 10px;"><div style="width: 440px; overflow: hidden;"><h5>'.$user->getstring($row, '2').' - '.nicedate($row['answer_date']).'</h5><span class="">'.secureOUT($row['answer_msg'], 1).'</span></div></td></tr>':'').'
	</table>
	</div>
</div>';
		}
	} else echo '
	<table cellspacing="0" class="cnt" style="width: 450px;">
	<tr><td>Inga inl�gg.</td></tr>
	</table>
';

		dopaging($paging, l('main', 'thought'), '', 'med');
?>
		</div>
	</div>
<?
	require(DESIGN.'foot.php');
?>