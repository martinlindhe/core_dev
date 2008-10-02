<?php
/**
 * $Id$
 *
 * Store user/server or other custom types of settings in database
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

define('SETTING_APPDATA',		1);		//setting global to the whole application
define('SETTING_USERDATA',		2);		//settings used to store personal userdata
define('SETTING_CALLERDATA',	3);		//settings used to store data of a caller
define('SETTING_EXTERNALDATA',	4);		//settings used to store data with external ownerid
//TODO remove these and make more userdata field types instead
$config['settings']['default_signature'] = 'Signature';	//default name of the userdata field used to contain the forum signature

/**
 * Saves a setting associated with $ownerId
 *
 * \param $_type type of setting
 * \param $categoryId setting category (use 0 if unneeded)
 * \param $ownerId owner of the setting
 * \param $settingName name of the setting, text-string
 * \param $settingValue value of the setting
 * \return true on success
 */
function saveSetting($_type, $categoryId, $ownerId, $settingName, $settingValue)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($categoryId) || !is_numeric($ownerId) || !$settingName) return false;
	if ($_type != SETTING_APPDATA && !$ownerId) return false;

	$settingName = $db->escape($settingName);
	$settingValue = $db->escape($settingValue);

	$q = 'SELECT settingId FROM tblSettings WHERE ownerId='.$ownerId;
	$q .= ' AND categoryId='.$categoryId;
	$q .= ' AND settingType='.$_type;
	$q .= ' AND settingName="'.$settingName.'"';
	if ($db->getOneItem($q)) {
		$q = 'UPDATE tblSettings SET settingValue="'.$settingValue.'",timeSaved=NOW() WHERE ownerId='.$ownerId;
		$q .= ' AND categoryId='.$categoryId;
		$q .= ' AND settingType='.$_type;
		$q .= ' AND settingName="'.$settingName.'"';
		$db->update($q);
	} else {
		$q = 'INSERT INTO tblSettings SET ownerId='.$ownerId.',';
		$q .= 'categoryId='.$categoryId.',';
		$q .= 'settingType='.$_type.',settingName="'.$settingName.'",';
		$q .= 'settingValue="'.$settingValue.'",timeSaved=NOW()';
		$db->insert($q);
	}
	return true;
}

/**
 * Loads a setting associated with $ownerId
 *
 * \param $_type type of setting
 * \param $categoryId setting category (use 0 if unneeded)
 * \param $ownerId owner of the setting
 * \param $settingName name of the setting, text-string
 * \param $defaultValue is the default value to return if no such setting was previously stored
 * \return the value of the requested setting
 */
function loadSetting($_type, $categoryId, $ownerId, $settingName, $defaultValue = '')
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($categoryId) || !is_numeric($ownerId) || !$settingName) return false;

	$q = 'SELECT settingValue FROM tblSettings';
	$q .= ' WHERE settingType='.$_type;
	$q .= ' AND categoryId='.$categoryId;
	$q .= ' AND ownerId='.$ownerId;
	$q .= ' AND settingName="'.$db->escape($settingName).'"';
	$result = $db->getOneRow($q);

	if ($result) return $result['settingValue'];
	return $defaultValue;
}

/**
 * Returns array of all settings for requested owner
 *
 * \param $_type type of settings
 * \param $categoryId setting category (use 0 for all)
 * \param $ownerId owner of the settings
 * \return array of settings
 */
function readAllSettings($_type, $categoryId = 0, $ownerId = 0)	//rename to loadSettings() ?
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($categoryId) || !is_numeric($ownerId)) return false;

	$q = 'SELECT * FROM tblSettings';
	$q .= ' WHERE settingType='.$_type;
	if ($categoryId) $q .= ' AND categoryId='.$categoryId;
	if ($ownerId) $q .= ' AND ownerId='.$ownerId;
	$q .= ' ORDER BY settingName ASC';
	return $db->getArray($q);
}

/**
 * Deletes all settings for owner, of specified type
 *
 * \param $_type type of settings
 * \param $categoryId setting category (use 0 for all)
 * \param $ownerId owner of the settings
 * \return number of settings removed
 */
function deleteSettings($_type, $categoryId, $ownerId)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($categoryId) || !is_numeric($ownerId)) return false;

	$q = 'DELETE FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.$_type;
	if ($categoryId) $q .= ' AND categoryId='.$categoryId;
	return $db->delete($q);
}

/**
 * Deletes specified setting for owner, of specified type
 *
 * \param $_type type of setting
 * \param $categoryId setting category
 * \param $ownerId owner of the setting
 * \param $settingName name of the setting
 * \return number of settings removed
 */
function deleteSetting($_type, $categoryId, $ownerId, $settingName)
{
	global $db;
	if (!is_numeric($_type) || !is_numeric($categoryId) || !is_numeric($ownerId)) return false;

	$q = 'DELETE FROM tblSettings WHERE ownerId='.$ownerId;
	$q .= ' AND categoryId='.$categoryId;
	$q .= ' AND settingType='.$_type;
	$q .= ' AND settingName = "'.$settingName.'" LIMIT 1';
	return $db->delete($q);
}

?>
