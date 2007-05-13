<?
	/*
		todo: kräv bekräftelse innan ett fält tas bort!
		
		clean up tables
	*/
	
	require_once('find_config.php');

	$session->requireAdmin();

	define("TEXT_ARROW_UP",			'<img src="gfx/arrow_up.png" alt="Move up" align="absmiddle"/>');
	define("TEXT_ARROW_DOWN",		'<img src="gfx/arrow_down.png" alt="Move down" align="absmiddle"/>');
	define("TEXT_ARROW_SPACE",	'<img src="gfx/arrow_space.png" align="absmiddle"/>');

	$allowHTML = 0;
	$regRequire = 0;

	if (isset($_POST['allowhtml']))		$allowHTML = 1;
	if (isset($_POST['regrequire']))	$regRequire = 1;

	if (isset($_GET['id'])) {
		$fieldId = $_GET['id'];
	}


	/* Remove field */
	if (isset($_GET['remove'])) {
		$remove = $_GET['remove'];
		removeUserdataField($remove);
	}

	require($project.'design_head.php');
	
	/* Create new field */
	if (isset($_GET['mode']) && ($_GET['mode'] == 'create')) {

		if (isset($_POST['fieldname']) && $_POST['fieldname']) {

			$fieldName = $_POST['fieldname'];
			if (isset($_POST['fieldtype'])) {
				$fieldType = $_POST['fieldtype'];
			} else {
				$fieldType = '';
			}

			if (isset($_POST['fieldaccess'])) {
				$fieldAccess = $_POST['fieldaccess'];
			} else {
				$fieldAccess = '';
			}

			if (isset($_POST['fielddefault'])) {
				$fieldDefault = $_POST['fielddefault'];
			} else {
				$fieldDefault = '';
			}

			if (!addUserdataField($fieldName, $fieldType, $fieldDefault, $allowHTML, $fieldAccess, $regRequire)) {
				echo 'A field with the name '.$fieldName.' already exists<br/>';
			}
		}
	}

	/* Update changes */

	if (isset($_GET['change']) && isset($_POST['fieldname']) && isset($_POST['fieldtype']) && isset($_POST['fieldaccess'])) {

		$changeId	 = $_GET['change'];

		$fieldName	 = $_POST['fieldname'];
		$fieldType    = $_POST['fieldtype'];
		$fieldAccess  = $_POST['fieldaccess'];
		if (isset($_POST['fielddefault'])) {
			$fieldDefault = $_POST['fielddefault'];
		} else {
			$fieldDefault = '';
		}

		/* Update changes for the field */
		setUserdataField($changeId, $fieldName, $fieldType, $fieldDefault, $allowHTML, $fieldAccess, $regRequire);

		/* Update changes for the field-options */
		$list = getUserdataFieldOptions($changeId);
		for($i=0; $i<count($list); $i++) {
			$chg = 'change_'.$list[$i]['optionId'];
			$del = 'delete_'.$list[$i]['optionId'];

			if (isset($_POST[$del]) && $_POST[$del] == '1') {

				/* Delete have highest priority */
				removeUserdataFieldOption($list[$i]['optionId']);

			} else if ($_POST[$chg] && ($list[$i]['optionName'] != $_POST[$chg])) {

				/* If not delete, update */
				setUserdataFieldOption($list[$i]['optionId'], $_POST[$chg]);
			}
		}
	}

	/* Add new option to field */
	if (isset($_GET['change']) && isset($_POST['optionname']) && $_POST['optionname']) {
		$changeId	 = $_GET['change'];
		$optionName  = $_POST['optionname'];
		if (!addUserdataFieldOption($changeId, $optionName)) {
			echo 'The option already exists<br/>';
		}
	}

	/* Move priority */
	if (isset($_GET['prio']) && isset($_GET['old']) && isset($_GET['new'])) {
		setUserdataFieldPriority($_GET['prio'], $_GET['old'], $_GET['new']);
	}

	compactUserdataFields();	//make sure the priorities are compacted
	$list = getUserdataFields();
	$max = count($list);

	for ($i=0; $i<$max; $i++) {
		echo '<table width="100%" border=0 cellspacing=0 cellpadding=1 bgcolor="#000000"><tr><td>';
		echo '<table width="100%" border=0 cellspacing=0 cellpadding=3 bgcolor="#FFFFFF">';
		echo '<tr><td width="38%" valign="top">';

		$fieldName = stripslashes($list[$i]['fieldName']);
		$prio = $list[$i]['fieldPriority'];
		$prio_up = $prio-1;
		$prio_dn = $prio+1;
		if ($prio_up >= 0) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?prio='.$list[$i]['fieldId'].'&old='.$prio.'&new='.$prio_up.'">'.TEXT_ARROW_UP.'</a>';
		} else {
			echo TEXT_ARROW_SPACE;
		}
		if ($prio_dn < $max) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?prio='.$list[$i]['fieldId'].'&old='.$prio.'&new='.$prio_dn.'">'.TEXT_ARROW_DOWN.'</a>';
		} else {
			echo TEXT_ARROW_SPACE;
		}

		echo '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?change='.$list[$i]['fieldId'].getProjectPath().'">'.$fieldName.'</a><br/>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?remove='.$list[$i]['fieldId'].getProjectPath().'">Remove</a><br/>';

		if ($list[$i]['allowTags'] != 0) {
			echo '('.$TEXT_ADMIN[TVAL_ADMIN_ALLOWHTML].')<br/>';
		}

		if ($list[$i]['regRequire']) {
			echo 'Kr&auml;vs vid registrering<br/>';
		}

		echo 'Visas f&ouml;r ';
		switch ($list[$i]['fieldAccess']) {
			case 0: echo 'enbart admin'; break;
			case 1: echo 'admin&anv&auml;ndaren'; break;
			case 2: echo 'alla'; break;
		}
		echo '</td>';

		echo '<td valign="top">'.getUserdataInput($list[$i]).'</td>';

		echo '</tr>';
		echo '</table>';
		echo '</td></tr></table>';
		echo '<br/>';
	}

	if (isset($_GET['change'])) {
		$changeId = $_GET['change'];
		echo '<form name="addOrUpdateField" method="post" action="?change='.$changeId.getProjectPath().'">';
	} else {
		echo '<form name="addOrUpdateField" method="post" action="?mode=create'.getProjectPath().'">';
	}

	if (isset($_GET['change'])) {
		$changeId	 = $_GET['change'];
		$data = getUserdataField($changeId);
		$fieldName = stripslashes($data['fieldName']);
		$header = '&Auml;ndra inst&auml;llningar f&ouml;r f&auml;ltet "'.$fieldName.'"';
		$submit = 'Uppdatera';

	} else {
		$header = 'Skapa nytt anv&auml;ndarinfof&auml;lt';
		$submit = 'Skapa';
		$fieldName = '';
	}

	echo '<table width="100%" border=0 cellspacing=0 cellpadding=1 bgcolor="#000000" height="*"><tr><td>';
	echo '<table cellspacing=0 cellpadding=2 width="100%" border=0 bgcolor="#FFFFFF">';
	echo '<tr><td colspan=3><b>'.$header.'</b><br/></td></tr>';
	echo '<tr><td>F&auml;ltnamn</td>';
	echo '<td>';
		echo '<input type="text" name="fieldname" value="'.$fieldName.'" maxlength="30"/>';
	echo '</td>';
	echo '<td>';
		echo '<input type="checkbox" name="regrequire" value="1" class="checkbox"';
		if (isset($data['regRequire']) && $data['regRequire']) echo ' checked="checked"';
		echo '/> Kr&auml;v vid registrering';
	echo '</td></tr>';

	/* Visa bara alternativet för textfält vid ändring */
	if ((!isset($_GET['change']) && isset($data)) || (isset($data) && (($data['fieldType'] == USERDATA_TYPE_TEXT) || ($data['fieldType'] == USERDATA_TYPE_TEXTAREA)))  ) {
		echo '<tr><td>Defaultv&auml;rde</td>';
		echo '<td colspan=2>';
		echo '<input type="text" name="fielddefault" value="'.$data['fieldDefault'].'"/>';
		echo '</td></tr>';
	}

	echo '<tr><td>Typ</td><td>';
	echo '<select name="fieldtype">';
		echo '<option value="'.USERDATA_TYPE_TEXT.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_TEXT)			echo ' selected'; echo '>Text';
		echo '<option value="'.USERDATA_TYPE_TEXTAREA.	'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_TEXTAREA)	echo ' selected'; echo '>Textarea';
		echo '<option value="'.USERDATA_TYPE_CHECKBOX.	'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_CHECKBOX)	echo ' selected'; echo '>Checkbox';
		echo '<option value="'.USERDATA_TYPE_RADIO.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_RADIO)		echo ' selected'; echo '>Radioknappar';
		echo '<option value="'.USERDATA_TYPE_SELECT.		'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_SELECT)		echo ' selected'; echo '>Dropdown-lista';
		echo '<option value="'.USERDATA_TYPE_IMAGE.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_IMAGE)		echo ' selected'; echo '>Bild';
		echo '<option value="'.USERDATA_TYPE_DATE.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_DATE)			echo ' selected'; echo '>Datum-f&auml;lt';
	echo '</select>';
	echo '</td>';

	/* Visa bara alternativet för textfält vid ändring */
	if (!isset($_GET['change']) || (isset($data) && (($data['fieldType'] == USERDATA_TYPE_TEXT) || ($data['fieldType'] == USERDATA_TYPE_TEXTAREA)))  ) {
		echo '<td>';
		echo '<input type="checkbox" name="allowhtml" value="1" class="checkbox"';
		if (isset($data) && $data['allowTags']) echo ' checked="checked"';
		echo '/> F&aring;r inneh&aring;lla HTML';
		echo '</td>';
	} else {
		echo '<td>&nbsp;</td>';
	}
	echo '</tr>';

	echo '<tr><td>Access</td>';
	echo '<td colspan=2><select name="fieldaccess">';
		echo '<option value="0"'; if (isset($data) && $data['fieldAccess']==0) echo ' selected'; echo '>Visas bara f&ouml;r admins';
		echo '<option value="1"'; if (isset($data) && $data['fieldAccess']==1) echo ' selected'; echo '>Visas f&ouml;r admins och anv&auml;ndaren';
		echo '<option value="2"'; if (isset($data) && $data['fieldAccess']==2) echo ' selected'; echo '>Visas f&ouml;r alla';
	echo '</select>';
	echo '</td></tr>';

	if (isset($data) && (($data['fieldType'] == USERDATA_TYPE_RADIO) || ($data['fieldType'] == USERDATA_TYPE_SELECT))) {
		echo '<tr><td colspan=3>&nbsp;</td></tr>';
		$list = getUserdataFieldOptions($data['fieldId']);
		echo '<tr><td valign="top" colspan=3>'.$TEXT_ADMIN[TVAL_ADMIN_CURRENTOPTIONS].' ('.count($list).' st)</td></tr>';

		for($i=0; $i<count($list); $i++) {
			echo '<tr>';
			echo '<td>&nbsp;</td>';
			echo '<td>';
			echo '<input type="text" name="change_'.$list[$i]['optionId'].'" value="'.$list[$i]['optionName'].'"/>';
			echo '</td>';
			echo '<td>';
			echo '<input type="checkbox" name="delete_'.$list[$i]['optionId'].'" value="1" class="checkbox"/> '.$TEXT_ADMIN[TVAL_ADMIN_REMOVE];
			echo '</td>';
			echo '</tr>';
		}

		echo '<tr><td>'.$TEXT_ADMIN[TVAL_ADMIN_ADDOPTION].'</td>';
		echo '<td colspan=2>';
		echo '<input type="text" name="optionname"/>';
		echo '</td></tr>';
	}

	echo '<tr><td colspan=3><input type="submit" class="button" value="'.$submit.'"/></td></tr>';
	echo '</form></table>';
	echo '</td></tr></table>';

	require($project.'design_foot.php');
?>