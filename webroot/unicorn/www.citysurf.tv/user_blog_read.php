<?
	require_once('config.php');
	$user->requireLoggedIn();
	
	if (empty($_GET['id']) || !is_numeric($_GET['id']) || empty($_GET['n']) || !is_numeric($_GET['n'])) die;
	$id = $_GET['id'];	//user-id
	$n = $_GET['n'];	//blog-id

	if (amIBlocked($id)) errorACT('Användaren har blockerat dig.');

	$res = $db->getOneRow("SELECT main_id, status_id, user_id, blog_title, blog_date, blog_cmt, hidden_id, blog_visit, blog_cmts FROM s_userblog WHERE main_id = '".$db->escape($n)."' LIMIT 1");
	if (empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1') {
		die('Felaktigt inlägg.');
	}

	$own = ($res['user_id'] == $user->id) ? true : false;

	if(!empty($_GET['del_msg']) && is_numeric($_GET['del_msg'])) {
		$r = $db->getOneRow('SELECT main_id, status_id, user_id, id_id FROM s_userblogcmt WHERE main_id = '.$_GET['del_msg'].' LIMIT 1');
		if(!empty($r) && count($r) && $r['status_id'] == '1') {
			if ($user->isAdmin || $r['user_id'] == $user->id || $r['id_id'] == $user->id) {
				$re = $db->update('UPDATE s_userblogcmt SET status_id = "2" WHERE main_id = '.$r['main_id'].' LIMIT 1');
			}
			if($re) {
				$db->update('UPDATE s_userblog SET blog_cmts = blog_cmts - 1 WHERE main_id = '.$res['main_id'].' LIMIT 1');
			}
			reloadACT('user_blog_read.php?id='.$res['user_id'].'&n='.$res['main_id']);
		}
	}
	if (!$own) {
		$hidden = $user->getinfo($user->id, 'hidden_bview');
		if($user->isAdmin && $res['hidden_id']) {
			$beenhere = true;
		} else {
			if ($res['hidden_id']) die;
			if(!$hidden) {
				$visit = $db->replace("REPLACE INTO s_userblogvisit SET status_id = '1', visit_date = NOW(), visitor_id = '".$user->id."', blog_id = '".$res['main_id']."'");
				$beenhere = ($visit != '2') ? false : true;
			} else {
				$visit = $db>replace("REPLACE INTO s_userblogvisit SET status_id = '2', visit_date = NOW(), visitor_id = '".$user->id."', blog_id = '".$res['main_id']."'");
				$beenhere = ($visit != '2') ? false : true;
			}
		}
		if (!$beenhere) {
			$db->update("UPDATE s_userblog SET blog_visit = blog_visit + 1 WHERE main_id = '".$res['main_id']."' LIMIT 1");
			if(!$hidden) {
				$db->update("UPDATE s_userblogvisit SET status_id = '1', visit_date = NOW() WHERE visitor_id = '".$user->id."' AND blog_id = '".$res['main_id']."' LIMIT 1");
			} else {
				$db->update("UPDATE s_userblogvisit SET status_id = '2', visit_date = NOW() WHERE visitor_id = '".$user->id."' AND blog_id = '".$res['main_id']."' LIMIT 1");
			}
		}
	}

	$action = 'blog';
	require(DESIGN.'head_user.php');
?>

<div class="subHead">blogg</div><br class="clr"/>

<?
	//makeButton(false,	'document.location=\''.l('user', 'blog', $s['id_id']).'\'',	'icon_blog.png',	'tillbaka');
	makeButton(false, 'makeBlogComment('.$s['id_id'].','.$res['main_id'].')', 'icon_mail_new.png', 'skriv kommentar');

	echo '<br/><br/><br/>';
	
	$title = $res['blog_title'] ? secureOUT($res['blog_title']) : '<i>ingen rubrik</i>';
?>

<div class="bigHeader"><?=$title?> - publicerad: <?=nicedate($res['blog_date'])?></div>
<div class="bigBody">
	<div style="width: 586px; overflow: hidden; margin: 0; padding: 0;" class="pdg">
		<?=formatText($res['blog_cmt'])?>
	</div>
</div><br/>

<div class="bigHeader">kommentarer</div>
<div class="bigBody">
<?
	$c_paging = paging(@$_GET['p'], 20);
	$c_paging['co'] = $db->getOneItem('SELECT COUNT(*) FROM s_userblogcmt WHERE blog_id = '.$res['main_id'].' AND status_id = "1"');

	$odd = 1;
	$cmt = $db->getArray("SELECT b.main_id, b.c_msg, b.c_date, b.c_html, b.private_id, u.id_id, u.u_alias, u.u_sex, u.level_id, u.u_birth, u.u_picd, u.u_picid, u.u_picvalid, u.account_date FROM s_userblogcmt b LEFT JOIN s_user u ON u.id_id = b.id_id AND u.status_id = '1' WHERE b.blog_id = '".$res['main_id']."' AND b.status_id = '1' ORDER BY b.main_id DESC LIMIT {$c_paging['slimit']}, {$c_paging['limit']}");
	if(count($cmt) && !empty($cmt)) {
		foreach($cmt as $val) {
			if ($val['private_id'] && (!$own && !$isAdmin)) continue;
			$msg_own = ($val['id_id'] == $user->id || $own || $isAdmin)?true:false;
			$odd = !$odd;
			echo
				'<table summary="" cellspacing="0" style="width: 100%;'.($odd?'':' background: #ecf1ea;').'">
				<tr><td class="pdg" style="width: 55px;" rowspan="2">'.$user->getimg($val['id_id']).'</td><td class="pdg"><h5 class="l">'.$user->getstring($val, '', array('noimg' => 1)).' - '.nicedate($val['c_date']).($val['private_id']?' <b>[privat inlägg]</b>':'').'</h5><div class="r"></div><br class="clr" />
				'.secureOUT($val['c_msg']).'
				</td>';
				if ($msg_own) {
					echo '<td class="pdg" width="66"><br/>';
					makeButton(false, 'if(confirm(\'Säker ?\')) goLoc(\'user_blog_read.php?id='.$res['user_id'].'&n='.$res['main_id'].'&del_msg='.$val['main_id'].'\');', 'icon_delete.png', 'radera');
					echo '</td>';
				}
				echo '</tr>';
			echo '</table>';
		}
	} else {
		echo '<table summary="" cellspacing="0" width="100%"><tr><td class="cnt pdg spac">Inga kommentarer.</td></tr></table>';
	}
?>
</div>
<?
	require(DESIGN.'foot_user.php');
?>
