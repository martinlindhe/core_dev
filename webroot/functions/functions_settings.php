<?
	//functions_settings.php - store user/server settings in database

	define('SETTING_SERVER',				1);			//settings associated with the server
	define('SETTING_USER',					2);			//settings associated with the user
	define('SETTING_LANGUAGE',			10);		//används av lang modulen för inställningar för varje språk
	define('SETTING_SUBSCRIPTION',	11);		//en inställning till en subscription

	function saveSetting($settingType, $ownerId, $settingName, $settingValue)
	{
		if (!is_numeric($ownerId) || !$ownerId || !is_numeric($settingType) || !$settingName) return false;

		global $db;

		$settingName = $db->escape($settingName);
		$settingValue = $db->escape($settingValue);

		$sql = 'SELECT settingId FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.$settingType.' AND settingName="'.$settingName.'"';
		$check = $db->query($sql);
		if ($db->num_rows($check)) {
			$sql = 'UPDATE tblSettings SET settingValue="'.$settingValue.'",timeSaved=NOW() WHERE ownerId='.$ownerId.' AND settingType='.$settingType.' AND settingName="'.$settingName.'"';
			$db->query($sql);
		} else {
			$sql = 'INSERT INTO tblSettings SET ownerId='.$ownerId.',settingType='.$settingType.',settingName="'.$settingName.'",settingValue="'.$settingValue.'",timeSaved=NOW()';
			$db->query($sql);
		}

		return true;
	}

	function readSetting($settingType, $ownerId, $settingName, $defaultValue = '')
	{
		if (!is_numeric($ownerId) || !$ownerId || !is_numeric($settingType) || !$settingName) return false;

		global $db;

		$settingName = $db->escape($settingName);
		$defaultValue = $db->escape($defaultValue);

		$sql = 'SELECT settingValue FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.$settingType.' AND settingName="'.$settingName.'"';
		$result = $db->getOneItem($sql);

		if ($result) return $result;
		return $defaultValue;
	}

?>