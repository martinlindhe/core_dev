<?
	require_once('config.php');
	$user->requireLoggedIn();

	$id = $user->id;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $id = $_GET['id'];

	if (amIBlocked($id)) errorACT('Användaren har blockerat dig.');

	if(isset($_GET['comment'])){
   		include('blog_comment.php');
		exit;
	}
	
	/* bevakningar */
	if (isset($_GET['subscribe'])) {
		spyAdd($id, 'b');
	}

	if (isset($_GET['unsubscribe'])) {
		spyDelete($id, 'b');
	}

	$allowed = $user->isFriends($id);
	if(!empty($key) && is_numeric($key)) {
		include('blog_read.php');
		exit;
	}

	//delete blog
	if (!empty($_GET['d'])) {
		$res = $db->getOneRow("SELECT main_id, status_id, user_id, blog_title, blog_date, blog_cmt FROM s_userblog WHERE main_id = '".$db->escape($_GET['d'])."' LIMIT 1", 1);
		if(empty($res) || !count($res) || empty($res['status_id']) || $res['status_id'] != '1' || $res['user_id'] != $user->id) {
			errorACT('Felaktigt inlägg.', 'user_blog.php?id='.$id);
		} else {
			$db->update("UPDATE s_userblog SET status_id = '2' WHERE main_id = '".$res['main_id']."' LIMIT 1");
			$user->counterDecrease('blog', $user->id);
			reloadACT('user_blog.php?id='.$id);
		}
	}
	$paging = paging(@$_GET['p'], 10);
	$q = "SELECT main_id, blog_title, blog_cmts, blog_date, hidden_id, blog_visit FROM s_userblog WHERE user_id = ".$id." AND status_id = '1' ORDER BY main_id DESC LIMIT ".$paging['slimit'].", ".$paging['limit'];
	$res = $db->getArray($q);
	$paging['co'] = $db->getOneItem("SELECT COUNT(*) FROM s_userblog WHERE user_id = ".$id." AND status_id = '1'");

	$action = 'blog';
	require(DESIGN.'head_user.php');
?>
<div class="subHead">blogg</div><br class="clr"/>
<?
			if ($user->id == $id) {
				makeButton(false,	'makeBlog()',	'icon_blog.png',	'skriv nytt');
			} else {
				if (spyActive($id, 'b')) {
					makeButton(false, 'goLoc(\'user_blog.php?id='.$id.'&amp;unsubscribe\')', 'icon_settings.png', 'sluta spana');
				} else {
					makeButton(false, 'goLoc(\'user_blog.php?id='.$id.'&amp;subscribe\')', 'icon_settings.png', 'spana');
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
					<td onclick="'.$url.'" class="cur pdg spac"><div style="width: 100%; height: 16px;">
					<a name="R'.$row['main_id'].'" href="user_blog_read.php?id='.$s['id_id'].'&amp;n='.$row['main_id'].'" class="bld">'.$title.' </a>'.(($row['hidden_id'])?'[privat]':'').'&nbsp;</div></td>
					<td onclick="'.$url.'" class="cur pdg spac">'.$row['blog_cmts'].' kommentarer</td>
					<td onclick="'.$url.'" class="cur pdg spac">'.$row['blog_visit'].' läsare</td>
					<td onclick="'.$url.'" class="cur pdg spac rgt nobr">'.nicedate($row['blog_date']).'</td>';
				if ($user->id == $id) {
					echo '<td width="150">';
					makeButton(false, 'makeBlog(\''.$row['main_id'].'\')', 'icon_blog.png', 'ändra');
					makeButton(false, 'if(confirm(\'Säker ?\')) goLoc(\'user_blog.php?id='.$s['id_id'].'&d='.$row['main_id'].'\')', 'icon_delete.png', 'radera');
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
