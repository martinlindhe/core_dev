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
	

	/* Returns $count random userId's for users who got $fieldName set */
	function getRandomUsersWithField(&$db, $fieldName, $count)
	{
		if (!is_numeric($count)) return false;
		$fieldName = dbAddSlashes($db, $fieldName);

		$sql  = 'SELECT tblUsers.userName, tblUsers.userId, tblUserdata.value ';
		$sql .= 'FROM tblUsers ';
		$sql .= 'INNER JOIN tblUserdataFields ON (tblUserdataFields.fieldName = "'.$fieldName.'") ';
		$sql .= 'INNER JOIN tblUserdata ON (tblUserdataFields.fieldId = tblUserdata.fieldId AND tblUserdata.userId = tblUsers.userId) ';
		$sql .= 'ORDER BY RAND() LIMIT 0,'.$count;

		return dbArray($db, $sql);
	}


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

	/* Uppdaterar namnet på $optionId */
	function setUserdataFieldOption(&$db, $optionId, $optionName)
	{
		if (!is_numeric($optionId)) return false;
		$optionName = dbAddSlashes($db, $optionName);

		dbQuery($db, 'UPDATE tblUserdataFieldOptions SET optionName="'.$optionName.'" WHERE optionId='.$optionId );
		return true;
	}

	function removeUserdataFieldOption(&$db, $optionId)
	{
		if (!is_numeric($optionId)) return false;

		dbQuery($db, 'DELETE FROM tblUserdataFieldOptions WHERE optionId='.$optionId );
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

	/* Returns all datafields that is required for registration, ordered by field priority */
	function getRequiredUserdataFields(&$db)
	{
		$sql = 'SELECT * FROM tblUserdataFields WHERE regRequire=1 ORDER BY fieldPriority ASC';
		return dbArray($db, $sql);
	}

	/* Sets the default values to $userId */
	function setDefaultUserdata(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$check = dbQuery($db, 'SELECT fieldId,fieldDefault FROM tblUserdataFields WHERE fieldDefault != ""');
		$cnt = dbNumRows($check);

		for ($i=0; $i<$cnt; $i++) {
			$row = dbFetchArray($check);
			setUserdata($db, $userId, $row['fieldId'], $row['fieldDefault']);
		}
	}

	/* Returns the userdata field id from field name */
	function getUserdataFieldId(&$db, $fieldName)
	{
		$fieldName = dbAddslashes($db, $fieldName);
		
		return dbOneResultItem($db, 'SELECT fieldId FROM tblUserdataFields WHERE fieldName = "'.$fieldName.'"');
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


	/* Uppdaterar en användares data för ett userfield */
	function setUserdata(&$db, $userId, $fieldId, $value)
	{
		if (!is_numeric($userId) || !is_numeric($fieldId)) return false;

		$value = strip_tags($value);
		$value = dbAddSlashes($db, $value);

		//kan inte använda REPLACE här eftersom tblUserdata inte har nåt index
		$check = dbQuery($db, 'SELECT * FROM tblUserdata WHERE userId='.$userId.' AND fieldId='.$fieldId );
		if (dbNumRows($check)) {
			$sql = 'UPDATE tblUserdata SET value="'.$value.'" WHERE userId='.$userId.' AND fieldId='.$fieldId;
			dbQuery($db, $sql);
		} else {
			$sql = 'INSERT INTO tblUserdata SET userId='.$userId.',fieldId='.$fieldId.',value="'.$value.'"';
			dbQuery($db, $sql);
		}
	}

	/* Returns $userId's data for $fieldName */
	function getUserdataByFieldname(&$db, $userId, $fieldName)
	{
		if (!is_numeric($userId)) return false;
		$fieldName = dbAddSlashes($db, $fieldName);

		$sql  = 'SELECT tblUserdata.value FROM tblUserdata ';
		$sql .= 'INNER JOIN tblUserdataFields ON (tblUserdata.fieldId=tblUserdataFields.fieldId) ';
		$sql .= 'WHERE tblUserdata.userId='.$userId.' AND tblUserdataFields.fieldName="'.$fieldName.'"';

		$value = dbOneResultItem($db, $sql);
		$value = htmlentities($value, ENT_COMPAT, 'UTF-8');

		return $value;
	}

	function getAllUserdataByFieldname(&$db, $userId, $fieldName)
	{
		if (!is_numeric($userId)) return false;
		$fieldName = dbAddSlashes($db, $fieldName);

		$sql  = 'SELECT tblUserdata.*,tblUserdataFields.fieldType ';
		$sql .= 'FROM tblUserdata ';
		$sql .= 'INNER JOIN tblUserdataFields ON (tblUserdata.fieldId=tblUserdataFields.fieldId) ';
		$sql .= 'WHERE tblUserdata.userId='.$userId.' AND tblUserdataFields.fieldName="'.$fieldName.'"';
		return dbOneResult($db, $sql);
	}

	/* Returnerar inställningarna för ett fält */
	function getUserdataField($fieldId)
	{
		global $db;

		if (!is_numeric($fieldId)) return false;

		$q = 'SELECT * FROM tblUserdataFields WHERE fieldId='.$fieldId;
		return $db->getOneRow($q);
	}

	/* Returnerar inställningarna för ett fält */
	function getUserdataFieldByName(&$db, $fieldName)
	{
		$fieldName = dbAddSlashes($db, $fieldName);

		$sql = 'SELECT * FROM tblUserdataFields WHERE fieldName="'.$fieldName.'"';
		return dbOneResult($db, $sql);
	}

	/* Returnerar värdet för ett fält */
	function getUserdataValue(&$db, $fieldId)
	{
		if (!is_numeric($fieldId)) return false;

		$sql = 'SELECT value FROM tblUserdata WHERE fieldId='.$fieldId;
		return dbOneResultItem($db, $sql);
	}


	/* Returnerar HTML-kod för input-fält, checkboxar, optionbuttons, dropdownlistor */
	/* för att redigera inställningar, skippa $thisUser och $userId för att visa allt (för admin-config) */
	function getUserdataFieldsHTMLEdit(&$db, $userId = 0)
	{
		global $config;

		if ($userId) {
			$list = getUserdataFields($db, $userId);
		} else {
			$list = getUserdataFields($db);
		}
		if (!$list) return false;

		$result = '';
		if ($userId) {
			$result .= '<form name="userdata" method="post" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?id='.$userId.'">';
			$result .= '<input type="hidden" name="userinfo" value=1>';
		}

		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['fieldAccess'] == 0) continue;	//skip "only visible to admin"-fields
			$fieldName = stripslashes($list[$i]['fieldName']);

			$result .= '<b>'.$fieldName.'</b>:<br>';
			$result .= getUserdataInput($db, $list[$i]).'<br><br>';
		}

		if ($userId) {
			$result .= '<input type="submit" class="button" value="'.$config['text']['link_save_changes'].'">';
			$result .= '</form>';
		}

		return $result;
	}

	/* Returns a full block of html with all fields that $userId has filled in */
	function getUserdataFieldsHTMLShow(&$db, $userId)
	{
		$rows = getAllUserdata($db, $userId);
		if (!$rows) return false;

		$endresult  = '<table cellpadding=2 cellspacing=0 border=0>';

		for ($i=0; $i<count($rows); $i++) {

			if ($rows[$i]['fieldAccess'] == 2) { //visible to all?

				$endresult .= '<tr><td valign="top" width=110>'.stripslashes($rows[$i]['fieldName']).':&nbsp;</td><td>';
				$endresult .= getUserdataShow($db, $rows[$i]);
				$endresult .= '</td></tr>';
			}
		}

		$endresult .= '</table>';
		return $endresult;
	}

	/* Returns an array of all userdata to show for $userId, excludes empty fields */
	function getAllUserdata(&$db, $userId)
	{
		if (!is_numeric($userId)) return false;

		$sql  = 'SELECT t1.*,t2.fieldName,t2.fieldType,t2.allowTags,t2.fieldAccess,t3.optionName ';
		$sql .= 'FROM tblUserdata AS t1 ';
		$sql .= 'INNER JOIN tblUserdataFields AS t2 ON (t1.fieldId=t2.fieldId) ';
		$sql .= 'LEFT OUTER JOIN tblUserdataFieldOptions AS t3 ON (t1.value=t3.optionId) ';
		$sql .= 'WHERE t1.userId='.$userId.' AND t1.value != "" ORDER BY t2.fieldPriority ASC';

		return dbArray($db, $sql);
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

	function getUserdataShow(&$db, $row)
	{
		global $config;

		$value = stripslashes($row['value']);

		switch ($row['fieldType']) {
			case USERDATA_TYPE_TEXT:
			case USERDATA_TYPE_TEXTAREA:
				return nl2br(formatUserInputText($value));

			case USERDATA_TYPE_CHECKBOX:
				if ($value == '1') return $config['text']['prompt_yes'];
				return $config['text']['prompt_no'];

			case USERDATA_TYPE_RADIO:
				if (isset($row['optionName'])) {
					return $row['optionName'];
				} else {
					return;
				}

			case USERDATA_TYPE_SELECT:
				return $row['optionName'];

			case USERDATA_TYPE_IMAGE:
				if ($value) {
					$fullname = UPLOAD_PATH.getFileName($db, $value);
					$arr=getResizedData($fullname, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT);

					$result  = '<a href="showimage.php?id='.$value.'">';
					$result .= '<img src="getFile.php?id='.$value.'" width="'.$arr['w'].'" height="'.$arr['h'].'" border=0></a><br>';
				}
				return $result;
			
			case USERDATA_TYPE_DATE:
				//todo: kan inte klura ut day of week eftersom datumet kan va innan 1970.. hm
				global $day, $month;
				$y = substr($value, 0, 4);
				$m = intval(substr($value, 4, 2));
				$d = intval(substr($value, 6, 2));
				$result = $day['pron'][$d].' '.$month['short'][$m].' '.$y;
				
				return $result;
			
			default:
				return 'UNKNOWN USERDATA_TYPE IN getUserdataShow()<br>';
		}
	}
	
	function getUserdataHTMLInput(&$db, $fieldName)
	{
		$row = getUserdataFieldByName($db, $fieldName);

		return getUserdataInput($db, $row);
	}


	/* Returnerar HTML-resultatet */
	function getUserdataHTML(&$db, $userId, $fieldName)
	{
		$row = getAllUserdataByFieldname($db, $userId, $fieldName);
		if (!$row) return false;

		return getUserdataShow($db, $userId, $row);
	}

	/* Returnerar html-resultat */
	function getMiniThumbnail(&$db, $userId, $fieldName)
	{
		$row = getAllUserdataByFieldname($db, $userId, $fieldName);
		if (!$row) return false;

		$value = stripslashes($row['value']);
		if (($row['fieldType'] == USERDATA_TYPE_IMAGE) && $value) {

			$fullname = UPLOAD_PATH.getFileName($db, $value);
			$arr=getResizedData($fullname, THUMBNAIL_MINI_WIDTH, THUMBNAIL_MINI_HEIGHT);

			$result = '<a href="showimage.php?id='.$value.'">';
			//$result .= '<img src="getThumbnail.php?id='.$value.'" width="'.$arr['w'].'" height="'.$arr['h'].'" border=0></a>';
			//todo: GD skit fix
			$result .= '<img src="getFile.php?id='.$value.'" width="'.$arr['w'].'" height="'.$arr['h'].'" border=0></a>';
			return $result;
		}
		return false;
	}


	/* Returnerar html-resultat */
	function getThumbnail(&$db, $userId, $fieldName, $width, $height, $url = true)
	{
		global $config;

		$row = getAllUserdataByFieldname($db, $userId, $fieldName);
		if (!$row) return false;

		$value = stripslashes($row['value']);
		if (($row['fieldType'] == USERDATA_TYPE_IMAGE) && $value) {

			$fullname = $config['upload_dir'].$value;
			list($tn_width, $tn_height) = resizeImageCalc($fullname, $width, $height);

			$result = '';
			
			if ($url == true) {
				$result .= '<a href="showimage.php?id='.$value.'">';
				//$result .= '<img src="getThumbnail.php?id='.$value.'" width="'.$tn_width.'" height="'.$tn_height.'" border=0></a>';
			}

			$result .= '<img src="file.php?id='.$value.'&width='.$tn_width.'" width="'.$tn_width.'" height="'.$tn_height.'" border=0>';
			
			if ($url == true) {
				$result .= '</a>';
			}
			
			return $result;
		}
	}

	function getUserIdFromUserdata(&$db, $data)
	{
		$data = dbAddSlashes($db, $data);
		$check = dbQuery($db, 'SELECT DISTINCT userId FROM tblUserdata WHERE value="'.$data.'"');
		$cnt = dbNumRows($check);
		$row = dbFetchArray($check);
		$userId = $row["userId"];

		if ($cnt>1 || $cnt<1) return false;
		return $userId;
	}
?>