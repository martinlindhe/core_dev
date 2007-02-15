<?
	/*
		functions_accessgroups.php - Funktioner för accessgrupper

		2002.11.29
			* Fixat upp returnkoder och optimerat lite sql
		
		2002.10.15
			* Skapad
	*/
	

	/* Predefined accessgroups */
	define('ACCESSGROUP_GUESTS',		1);
	define('ACCESSGROUP_NORMALUSERS',	2);


	/* Returns an array with all existing accessgroups and group ID's */
	function getAccessgroups(&$db)
	{
		$sql = 'SELECT * FROM tblAccessgroups ORDER BY groupName ASC';

		return dbArray($db, $sql);
	}
	

	function createAccessgroup(&$db, $groupName)
	{
		$groupName = dbAddSlashes($db, $groupName);

		if($groupName) {
			$check = dbQuery($db, 'SELECT groupId FROM tblAccessgroups WHERE groupName="'.$groupName.'"');
			if(!dbNumRows($check)) {
				$query = dbQuery($db, 'INSERT INTO tblAccessgroups SET groupName="'.$groupName.'"');
				return $db['insert_id'];
			} else {
				echo 'A accessgroup with the name "'.$groupName.'" already exists!<br>';
				return false;
			}
		} else {
			echo 'You must enter a name of the accessgroup!<br>';
			return false;
		}
	}
	
	function removeAccessgroup(&$db, $groupId)
	{
		if (!is_numeric($groupId)) return false;

		$sql = 'DELETE FROM tblAccessgroups WHERE groupId='.$groupId;
		if (dbQuery($db, $sql)) {
			return true;
		}
		return false;
	}

	function getAccessgroupName(&$db, $groupId)
	{
		if (!is_numeric($groupId)) return false;

		$sql = 'SELECT groupName FROM tblAccessgroups WHERE groupId='.$groupId;
		return dbOneResultItem($db, $sql);
	}

	function getAccessgroupId(&$db, $groupName)
	{
		$groupName = dbAddSlashes($db, $groupName);

		$sql = 'SELECT groupId FROM tblAccessgroups WHERE groupName="'.$groupName.'"';
		return dbOneResultItem($db, $sql);
	}
	
	function getAllAccessgroupFlags(&$db)
	{
		$sql = 'SELECT * FROM tblAccessgroupFlags ORDER BY flagName ASC';
		return dbArray($db, $sql);
	}
	
	/* Returns all flags for group */
	function getAccessgroupFlags(&$db, $groupId)
	{
		if (!is_numeric($groupId)) return false;
		
		$sql  = 'SELECT t1.*, t2.value FROM tblAccessgroupFlags AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblAccessgroupSettings AS t2 ON (t1.flagId = t2.flagId AND t2.groupId='.$groupId.') ';
		$sql .= 'ORDER BY t1.flagName ASC';
		return dbArray($db, $sql);
	}
	
	function setAccessgroupFlag(&$db, $groupId, $flagId, $value)
	{
		if (!is_numeric($groupId) || !is_numeric($flagId) || !is_numeric($value)) return false;

		//kan inte använda REPLACE för vi saknar index i tblAccessgroupSettings
		$check = dbQuery($db, 'SELECT * FROM tblAccessgroupSettings WHERE groupId='.$groupId.' AND flagId='.$flagId);
		if (dbNumRows($check)) {
			dbQuery($db, 'UPDATE tblAccessgroupSettings SET value='.$value.' WHERE groupId='.$groupId.' AND flagId='.$flagId);
		} else {
			dbQuery($db, 'INSERT tblAccessgroupSettings SET groupId='.$groupId.',flagId='.$flagId.',value='.$value);
		}
	}

	//returns false if user already is in that group
	function addAccessgroupMember(&$db, $groupId, $userId)
	{
		if (!is_numeric($groupId) || !is_numeric($userId)) return false;

		$check = dbQuery($db, 'SELECT * FROM tblAccessgroupMembers WHERE userId='.$userId.' AND groupId='.$groupId);
		if (dbNumRows($check)) return false;

		dbQuery($db, 'INSERT INTO tblAccessgroupMembers SET userId='.$userId.',groupId='.$groupId);
		return true;
	}
	
	function removeAccessgroupMember(&$db, $groupId, $userId)
	{
		if (!is_numeric($groupId) || !is_numeric($userId)) return false;

		$sql = 'DELETE FROM tblAccessgroupMembers WHERE userId='.$userId.' AND groupId='.$groupId;
		dbQuery($db, $sql);
	}
	
	/* Returnerar true/false */
	function isUserInAccessgroup(&$db, $userId, $groupName)
	{
		if (!is_numeric($userId)) return false;

		$groupName = dbAddSlashes($db, $groupName);

		$sql  = 'SELECT t2.userId FROM tblAccessgroups AS t1 ';
		$sql .= 'INNER JOIN tblAccessgroupMembers AS t2 ON (t1.groupId=t2.groupId AND t2.userId='.$userId.') ';
		$sql .= 'WHERE t1.groupName="'.$groupName.'"';

		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) return true;
		return false;
	}
	
	
	/* Returns an array with all accessgroups that $userId is in */
	function getUserAccessgroups(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;		

		$sql  = 'SELECT t1.*,t2.groupName AS groupName ';
		$sql .= 'FROM tblAccessgroupMembers AS t1 ';
		$sql .= 'INNER JOIN tblAccessgroups AS t2 ON (t1.groupId=t2.groupId) ';
		$sql .= 'WHERE t1.userId='.$userId.' ';
		$sql .= 'ORDER BY t2.groupName ASC';

		return dbArray($db, $sql);
	}
	
	function getAccessgroupMemberCount(&$db, $groupId)
	{
		if (!is_numeric($groupId)) return false;

		$sql = 'SELECT COUNT(DISTINCT userId) FROM tblAccessgroupMembers WHERE groupId='.$groupId;
		return dbOneResultItem($db, $sql);
	}

	function getAccessgroupMembers(&$db, $groupId)
	{
		if (!is_numeric($groupId)) return false;

		$sql  = 'SELECT t2.* ';
		$sql .= 'FROM tblAccessgroupMembers AS t1 ';
		$sql .= 'INNER JOIN tblUsers AS t2 ON (t1.userId=t2.userId) ';
		$sql .= 'WHERE t1.groupId='.$groupId.' ';
		$sql .= 'ORDER BY t2.userName ASC';

		return dbArray($db, $sql);
	}



	/* Checks if acessfield fieldName is set for any of the accessgroups current user is in and returns true/false accordingly */
	function userAccess(&$db, $fieldName)
	{
		if (!$_SESSION['loggedIn']) return false;

		$fieldName = dbAddSlashes($db, $fieldName);

		//Query returns row(s) if a group has fieldName set to 1, otherwise no rows returned
		$sql  = 'SELECT t2.value ';
		$sql .= 'FROM tblAccessgroupMembers AS t1 ';
		$sql .= 'LEFT OUTER JOIN tblAccessgroupSettings AS t2 ON (t2.groupId=t1.groupId) ';
		$sql .= 'INNER JOIN tblAccessgroupFlags AS t3 ON (t2.flagId=t3.flagId) ';
		$sql .= 'WHERE userId='.$_SESSION['userId'].' AND flagName="'.$fieldName.'" AND value=1';

		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) return true;
		return false;
	}


	/* Returns all folder access flags */
	function getAllFolderAccessFlags(&$db)
	{
		$sql = 'SELECT * FROM tblFolderAccessFlags ORDER BY flagName ASC';
		return dbArray($db, $sql);	
	}
	
	function addFolderAccessRule(&$db, $groupId, $itemId)
	{
		if (!is_numeric($groupId) || !is_numeric($itemId)) return false;

		$sql = 'SELECT * FROM tblFolderAccessRules WHERE groupId='.$groupId.' AND itemId='.$itemId;
		$query = dbQuery($db, $sql);
		if (dbNumRows($query)) return false;

		$sql = 'INSERT INTO tblFolderAccessRules SET groupId='.$groupId.',itemId='.$itemId;
		$query = dbQuery($db, $sql);
		return $db['insert_id'];
	}
	
	
	function setFolderAccessFlag(&$db, $ruleId, $flagId, $value)
	{
		if (!is_numeric($ruleId) || !is_numeric($flagId) || !is_numeric($value)) return false;

		//kan inte använda REPLACE för vi saknar index i tblFolderAccessSettings
		$sql = 'SELECT * FROM tblFolderAccessSettings WHERE ruleId='.$ruleId.' AND flagId='.$flagId;
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) {
			$sql = 'UPDATE tblFolderAccessSettings SET value='.$value.' WHERE ruleId='.$ruleId.' AND flagId='.$flagId;
			dbQuery($db, $sql);
		} else {
			$sql = 'INSERT INTO tblFolderAccessSettings SET ruleId='.$ruleId.',flagId='.$flagId.',value='.$value;
			dbQuery($db, $sql);
		}
	}

	function getFolderAccessRules(&$db, $groupId)
	{
		if (!is_numeric($groupId)) return false;

		$sql = 'SELECT itemId,ruleId FROM tblFolderAccessRules WHERE groupId='.$groupId;
		return dbArray($db, $sql);
	}

	/* Returns an array with all settings for $ruleId */
	function getFolderAccessRuleFlags(&$db, $ruleId)
	{
		if (!is_numeric($ruleId)) return false;

		$sql  = 'SELECT t1.*, t2.flagName AS flagName, t2.flagDesc AS flagDesc ';
		$sql .= 'FROM tblFolderAccessSettings AS t1 ';
		$sql .= 'INNER JOIN tblFolderAccessFlags AS t2 ON (t1.flagId = t2.flagId) ';
		$sql .= 'WHERE t1.ruleId='.$ruleId.' ';
		$sql .= 'ORDER BY t2.flagName ASC';
		return dbArray($db, $sql);
	}
	
	
	/* Removes access rule $itemId from accessgroup $groupId */
	function removeFolderAccessRule(&$db, $ruleId)
	{
		if (!is_numeric($ruleId)) return false;		

		$sql = 'DELETE FROM tblFolderAccessSettings WHERE ruleId='.$ruleId;
		dbQuery($db, $sql);
		
		$sql = 'DELETE FROM tblFolderAccessRules WHERE ruleId='.$ruleId;
		dbQuery($db, $sql);
	}

	//fixme: folderAccess() checkar bara access på gruppen "guests"
	function folderAccess(&$db, $itemId, $ruleName)
	{
		if (!$_SESSION['loggedIn'] || !is_numeric($itemId)) return false;
		$ruleName = dbAddSlashes($db, $ruleName);


		$groupId = getAccessgroupId($db, 'guests');

		//returnerar itemId och value, itemId är ställen i forumet där speciella regler råder, value e 1 / 0
		$sql  = 'SELECT t1.itemId, t2.value FROM tblFolderAccessRules AS t1 ';
		$sql .= 'INNER JOIN tblFolderAccessSettings AS t2 ON (t1.ruleId = t2.ruleId) ';
		$sql .= 'INNER JOIN tblFolderAccessFlags AS t3 ON (t2.flagId = t3.flagId) ';
		$sql .= 'WHERE groupId='.$groupId.' ';
		$sql .= 'AND t3.flagName="'.$ruleName.'"';

		$list = dbArray($db, $sql);
		for ($i=0; $i<count($list); $i++) {
				
			//kontrollera om itemId är parent till vart vi e nu
			if (($list[$i]['itemId'] == $itemId) || forumIsItemParent($db, $list[$i]['itemId'], $itemId)) {
				return $list[$i]['value'];
			}
		}
		return false;
	}
	
?>