<?
	$o = array();
	$o[0] = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE status_id = '1' AND account_date > '".$user->timeout(UO)."'");
	$o[1] = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE status_id = '1' AND u_sex = 'M' AND account_date > '".$user->timeout(UO)."'");
	$o[2] = $sql->queryResult("SELECT COUNT(*) as count FROM s_user WHERE status_id = '1' AND u_sex = 'F' AND account_date > '".$user->timeout(UO)."'");
	$sql->queryUpdate("UPDATE s_text SET text_cmt = '".implode(':', $o)."' WHERE main_id = 'stat_online' LIMIT 1");
	exit;
?> 