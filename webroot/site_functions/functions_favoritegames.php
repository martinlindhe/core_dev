<?
	//favorite games - modul för ESP telenor community

	function setFavoriteGame(&$db, $gameId)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($gameId)) return false;
		
		$sql = 'SELECT indexId FROM tblFavoriteGames WHERE gameId='.$gameId.' AND userId='.$_SESSION['userId'];
		$id = dbOneResultItem($db, $sql);
		if ($id) return true;

		$sql = 'INSERT INTO tblFavoriteGames SET gameId='.$gameId.',userId='.$_SESSION['userId'];
		dbQuery($db, $sql);
	}

	function clearFavoriteGame(&$db, $gameId)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($gameId)) return false;
		
		$sql = 'DELETE FROM tblFavoriteGames WHERE gameId='.$gameId.' AND userId='.$_SESSION['userId'];
		dbQuery($db, $sql);
	}
	
	//fixme: eh kom på ett bättre namn på funktionen:
	function getFavoriteGameCategoriesWithMyOptions(&$db)
	{
		if (!$_SESSION['loggedIn']) return false;

		//fungerar som getCategories($db, CATEGORY_FAVORITEGAMES) fast joinar in mina val
		$sql  = 'SELECT t1.*,t2.indexId AS selected FROM tblCategories AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblFavoriteGames AS t2 ON (t1.categoryId=t2.gameId AND t2.userId='.$_SESSION['userId'].') ';
		$sql .= 'WHERE t1.categoryType='.CATEGORY_FAVORITEGAMES.' ';
		$sql .= 'GROUP BY t1.categoryId ';
		$sql .= 'ORDER BY t1.categoryName ASC';
		
		//selected = not null om användaren har valt denna kategori

		return dbArray($db, $sql);
	}
	
	function getFavoriteGames(&$db, $userId, $limit = 0)
	{
		if (!is_numeric($userId) || !is_numeric($limit)) return false;
		
		$sql  = 'SELECT t1.* FROM tblCategories AS t1 ';
		$sql .= 'INNER JOIN tblFavoriteGames AS t2 ON (t1.categoryId=t2.gameId) ';
		$sql .= 'WHERE t2.userId='.$userId.' ';
		$sql .= 'ORDER BY t1.categoryName ASC';
		if ($limit) $sql .= ' LIMIT 0,'.$limit;

		return dbArray($db, $sql);
	}

?>