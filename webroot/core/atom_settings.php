<?
	//atom_settings.php - store user/server or other custom types of settings in database

	define('SETTING_SERVER',				1);			//settings associated with the server
	define('SETTING_USERDATA',			2);			//settings used to store personal userdata
	define('SETTING_LANGUAGE',			10);		//används av lang modulen för inställningar för varje språk
	define('SETTING_SUBSCRIPTION',	11);		//en inställning till en subscription

	function saveSetting($_type, $ownerId, $settingName, $settingValue)
	{
		if (!is_numeric($ownerId) || !$ownerId || !is_numeric($_type) || !$settingName) return false;

		global $db;

		$settingName = $db->escape($settingName);
		$settingValue = $db->escape($settingValue);

		$q = 'SELECT settingId FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.$_type.' AND settingName="'.$settingName.'"';
		if ($db->getOneItem($q)) {
			$q = 'UPDATE tblSettings SET settingValue="'.$settingValue.'",timeSaved=NOW() WHERE ownerId='.$ownerId.' AND settingType='.$_type.' AND settingName="'.$settingName.'"';
			$db->query($q);
		} else {
			$q = 'INSERT INTO tblSettings SET ownerId='.$ownerId.',settingType='.$_type.',settingName="'.$settingName.'",settingValue="'.$settingValue.'",timeSaved=NOW()';
			$db->query($q);
		}

		return true;
	}

	function loadSetting($_type, $ownerId, $settingName, $defaultValue = '')
	{
		if (!is_numeric($ownerId) || !$ownerId || !is_numeric($_type) || !$settingName) return false;

		global $db;

		$settingName = $db->escape($settingName);
		$defaultValue = $db->escape($defaultValue);

		$q = 'SELECT settingValue FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.$_type.' AND settingName="'.$settingName.'"';
		$result = $db->getOneItem($q);

		if ($result) return $result;
		return $defaultValue;
	}

	function readAllSettings($_type, $ownerId)
	{
		if (!is_numeric($ownerId) || !$ownerId || !is_numeric($_type)) return false;

		global $db;

		$q = 'SELECT settingName,settingId,settingValue FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.$_type.' ORDER BY settingName ASC';
		return $db->getArray($q);
	}

?>