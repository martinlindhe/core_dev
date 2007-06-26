<?
	die;

	include("_config/online.include.php");

	$q = 'select t1.u_email from s_user as t1 left join tblVerifyUsers as t2 on (t1.id_id=t2.user_id) where t2.verified!=1';

	$list = $sql->query($q);
	
	echo count($list).' addresser:<br/>';
	
	foreach($list as $row) {
		echo $row[0]."\n";
	}
?>
