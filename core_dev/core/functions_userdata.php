<?
/**
 * $Id$
 *
 * \todo do a more generic zip-to-location mapper function based on USERDATA_TYPE_LOCATION_SWE code
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('atom_categories.php');	//for multi-choise userdata types
require_once('functions_textformat.php');	//for ValidEmail()
require_once('functions_validate_ssn.php');	//to validate swedish ssn's
require_once('functions_locale.php');	//for translations
require_once('class.ZipLocation.php');	//for location datatype

/* Userdata field types */
define('USERDATA_TYPE_TEXT',				1);
define('USERDATA_TYPE_CHECKBOX',			2);
define('USERDATA_TYPE_RADIO',				3);
define('USERDATA_TYPE_SELECT',				4);
define('USERDATA_TYPE_TEXTAREA',			5);
define('USERDATA_TYPE_IMAGE',				6);	//UNIQUE: Used as presentation picture
define('USERDATA_TYPE_BIRTHDATE_SWE',		7);	//UNIQUE: Swedish date of birth, with last-4-digits control check
define('USERDATA_TYPE_EMAIL',				8);	//UNIQUE: text string holding a email address
define('USERDATA_TYPE_THEME',				9); //UNIQUE: select-dropdown in display. contains user preferred theme (.css file)
define('USERDATA_TYPE_LOCATION_SWE',		10);//UNIQUE: location gadget,user inputs zipcode which maps to "län" and "ort" 
define('USERDATA_TYPE_CELLPHONE',			11);//UNIQUE: cellphone number
define('USERDATA_TYPE_AVATAR',				12);//UNIQUE: avatar is radiobutton list but with images

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
	 * Changes the display order of the userdata field
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
	 * Returns a input field from the passed data, used together with editUserdataSettings()
	 */
	function getUserdataInput($row)
	{
		global $config;

		$fieldId = $row['fieldId'];
		if (isset($row['value'])) {
			$value = stripslashes($row['value']);	//doesnt nessecary exist
		} else if (!empty($row['settingValue'])) {
			$value = stripslashes($row['settingValue']);
		} else { //for default values in admin display
			$value = stripslashes($row['fieldDefault']);
		}

		switch ($row['fieldType']) {
			case USERDATA_TYPE_EMAIL:
			case USERDATA_TYPE_TEXT:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				$result .= '<input name="userdata_'.$fieldId.'" type="text" value="'.$value.'" size="30" maxlength="50"/>';
				if ($row['private']) $result .= '<br/>'.t('This setting is hidden from other users.');
				$result .= '</td>';
				break;

			case USERDATA_TYPE_TEXTAREA:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				$result .= '<textarea name="userdata_'.$fieldId.'" rows="6" cols="40">'.$value.'</textarea>';
				$result .= '</td>';
				break;

			case USERDATA_TYPE_CHECKBOX:
				$result = '<td colspan="2">';
				$result .= '<input name="userdata_'.$fieldId.'" type="hidden" value="0"/>';
				$result .= '<input name="userdata_'.$fieldId.'" id="userdata_'.$fieldId.'" type="checkbox" class="checkbox" value="1"'.($value == '1'?' checked="checked"':'').'/>';
				$result .= ' <label for="userdata_'.$fieldId.'">'.$row['fieldName'].'</label>';
				$result .= '</td>';
				break;

			case USERDATA_TYPE_AVATAR:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				$options = getCategoriesByOwner(CATEGORY_USERDATA, $fieldId);

				foreach($options as $row) {
					$result .= '<input name="userdata_'.$fieldId.'" type="radio" id="lab_'.$row['categoryId'].'" value="'.$row['categoryId'].'"'.($row['categoryId'] == $value?' checked="checked"':'').'/>';
					$result .= ' <label for="lab_'.$row['categoryId'].'">';
					$result .= '<img src="'.$row['categoryName'].'"/>';
					$result .= '</label><br/>';
				}
				$result .= '</td>';
				break;

			case USERDATA_TYPE_RADIO:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				$options = getCategoriesByOwner(CATEGORY_USERDATA, $fieldId);

				foreach($options as $row) {
					$result .= '<input name="userdata_'.$fieldId.'" type="radio" id="lab_'.$row['categoryId'].'" value="'.$row['categoryId'].'"'.($row['categoryId'] == $value?' checked="checked"':'').'/>';
					$result .= ' <label for="lab_'.$row['categoryId'].'">'.$row['categoryName'].'</label><br/>';
				}
				$result .= '</td>';
				break;

			case USERDATA_TYPE_THEME:
			case USERDATA_TYPE_SELECT:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				$result .= getCategoriesSelect(CATEGORY_USERDATA, $fieldId, 'userdata_'.$fieldId, $value);
				$result .= '</td>';
				break;

			case USERDATA_TYPE_IMAGE:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				if ($value) {
					$result .= makeThumbLink($value);
					$result .= '<input name="userdata_'.$fieldId.'_remove" id="userdata_'.$fieldId.'_remove" type="checkbox" class="checkbox"/> ';
					$result .= '<label for="userdata_'.$fieldId.'_remove">'.t('Delete image').'</label>';
				} else {
					$result .= '<input name="userdata_'.$fieldId.'" type="file"/>';
				}
				$result .= '</td>';
				break;

			case USERDATA_TYPE_BIRTHDATE_SWE:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				$d = $m = $y = '';

				if ($value) {
					$result .= date('Y-m-d', strtotime($row['settingValue']));
				} else {
					$result .= '<select name="userdata_'.$fieldId.'_year">';
					$result .= '<option value="">- '.t('Year').' -';
					for ($j=date('Y')-100; $j<=date('Y'); $j++) {
						$result .= '<option value="'.$j.'"'.($j==$y?' selected':'').'>'.$j;
					}
					$result .= '</select>';

					$result .= '<select name="userdata_'.$fieldId.'_month">';
					$result .= '<option value="">- '.t('Month').' -';
					for ($j=1; $j<=12; $j++) {
						$k = $j;
						if ($j<10) $k = '0'.$k;
						$result .= '<option value="'.$k.'"'.($j==$m?' selected':'').'>'.$j;
					}
					$result .= '</select>';

					$result .= '<select name="userdata_'.$fieldId.'_day">';
					$result .= '<option value="">- '.t('Day').' -';
					for ($j=1; $j<=31; $j++) {
						$result .= '<option value="'.($j<10?'0'.$j:$j).'"'.($j==$d?' selected':'').'>'.$j;
					}
					$result .= '</select>';

					$result .= '<input type="text" name="userdata_'.$fieldId.'_chk" size="4"/>';
				}
				$result .= '</td>';
				break;

			case USERDATA_TYPE_LOCATION_SWE:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				$result .= '<input name="userdata_'.$fieldId.'" type="text" value="'.$value.'" size="5" maxlength="5"/>';
				$result .= '</td>';
				break;

			case USERDATA_TYPE_CELLPHONE:
				$result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
				$result .= '<input name="userdata_'.$fieldId.'" type="text" value="'.$value.'" size="12" maxlength="12"/>';
				$result .= '</td>';
				break;

			default:
				die('FATAL: unhandled userdata type in getUserdataInput()');
		}

		return $result;
	}

	/**
	 * Returns a input field from the passed data, used by Users::search()
	 */
	function getUserdataSearch($row)
	{
		global $config;

		switch ($row['fieldType']) {
			case USERDATA_TYPE_IMAGE:
				$result  = '<td colspan="2"><input name="userdata_'.$row['fieldId'].'" id="userdata_'.$row['fieldId'].'" type="checkbox" value="1" class="checkbox"/>';
				$result .= ' <label for="userdata_'.$row['fieldId'].'">'.t('Has image').'</label></td>';
				break;

			case USERDATA_TYPE_LOCATION_SWE:
				$result = '<td>'.ZipLocation::regionSelect().'</td>';
				$result .= '<td><div id="ajax_cities"></div></td>';
				break;

			case USERDATA_TYPE_BIRTHDATE_SWE:
				$result = '<td>'.t('Age').':</td>';
				$result .= '<td><select name="userdata_'.$row['fieldId'].'">';
				$result .= '<option value="0">'.t('Select age').'</option>';

				$low_age = 18;
				$hi_age = 65;
				$inc = 6;

				$date = new DateTime();
				$date->modify('-'.$low_age.' years');
				$from = $date->format('Y-m-d');

				$result .= '<option value="'.$from.'_">'.t('Below '.$low_age).'</option>';

				for ($i = $low_age; $i <= $hi_age; $i += $inc) {
					$date = new DateTime();
					$date->modify('-'.$i.' years');
					$date->modify('-1 days');
					$to = $date->format('Y-m-d');

					$date->modify('-'.$inc.' years');
					$date->modify('+1 days');
					$from = $date->format('Y-m-d');

					$result .= '<option value="'.$from.'_'.$to.'">'.$i.' '.t('to').' '.($i+($inc-1)).'</option>';
				}
				$date = new DateTime();
				$date->modify('-'.($hi_age+1).' years');
				$date->modify('-1 days');
				$to = $date->format('Y-m-d');

				$result .= '<option value="_'.$to.'">'.t('Above '.$hi_age).'</option>';
				$result .= '</select></td>';
				break;

			default:
				$result = getUserdataInput($row);
				break;
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
	 * Verify userdata field input from registration. Returns error on invalid e-mail or if email is in use
	 */
	function verifyRequiredUserdataFields()
	{
		global $db;

		$list = getUserdataFields(true);
		foreach ($list as $row) {
			if ($row['regRequire'] != 1) continue;
			if (!empty($_POST['userdata_'.$row['fieldId']])) {
				$_POST['userdata_'.$row['fieldId']] = trim($_POST['userdata_'.$row['fieldId']]);
			}

			switch ($row['fieldType']) {
				case USERDATA_TYPE_EMAIL:
					if (empty($_POST['userdata_'.$row['fieldId']])) return t('No email entered!');
					if (!ValidEmail($_POST['userdata_'.$row['fieldId']])) return t('The email entered is not valid!');
					if (findUserByEmail($_POST['userdata_'.$row['fieldId']])) return t('The email entered already taken!');
					break;

				case USERDATA_TYPE_BIRTHDATE_SWE:
					if (empty($_POST['userdata_'.$row['fieldId'].'_year']) ||
						SsnValidateSwedishNum(
						$_POST['userdata_'.$row['fieldId'].'_year'],
						$_POST['userdata_'.$row['fieldId'].'_month'],
						$_POST['userdata_'.$row['fieldId'].'_day'],
						$_POST['userdata_'.$row['fieldId'].'_chk']
						) !== true) return t('The Swedish SSN you entered is not valid!');
					break;

				case USERDATA_TYPE_LOCATION_SWE:
					if (!ZipLocation::isValid($_POST['userdata_'.$row['fieldId']])) return t('The Swedish zipcode you entered is not valid!');
					break; 
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
			if (empty($_POST['userdata_'.$row['fieldId']]) && $row['fieldType'] != USERDATA_TYPE_BIRTHDATE_SWE) continue;

			switch ($row['fieldType']) {
				case USERDATA_TYPE_BIRTHDATE_SWE:
					//ssn was already verified in verifyRequiredUserdataFields()
					$born = mktime(0, 0, 0,
						$_POST['userdata_'.$row['fieldId'].'_month'],
						$_POST['userdata_'.$row['fieldId'].'_day'],
						$_POST['userdata_'.$row['fieldId'].'_year']
					);
					$val = sql_datetime($born);
					break;
			
				case USERDATA_TYPE_LOCATION_SWE:
					saveSetting(SETTING_USERDATA, $userId, 'city', ZipLocation::cityId($_POST['userdata_'.$row['fieldId']]));
					saveSetting(SETTING_USERDATA, $userId, 'region', ZipLocation::regionId($_POST['userdata_'.$row['fieldId']]));
					$val = $_POST['userdata_'.$row['fieldId']];
					break;

				default:
					$val = $_POST['userdata_'.$row['fieldId']];
					break;
			}				

			saveSetting(SETTING_USERDATA, $userId, $row['fieldId'], $val);
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
		global $db;
		if (!is_numeric($userId)) return false;

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
				if (!$result) return false;
				$val = showThumb($result, $settingName, 270, 200);
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
	 * Returns user's stored theme
	 *
	 * \param $userId user id
	 * \return theme
	 */
	function loadUserdataTheme($userId, $default)
	{
		if (!is_numeric($userId)) return false;

		$fieldId = getUserdataFieldIdByType(USERDATA_TYPE_THEME);
		$theme = loadUserdataSetting($userId, $fieldId);
		if ($theme) return $theme;
		return $default;
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
	 * Used for "forgot my password" feature and to verify that email address is not taken when someone sets email
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

		$q = 'SELECT t1.ownerId FROM tblSettings AS t1';
		$q .= ' LEFT JOIN tblUsers AS t2 ON (t1.ownerId=t2.userId) ';
		$q .= ' WHERE t2.timeDeleted IS NULL';
		$q .= ' AND t1.settingName="'.$db->escape($email_field).'" AND t1.settingValue="'.$db->escape($email).'" AND t1.settingType='.SETTING_USERDATA;
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
		echo '<table>';
		foreach ($list as $row) {
			if (!empty($_POST)) {
				switch ($row['fieldType']) {
					case USERDATA_TYPE_IMAGE:
						if (!empty($_POST['userdata_'.$row['fieldId'].'_remove'])) {
							$files->deleteFile($row['settingValue']);
							$row['settingValue'] = 0;
						} else if (isset($_FILES['userdata_'.$row['fieldId']])) {
							$row['settingValue'] = $files->handleUpload($_FILES['userdata_'.$row['fieldId']], FILETYPE_USERDATA, $row['fieldId']);
						}
						break;

					case USERDATA_TYPE_EMAIL:
						if (!ValidEmail($_POST['userdata_'.$row['fieldId']])) {
							echo '<div class="critical">'.t('The email entered is not valid!').'</div>';
						} else {
							$chk = findUserByEmail($_POST['userdata_'.$row['fieldId']]);
							if ($chk && $chk != $session->id) {
								echo '<div class="critical">'.t('The email entered already taken!').'</div>';
							} else {
								$row['settingValue'] = $_POST['userdata_'.$row['fieldId']];
							}
						}
						break;

					case USERDATA_TYPE_BIRTHDATE_SWE:
						if (empty($_POST['userdata_'.$row['fieldId'].'_year'])) break;
						$born = mktime(0, 0, 0,
							$_POST['userdata_'.$row['fieldId'].'_month'],
							$_POST['userdata_'.$row['fieldId'].'_day'],
							$_POST['userdata_'.$row['fieldId'].'_year']
						);
						if ($check = SsnValidateSwedishNum(
							$_POST['userdata_'.$row['fieldId'].'_year'],
							$_POST['userdata_'.$row['fieldId'].'_month'],
							$_POST['userdata_'.$row['fieldId'].'_day'],
							$_POST['userdata_'.$row['fieldId'].'_chk']
						) === true) {
							$row['settingValue'] = sql_datetime($born);
						} else {
							echo '<div class="critical">'.t('The Swedish SSN you entered is not valid!').'</div>';
						}
						break;

					case USERDATA_TYPE_LOCATION_SWE:
						if (empty($_POST['userdata_'.$row['fieldId']])) break;
						if (!ZipLocation::isValid($_POST['userdata_'.$row['fieldId']])) {
							echo '<div class="critical">'.t('The Swedish zipcode you entered is not valid!').'</div>';
							$session->log('User entered invalid swedish zipcode: '.$_POST['userdata_'.$row['fieldId']], LOGLEVEL_WARNING);
						} else {
							saveSetting(SETTING_USERDATA, $session->id, 'city', ZipLocation::cityId($_POST['userdata_'.$row['fieldId']]));
							saveSetting(SETTING_USERDATA, $session->id, 'region', ZipLocation::regionId($_POST['userdata_'.$row['fieldId']]));
							$row['settingValue'] = $_POST['userdata_'.$row['fieldId']];
						}
						break;

					default:
						if (!empty($_POST['userdata_'.$row['fieldId']])) {
							$row['settingValue'] = $_POST['userdata_'.$row['fieldId']];
						} else {
							$row['settingValue'] = '';
						}
						break;
				}

				//Stores the setting
				saveSetting(SETTING_USERDATA, $session->id, $row['fieldId'], $row['settingValue']);
			}

			echo '<tr>'.getUserdataInput($row).'</tr>';
		}
		echo '</table>';
		echo '<input type="submit" class="button" value="'.t('Save').'"/>';
		echo '</form>';
		echo '</div>';
	}
?>
