<?
	$paging = paging(1, 10);
	$visit = $sql->query("SELECT v.visit_date, u.id_id, u.u_alias, u.u_sex, u.level_id, u.u_birth, u.u_picid, u.u_picd, u.account_date FROM s_uservisit v RIGHT JOIN s_user u ON u.id_id = v.visitor_id AND u.status_id = '1' WHERE v.user_id = '".$s['id_id']."' AND v.status_id = '1' ORDER BY v.main_id DESC LIMIT {$paging['slimit']}, {$paging['limit']}", 0, 1);
	$paging['co'] = count($visit);
	require(DESIGN.'head.php');
echo $user->getimg($s['id_id']);
echo $user->getstring($s);
echo '<h1>Visit</h1>';
echo '<a href="'.l('user', 'view', $s['id_id']).'">pres</a>';
echo '<a href="'.l('user', 'gb', $s['id_id']).'">gb</a>';
echo '<a href="'.l('user', 'blog', $s['id_id']).'">blog</a>';
echo '<a href="'.l('user', 'visit', $s['id_id']).'">visit</a>';
echo '<a href="'.l('user', 'gallery', $s['id_id']).'">gallery</a>';
echo '<a href="'.l('user', 'relations', $s['id_id']).'">relation</a>';
	require(DESIGN.'foot.php');
?>
