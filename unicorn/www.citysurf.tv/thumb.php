<?
	//visar presentationsbild för userid "id"
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require_once('config.php');
	
	$user_id = $_GET['id'];

	$q = 'SELECT u_picid, u_picd, u_sex, u_picvalid FROM s_user WHERE id_id='.$user_id;
	$data = $db->getOneRow($q);

	if (!$data || !$data['u_picid'] || !$data['u_picvalid']) $loc = $config['web_root'].'_gfx/u_noimg'.$data['u_sex'].'_2.gif';
	else $loc = $config['web_root'].UPLA.'images/'.$data['u_picd'].'/'.$user_id.$data['u_picid'].'_2.jpg';

	header('Location: '.$loc);
?>
