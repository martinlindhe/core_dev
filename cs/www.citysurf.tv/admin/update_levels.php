<?
	require_once('find_config.php');

	$d = $db->getOneItem('SELECT NOW()');
	$res = $db->getArray("SELECT u.id_id, u.level_enddate, u.level_pending, u.level_id, l.level_id AS search FROM s_user u LEFT JOIN s_userlevel l ON l.id_id = u.id_id WHERE u.status_id = '1'");
	foreach ($res as $row) {
		if ($row['level_pending'] && $row['level_id'] > 1 && date("Y-m-d", strtotime($d)) > $row['level_enddate']) {
			$db->update("UPDATE s_user SET level_id = '1', level_enddate = '', level_pending = '0' WHERE id_id = '".$row['id_id']."' LIMIT 1");
			if ($row['search'] && strpos($row['search'], 'LEVEL'.$row['level_id'])) {
				$row['search'] = str_replace('LEVEL'.$row['level_id'], 'LEVEL1', $row['search']);
				$db->update('UPDATE s_userlevel SET level_id = "'.$row['search'].'" WHERE id_id = '.$row['id_id'].' LIMIT 1');
			}
		}
	}
	$rtu = (!empty($_SESSION['c_i']) || isset($_GET['rtu'])) ? true : false;

	$db->update("UPDATE s_text SET text_cmt = NOW() WHERE main_id = 'admin_latestupdate' LIMIT 1");
	$stat = array();
	$stat['USER'] = $db->getOneItem("SELECT COUNT(*) FROM s_user");
	$stat['USERVISIT'] = $db->getOneItem("SELECT COUNT(*) FROM s_uservisit");
	$stat['VISIT'] = $db->getOneItem("SELECT COUNT(*) FROM s_logvisit");
	$stat['SPY'] = $db->getOneItem("SELECT COUNT(*) FROM s_userspy");
	$stat['BLOG'] = $db->getOneItem("SELECT COUNT(*) FROM s_userblog");
	$stat['BLOGCMT'] = $db->getOneItem("SELECT COUNT(*) FROM s_userblogcmt");
	$stat['BLOGVISIT'] = $db->getOneItem("SELECT COUNT(*) FROM s_userblogvisit");
	$stat['BLOGSPY'] = $db->getOneItem("SELECT COUNT(*) FROM s_userblogspy");
	$stat['CHAT'] = $db->getOneItem("SELECT COUNT(*) FROM s_userchat");
	$stat['MOVIE'] = $db->getOneItem("SELECT COUNT(*) FROM s_pmovie");
	$stat['MOVIEVISIT'] = $db->getOneItem("SELECT COUNT(*) FROM s_pmovievisit");
	$stat['MOVIECMT'] = $db->getOneItem("SELECT COUNT(*) FROM s_pmoviecmt");
	$stat['FORUM'] = $db->getOneItem("SELECT COUNT(*) FROM s_f");
	$stat['PHOTO'] = $db->getOneItem("SELECT COUNT(*) FROM s_userphoto");
	$stat['PHOTOCMT'] = $db->getOneItem("SELECT COUNT(*) FROM s_userphotocmt");
	$stat['PHOTOVISIT'] = $db->getOneItem("SELECT COUNT(*) FROM s_userphotovisit");
	$stat['GB'] = $db->getOneItem("SELECT COUNT(*) FROM s_usergb");
	$stat['LOGIN'] = $db->getOneItem("SELECT COUNT(*) FROM s_usersess");
	$stat['MAIL'] = $db->getOneItem("SELECT COUNT(*) FROM s_usermail");
	$stat['CALENDAR'] = $db->getOneItem("SELECT COUNT(*) FROM s_cal");
	$stat['RELATION'] = $db->getOneItem("SELECT COUNT(*) FROM s_userrel");
	$stat['THOUGHT'] = $db->getOneItem("SELECT COUNT(*) FROM s_thought");
	$stat['GALLERY'] = $db->getOneItem("SELECT COUNT(*) FROM s_ppic");
	$stat['GALLERYVIEW'] = $db->getOneItem("SELECT COUNT(*) FROM s_ppicview");
	$stat['GALLERYCMT'] = $db->getOneItem("SELECT COUNT(*) FROM s_pcmt");
	if ($rtu) $yester = date("Y-m-d");
	else $yester = date("Y-m-d", strtotime("-1 DAYS"));

	$try = $db->insert("INSERT INTO s_logobject SET date_cnt = '$yester', data_s = '".serialize($stat)."'");
	if (!$try) $db->update("UPDATE s_logobject SET data_s = '".serialize($stat)."' WHERE date_cnt = '$yester'");
	if (isset($_GET['rtu'])) header("Location: stat_obj.php");
?>
