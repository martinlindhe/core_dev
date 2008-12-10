<?php
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('COMMENT_ADBLOCKRULE',	20);
define('FEEDBACK_ADBLOCK_ADS', 20);
define('FEEDBACK_ADBLOCK_BROKEN_RULE', 21);

$ruleset_types[0] = 'Unknown';
$ruleset_types[1] = 'Advertisment';
$ruleset_types[2] = 'Tracking';
$ruleset_types[3] = 'Counter';

define('DOWNLOAD_METHOD_WEBFORM', 'webform');
define('DOWNLOAD_METHOD_SUBSCRIPTION', 'subscription');
define('DOWNLOAD_METHOD_RSS', 'rss');		//todo...

function addAdblockRule($ruleText, $ruleType, $sampleUrl)
{
	global $db, $session;
	if (!$session->id || !is_numeric($ruleType)) return false;

	$ruleText = $db->escape(strip_tags(trim($ruleText)));
	$sampleUrl = $db->escape(strip_tags(trim($sampleUrl)));

	$exists = $db->getOneItem('SELECT COUNT(ruleId) FROM tblAdblockRules WHERE deletedBy=0 AND ruleText="'.$ruleText.'"');
	if ($exists) {
		return 'This rule already exists!';
	}

	$q = 'INSERT INTO tblAdblockRules SET ruleText="'.$ruleText.'",sampleUrl="'.$sampleUrl.'",ruleType='.$ruleType.',creatorId='.$session->id.',timeCreated=NOW()';
	return $db->insert($q);
}

function updateAdblockRule($ruleId, $ruleText, $ruleType, $sampleUrl)
{
	global $db, $session;
	if (!$session->id || !is_numeric($ruleId) || !is_numeric($ruleType)) return false;

	$ruleText = $db->escape(strip_tags(trim($ruleText)));
	$sampleUrl = $db->escape(strip_tags(trim($sampleUrl)));

	$db->update('UPDATE tblAdblockRules SET ruleText="'.$ruleText.'",sampleUrl="'.$sampleUrl.'",ruleType='.$ruleType.',editorId='.$session->id.',timeEdited=NOW() WHERE ruleId='.$ruleId);
}

function removeAdblockRule($ruleId)
{
	global $db, $session;
	if (!$session->id || !is_numeric($ruleId)) return false;

	$db->update('UPDATE tblAdblockRules SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE ruleId='.$ruleId);
	$db->update('UPDATE tblComments SET deletedBy='.$session->id.',timeDeleted=NOW() WHERE ownerId='.$ruleId.' AND commentType='.COMMENT_ADBLOCKRULE);
}

/**
 * Return a row of data about $ruleId
 */
function getAdblockRule($ruleId)
{
	global $db;
	if (!is_numeric($ruleId)) return false;

	$q  = 'SELECT t1.*,t2.userName AS creatorName,t3.userName AS editorName FROM tblAdblockRules AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'LEFT JOIN tblUsers AS t3 ON (t1.editorId=t3.userId) ';
	$q .= 'WHERE t1.ruleId='.$ruleId;
	return $db->getOneRow($q);
}

/**
 * Returns a list of rules from the db. $types looks like this: "1,2,3"
 * no types = get full list
 * used to generate text files for subscriptions
 */
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

	$q = 'SELECT ruleText FROM tblAdblockRules WHERE deletedBy=0'.($types_sql ? ' AND ('.$types_sql.')':'').' ORDER BY ruleText ASC'.$limit_sql;

	return $db->getNumArray($q);
}

/**
 * Returns the total number of rules in database
 */
function getAdblockRulesCount()
{
	global $db;
	return $db->getOneItem('SELECT COUNT(ruleId) FROM tblAdblockRules WHERE deletedBy=0');
}

function getAdblockAllRulesCount()
{
	global $db;

	$q  = 'SELECT ruleType, COUNT(ruleId) AS cnt FROM tblAdblockRules WHERE deletedBy=0 ';
	$q .= 'GROUP BY ruleType';
	$list = $db->getArray($q);

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

/**
 * Return the number of rules added in database the last X days
 */
function getAdblockNewRuleCount($days)
{
	global $db;
	if (!is_numeric($days)) return false;

	$q = 'SELECT COUNT(ruleId) FROM tblAdblockRules WHERE deletedBy=0 AND timeCreated>'. (time()-($days*24*3600));
	return $db->getOneItem($q);
}

function makeAdblockTypeSQL($types)
{
	if (!$types) return '';

	$types_sql = '';
	$list = explode(',', $types);
	for ($i=0; $i<count($list); $i++) {
		if (is_numeric($list[$i])) $types_sql .= 'ruleType='.$list[$i].' OR ';
	}

	if (substr($types_sql,-4) == ' OR ') $types_sql = substr($types_sql,0,-4);
	return trim($types_sql);
}

/**
 * Returns a list of rules from the db. $types looks like this: "1,2,3"
 * no types = get full list
 *
 * \param $_limit $pager['limit']
 */
function searchAdblockRules($searchword, $types='', $_limit = '', $sortByTime = false)
{
	global $db;
	if ($_limit != $db->escape($_limit)) return false;	//verifies that LIMIT sql dont contain escape characters

	$searchword = $db->escape(strip_tags($searchword));
	$types_sql = makeAdblockTypeSQL($types);

	$q = 'SELECT * FROM tblAdblockRules WHERE deletedBy=0';
	if ($searchword) $q .= ' AND ruleText LIKE "%'.$searchword.'%"';
	if ($types_sql) $q .= ' AND ('.$types_sql.')';

	if ($sortByTime) {
		$q .= ' ORDER BY timeCreated DESC'.$_limit;		//returns the last rule first
	} else {
		$q .= ' ORDER BY ruleText ASC'.$_limit;		//returns alphabetical, a to z
	}

	return $db->getArray($q);
}

/**
 * Used with adblock ruleset searches
 */
function searchAdblockRuleCount($searchword, $types='')
{
	global $db;

	$types_sql = makeAdblockTypeSQL($types);

	$searchword = $db->escape(strip_tags($searchword));
	$q = 'SELECT COUNT(ruleId) FROM tblAdblockRules WHERE ruleText LIKE "%'.$searchword.'%" AND deletedBy=0';
	if ($types_sql) $q .= ' AND ('.$types_sql.')';
	return $db->getOneItem($q);
}
/**
 * Returns a list of the last $cnt additions
 */
function getAdblockLatestAdditions($cnt)
{
	global $db;
	if (!is_numeric($cnt)) return false;

	$q  = 'SELECT t1.*,t2.userName FROM tblAdblockRules AS t1 ';
	$q .= 'LEFT JOIN tblUsers AS t2 ON (t1.creatorId=t2.userId) ';
	$q .= 'ORDER BY t1.timeCreated DESC LIMIT 0,'.$cnt;
	return $db->getArray($q);
}

/**
 * Handles a download request for the adblock ruleset
 * Input:
 * POST param: type_0, type_1, type_2, type_3 bool
 * POST param: type string
 * POST param:
 * GET param: type
 * \return true if download request was handled
 */
function handleAdblockDownloadRequest()
{
	global $session, $files, $config;
	$requestType = 0;

	if (isset($_POST['type_0']) || isset($_POST['type_1']) || isset($_POST['type_2']) || isset($_POST['type_3'])) {
		@$types = $_POST['type_0'].','.$_POST['type_1'].','.$_POST['type_2'].','.$_POST['type_3'];
		if ($types == ',,,') die;	//javascript blocks this from happening too
		$requestType = DOWNLOAD_METHOD_WEBFORM;
	}

	if (isset($_GET['type'])) {
		switch ($_GET['type']) {
			case 'unsorted':	$types = '0'; break;
			case 'ads':				$types = '1'; break;
			case 'trackers':	$types = '2'; break;
			case 'counters':	$types = '3'; break;
			case 'all':				$types = '0,1,2,3'; break;
			default: die;
		}
		$requestType = DOWNLOAD_METHOD_SUBSCRIPTION;
	}

	if (!$requestType) return false;

	$type_ext = '';

	switch ($types) {
		case '0': case '0,,,':	$type_ext = '-unsorted'; break;
		case '1': case ',1,,':	$type_ext = '-ads'; break;
		case '2': case ',,2,':	$type_ext = '-trackers'; break;
		case '3': case ',,,3':	$type_ext = '-counters'; break;
		case '0,1,2,3';					$type_ext = '-all'; break;
		default:								$type_ext = '-custom-'.$types; break;
	}

	$datestr	= date('Ymd');
	$hour		= date('H');

	$cache_file = $config['adblock']['cachepath'].'adblockfilters'.$type_ext.'.txt';

	if ($config['debug']) {
		$str = 'Downloaded ruleset '.$cache_file.' ('.$requestType.')';
		$session->log($str);
	}

	$lastchanged = 0;
	if (file_exists($cache_file)) $lastchanged = filemtime($cache_file);

	if ($lastchanged < time()-($config['adblock']['cacheage']))
	{
		if (!$lastchanged) $lastchanged = time();
		$list = getAdblockRules($types);

		$text = "[Adblock]\n".
				"! Adblock Plus ruleset from http://adblockrules.org\n".
				"! Please contact info@adblockrules.org in case of problems with this ruleset\n".
				"!\n".
				"! Last updated ".date("Y-m-d", $lastchanged)."\n\n";

		foreach ($list as $row => $val) {
			$text .= $val[0]."\n";
		}
		file_put_contents($cache_file, $text);
	}

	if (DOWNLOAD_METHOD_SUBSCRIPTION) {
		//Send special headers to the subscriber
		header('Filterset-timestamp: '. $lastchanged);
	}

	$files->sendTextfile($cache_file);
	return true;
}
?>
