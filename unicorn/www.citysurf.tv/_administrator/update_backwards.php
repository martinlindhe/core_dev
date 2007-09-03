<?
	require("./set_onl.php");
$days = date_diff(date("Y-m-d"), '2006-05-25');
$days = $days['days'];
	for($i = $days; $i >= 0; $i--) {
		$day = date("Y-m-d", strtotime("-$i DAYS"));
		$stat = array();
		$stat['USER'] = $sql->queryResult("SELECT COUNT(*) FROM s_user WHERE TO_DAYS(u_regdate) <= TO_DAYS('".$day."')");
		$stat['USERVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM s_uservisit WHERE TO_DAYS(visit_date) <= TO_DAYS('".$day."')");
		$stat['VISIT'] = $sql->queryResult("SELECT COUNT(*) FROM s_logvisit WHERE TO_DAYS(date_snl) <= TO_DAYS('".$day."')");
		$stat['SPY'] = $sql->queryResult("SELECT COUNT(*) FROM s_userspy WHERE TO_DAYS(spy_date) <= TO_DAYS('".$day."')");
		$stat['BLOG'] = $sql->queryResult("SELECT COUNT(*) FROM s_userblog WHERE TO_DAYS(blog_date) <= TO_DAYS('".$day."')");
		$stat['BLOGCMT'] = $sql->queryResult("SELECT COUNT(*) FROM s_userblogcmt WHERE TO_DAYS(c_date) <= TO_DAYS('".$day."')");
		$stat['BLOGVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM s_userblogvisit WHERE TO_DAYS(visit_date) <= TO_DAYS('".$day."')");
		$stat['BLOGSPY'] = 0;#$sql->queryResult("SELECT COUNT(*) FROM s_userblogspy WHERE TO_DAYS(u_regdate) <= TO_DAYS('".$day."')");
		$stat['CHAT'] = $sql->queryResult("SELECT COUNT(*) FROM s_userchat WHERE TO_DAYS(sent_date) <= TO_DAYS('".$day."')");
		$stat['MOVIE'] = $sql->queryResult("SELECT COUNT(*) FROM s_pmovie WHERE TO_DAYS(date_cnt) <= TO_DAYS('".$day."')");
		$stat['MOVIEVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM s_pmovievisit WHERE TO_DAYS(date_snl) <= TO_DAYS('".$day."')");
		$stat['MOVIECMT'] = $sql->queryResult("SELECT COUNT(*) FROM s_pmoviecmt WHERE TO_DAYS(c_date) <= TO_DAYS('".$day."')");
		$stat['FORUM'] = $sql->queryResult("SELECT COUNT(*) FROM s_f WHERE TO_DAYS(sent_date) <= TO_DAYS('".$day."')");
		$stat['PHOTO'] = $sql->queryResult("SELECT COUNT(*) FROM s_userphoto WHERE TO_DAYS(pht_date) <= TO_DAYS('".$day."')");
		$stat['PHOTOCMT'] = $sql->queryResult("SELECT COUNT(*) FROM s_userphotocmt WHERE TO_DAYS(c_date) <= TO_DAYS('".$day."')");
		$stat['PHOTOVISIT'] = $sql->queryResult("SELECT COUNT(*) FROM s_userphotovisit WHERE TO_DAYS(visit_date) <= TO_DAYS('".$day."')");
		$stat['GB'] = $sql->queryResult("SELECT COUNT(*) FROM s_usergb WHERE TO_DAYS(sent_date) <= TO_DAYS('".$day."')");
		$stat['LOGIN'] = $sql->queryResult("SELECT COUNT(*) FROM s_usersess WHERE TO_DAYS(sess_date) <= TO_DAYS('".$day."')");
		$stat['MAIL'] = $sql->queryResult("SELECT COUNT(*) FROM s_usermail WHERE TO_DAYS(sent_date) <= TO_DAYS('".$day."')");
		$stat['CALENDAR'] = $sql->queryResult("SELECT COUNT(*) FROM s_cal WHERE TO_DAYS(date_cnt) <= TO_DAYS('".$day."')");
		$stat['RELATION'] = $sql->queryResult("SELECT COUNT(*) FROM s_userrel WHERE TO_DAYS(activated_date) <= TO_DAYS('".$day."')");
		$stat['THOUGHT'] = $sql->queryResult("SELECT COUNT(*) FROM s_thought WHERE TO_DAYS(gb_date) <= TO_DAYS('".$day."')");
		$stat['GALLERY'] = $sql->queryResult("SELECT COUNT(*) FROM s_ppic WHERE TO_DAYS(p_date) <= TO_DAYS('".$day."')");
		$stat['GALLERYVIEW'] = $sql->queryResult("SELECT COUNT(*) FROM s_ppicview WHERE TO_DAYS(date_snl) <= TO_DAYS('".$day."')");
		$stat['GALLERYCMT'] = $sql->queryResult("SELECT COUNT(*) FROM s_pcmt WHERE TO_DAYS(c_date) <= TO_DAYS('".$day."')");
		$try = $sql->queryInsert("INSERT INTO s_logobject SET date_cnt = '".$day."', data_s = '".serialize($stat)."'");
		if(!$try) $sql->queryUpdate("UPDATE s_logobject SET data_s = '".serialize($stat)."' WHERE date_cnt = '".$day."'");
	}
?>