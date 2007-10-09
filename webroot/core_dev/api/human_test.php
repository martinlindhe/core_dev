<?
/*
	GET r: random integer value chosen by client-side javascript
	POST v: the client's response to the question we asked before
	
*/

	require_once('find_config.php');

	if (!empty($_GET['r']) && is_numeric($_GET['r'])) $_rand = $_GET['r'];

	if(!empty($_POST['v'])) {
		$_answer = $_POST['v'];

		//verify the user's input
		$type = 1;
		
		//TODO: verify that timeCreated isnt > 60 minutes
		$q = 'SELECT COUNT(entryId) FROM tblHumanTest WHERE type='.$type.' AND rnd="'.$db->escape($_rand).'" AND answer="'.$db->escape($_answer).'"';
		if ($db->getOneItem($q)) {
			echo 'passed!';
		} else {
			echo 'failed!';
		}
	} else {

		if ($_rand < 0) die('bad');

		//produce a png image

		//header('Content-type: image/png');
		$im = imagecreate(100, 20);
		$bg_col = imagecolorallocate($im, 20, 20, 20);
		$text_col = imagecolorallocate($im, 25, 255, 255);

		$type = 1;		//FIXME not used yet
		$n1 = rand(1, 9);
		$n2 = rand(1, 9);
		$question = 'what is '.$n1.' + '.$n2;
		$answer = $n1 + $n2;

		if (!is_numeric($type)) die;
		$q = 'INSERT INTO tblHumanTest SET type='.$type.',rnd="'.$_rand.'",answer="'.$db->escape($answer).'",timeCreated=NOW()';
		$db->insert($q);

		imagestring($im, 2, 0, 0, $question, $text_col);
		imagepng($im);
		imagedestroy($im);

	} 
?>