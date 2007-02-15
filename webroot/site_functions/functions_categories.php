<?
	/*
		categories. WIP

		gör om dessa tabeller till att använda tblCategories:
		tblAccessgroups
		tblEstoreCategories / tblEstoreCategoryDesc
		tblMessageFolders
		tblQuicklistGroups
		tblFileCategories
		tblTimedSubscriptionCategories
		tblTodoListCategories
	*/
	

	define('CATEGORY_BLOGS',					10);		//blog categories. uses globalCategory
	define('CATEGORY_LANGUAGES',			11);		//languages categories. used by "lang" project
	define('CATEGORY_FAVORITEGAMES',	40);		//ignores globalCategory

	function addCategory(&$db, $categoryType, $categoryName, $globalCategory = false)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($categoryType) || !is_bool($globalCategory)) return false;

		$categoryName = dbAddSlashes($db, $categoryName);
		if (!$categoryName) return false;

		$sql = 'INSERT INTO tblCategories SET categoryType='.$categoryType.',categoryName="'.$categoryName.'",timeCreated=NOW(),creatorId='.$_SESSION['userId'];
		if ($_SESSION['isAdmin'] && $globalCategory) $sql .= ',globalCategory=1';

		dbQuery($db, $sql);
		return $db['insert_id'];
	}
	
	function removeCategory(&$db, $categoryType, $categoryId)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($categoryType) || !is_numeric($categoryId)) return false;
		
		$sql = 'DELETE FROM tblCategories WHERE categoryType='.$categoryType.' AND categoryId='.$categoryId;
		dbQuery($db, $sql);

		return true;
	}

	function getCategory(&$db, $categoryType, $categoryId)
	{
		if (!is_numeric($categoryType) || !is_numeric($categoryId)) return false;
		
		$sql  = 'SELECT * FROM tblCategories WHERE categoryType='.$categoryType.' AND categoryId='.$categoryId;

		return dbOneResult($db, $sql);
	}

	function getCategories(&$db, $categoryType)
	{
		if (!is_numeric($categoryType)) return false;
		
		$sql  = 'SELECT * FROM tblCategories WHERE categoryType='.$categoryType.' ';
		$sql .= 'ORDER BY categoryName ASC';

		return dbArray($db, $sql);
	}

	/* Returnerar alla kategorier, först systemkategorier, sen användarens egna kategorier */
	function getGlobalAndUserCategories(&$db, $categoryType)
	{
		if (!is_numeric($categoryType)) return false;

		$sql  = 'SELECT * FROM tblCategories ';
		$sql .= 'WHERE categoryType='.$categoryType.' ';
		if ($_SESSION['loggedIn']) {
			$sql .= 'AND (globalCategory=1 OR creatorId='.$_SESSION['userId'].') ';
		} else {
			$sql .= 'AND globalCategory=1 ';
		}
		$sql .= 'ORDER BY globalCategory DESC,categoryName ASC';
		
		return dbArray($db, $sql);
	}
	
	/* helper function */
	function getCategoriesHTML_Options(&$db, $categoryType, $selectedId = 0)
	{
		global $config;

		if (!is_numeric($categoryType)) return false;

		$content = '<option value="0">&nbsp;</option>';
		$list = getGlobalAndUserCategories($db, $categoryType);
		$shown_global_grop = 0;

		for ($i=0; $i<count($list); $i++) {
			if (!$shown_global_grop && $list[$i]['globalCategory']) {
				$content .= '<optgroup label="'.$config['text']['global_categories'].'">';
				$shown_global_grop = 1;
			}
			if ($shown_global_grop && !$list[$i]['globalCategory']) {
				$content .= '</optgroup>';
				$content .= '<optgroup label="'.$config['text']['your_categories'].'">';
				$shown_global_grop = 0;
			}
			$content .= '<option value="'.$list[$i]['categoryId'].'"';
			if ($selectedId == $list[$i]['categoryId']) $content .= ' selected';
			$content .= '>'.$list[$i]['categoryName'].'</option>';
		}
		if ($shown_global_grop) $content .= '</optgroup>';

		return $content;
	}
?>