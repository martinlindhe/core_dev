<?
	require_once('config.php');

	require('design_head.php');

	wiki('Recent');

	echo '<br/>';

	$list = getAdblockLatestAdditions(10);

	for ($i=0; $i<count($list); $i++) {
		$mode = 'added';
		if ($list[$i]['editorId']) $mode = 'edited';
		if ($list[$i]['deletedBy']) $mode = 'deleted';

		switch ($mode) {
			case 'added':
				echo 'Added at '.$list[$i]['timeCreated'].' by '.$list[$i]['userName'];
				break;

			case 'edited':
				echo 'Edited at '.$list[$i]['timeEdited'].' by '.$db->getUserName($list[$i]['editorId']);
				break;

			case 'deleted':
				echo 'Deleted at '.$list[$i]['timeDeleted'].' by '.$db->getUserName($list[$i]['deletedBy']);
				break;
		}

		echo ':<br/>';
		echo '<a href="editrule.php?id='.$list[$i]['ruleId'].'">'.$list[$i]['ruleText'].'</a><br/><br/>';
	}

	require('design_foot.php');
?>