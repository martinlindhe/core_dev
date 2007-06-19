<?
	if(isset($_GET['write'])) {
		include('blog_write.php');
		exit;
	}
	if(isset($_GET['comment'])){
   		include('blog_comment.php');
		exit;
	}
	
	/* bevakningar */
	if ($s['id_id'] && isset($_GET['subscribe'])) {
		spyAdd($s['id_id'], 'b');
	}

	if ($s['id_id'] && isset($_GET['unsubscribe'])) {
		spyDelete($s['id_id'], 'b');
	}

	$allowed = $user->isFriends($s['id_id']);
	if(!empty($key) && is_numeric($key)) {
		include('blog_read.php');
		exit;
	}
	if(!empty($_GET['d'])) {
		$res = $sql->queryLine("SELECT main_id, status_id, user_id, blog_title, blog_date, blog_cmt FROM s_userblog WHERE main_id = '".secureINS($_GET['d'])."' LIMIT 1", 1);
		if(empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1' || $res['user_id'] != $l['id_id']) {
			errorACT('Felaktigt inlägg.', l('user', 'blog', $s['id_id']));
		} else {
			$sql->queryUpdate("UPDATE s_userblog SET status_id = '2' WHERE main_id = '".$res['main_id']."' LIMIT 1");
			$user->counterDecrease('blog', $l['id_id']);
			reloadACT(l('user', 'blog', $s['id_id']));
		}
	}
	$paging = paging(@$_GET['p'], 10);
	$res = $db->getArray('SELECT main_id, blog_title, blog_cmts, blog_date, hidden_id, blog_visit FROM s_userblog WHERE user_id = '.$s['id_id'].'" AND status_id = "1" ORDER BY main_id DESC LIMIT '.$paging['slimit'].', '.$paging['limit']);
	$paging['co'] = $db->getOneItem('SELECT COUNT(*) as count FROM s_userblog WHERE user_id = '.$s['id_id'].' AND status_id = "1"');
	$page = 'blog';

	require(DESIGN.'head_user.php');
?>
<div class="subHead">blogg</div><br class="clr"/>
<?
			if ($own) {
				makeButton(false,	'makeBlog(\''.$l['id_id'].'\')',	'icon_blog.png',	'skriv nytt');
			} else {
				if (spyActive($s['id_id'], 'b')) {
					makeButton(false, 'goLoc(\''.l('user', 'blog', $s['id_id']).'&unsubscribe'.'\')', 'icon_settings.png', 'sluta spana');
				} else {
					makeButton(false, 'goLoc(\''.l('user', 'blog', $s['id_id']).'&subscribe'.'\')', 'icon_settings.png', 'spana');
				}
			}
?>
<br/><br/><br/>

<div class="centerMenuBodyWhite">
<?
	if (!empty($res) && count($res)) dopaging($paging, l('user', 'blog', $s['id_id'], '0').'p=', '', 'med', STATSTR);

	echo '<table summary="" cellspacing="0" width="586">';

	if (!empty($res) && count($res)) {
		foreach ($res as $row) {
			if ($allowed && $row['hidden_id'] || !$row['hidden_id']) {
				$url = 'goLoc(\''.l('user', 'blog', $s['id_id'], $row['main_id']).'\')';
				
				$title = $row['blog_title'] ? secureOUT($row['blog_title']) : '<i>ingen rubrik</i>';
				
				echo '<tr>
					<td onclick="'.$url.'" class="cur pdg spac"><div style="width: 100%; height: 16px;"><a name="R'.$row['main_id'].'" href="'.l('user', 'blog', $s['id_id'], $row['main_id']).'" class="bld">'.$title.' </a>'.(($row['hidden_id'])?'[privat]':'').'&nbsp;</div></td>
					<td onclick="'.$url.'" class="cur pdg spac">'.$row['blog_cmts'].' kommentarer</td>
					<td onclick="'.$url.'" class="cur pdg spac">'.$row['blog_visit'].' läsare</td>
					<td onclick="'.$url.'" class="cur pdg spac rgt nobr">'.nicedate($row['blog_date']).'</td>';
				if ($own) {
					echo '<td width="150">';
					makeButton(false, 'makeBlog(\''.$s['id_id'].'\',\''.$row['main_id'].'\')', 'icon_blog.png', 'ändra');
					makeButton(false, 'if(confirm(\'Säker ?\')) goLoc(\''.l('user', 'blog', $s['id_id'], '0').'&amp;d='.$row['main_id'].'\')', 'icon_delete.png', 'radera');
					echo '</td>';
				}
				echo '</tr>';
			}
		}
	} else {
		echo '<tr><td class="cnt">Inga blogginlägg.</td></tr>';
	}
	echo '</table>';
?>
</div>
<?
	require(DESIGN.'foot_user.php');
?>