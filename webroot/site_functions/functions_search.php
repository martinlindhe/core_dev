<?
	/*
		functions_search.php - Funktioner för sökning i forum och av användare
	*/

	/* $list är en array med ord att söka på */
	function getForumSearchQuery($list)
	{
		$sql = '';
		for ($i=0; $i<count($list); $i++) {

			$curr = $list[$i];
			if (substr($curr,0,1) == '+') {
				//kräv detta

				$curr = substr($curr,1);
				if ($i>0) {
					$sql .= 'AND ';
				}
				$sql .= '(t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';

			} else if (substr($curr,0,1) == '-') {
				//INTE detta

				if (count($list)==1) { //tillåt inte sökning på allt UTAN ett ord..
					return;
				}

				$curr = substr($curr,1);
				if ($i>0) {
					$sql .= 'AND ';
				}
				$sql .= 'NOT (t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';

			} else {
				//frivilligt (typ detta ELLER nåt annat)

				if ($i>0) {
					$sql .= 'OR ';
				}
				$sql .= '(t1.itemSubject LIKE "%'.$curr.'%" OR t1.itemBody LIKE "%'.$curr.'%") ';
			}
		}

		$sql .= 'AND t1.itemDeleted=0 ';
		return $sql;
	}

	/* Returns a list of search results with forum items */
	function getForumSearchResults(&$db, $criteria, $method, $page, $limit)
	{
		$criteria = dbAddSlashes($db, $criteria);
		if (!is_numeric($page) || !is_numeric($limit)) return false;

		if (!$criteria || !$method || !$page || !$limit) {
			return false;
		}

		$list = explode(' ', $criteria);

		$sql  = 'SELECT t1.*,t2.userName AS authorName FROM tblForums AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.authorId=t2.userId) ';
		$sql .= 'WHERE ';

		$sql .= getForumSearchQuery($list);

		switch ($method) {
			case 'mostread': //mest läst
				$sql .= 'ORDER BY t1.itemRead DESC '; break;

			case 'oldfirst': //älst först
				$sql .= 'ORDER BY t1.timestamp ASC '; break;

			case 'newfirst': default: //nyast först, default
				$sql .= 'ORDER BY t1.timestamp DESC '; break;
		}


		$sql .= 'LIMIT '.(($page-1) * $limit).','.$limit;

		return dbArray($db, $sql);
	}

	function getForumSearchResultsCount(&$db, $criteria)
	{
		$criteria = dbAddSlashes($db, $criteria);

		if (!$criteria) {
			return false;
		}

		$list = explode(' ', $criteria);

		$sql  = 'SELECT COUNT(t1.itemId) FROM tblForums AS t1 ';
		$sql .= 'WHERE ';

		$sql .= getForumSearchQuery($list);

		return dbOneResultItem($db, $sql);
	}


?>