<?
	require("./set_onl.php");
	$d = $sql->queryResult("SELECT NOW()");
	$res = $sql->query("SELECT u.id_id, u.level_enddate, u.level_pending, u.level_id, l.level_id AS search FROM {$t}user u LEFT JOIN {$t}userlevel l ON l.id_id = u.id_id WHERE u.status_id = '1'", 0, 1);
	foreach($res as $row) {
		if($row['level_pending'] && $row['level_id'] > 1 && date("Y-m-d", strtotime($d)) > $row['level_enddate']) {
			$sql->queryUpdate("UPDATE {$t}user SET level_id = '1', level_enddate = '', level_pending = '0' WHERE id_id = '".$row['id_id']."' LIMIT 1");
			#$user->spy($row['id_id'], 'MSG', 'MSG', array('Din uppgradering har tyvärr inte förlängts innan slutdatum och du har degraderats till STANDARD. Du kan alltid uppgradera igen om du vill under <b>uppgradera</b> i menyn!'));
			if($row['search'] && strpos($row['search'], 'LEVEL'.$row['level_id'])) {
				$row['search'] = str_replace('LEVEL'.$row['level_id'], 'LEVEL1', $row['search']);
				$sql->queryUpdate("UPDATE {$t}userlevel SET level_id = '{$row['search']}' WHERE id_id = '".$row['id_id']."' LIMIT 1");
			}
		}
	}
	$rtu = (!empty($_SESSION['c_i']) || isset($_GET['rtu']))?true:false;
	$sql->queryUpdate("UPDATE {$t}text SET text_cmt = NOW() WHERE main_id = 'admin_latestupdate' LIMIT 1");
	$stat = array();
	$stat['USER'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}user");
	$stat['USERVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}uservisit");
	$stat['VISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}logvisit");
	$stat['SPY'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userspy");
	$stat['BLOG'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userblog");
	$stat['BLOGCMT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userblogcmt");
	$stat['BLOGVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userblogvisit");
	$stat['BLOGSPY'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userblogspy");
	$stat['CHAT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userchat");
	$stat['MOVIE'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}pmovie");
	$stat['MOVIEVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}pmovievisit");
	$stat['MOVIECMT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}pmoviecmt");
	$stat['FORUM'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}f");
	$stat['PHOTO'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userphoto");
	$stat['PHOTOCMT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userphotocmt");
	$stat['PHOTOVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userphotovisit");
	$stat['GB'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}usergb");
	$stat['LOGIN'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}usersess");
	$stat['MAIL'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}usermail");
	$stat['CALENDAR'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}cal");
	$stat['RELATION'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userrel");
	$stat['THOUGHT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}thought");
	$stat['GALLERY'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}ppic");
	$stat['GALLERYVIEW'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}ppicview");
	$stat['GALLERYCMT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}pcmt");
	if($rtu) $yester = date("Y-m-d");
	else $yester = date("Y-m-d", strtotime("-1 DAYS"));
	$try = $sql->queryInsert("INSERT INTO {$t}logobject SET date_cnt = '$yester', data_s = '".serialize($stat)."'");
	if(!$try) $sql->queryUpdate("UPDATE {$t}logobject SET data_s = '".serialize($stat)."' WHERE date_cnt = '$yester'");
	if(isset($_GET['rtu'])) header("Location: stat_obj.php");
?>