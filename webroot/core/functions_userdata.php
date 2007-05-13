<?

	//userdata module settings:
	$config['userdata']['maxsize_text'] = 4000;	//max length of userdata-textfield

	//todo: rensa upp!

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

		$fieldName = $db->escape($fieldName);
		$fieldDefault = $db->escape($fieldDefault);

		$check = $db->getOneItem('SELECT fieldId FROM tblUserdataFields WHERE fieldName="'.$fieldName.'"');
		if ($check) return false;

		$prio = compactUserdataFields();	//returnerar högsta prioritetstalet

		$q = 'INSERT INTO tblUserdataFields SET fieldName="'.$fieldName.'",fieldDefault="'.$fieldDefault.'",fieldType='.$fieldType.',allowTags='.$allowTags.',fieldAccess='.$fieldAccess.',fieldPriority='.$prio.',regRequire='.$regRequire;
		$db->query($q);
		return true;
	}

	/* Uppdaterar inställningarna för $fieldId */
	function setUserdataField($fieldId, $fieldName, $fieldType, $fieldDefault, $allowTags, $fieldAccess, $regRequire)
	{
		global $db;

		if (!is_numeric($fieldType) || !is_numeric($allowTags) || !is_numeric($fieldAccess) || !is_numeric($regRequire)) return false;

		$fieldName = $db->escape($fieldName);
		$fieldDefault = $db->escape($fieldDefault);

		$q = 'UPDATE tblUserdataFields SET fieldName="'.$fieldName.'",fieldDefault="'.$fieldDefault.'",fieldType='.$fieldType.',allowTags='.$allowTags.',fieldAccess='.$fieldAccess.',regRequire='.$regRequire.' WHERE fieldId='.$fieldId;
		$db->query($q);

		return true;
	}

	/* Tar bort ett userfield */
	function removeUserdataField($fieldId)
	{
		global $db;

		if (!is_numeric($fieldId)) return false;

		$db->query('DELETE FROM tblUserdataFields WHERE fieldId='.$fieldId);
		//$db->query($db, 'DELETE FROM tblUserdataFieldOptions WHERE fieldId='.$fieldId );
		//$db->query($db, 'DELETE FROM tblUserdata WHERE fieldId='.$fieldId );
	}

	/* Compacts the userdata field priorities, so the boundary 0-max is used */
	/* Returns the first free priority number */
	
	function compactUserdataFields()
	{
		global $db;

		$list = $db->getArray('SELECT fieldId,fieldPriority FROM tblUserdataFields ORDER BY fieldPriority ASC');

		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['fieldPriority'] != $i) {
				$db->query('UPDATE tblUserdataFields SET fieldPriority='.$i.' WHERE fieldId='.$list[$i]['fieldId'] );
			}
		}

		return $i;
	}

	function setUserdataFieldPriority($fieldId, $old, $new)
	{
		global $db;

		if (!is_numeric($fieldId) || !is_numeric($old) || !is_numeric($new)) return false;

		/* get fieldId for the one to be replaced */
		$q = 'SELECT fieldId FROM tblUserdataFields WHERE fieldPriority='.$new;
		$newfieldId = $db->getOneItem($q);

		$db->query('UPDATE tblUserdataFields SET fieldPriority='.$new.' WHERE fieldId='.$fieldId);
		$db->query('UPDATE tblUserdataFields SET fieldPriority='.$old.' WHERE fieldId='.$newfieldId);
	}

	/* Skapar ett nytt alternativ för ett userfield */
	function addUserdataFieldOption(&$db, $fieldId, $optionName)
	{
		if (!is_numeric($fieldId)) return false;
		$optionName = dbAddSlashes($db, $optionName);

		$check = dbQuery($db, 'SELECT optionId FROM tblUserdataFieldOptions WHERE optionName="'.$optionName.'"');
		if (dbNumRows($check)) {
			return false;
		} else {
			dbQuery($db, 'INSERT INTO tblUserdataFieldOptions SET fieldId='.$fieldId.',optionName="'.$optionName.'"');
			return true;
		}
	}

	/* Returnerar alla alternativ för userfield $fieldId */
	function getUserdataFieldOptions($fieldId, $sorted = true)
	{
		global $db;

		if (!is_numeric($fieldId)) return false;
		$q = 'SELECT * FROM tblUserdataFieldOptions WHERE fieldId='.$fieldId;
		if ($sorted == true) {
			$q .= ' ORDER BY optionName ASC';
		}

		return $db->getArray($q);
	}

	/* Returns all userdata fields, if userId is specified it also returns the set values for the fields */
	function getUserdataFields($userId = 0)
	{
		global $db;

		if ($userId && is_numeric($userId)) {
			$q  = 'SELECT tblUserdataFields.*, tblUserdata.value AS value ';
			$q .= 'FROM tblUserdataFields ';
			$q .= 'LEFT OUTER JOIN tblUserdata ON (tblUserdata.fieldId=tblUserdataFields.fieldId AND tblUserdata.userId='.$userId.') ';
			$q .= 'ORDER BY tblUserdataFields.fieldPriority ASC';

		} else {
			$q  = 'SELECT * FROM tblUserdataFields ORDER BY fieldPriority ASC';
		}

		return $db->getArray($q);
	}

	/* Returnerar inställningarna för ett fält */
	function getUserdataField($fieldId)
	{
		global $db;

		if (!is_numeric($fieldId)) return false;

		$q = 'SELECT * FROM tblUserdataFields WHERE fieldId='.$fieldId;
		return $db->getOneRow($q);
	}

	/* Returns a input field from the passed data, used together with getUserdataFieldsHTMLEdit() */
	function getUserdataInput($row)
	{
		global $config;

		$fieldId = $row['fieldId'];
		if (isset($row['value'])) {
			$value = stripslashes($row['value']);	//doesnt nessecary exist
		} else { //for default values in admin display
			$value = stripslashes($row['fieldDefault']);
		}

		switch ($row['fieldType']) {
			case USERDATA_TYPE_TEXT:
				$result = '<input type="text" name="'.$fieldId.'" value="'.$value.'" size="30" maxlength="50"/>';
				break;

			case USERDATA_TYPE_TEXTAREA:
				$result  = '<textarea name="'.$fieldId.'" rows=6 cols=40>'.$value.'</textarea>';
				break;

			case USERDATA_TYPE_CHECKBOX:
				$result = '<input type="checkbox" class="checkbox" name="'.$fieldId.'" value="1"';
				if ($value == '1') {
					$result .= ' checked="checked"';
				}
				$result .= '/>';
				break;

			case USERDATA_TYPE_RADIO:
				$options = getUserdataFieldOptions($fieldId);
				$result = '';

				for ($j=0; $j<count($options); $j++) {
					$result .= '<input type="radio" class="radiostyle" name="'.$options[$j]['fieldId'].'" value="'.$options[$j]['optionId'].'"';

					if ($options[$j]['optionId'] == $value) {
						$result .= ' checked="checked"';
					}
					$result .= '/>'.$options[$j]['optionName'];
				}
				break;

			case USERDATA_TYPE_SELECT:
				$options = getUserdataFieldOptions($fieldId);

				$hasvalue = false;
				
				for ($j=0; $j<count($options); $j++) {
					if ($options[$j]['optionId'] == $value) {
						$hasvalue = true;
					}
				}

				$result = '<select name="'.$fieldId.'">';

				if ($hasvalue == false) {
					$result .= '<option value="">&nbsp;';
				}

				for($j=0; $j<count($options); $j++) {
					$result .= '<option value="'.$options[$j]['optionId'].'"';
					if ($options[$j]['optionId'] == $value) {
						$result .= ' selected';
					}
					$result .= '>'.$options[$j]['optionName'];
				}
				$result .= '</select>';
				break;

			case USERDATA_TYPE_IMAGE:
				$result = '';
				if ($value) {
					$fullname = $config['upload_dir'].$value;

					list($org_width, $org_height) = getimagesize($fullname);
					list($tn_width, $tn_height) = resizeImageCalc($fullname, $config['thumbnail_width'], $config['thumbnail_height']);

					$result .= '<a href="javascript:wnd_imgview('.$value.','.$org_width.','.$org_height.')">';
					$result .= '<img src="file.php?id='.$value.'&width='.$tn_width.'" width="'.$tn_width.'" height="'.$tn_height.'" border=0>';
					$result .= '</a>';
					$result .= '&nbsp;&nbsp;<input type="checkbox" class="checkbox" name="'.$fieldId.'_remove">Delete image<br>';
				}
				$result .= '<input type="file" name="'.$fieldId.'">';
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
				
				global $day;
				global $month;

				$result  = '<select name="'.$fieldId.'_day">';
				$result .= '<option value="">- Dag -';

				$selected = '';
				for ($j=1; $j<=31; $j++) {
					$k = $j;
					if ($j<10) $k = '0'.$k;
					if ($j == $d) $selected = ' selected'; else $selected = '';
					$result .= '<option value="'.$k.'"'.$selected.'>'.$day['pron'][$j];
				}
				$result .= '</select>';

				$result .= '<select name="'.$fieldId.'_month">';
				$result .= '<option value="">- Månad -';
				
				for ($j=1; $j<=12; $j++) {
					$k = $j;
					if ($j<10) $k = '0'.$k;
					if ($j == $m) $selected = ' selected'; else $selected = '';
					$result .= '<option value="'.$k.'"'.$selected.'>'.$month['long'][$j];
				}
				$result .= '</select>';

				$result .= '<select name="'.$fieldId.'_year">';
				$result .= '<option value="">- År -';
				for ($j=1920; $j<=2000; $j++) {
					if ($j == $y) $selected = ' selected'; else $selected = '';
					$result .= '<option value="'.$j.'"'.$selected.'>'.$j;
				}
				$result .= '</select>';
		}

		return $result;
	}

?>