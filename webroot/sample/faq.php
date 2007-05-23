<?
	require_once('config.php');

	require('design_head.php');

	echo 'Frequently asked Questions<br/><br/>';

	$list = getFAQ();
	foreach ($list as $row) {
		echo '<b>Q: '.$row['question'].'</b><br/>';
		echo '<b>A:</b><br/>';
		echo $row['answer'];
		echo '<hr/>';
	}

	require('design_foot.php');
?>