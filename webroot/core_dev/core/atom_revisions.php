<?
	/*
		atom_revisions.php - set of functions to implement revisioned backups of data, used by various modules

		By Martin Lindhe, 2007
	*/

	//revision types:
	define('REVISIONS_WIKI', 1);

	//revision categories:
	define('REV_CAT_TEXT_CHANGED', 1);
	define('REV_CAT_FILE_UPLOADED', 2);
	define('REV_CAT_FILE_DELETED', 3);
	define('REV_CAT_LOCKED', 4);
	define('REV_CAT_UNLOCKED', 5);

	//kanske kunna minska ner antalet parametrar på nåt sätt?
	function addRevision($fieldType, $fieldId, $fieldText, $timestamp, $creatorId, $categoryId = 0)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($fieldId) || !is_numeric($creatorId) || !is_numeric($categoryId)) return false;

		$timestamp = $db->escape($timestamp);		//todo: validate timestamp bättre

		$q = 'INSERT INTO tblRevisions SET fieldId='.$fieldId.',fieldType='.$fieldType.',fieldText="'.$db->escape($fieldText).'",createdBy='.$creatorId.',timeCreated="'.$timestamp.'",categoryId='.$categoryId;
		return $db->insert($q);
	}

	function showRevisions($articleType, $articleId, $articleName)
	{
		global $db;

		if (!is_numeric($articleType) || !is_numeric($articleId)) return false;

		echo 'History of article '.$articleName.'<br/><br/>';

		$q = 'SELECT COUNT(*) FROM tblRevisions WHERE fieldId='.$articleId.' AND fieldType='.$articleType;
		$tot_cnt = $db->getOneItem($q);
		$pager = makePager($tot_cnt, 5);

		$q  = 'SELECT t1.*,t2.userName AS creatorName FROM tblRevisions AS t1 ';
		$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.createdBy=t2.userId) ';
		$q .= 'WHERE t1.fieldId='.$articleId.' AND t1.fieldType='.$articleType;
		$q .= ' ORDER BY t1.timeCreated DESC'.$pager['limit'];
		$list = $db->getArray($q);

		echo $pager['head'];

		if (!$list) {
			echo '<b>There is no edit history of this wiki in the database.</b><br/>';
			return;
		}

		echo 'Archived versions ('.count($list).' entries):<br/>';
		foreach ($list as $row)
		{
			echo $row['timeCreated'].': ';
			switch ($row['categoryId'])
			{
				case REV_CAT_LOCKED:
					echo '<img src="/gfx/icon_locked.png" width="16" height="16" alt="Locked"/>';
					echo ' Locked by '.$row['creatorName'].'<br/>';
					break;

				case REV_CAT_UNLOCKED:
					echo '<img src="/gfx/icon_unlocked.png" width="16" height="16" alt="Unlocked"/>';
					echo ' Unlocked by '.$row['creatorName'].'<br/>';
					break;

				case REV_CAT_FILE_UPLOADED:
					echo ' File uploaded by '.$row['creatorName'].'<br/>';
					break;

				case REV_CAT_FILE_DELETED:
					echo ' File deleted by '.$row['creatorName'].'<br/>';
					break;

				case REV_CAT_TEXT_CHANGED:
				default:
					echo '<a href="#" onclick="return toggle_element_by_name(\'layer_history'.$row['indexId'].'\')">';
					echo 'Text edited by '.$row['creatorName']. ' ('.strlen($row['fieldText']).' bytes)</a><br/>';
					echo '<div id="layer_history'.$row['indexId'].'" class="revision_entry" style="display: none;">';
					echo nl2br(htmlentities($row['fieldText'], ENT_COMPAT, 'UTF-8'));
					echo '</div>';
					break;
			}
		}
	}
?>