<?
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	require_once('atom_categories.php');	//for multi-choise userdata types
	require_once('functions_textformat.php');	//for ValidEmail()
	require_once('functions_validate_ssn.php');	//to validate swedish ssn's
	require_once('functions_locale.php');	//for translations

	/* Userdata field types */
	define('USERDATA_TYPE_TEXT',					1);
	define('USERDATA_TYPE_CHECKBOX',			2);
	define('USERDATA_TYPE_RADIO',					3);
	define('USERDATA_TYPE_SELECT',				4);
	define('USERDATA_TYPE_TEXTAREA',			5);
	define('USERDATA_TYPE_IMAGE',					6);	//Used as presentation picture, can only exist one per site
	define('USERDATA_TYPE_BIRTHDATE_SWE',	7);	//Swedish date of birth, with last-4-digits control check, can only exist one per site
	define('USERDATA_TYPE_EMAIL',					8);	//text string holding a email address, can only exist one per site
	define('USERDATA_TYPE_THEME',					9); //select-dropdown in display. contains user preferred theme (.css file), can only exist one per site

	//userdata module settings:
	$config['userdata']['maxsize_text'] = 4000;	//max length of userdata-textfield


	/**
	 * Creates a new userfield, and gives it a free priority level
	 */
	function addUserdataField($fieldName, $fieldType, $fieldDefault, $allowTags, $isPrivate, $regRequire)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($allowTags) || !is_numeric($isPrivate) || !is_numeric($regRequire)) return false;

		$check = $db->getOneItem('SELECT fieldId FROM tblUserdata WHERE fieldName="'.$db->escape($fieldName).'"');
		if ($check) return false;

		$prio = compactUserdataFields();	//returnerar högsta prioritetstalet

		$q = 'INSERT INTO tblUserdata SET fieldName="'.$db->escape($fieldName).'",fieldDefault="'.$db->escape($fieldDefault).'",fieldType='.$fieldType.',allowTags='.$allowTags.',private='.$isPrivate.',fieldPriority='.$prio.',regRequire='.$regRequire;
		$db->insert($q);
		return true;
	}

	/**
	 * Updates a userdata field
	 */
	function setUserdataField($fieldId, $fieldName, $fieldType, $fieldDefault, $allowTags, $isPrivate, $regRequire)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($allowTags) || !is_numeric($isPrivate) || !is_numeric($regRequire)) return false;

		$q = 'UPDATE tblUserdata SET fieldName="'.$db->escape($fieldName).'",fieldDefault="'.$db->escape($fieldDefault).'",fieldType='.$fieldType.',allowTags='.$allowTags.',private='.$isPrivate.',regRequire='.$regRequire.' WHERE fieldId='.$fieldId;
		$db->update($q);
		return true;
	}

	/**
	 * Removes a userdata field, and all user settings for this field
	 */
	function removeUserdataField($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$db->delete('DELETE FROM tblUserdata WHERE fieldId='.$_id);
		$db->delete('DELETE FROM tblCategories WHERE categoryType='.CATEGORY_USERDATA.' AND ownerId='.$_id);
		$db->delete('DELETE FROM tblSettings WHERE settingName='.$_id);
	}

	/**
	 * Compacts the userdata field priorities, so the boundary 0-max is used
	 * Returns the first free priority number
	 */
	function compactUserdataFields()
	{
		global $db;

		$list = $db->getArray('SELECT fieldId,fieldPriority FROM tblUserdata ORDER BY fieldPriority ASC');

		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['fieldPriority'] != $i) {
				$db->update('UPDATE tblUserdata SET fieldPriority='.$i.' WHERE fieldId='.$list[$i]['fieldId']);
			}
		}

		return $i;
	}

	/**
	 *
	 */
	function setUserdataFieldPriority($fieldId, $old, $new)
	{
		global $db;

		if (!is_numeric($fieldId) || !is_numeric($old) || !is_numeric($new)) return false;

		/* get fieldId for the one to be replaced */
		$q = 'SELECT fieldId FROM tblUserdata WHERE fieldPriority='.$new;
		$newfieldId = $db->getOneItem($q);

		$db->update('UPDATE tblUserdata SET fieldPriority='.$new.' WHERE fieldId='.$fieldId);
		$db->update('UPDATE tblUserdata SET fieldPriority='.$old.' WHERE fieldId='.$newfieldId);
	}

	/**
	 * Returns userdata fieldId for specified field name
	 *
	 * \param $_name field name
	 * \return field id
	 */
	function getUserdataFieldIdByName($_name)
	{
		global $db;

		$q = 'SELECT fieldId FROM tblUserdata WHERE fieldName="'.$db->escape($_name).'"';
		return $db->getOneItem($q);
	}

	/**
	 * Used to retrieve field id for "email" field. Assumes there is just 1 field of this type in the db
	 *
	 * \param $_type field type
	 * \return field id
	 */
	function getUserdataFieldIdByType($_type)
	{
		global $db;
		if (!is_numeric($_type)) return false;

		$q = 'SELECT fieldId FROM tblUserdata WHERE fieldType='.$_type.' LIMIT 1';
		return $db->getOneItem($q);
	}

	/**
	 * Returns all userdata fields
	 */
	function getUserdataFields($_required = false)
	{
		global $db;

		$q = 'SELECT * FROM tblUserdata ';
		if ($_required) $q .= 'WHERE regRequire=1 ';
		$q .= 'ORDER BY fieldPriority ASC';

		return $db->getArray($q);
	}

	/**
	 * Returns the settings for one userdata field
	 */
	function getUserdataField($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT * FROM tblUserdata WHERE fieldId='.$_id;
		return $db->getOneRow($q);
	}

	/**
	 * Returns the settings for one userdata field
	 */
	function getUserdataFieldByName($_name)
	{
		global $db;

		$q = 'SELECT * FROM tblUserdata WHERE fieldName="'.$db->escape($_name).'"';
		return $db->getOneRow($q);
	}

	/**
	 * Returns the settings for one userdata field
	 */
	function getUserdataFieldName($_id)
	{
		global $db;
		if (!is_numeric($_id)) return false;

		$q = 'SELECT fieldName FROM tblUserdata WHERE fieldId='.$_id;
		return $db->getOneItem($q);
	}

	/**
	 * Returns a input field from the passed data, used together with getUserdataFieldsHTMLEdit()
	 */
	function getUserdataInput($row)
	{
		global $config;

		$fieldId = $row['fieldId'];
		if (isset($row['value'])) {
			$value = stripslashes($row['value']);	//doesnt nessecary exist
		} else if (!empty($row['settingValue'])) {
			$value = stripslashes($row['settingValue']);
		} else if (!empty($_POST['userdata_'.$row['fieldId']])) {
			$value = strip_tags($_POST['userdata_'.$row['fieldId']]);	//if user previously POST'ed data
		} else { //for default values in admin display
			$value = stripslashes($row['fieldDefault']);
		}

		switch ($row['fieldType']) {
			case USERDATA_TYPE_EMAIL:
			case USERDATA_TYPE_TEXT:
				$result = stripslashes($row['fieldName']).': ';
				$result .= '<input name="userdata_'.$fieldId.'" type="text" value="'.$value.'" size="30" maxlength="50"/>';
				break;

			case USERDATA_TYPE_TEXTAREA:
				$result = stripslashes($row['fieldName']).':<br/>';
				$result .= '<textarea name="userdata_'.$fieldId.'" rows="6" cols="40">'.$value.'</textarea>';
				break;

			case USERDATA_TYPE_CHECKBOX:
				$result  = '<input name="userdata_'.$fieldId.'" type="hidden" value="0"/>';
				$result .= '<input name="userdata_'.$fieldId.'" id="userdata_'.$fieldId.'" type="checkbox" class="checkbox" value="1"'.($value == '1'?' checked="checked"':'').'/>';
				$result .= ' <label for="userdata_'.$fieldId.'">'.$row['fieldName'].'</label>';
				break;

			case USERDATA_TYPE_RADIO:
				$result = stripslashes($row['fieldName']).':<br/>';
				$options = getCategoriesByOwner(CATEGORY_USERDATA, $fieldId);

				foreach($options as $row) {
					$result .= '<input name="userdata_'.$fieldId.'" type="radio" id="lab_'.$row['categoryId'].'" value="'.$row['categoryId'].'"'.($row['categoryId'] == $value?' checked="checked"':'').'/>';
					$result .= ' <label for="lab_'.$row['categoryId'].'">'.$row['categoryName'].'</label><br/>';
				}
				break;

			case USERDATA_TYPE_THEME:
			case USERDATA_TYPE_SELECT:
				$result = stripslashes($row['fieldName']).': ';
				$result .= getCategoriesSelect(CATEGORY_USERDATA, $fieldId, 'userdata_'.$fieldId, $value);
				break;

			case USERDATA_TYPE_IMAGE:
				$result = stripslashes($row['fieldName']).':<br/>';
				if ($value) {
					$result .= makeThumbLink($value);
					$result .= '<input name="userdata_'.$fieldId.'_remove" id="userdata_'.$fieldId.'_remove" type="checkbox" class="checkbox"/> ';
					$result .= '<label for="userdata_'.$fieldId.'_remove">'.t('Delete image').'</label>';
				} else {
					$result .= '<input name="userdata_'.$fieldId.'" type="file"/>';
				}
				break;

			case USERDATA_TYPE_BIRTHDATE_SWE:
				$result = stripslashes($row['fieldName']).':<br/>';
				$d = $m = $y = '';				

				if ($value) {
					$born = datetime_to_timestamp($value);
					$y = date('Y', $born);
					$m = date('n', $born);
					$d = date('d', $born);
				}

				$result .= '<select name="userdata_'.$fieldId.'_year">';
				$result .= '<option value="">- Year -';
				for ($j=date('Y')-100; $j<=date('Y'); $j++) {
					$result .= '<option value="'.$j.'"'.($j==$y?' selected':'').'>'.$j;
				}
				$result .= '</select>';

				$result .= '<select name="userdata_'.$fieldId.'_month">';
				$result .= '<option value="">- Month -';
				for ($j=1; $j<=12; $j++) {
					$k = $j;
					if ($j<10) $k = '0'.$k;
					$result .= '<option value="'.$k.'"'.($j==$m?' selected':'').'>'.$j;
				}
				$result .= '</select>';

				$result .= '<select name="userdata_'.$fieldId.'_day">';
				$result .= '<option value="">- Day -';
				for ($j=1; $j<=31; $j++) {
					$result .= '<option value="'.($j<10?'0'.$j:$j).'"'.($j==$d?' selected':'').'>'.$j;
				}
				$result .= '</select>';

				//FIXME this should only be used if core_dev is configured for swedish ssn validation
				$result .= '<input type="text" name="userdata_'.$fieldId.'_chk" size="4"/>';
		}

		return $result;
	}

	/**
	 * Returns a input field from the passed data, used in search_users.php
	 */
	function getUserdataSearch($row)
	{
		global $config;

		if ($row['fieldType'] == USERDATA_TYPE_IMAGE) {
			if ($row['fieldName'] != $config['settings']['default_image']) return '';
			$result  = '<input name="userdata_'.$row['fieldId'].'" id="userdata_'.$row['fieldId'].'" type="checkbox" value="1" class="checkbox"/>';
			$result .= ' <label for="userdata_'.$row['fieldId'].'">Has image</label>';
		} else {
			$result = getUserdataInput($row);
		}

		return $result;
	}

	/**
	 * Shows all input fields that are required to be filled in by the user at time of registration
	 */
	function showRequiredUserdataFields()
	{
		$list = getUserdataFields(true);
		foreach ($list as $row) {
			echo '<tr><td colspan="2">'.getUserdataInput($row).'</td></tr>';
		}
	}

	/**
	 * Verify userdata field input from registration. Returns error on invalid e-mail
	 */
	function verifyRequiredUserdataFields()
	{
		global $db;

		$list = getUserdataFields(true);
		foreach ($list as $row) {
			if ($row['fieldType'] == USERDATA_TYPE_EMAIL && $row['regRequire'] == 1)
			{
				if (empty($_POST['userdata_'.$row['fieldId']])) return false;
				if (!ValidEmail($_POST['userdata_'.$row['fieldId']])) return false;
			}
		}

		return true;
	}

	/**
	 * Processes all userdata input from registration and stores the entries
	 */
	function handleRequiredUserdataFields($userId)
	{
		global $db;
		if (!is_numeric($userId)) return false;

		$list = getUserdataFields(true);
		foreach ($list as $row) {
			if (empty($_POST['userdata_'.$row['fieldId']])) continue;

			saveSetting(SETTING_USERDATA, $userId, $row['fieldId'], $_POST['userdata_'.$row['fieldId']]);
		}
	}

	/**
	 * Returns all userdata settings for specified user
	 *
	 * \param $userId user id
	 * \return array of settings
	 */
	function readAllUserdata($userId)
	{
		if (!is_numeric($userId)) return false;

		global $db;

		$q  = 'SELECT t1.*,t2.settingValue FROM tblUserdata AS t1 ';
		$q .= 'LEFT JOIN tblSettings AS t2 ON (t1.fieldId=t2.settingName AND t2.ownerId='.$userId.') ORDER BY t1.fieldPriority ASC';
		return $db->getArray($q);
	}

	/**
	 * Helper function to display userdata content
	 */
	function showUserdataField($userId, $settingName, $defaultValue = '')
	{
		global $db;
		if (!is_numeric($userId)) return false;

		if (!is_numeric($settingName)) {
			$userdata = getUserdataFieldByName($settingName);
			if (!$userdata) return $defaultValue;
		}

		$q = 'SELECT settingValue FROM tblSettings WHERE ownerId='.$userId.' AND settingType='.SETTING_USERDATA.' AND settingName="'.$userdata['fieldId'].'"';
		$result = $db->getOneItem($q);

		switch ($userdata['fieldType']) {
			case USERDATA_TYPE_RADIO:
			case USERDATA_TYPE_SELECT:
				$val = getCategoryName(CATEGORY_USERDATA, $result);
				break;

			case USERDATA_TYPE_IMAGE:
				$val = makeThumbLink($result);
				break;

			default:
				$val = $result;
		}

		return $val;

	}

	/**
	 * Looks up setting id from tblUserdata. useful for SETTING_USERDATA
	 *
	 * \param $ownerId owner of the setting to load
	 * \param $settingName name of the setting, text-string. for userdata it is actually a numeric
	 * \param $defaultValue is the default value to return if no such setting was previously stored
	 * \return the value of the requested setting
	 */
	function loadUserdataSetting($ownerId, $settingName, $defaultValue = '')
	{
		global $db;
		if (!is_numeric($ownerId) || !$ownerId || !$settingName) return false;

		if (!is_numeric($settingName)) {
			$settingName = getUserdataFieldIdByName($settingName);
			if (!$settingName) return $defaultValue;
		}

		$q = 'SELECT settingValue FROM tblSettings WHERE ownerId='.$ownerId.' AND settingType='.SETTING_USERDATA.' AND settingName="'.$settingName.'"';
		$result = $db->getOneItem($q);

		if ($result) return $result;
		return $defaultValue;
	}

	function saveUserdataSetting($ownerId, $settingName, $settingValue)
	{
		return saveSetting(SETTING_USERDATA, $ownerId, $settingName, $settingValue);
	}

	/**
	 * Returns entered email address for specified user. Needed functionality for
	 * email activation code
	 *
	 * \param $userId user id
	 * \return email or false on error
	 */
	function loadUserdataEmail($userId)
	{
		if (!is_numeric($userId)) return false;

		$fieldId = getUserdataFieldIdByType(USERDATA_TYPE_EMAIL);
		return loadUserdataSetting($userId, $fieldId);
	}

	/**
	 * Returns fileId of user's image id or false if none is set
	 *
	 * \param $userId user id
	 */
	function loadUserdataImage($userId)
	{
		if (!is_numeric($userId)) return false;

		$fieldId = getUserdataFieldIdByType(USERDATA_TYPE_IMAGE);
		return loadUserdataSetting($userId, $fieldId);
	}

	/**
	 * Used for "forgot my password" feature
	 *
	 * \param $email e-mail to look for
	 * \return userId that has this email, or false
	 */
	function findUserByEmail($email)
	{
		global $db;
		$email = trim($email);
		if (!ValidEmail($email)) return false;

		$email_field = getUserdataFieldIdByType(USERDATA_TYPE_EMAIL);

		$q = 'SELECT ownerId FROM tblSettings WHERE settingName="'.$db->escape($email_field).'" AND settingValue="'.$db->escape($email).'" AND settingType='.SETTING_USERDATA;
		$id = $db->getOneItem($q);
		if ($id) return $id;
		return false;
	}

	/**
	 * Renders html for editing all tblSettings field for current user
	 *
	 * \return nothing
	 */
	function editUserdataSettings()
	{
		global $config, $session, $files;

		$list = readAllUserdata($session->id);
		if (!$list) return;

		echo '<div class="settings">';
		echo '<form name="edit_settings_frm" method="post" enctype="multipart/form-data" action="">';
		foreach($list as $row) {
			if (!empty($_POST)) {
				if ($row['fieldType'] == USERDATA_TYPE_IMAGE) {

					if (!empty($_POST['userdata_'.$row['fieldId'].'_remove'])) {
						$files->deleteFile($row['settingValue']);
						$row['settingValue'] = 0;
					} else if (isset($_FILES['userdata_'.$row['fieldId']])) {
						//FIXME: ska det va 'fieldId' som ägare??
						$row['settingValue'] = $files->handleUpload($_FILES['userdata_'.$row['fieldId']], FILETYPE_USERDATA, $row['fieldId']);
					}
				} else if (isset($_POST['userdata_'.$row['fieldId']])) {
					if ($row['fieldType'] == USERDATA_TYPE_EMAIL && !ValidEmail($_POST['userdata_'.$row['fieldId']])) {
						echo '<div class="critical">WARNING: The email entered is not valid!</div>';
					} else {
						$row['settingValue'] = $_POST['userdata_'.$row['fieldId']];
					}
				}

				if ($row['fieldType'] == USERDATA_TYPE_BIRTHDATE_SWE) {

					if (!empty($_POST['userdata_'.$row['fieldId'].'_year'])) {
						$born = mktime(0, 0, 0,
							$_POST['userdata_'.$row['fieldId'].'_month'],
							$_POST['userdata_'.$row['fieldId'].'_day'],
							$_POST['userdata_'.$row['fieldId'].'_year']
						);

						$chk = $_POST['userdata_'.$row['fieldId'].'_chk'];

						if ($check = SsnValidateSwedishNum(
							$_POST['userdata_'.$row['fieldId'].'_year'],
							$_POST['userdata_'.$row['fieldId'].'_month'],
							$_POST['userdata_'.$row['fieldId'].'_day'],
							$_POST['userdata_'.$row['fieldId'].'_chk']
							) === true) {
							$row['settingValue'] = sql_datetime($born);
						} else {
							echo '<div class="critical">Swedish SSN is not valid!</div>';
						}
					}
				}

				if ($row['fieldType'] == USERDATA_TYPE_THEME) {
					$session->theme = $row['settingValue'];
				}

				//Stores the setting
				saveSetting(SETTING_USERDATA, $session->id, $row['fieldId'], $row['settingValue']);
			}

			echo '<div id="edit_setting_div_'.$row['fieldId'].'">';

			if ($row['fieldType'] == USERDATA_TYPE_BIRTHDATE_SWE && $row['settingValue']) {
				echo stripslashes($row['fieldName']).': '.date('Y-m-d', strtotime($row['settingValue']));
			} else {
				echo getUserdataInput($row);
			}
			echo '</div>';
		}
		echo '<input type="submit" class="button" value="'.t('Save').'"/>';
		echo '</form>';
		echo '</div>';
	}
?>