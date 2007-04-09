<?
	/*
		Generic set of functions & db-table to generate revisioned backups of other field data for other modules
		
		By Martin Lindhe, 2007
	*/
	
	define('REVISIONS_WIKI', 1);
	
	function getRevisions($fieldType, $fieldId)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($fieldId)) return false;
		
		$sql  = 'SELECT t1.*,t2.userName AS editorName FROM tblRevisions AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.editedBy=t2.userId) ';
		$sql .= 'WHERE t1.fieldId='.$fieldId.' AND t1.fieldType='.$fieldType;
		$sql .= ' ORDER BY t1.timeEdited DESC';
		
		return $db->getArray($sql);
	}
/*	
	function clearInfoFieldHistory(&$db)
	{
		if (!$_SESSION['isSuperAdmin']) return false;

		$sql  = 'DELETE FROM tblInfoFieldsHistory';
		dbQuery($db, $sql);
		return true;
	}

	function getInfoFieldHistoryCountAll(&$db)
	{
		if (!$_SESSION['isSuperAdmin']) return false;

		$sql  = 'SELECT COUNT(fieldId) FROM tblInfoFieldsHistory';

		return dbOneResultItem($db, $sql);
	}
*/
?>