<?
	include(CONFIG.'secure.fnc.php');
	$res = $sql->queryLine("SELECT ".CH." main_id, status_id, user_id, blog_title, blog_date, blog_cmt, hidden_id, blog_visit, blog_cmts FROM {$t}userblog WHERE main_id = '".secureINS($key)."' LIMIT 1", 1);
	if(empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1' || $s['id_id'] != $res['user_id']) {
		errorACT('Felaktigt inlägg.', l('user', 'blog', $s['id_id']));
	}
	if(!empty($_GET['del_msg']) && is_numeric($_GET['del_msg'])) {
		$r = $sql->queryLine("SELECT main_id, status_id, user_id, id_id FROM {$t}userblogcmt WHERE main_id = '".secureINS($_GET['del_msg'])."' LIMIT 1");
		if(!empty($r) && count($r) && $r[1] == '1') {
			if($isAdmin || $r[2] == $l['id_id'] || $r[3] == $l['id_id']) {
				$re = $sql->queryUpdate("UPDATE {$t}userblogcmt SET status_id = '2' WHERE main_id = '".secureINS($r[0])."' LIMIT 1");
			}
			if($re) {
				$sql->queryUpdate("UPDATE {$t}userblog SET blog_cmts = blog_cmts - 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
			}
			reloadACT(l('user', 'blog', $s['id_id'], $res['main_id']));
		}
	}
#what is this???????????
#	$res0 = $sql->queryUpdate("UPDATE {$t}userblog SET blog_visit=(blog_visit+1) WHERE main_id = '".secureINS($key)."' LIMIT 1", 0);
#	$res1 = $sql->queryInsert("INSERT INTO {$t}userblogvisit VALUES ('','".$_SESSION['data']['id_id']."','".secureINS($key)."','1', NOW())");
#???? look below, that code is correct!
	if(!$own) {
		$hidden = $user->getinfo($l['id_id'], 'hidden_bview');
		if($isAdmin && $res['hidden_id']) {
			$beenhere = true;
		} else {
			if(!$hidden) {
				$visit = @$sql->queryUpdate("REPLACE INTO {$t}userblogvisit SET status_id = '1', visit_date = NOW(), visitor_id = '".secureINS($l['id_id'])."', blog_id = '".secureINS($res['main_id'])."'");
				$beenhere = ($visit != '2')?false:true;
			} else {
				$visit = @$sql->queryUpdate("REPLACE INTO {$t}userblogvisit SET status_id = '2', visit_date = NOW(), visitor_id = '".secureINS($l['id_id'])."', blog_id = '".secureINS($res['main_id'])."'");
				$beenhere = ($visit != '2')?false:true;
			}
		}
		if(!$beenhere) {
			$sql->queryUpdate("UPDATE {$t}userblog SET blog_visit = blog_visit + 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
			if(!$hidden) {
				$sql->queryUpdate("UPDATE {$t}userblogvisit SET status_id = '1', visit_date = NOW() WHERE visitor_id = '".secureINS($l['id_id'])."' AND blog_id = '".secureINS($res['main_id'])."' LIMIT 1");
			} else {
				$sql->queryUpdate("UPDATE {$t}userblogvisit SET status_id = '2', visit_date = NOW() WHERE visitor_id = '".secureINS($l['id_id'])."' AND blog_id = '".secureINS($res['main_id'])."' LIMIT 1");
			}
		}
	}

	$page = 'blog';
/*
	if($allowed) {
		$bL = $sql->queryResult("SELECT main_id FROM {$tab['blog']} WHERE status_id = '1' AND user_id = '".$s['id_id']."' AND main_id < '".$res['main_id']."' ORDER BY main_id DESC LIMIT 1");
		$nL = $sql->queryResult("SELECT main_id FROM {$tab['blog']} WHERE status_id = '1' AND user_id = '".$s['id_id']."' AND main_id > '".$res['main_id']."' ORDER BY main_id ASC LIMIT 1");
	} else {
		$bL = $sql->queryResult("SELECT main_id FROM {$tab['blog']} WHERE status_id = '1' AND hidden_id = '0' AND user_id = '".$s['id_id']."' AND main_id < '".$res['main_id']."' ORDER BY main_id DESC LIMIT 1");
		$nL = $sql->queryResult("SELECT main_id FROM {$tab['blog']} WHERE status_id = '1' AND hidden_id = '0' AND user_id = '".$s['id_id']."' AND main_id > '".$res['main_id']."' ORDER BY main_id ASC LIMIT 1");
	}
*/
	require(DESIGN.'head_user.php');
?>
		<div class="mainHeader2"><h4><?=secureOUT($res['blog_title'])?> - publicerad: <?=nicedate($res['blog_date'])?> - <a class="wht" href="<?=l('user', 'blog', $s['id_id'])?>">tillbaka</a></h4></div>
		<div class="mainBoxed2">
	<style type="text/css">
	#blog_text p { margin: 0; padding: 0; }
	</style>
	<div style="width: 586px; overflow: hidden;" id="blog_text" class="pdg">
		<?=formatText($res['blog_cmt'])?>
	</div>
	<br class="clr" />
		</div>
		<div class="mainHeader2"><a name="cmt"></a><h4>kommentarer - <a class="wht" href="javascript:makeBlogComment('<?=$s['id_id']?>', '<?=$res['main_id']?>');">skriv kommentar</a></h4></div>
		<div class="mainBoxed2">
<?
	$c_paging = paging(@$_GET['p'], 20);
	$c_paging['co'] = $sql->queryResult("SELECT ".CH." COUNT(*) as count FROM {$t}userblogcmt WHERE blog_id = '".$res['main_id']."' AND status_id = '1'");
	#dopaging($c_paging, '', '', 'bigmed', STATSTR);
	$odd = 1;
	$cmt = $sql->query("SELECT ".CH." b.main_id, b.c_msg, b.c_date, b.c_html, b.private_id, u.id_id, u.u_alias, u.u_sex, u.level_id, u.u_birth, u.u_picd, u.u_picid, u.u_picvalid, u.account_date FROM {$t}userblogcmt b LEFT JOIN {$t}user u ON u.id_id = b.id_id AND u.status_id = '1' WHERE b.blog_id = '".$res['main_id']."' AND b.status_id = '1' ORDER BY b.main_id DESC LIMIT {$c_paging['slimit']}, {$c_paging['limit']}", 0, 1);
	if(count($cmt) && !empty($cmt)) { foreach($cmt as $val) {
		$msg_own = ($val['id_id'] == $l['id_id'] || $own || $isAdmin)?true:false;
		$odd = !$odd;
		echo
'
	<table cellspacing="0" style="width: 594px;'.($odd?'':' background: #ecf1ea;').'">
	<tr><td class="pdg" style="width: 55px;" rowspan="2">'.$user->getimg($val['id_id'].$val['u_picid'].$val['u_picd'].$val['u_sex'], $val['u_picvalid']).'</td><td class="pdg"><h5 class="l">'.$user->getstring($val, '', array('noimg' => 1)).' - '.nicedate($val['c_date']).'</h5><div class="r"></div><br class="clr" />
	'.secureOUT($val['c_msg']).'
	</td></tr>
	<tr><td class="btm rgt pdg">&nbsp;'.(($msg_own)?'<a href="'.l('user', 'blog', $s['id_id'], $res['main_id']).'&del_msg='.$val['main_id'].'" onclick="if(confirm(\'Säker ?\')) goLoc(\''.l('user', 'blog', $s['id_id'], $res['main_id']).'del_msg='.$val['main_id'].'\');"><img src="'.OBJ.'icon_del.gif" style="margin-bottom: -2px;" /></a>':'').'</td></tr>
	</table>
';
/*		<table cellspacing="0" style="width: 586px;">
		<tr>
		<td'.(($msg_own)?' rowspan="2"':'').' class="pdg" style="width: 75px; padding-right: 0;">'.$user->getimg($val['id_id'].$val['u_picid'].$val['u_picd'], $val['u_picvalid'], 0).'</td>
		<td class="pdg" style="height: 87px;"><div class="rgtf">'.nicedate($val['c_date']).'</div><span class="up">'.$user->getstring($val).'</span>'.(($val['private_id'])?' <span class="off">[privat inlägg]</span>':'').'
		<div style="width: 565px; overflow: hidden;">'.(($val['private_id'] == '1' && !$msg_own)?'<em>Privat inlägg.</em>':(($val['c_html'])?safeOUT($val['c_msg']):secureOUT($val['c_msg']))).'</div>
		</td>
		</tr><tr><td class="btm pdg rgt">'.(($msg_own)?'<a target="commain" href="'.l('user', 'blog', $s['id_id'], $res['main_id']).'&p='.$c_paging['p'].'&del_msg='.$val['main_id'].'" onclick="return confirm(\'Säker ?\')"><img src="./_img/icon_del.gif" style="margin-bottom: -2px;" title="Radera" /></a>':'').($own && $l['id_id'] != $val['id_id']?'&nbsp;&nbsp;<input type="button" class="b" value="svara" onclick="ref = window.open(\'user_blog_comment.php?id='.$res['main_id'].'&a='.$val['main_id'].'\', \'\', \'left=\'+((screen.availWidth - 476)/2)+\',top=\'+((screen.availHeight - 310)/2)+\', resizable=0, status=no, width=476, height=310\'); ref.focus();" />':'').'</td>
		</tr>
		</table>
';*/
	} } else { echo '<table cellspacing="0" width="100%"><tr><td class="cnt pdg spac">Inga kommentarer.</td></tr></table>'; }
?>
		</div>
	</div>
<?
	require(DESIGN.'foot_user.php');
	require(DESIGN.'foot.php');
?>