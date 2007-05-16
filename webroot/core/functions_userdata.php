<?
	require_once('atom_categories.php');	//for multi-choise userdata types

	//userdata module settings:
	$config['userdata']['maxsize_text'] = 4000;	//max length of userdata-textfield

	/* Userdata field types */
	define('USERDATA_TYPE_TEXT',			1);
	define('USERDATA_TYPE_CHECKBOX',	2);
	define('USERDATA_TYPE_RADIO',			3);
	define('USERDATA_TYPE_SELECT',		4);
	define('USERDATA_TYPE_TEXTAREA',	5);
	define('USERDATA_TYPE_IMAGE',			6);
	define('USERDATA_TYPE_DATE',			7);
	


	/* Skapar ett nytt userfield, och ger fältet en prioritetsnivå som är ledig */
	function addUserdataField($fieldName, $fieldType, $fieldDefault, $allowTags, $fieldAccess, $regRequire)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($allowTags) || !is_numeric($fieldAccess) || !is_numeric($regRequire)) return false;

		$check = $db->getOneItem('SELECT fieldId FROM tblUserdata WHERE fieldName="'.$db->escape($fieldName).'"');
		if ($check) return false;

		$prio = compactUserdataFields();	//returnerar högsta prioritetstalet

		$q = 'INSERT INTO tblUserdata SET fieldName="'.$db->escape($fieldName).'",fieldDefault="'.$db->escape($fieldDefault).'",fieldType='.$fieldType.',allowTags='.$allowTags.',fieldAccess='.$fieldAccess.',fieldPriority='.$prio.',regRequire='.$regRequire;
		$db->query($q);
		return true;
	}

	/* Uppdaterar inställningarna för $fieldId */
	function setUserdataField($fieldId, $fieldName, $fieldType, $fieldDefault, $allowTags, $fieldAccess, $regRequire)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($allowTags) || !is_numeric($fieldAccess) || !is_numeric($regRequire)) return false;

		$q = 'UPDATE tblUserdata SET fieldName="'.$db->escape($fieldName).'",fieldDefault="'.$db->escape($fieldDefault).'",fieldType='.$fieldType.',allowTags='.$allowTags.',fieldAccess='.$fieldAccess.',regRequire='.$regRequire.' WHERE fieldId='.$fieldId;
		$db->query($q);
		return true;
	}

	/* Tar bort ett userfield */
	function removeUserdataField($fieldId)
	{
		global $db;

		if (!is_numeric($fieldId)) return false;

		$db->query('DELETE FROM tblUserdata WHERE fieldId='.$fieldId);
		//$db->query($db, 'DELETE FROM tblCategories WHERE fieldId='.$fieldId );
		//$db->query($db, 'DELETE FROM tblSettings WHERE fieldId='.$fieldId );
	}

	/* Compacts the userdata field priorities, so the boundary 0-max is used */
	/* Returns the first free priority number */
	
	function compactUserdataFields()
	{
		global $db;

		$list = $db->getArray('SELECT fieldId,fieldPriority FROM tblUserdata ORDER BY fieldPriority ASC');

		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['fieldPriority'] != $i) {
				$db->query('UPDATE tblUserdata SET fieldPriority='.$i.' WHERE fieldId='.$list[$i]['fieldId'] );
			}
		}

		return $i;
	}

	function setUserdataFieldPriority($fieldId, $old, $new)
	{
		global $db;

		if (!is_numeric($fieldId) || !is_numeric($old) || !is_numeric($new)) return false;

		/* get fieldId for the one to be replaced */
		$q = 'SELECT fieldId FROM tblUserdata WHERE fieldPriority='.$new;
		$newfieldId = $db->getOneItem($q);

		$db->query('UPDATE tblUserdata SET fieldPriority='.$new.' WHERE fieldId='.$fieldId);
		$db->query('UPDATE tblUserdata SET fieldPriority='.$old.' WHERE fieldId='.$newfieldId);
	}

	/* Returns all userdata fields */
	function getUserdataFields($_required = false)
	{
		global $db;
		
		$q = 'SELECT * FROM tblUserdata ';
		if ($_required) $q .= 'WHERE regRequire=1 ';
		$q .= 'ORDER BY fieldPriority ASC';
		
		return $db->getArray($q);
	}

	/* Returnerar inställningarna för ett fält */
	function getUserdataField($_id)
	{
		global $db;

		if (!is_numeric($_id)) return false;

		$q = 'SELECT * FROM tblUserdata WHERE fieldId='.$_id;
		return $db->getOneRow($q);
	}

	/* Returns a input field from the passed data, used together with getUserdataFieldsHTMLEdit() */
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
		$result = stripslashes($row['fieldName']).':<br/>';

		switch ($row['fieldType']) {
			case USERDATA_TYPE_TEXT:
				$result .= '<input name="userdata_'.$fieldId.'" type="text" value="'.$value.'" size="30" maxlength="50"/>';
				break;

			case USERDATA_TYPE_TEXTAREA:
				$result .= '<textarea name="userdata_'.$fieldId.'" rows="6" cols="40">'.$value.'</textarea>';
				break;

			case USERDATA_TYPE_CHECKBOX:
				$result .= '<input name="userdata_'.$fieldId.'" type="checkbox" class="checkbox" value="1"'.($value == '1'?' checked="checked"':'').'/>';
				break;

			case USERDATA_TYPE_RADIO:
				$options = getCategoriesByOwner(CATEGORY_USERDATA, $fieldId);

				foreach($options as $row) {
					$result .= '<input name="userdata_'.$fieldId.'" type="radio" id="lab_'.$row['categoryId'].'" value="'.$row['categoryId'].'"'.($row['categoryId'] == $value?' checked="checked"':'').'/>';
					$result .= ' <label for="lab_'.$row['categoryId'].'">'.$row['categoryName'].'</label><br/>';
				}
				break;

			case USERDATA_TYPE_SELECT:
				$result .= getCategoriesSelect(CATEGORY_USERDATA, $fieldId, 'userdata_'.$fieldId, $value);
				break;
			
			case USERDATA_TYPE_IMAGE:
				if ($value) {
					$fullname = $config['upload_dir'].$value;

					list($org_width, $org_height) = getimagesize($fullname);
					list($tn_width, $tn_height) = resizeImageCalc($fullname, $config['thumbnail_width'], $config['thumbnail_height']);

					$result .= '<a href="javascript:wnd_imgview('.$value.','.$org_width.','.$org_height.')">';
					$result .= '<img src="file.php?id='.$value.'&width='.$tn_width.'" width="'.$tn_width.'" height="'.$tn_height.'"/>';
					$result .= '</a>';
					$result .= '&nbsp;&nbsp;<input name="userdata_'.$fieldId.'_remove" type="checkbox" class="checkbox"/>Delete image<br/>';
				}
				$result .= '<input name="userdata_'.$fieldId.'" type="file"/>';
				break;

			case USERDATA_TYPE_DATE:
				if ($value && (strlen($value) == 8)) {
					$y = substr($value,0,4);
					$m = substr($value,4,2);
					$d = substr($value,6,2);
				} else {
					$d = '';
					$m = '';
					$y = '';
				}

				$result .= '<select name="userdata_'.$fieldId.'_day">';
				$result .= '<option value="">- Day -';
				for ($j=1; $j<=31; $j++) {
					$k = $j;
					if ($j<10) $k = '0'.$k;
					if ($j == $d) $selected = ' selected'; else $selected = '';
					$result .= '<option value="'.$k.'"'.$selected.'>'.$j;
				}
				$result .= '</select>';

				$result .= '<select name="userdata_'.$fieldId.'_month">';
				$result .= '<option value="">- Month -';
				for ($j=1; $j<=12; $j++) {
					$k = $j;
					if ($j<10) $k = '0'.$k;
					if ($j == $m) $selected = ' selected'; else $selected = '';
					$result .= '<option value="'.$k.'"'.$selected.'>'.$j;
				}
				$result .= '</select>';

				$result .= '<select name="userdata_'.$fieldId.'_year">';
				$result .= '<option value="">- Year -';
				for ($j=1980; $j<=date('Y'); $j++) {
					if ($j == $y) $selected = ' selected'; else $selected = '';
					$result .= '<option value="'.$j.'"'.$selected.'>'.$j;
				}
				$result .= '</select>';
		}

		return $result;
	}

	/* Shows all input fields that are required to be filled in by the user at time of registration */
	function showRequiredUserdataFields()
	{
		$list = getUserdataFields(true);
		foreach ($list as $row) {
			echo '<tr><td colspan="2">'.getUserdataInput($row).'</td></tr>';
		}
	}

	/* Processes all userdata input from registration and stores the entries */
	function handleRequiredUserdataFields($userId)
	{
		global $db;
		if (!is_numeric($userId)) return false;

		$list = getUserdataFields(true);
		foreach ($list as $row) {
			if (!empty($_POST['userdata_'.$row['fieldId']])) {
				saveSetting(SETTING_USERDATA, $userId, $row['fieldId'], $_POST['userdata_'.$row['fieldId']]);
			}
		}
	}

	function readAllUserdata($ownerId)
	{
		if (!is_numeric($ownerId)) return false;

		global $db;

		$q  = 'SELECT t1.*,t2.settingValue FROM tblUserdata AS t1 ';
		$q .= 'LEFT JOIN tblSettings AS t2 ON (t1.fieldId=t2.settingName AND t2.ownerId='.$ownerId.') ORDER BY t1.fieldPriority ASC';

		return $db->getArray($q);
	}
?>