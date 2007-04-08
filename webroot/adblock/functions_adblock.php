<?

	function addAdblockRule($ruleText, $ruleType, $sampleUrl)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($userId) || !is_numeric($ruleType)) return false;

		$ruleText = $db->escape(strip_tags(trim($ruleText)));
		$sampleUrl = $db->escape(strip_tags(trim($sampleUrl)));
		
		$exists = $db->getOneItem('SELECT COUNT(ruleId) FROM tblAdblockRules WHERE deletedBy=0 AND ruleText="'.$ruleText.'"');
		if ($exists) {
			return 'This rule already exists!';
		}	

		$db->query('INSERT INTO tblAdblockRules SET ruleText="'.$ruleText.'",sampleUrl="'.$sampleUrl.'",ruleType='.$ruleType.',creatorId='.$session->id.',timeCreated=NOW()' );

		return $db->insert_id;
	}

	function updateAdblockRule($ruleId, $ruleText, $ruleType, $sampleUrl)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($ruleId) || !is_numeric($ruleType)) return false;
		
		$ruleText = $db->escape(strip_tags(trim($ruleText)));
		$sampleUrl = $db->escape(strip_tags(trim($sampleUrl)));

		$db->query('UPDATE tblAdblockRules SET ruleText="'.$ruleText.'",sampleUrl="'.$sampleUrl.'",ruleType='.$ruleType.',editorId='.$session->id.',timeEdited=NOW() WHERE ruleId='.$ruleId);
	}

	function removeAdblockRule($ruleId)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($ruleId)) return false;
		
		$db->query('UPDATE tblAdblockRules SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE ruleId='.$ruleId);
		$db->query('UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE ownerId='.$ruleId.' AND commentType='.COMMENT_ADBLOCKRULE);
	}
	
	/* Return a row of data about $ruleId */
	function getAdblockRule($ruleId)
	{
		global $db;

		if (!is_numeric($ruleId)) return false;
		
		$sql  = 'SELECT t1.*,t2.userName AS creatorName,t3.userName AS editorName FROM tblAdblockRules AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
		$sql .= 'WHERE t1.ruleId='.$ruleId;
		
		return $db->getOneRow($sql);
	}
	
	/* Returns a list of rules from the db. $types looks like this: "1,2,3" */
	// no types = get full list
	function getAdblockRules($types='', $page=0, $limit=10)
	{
		global $db;

		$types_sql = '';
		if ($types) {
		
			$list = explode(',', $types);
			for ($i=0; $i<count($list); $i++) {
				if (is_numeric($list[$i])) $types_sql .= 'ruleType='.$list[$i].' OR ';
			}

			if (substr($types_sql,-4) == ' OR ') $types_sql = substr($types_sql,0,-4);
			$types_sql = trim($types_sql);
		}
		
		$limit_sql = '';
		if (is_numeric($page) && $page && $limit) {
			$index = ($page-1)*$limit;
			$limit_sql = ' LIMIT '.$index.','.$limit;
		}
		

		if ($types_sql) {
			$sql = 'SELECT * FROM tblAdblockRules WHERE deletedBy=0 AND ('.$types_sql.') ORDER BY ruleText ASC'.$limit_sql;
		} else {
			$sql = 'SELECT * FROM tblAdblockRules WHERE deletedBy=0 ORDER BY ruleText ASC'.$limit_sql;
		}
		
		return $db->getArray($sql);
	}
	
	/* Returns the total number of rules in database */
	function getAdblockRulesCount()
	{
		global $db;

		return $db->getOneItem('SELECT COUNT(ruleId) FROM tblAdblockRules WHERE deletedBy=0');
	}

	function getAdblockAllRulesCount()
	{
		global $db;

		/* Returns array of count by type */
		$sql  = 'SELECT ruleType, COUNT(ruleId) AS cnt FROM tblAdblockRules WHERE deletedBy=0 ';
		$sql .= 'GROUP BY ruleType';
		
		$list = $db->getArray($sql);

		$data['total'] = 0;

		for ($i=0; $i<count($list); $i++) {
			switch($list[$i]['ruleType']) {
				case '0': $data['unsorted'] = $list[$i]['cnt']; break;
				case '1': $data['ads'] = $list[$i]['cnt']; break;
				case '2': $data['trackers'] = $list[$i]['cnt']; break;
				case '3': $data['counters'] = $list[$i]['cnt']; break;
			}
			$data['total'] += $list[$i]['cnt'];
		}

		return $data;
	}
	
	
	/* Return the number of rules added in database the last X days */
	function getAdblockNewRuleCount($days)
	{
		global $db;

		if (!is_numeric($days)) return false;
		
		$sql = 'SELECT COUNT(ruleId) FROM tblAdblockRules WHERE deletedBy=0 AND timeCreated>'. (time()-($days*24*3600));
		return $db->getOneItem($sql);
	}
	
	/* Returns a list of rules from the db. $types looks like this: "1,2,3" */
	// no types = get full list
	function searchAdblockRules($searchword, $types='', $page=0, $limit=10, $sortByTime=false)
	{
		global $db;

		$searchword = $db->escape(strip_tags($searchword));

		$types_sql = '';
		if ($types) {
		
			$list = explode(',', $types);
			for ($i=0; $i<count($list); $i++) {
				if (is_numeric($list[$i])) $types_sql .= 'ruleType='.$list[$i].' OR ';
			}

			if (substr($types_sql,-4) == ' OR ') $types_sql = substr($types_sql,0,-4);
			$types_sql = trim($types_sql);
		}

		$limit_sql = '';
		if (is_numeric($page) && $page && $limit) {
			$index = ($page-1)*$limit;
			$limit_sql = ' LIMIT '.$index.','.$limit;
		}

		if ($types_sql) {
			$sql = 'SELECT * FROM tblAdblockRules WHERE ruleText LIKE "%'.$searchword.'%" AND deletedBy=0 AND ('.$types_sql.')';
		} else {		
			$sql = 'SELECT * FROM tblAdblockRules WHERE ruleText LIKE "%'.$searchword.'%" AND deletedBy=0';
		}
		
		if ($sortByTime) {
			$sql .= ' ORDER BY timeCreated DESC'.$limit_sql;		//returnerar senaste regeln först
		} else {
			$sql .= ' ORDER BY ruleText ASC'.$limit_sql;		//returnerar alfabetiskt, a-z
		}

		return $db->getArray($sql);
	}

	function searchAdblockRuleCount($searchword)
	{
		global $db;

		$searchword = $db->escape(strip_tags($searchword));
		$sql = 'SELECT COUNT(ruleId) FROM tblAdblockRules WHERE ruleText LIKE "%'.$searchword.'%" AND deletedBy=0';
		
		return $db-getOneItem($sql);
	}

	
	

	//type being 1=site has ads, 2=site is broken by blocking rules
	function addProblemSite($url, $type, $comment)
	{
		global $db, $session;

		if (!is_numeric($type)) return false;

		$url = $db->escape(trim($url));
		$comment = $db->escape(trim($comment));

		$db->query('INSERT INTO tblProblemSites SET url="'.$url.'",type='.$type.',comment="'.$comment.'",userId='.$session->id.',userIP='.IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']).',timeCreated=NOW()');

		return $db->insert_id;
	}
	
	function removeProblemSite($siteId)
	{
		global $db, $session;

		if (!$session->id || !is_numeric($siteId)) return false;
		
		$db->query('UPDATE tblProblemSites SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE siteId='.$siteId);
	}
	
	/* Return list of problem sites, oldest first */
	function getProblemSites()
	{
		global $db;

		$sql  = 'SELECT t1.*,t2.userName,t3.ci ';
		$sql .= 'FROM tblProblemSites AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'LEFT OUTER JOIN dbGeoIP.tblGeoIP AS t3 ON (t1.userIP BETWEEN t3.start AND t3.end) ';
		$sql .= 'WHERE t1.deletedBy=0 ';
		$sql .= 'ORDER BY t1.timeCreated ASC';

		return $db->getArray($sql);
	}
	
	/* Return number of items in problem site list */
	function getProblemSiteCount()
	{
		global $db;

		return $db->getOneItem('SELECT COUNT(siteId) FROM tblProblemSites WHERE deletedBy=0');
	}

	/* Returns a list of the last $cnt additions */
	function getAdblockLatestAdditions($cnt)
	{
		global $db;

		if (!is_numeric($cnt)) return false;
		
		$sql  = 'SELECT t1.*,t2.userName FROM tblAdblockRules AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
		$sql .= 'ORDER BY t1.timeCreated DESC LIMIT 0,'.$cnt;
		
		return $db->getArray($sql);
	}

?>