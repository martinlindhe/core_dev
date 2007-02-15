<?
	//functions_settings.php - store user/server settings in database

	define('SETTING_SERVER',				1);
	define('SETTING_USER',					2);

	define('SETTING_LANGUAGE',			10);			//används av lang modulen för inställningar för varje språk
	define('SETTING_SUBSCRIPTION',	11);		//en inställning till en subscription

	function saveSetting(&$db, $settingType, $ownerId, $settingName, $settingValue)
	{
		if (!is_numeric($ownerId) || !is_numeric($settingType) || !$settingName) return false;

		$settingName = dbAddSlashes($db, $settingName);
		$settingValue = dbAddSlashes($db, $settingValue);

		$sql = 'SELECT settingId FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.$settingType.' AND settingName="'.$settingName.'"';
		$check = dbQuery($db, $sql);
		if (dbNumRows($check)) {
			$sql = 'UPDATE tblSettings SET settingValue="'.$settingValue.'",timeSaved=NOW() WHERE ownerId='.$ownerId.' AND settingType='.$settingType.' AND settingName="'.$settingName.'"';
			dbQuery($db, $sql);
		} else {
			$sql = 'INSERT INTO tblSettings SET ownerId='.$ownerId.',settingType='.$settingType.',settingName="'.$settingName.'",settingValue="'.$settingValue.'",timeSaved=NOW()';
			dbQuery($db, $sql);
		}
	}

	function getSetting(&$db, $settingType, $ownerId, $settingName, $defaultValue = '')
	{
		if (!is_numeric($ownerId) || !is_numeric($settingType) || !$settingName) return false;

		$settingName = dbAddSlashes($db, $settingName);
		$defaultValue = dbAddSlashes($db, $defaultValue);

		$sql = 'SELECT settingValue FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.$settingType.' AND settingName="'.$settingName.'"';
		$result = dbOneResultItem($db, $sql);
		if ($result) {
			return $result;
		}
		
		return $defaultValue;
	}
	
	/* Removes all settings associated with this owner & type */
	function removeAllSettings(&$db, $settingType, $ownerId)
	{
		if (!is_numeric($settingType) || !is_numeric($ownerId)) return false;
		
		$sql = 'DELETE FROM tblSettings WHERE settingType='.$settingType.' AND ownerId='.$ownerId;
		dbQuery($db, $sql);
	}


	function getUserSetting(&$db, $ownerId, $settingName, $defaultValue = '')
	{
		if (!$ownerId) return $defaultValue;

		return getSetting($db, SETTING_USER, $ownerId, $settingName, $defaultValue);
	}

	function getServerSetting(&$db, $settingName, $defaultValue = '')
	{
		return getSetting($db, SETTING_SERVER, 0, $settingName, $defaultValue);
	}

	function saveUserSetting(&$db, $ownerId, $settingName, $settingValue)
	{
		if (!$ownerId) return false;

		return saveSetting($db, SETTING_USER, $ownerId, $settingName, $settingValue);
	}

?>