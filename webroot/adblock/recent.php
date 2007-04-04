<?
	include_once('include_all.php');

	include('design_head.php');

	echo getInfoField($db, 'page_recent changes').'<br/>';

	$list = getAdblockLatestAdditions($db, 10);

	for ($i=0; $i<count($list); $i++) {
		$mode = 'added';
		if ($list[$i]['editorId']) $mode = 'edited';
		if ($list[$i]['deletedBy']) $mode = 'deleted';

		switch ($mode) {
			case 'added':
				echo 'Added at '.$list[$i]['timeCreated'].' by '.$list[$i]['userName'];
				break;

			case 'edited':
				echo 'Edited at '.$list[$i]['timeEdited'].' by '.getUserName($db, $list[$i]['editorId']);
				break;

			case 'deleted':
				echo 'Deleted at '.$list[$i]['timeDeleted'].' by '.getUserName($db, $list[$i]['deletedBy']);
				break;
		}

		echo ':<br/>';
		echo '<a href="editrule.php?id='.$list[$i]['ruleId'].'">'.$list[$i]['ruleText'].'</a><br/><br/>';
	}

	include('design_foot.php');
?>