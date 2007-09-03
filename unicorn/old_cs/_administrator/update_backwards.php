<?
	require("./set_onl.php");
$days = date_diff(date("Y-m-d"), '2006-05-25');
$days = $days['days'];
	for($i = $days; $i >= 0; $i--) {
		$day = date("Y-m-d", strtotime("-$i DAYS"));
		$stat = array();
		$stat['USER'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}user WHERE TO_DAYS(u_regdate) <= TO_DAYS('".$day."')");
		$stat['USERVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}uservisit WHERE TO_DAYS(visit_date) <= TO_DAYS('".$day."')");
		$stat['VISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}logvisit WHERE TO_DAYS(date_snl) <= TO_DAYS('".$day."')");
		$stat['SPY'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userspy WHERE TO_DAYS(spy_date) <= TO_DAYS('".$day."')");
		$stat['BLOG'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userblog WHERE TO_DAYS(blog_date) <= TO_DAYS('".$day."')");
		$stat['BLOGCMT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userblogcmt WHERE TO_DAYS(c_date) <= TO_DAYS('".$day."')");
		$stat['BLOGVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userblogvisit WHERE TO_DAYS(visit_date) <= TO_DAYS('".$day."')");
		$stat['BLOGSPY'] = 0;#$sql->queryResult("SELECT COUNT(*) FROM {$t}userblogspy WHERE TO_DAYS(u_regdate) <= TO_DAYS('".$day."')");
		$stat['CHAT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userchat WHERE TO_DAYS(sent_date) <= TO_DAYS('".$day."')");
		$stat['MOVIE'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}pmovie WHERE TO_DAYS(date_cnt) <= TO_DAYS('".$day."')");
		$stat['MOVIEVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}pmovievisit WHERE TO_DAYS(date_snl) <= TO_DAYS('".$day."')");
		$stat['MOVIECMT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}pmoviecmt WHERE TO_DAYS(c_date) <= TO_DAYS('".$day."')");
		$stat['FORUM'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}f WHERE TO_DAYS(sent_date) <= TO_DAYS('".$day."')");
		$stat['PHOTO'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userphoto WHERE TO_DAYS(pht_date) <= TO_DAYS('".$day."')");
		$stat['PHOTOCMT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userphotocmt WHERE TO_DAYS(c_date) <= TO_DAYS('".$day."')");
		$stat['PHOTOVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userphotovisit WHERE TO_DAYS(visit_date) <= TO_DAYS('".$day."')");
		$stat['GB'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}usergb WHERE TO_DAYS(sent_date) <= TO_DAYS('".$day."')");
		$stat['LOGIN'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}usersess WHERE TO_DAYS(sess_date) <= TO_DAYS('".$day."')");
		$stat['MAIL'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}usermail WHERE TO_DAYS(sent_date) <= TO_DAYS('".$day."')");
		$stat['CALENDAR'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}cal WHERE TO_DAYS(date_cnt) <= TO_DAYS('".$day."')");
		$stat['RELATION'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}userrel WHERE TO_DAYS(activated_date) <= TO_DAYS('".$day."')");
		$stat['THOUGHT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}thought WHERE TO_DAYS(gb_date) <= TO_DAYS('".$day."')");
		$stat['GALLERY'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}ppic WHERE TO_DAYS(p_date) <= TO_DAYS('".$day."')");
		$stat['GALLERYVIEW'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}ppicview WHERE TO_DAYS(date_snl) <= TO_DAYS('".$day."')");
		$stat['GALLERYCMT'] = $sql->queryResult("SELECT COUNT(*) FROM {$t}pcmt WHERE TO_DAYS(c_date) <= TO_DAYS('".$day."')");
		$try = $sql->queryInsert("INSERT INTO {$t}logobject SET date_cnt = '".$day."', data_s = '".serialize($stat)."'");
		if(!$try) $sql->queryUpdate("UPDATE {$t}logobject SET data_s = '".serialize($stat)."' WHERE date_cnt = '".$day."'");
	}
?>