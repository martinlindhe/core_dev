<?
	/*
		Generic set of functions & db-table to generate revisioned backups of other field data for other modules
		
		By Martin Lindhe, 2007
	*/
	
	//revision types:
	define('REVISIONS_WIKI', 1);
	
	//revision categories:
	define('REV_WIKI_TEXT_CHANGED', 1);
	define('REV_WIKI_FILE_UPLOADED', 2);
	define('REV_WIKI_FILE_DELETED', 3);
	define('REV_WIKI_LOCKED', 4);
	define('REV_WIKI_UNLOCKED', 5);
	
	function getRevisions($fieldType, $fieldId)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($fieldId)) return false;
		
		$sql  = 'SELECT t1.*,t2.userName AS creatorName FROM tblRevisions AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.createdBy=t2.userId) ';
		$sql .= 'WHERE t1.fieldId='.$fieldId.' AND t1.fieldType='.$fieldType;
		$sql .= ' ORDER BY t1.timeCreated DESC';
		
		return $db->getArray($sql);
	}

	//kanske kunna minska ner antalet parametrar på nåt sätt?
	function addRevision($fieldType, $fieldId, $fieldText, $timestamp, $creatorId, $categoryId = 0)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($fieldId) || !is_numeric($creatorId) || !is_numeric($categoryId)) return false;
		
		$timestamp = $db->escape($timestamp);		//todo: validate timestamp bättre
		$fieldText = $db->escape($fieldText);

		$q = 'INSERT INTO tblRevisions SET fieldId='.$fieldId.',fieldType='.$fieldType.',fieldText="'.$fieldText.'",createdBy='.$creatorId.',timeCreated="'.$timestamp.'",categoryId='.$categoryId;
		$db->query($q);
	}
	
	function showRevisions($articleType, $articleId, $articleName)
	{
		echo 'History of article '.$articleName.'<br/><br/>';

		$list = getRevisions($articleType, $articleId);
		if ($list)
		{
			echo '<br/>Archived versions ('.count($list).' entries):<br/>';
			for ($i=0; $i<count($list); $i++)
			{
				echo '<br/>#'.($i+1).': <a href="#" onclick="return toggle_element_by_name(\'layer_history'.$i.'\')">';
				echo 'Written by '.$list[$i]['creatorName']. ' at '.$list[$i]['timeCreated'].' ('.strlen($list[$i]['fieldText']).' bytes)</a><br/>';
				echo '<div id="layer_history'.$i.'" class="revision_entry" style="display: none;">';

				echo nl2br(htmlentities($list[$i]['fieldText'], ENT_COMPAT, 'UTF-8'));

				echo '</div>';
			}
		}
		else
		{
			echo '<br/><b>There is no edit history of this wiki in the database.</b><br/>';
		}

	}


?>