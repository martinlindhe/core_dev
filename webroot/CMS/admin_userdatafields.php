<?
	/*
		todo: kräv bekräftelse innan ett fält tas bort!
	*/

	define("TEXT_ARROW_UP",			'<img src="gfx/arrow_up.png" border=0 alt="Move up" align="absmiddle">');
	define("TEXT_ARROW_DOWN",		'<img src="gfx/arrow_down.png" border=0 alt="Move down" align="absmiddle">');
	define("TEXT_ARROW_SPACE",	'<img src="gfx/arrow_space.png" align="absmiddle">');

	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

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
		removeUserdataField($db, $remove);
	}

	include('design_head.php');
	include('design_user_head.php');
	
	$content = '';

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

			if (!addUserdataField($db, $fieldName, $fieldType, $fieldDefault, $allowHTML, $fieldAccess, $regRequire)) {
				$content .= 'A field with the name '.$fieldName.' already exists<br>';
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
		setUserdataField($db, $changeId, $fieldName, $fieldType, $fieldDefault, $allowHTML, $fieldAccess, $regRequire);

		/* Update changes for the field-options */
		$list = getUserdataFieldOptions($db, $changeId);
		for($i=0; $i<count($list); $i++) {
			$chg = 'change_'.$list[$i]['optionId'];
			$del = 'delete_'.$list[$i]['optionId'];

			if (isset($_POST[$del]) && $_POST[$del] == '1') {

				/* Delete have highest priority */
				removeUserdataFieldOption($db, $list[$i]['optionId']);

			} else if ($_POST[$chg] && ($list[$i]['optionName'] != $_POST[$chg])) {

				/* If not delete, update */
				setUserdataFieldOption($db, $list[$i]['optionId'], $_POST[$chg]);
			}
		}
	}

	/* Add new option to field */
	if (isset($_GET['change']) && isset($_POST['optionname']) && $_POST['optionname']) {
		$changeId	 = $_GET['change'];
		$optionName  = $_POST['optionname'];
		if (!addUserdataFieldOption($db, $changeId, $optionName)) {
			$content .= 'The option already exists<br>';
		}
	}

	/* Move priority */
	if (isset($_GET['prio']) && isset($_GET['old']) && isset($_GET['new'])) {
		setUserdataFieldPriority($db, $_GET['prio'], $_GET['old'], $_GET['new']);
	}

	compactUserdataFields($db);	//make sure the priorities are compacted
	$list = getUserdataFields($db);
	$max = count($list);

	for ($i=0; $i<$max; $i++) {
		$content .= '<table width="100%" border=0 cellspacing=0 cellpadding=1 bgcolor="#000000"><tr><td>';
		$content .= '<table width="100%" border=0 cellspacing=0 cellpadding=3 bgcolor="#FFFFFF">';
		$content .= '<tr><td width="38%" valign="top">';

		$fieldName = stripslashes($list[$i]['fieldName']);
		$prio = $list[$i]['fieldPriority'];
		$prio_up = $prio-1;
		$prio_dn = $prio+1;
		if ($prio_up >= 0) {
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?prio='.$list[$i]['fieldId'].'&old='.$prio.'&new='.$prio_up.'">'.TEXT_ARROW_UP.'</a>';
		} else {
			$content .= TEXT_ARROW_SPACE;
		}
		if ($prio_dn < $max) {
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?prio='.$list[$i]['fieldId'].'&old='.$prio.'&new='.$prio_dn.'">'.TEXT_ARROW_DOWN.'</a>';
		} else {
			$content .= TEXT_ARROW_SPACE;
		}

		$content .= '&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?change='.$list[$i]['fieldId'].'">'.$fieldName.'</a><br>';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'?remove='.$list[$i]['fieldId'].'">'.$config['text']['link_remove'].'</a><br>';

		if ($list[$i]['allowTags'] != 0) {
			$content .= '('.$TEXT_ADMIN[TVAL_ADMIN_ALLOWHTML].')<br>';
		}

		if ($list[$i]['regRequire']) {
			$content .= 'Kr&auml;vs vid registrering<br>';
		}

		$content .= 'Visas f&ouml;r ';
		switch ($list[$i]['fieldAccess']) {
			case 0: $content .= 'enbart admin'; break;
			case 1: $content .= 'admin&anv&auml;ndaren'; break;
			case 2: $content .= 'alla'; break;
		}
		$content .= '</td>';

		$content .= '<td valign="top">'.getUserdataInput($db, $list[$i]).'</td>';

		$content .= '</tr>';
		$content .= '</table>';
		$content .= '</td></tr></table>';
		$content .= '<br>';
	}

	if (isset($_GET['change'])) {
		$changeId = $_GET['change'];
		$content .= '<form name="addOrUpdateField" method="post" action="'.$_SERVER['PHP_SELF'].'?change='.$changeId.'">';
	} else {
		$content .= '<form name="addOrUpdateField" method="post" action="'.$_SERVER['PHP_SELF'].'?mode=create">';
	}

	if (isset($_GET['change'])) {
		$changeId	 = $_GET['change'];
		$data = getUserdataField($db, $changeId);
		$fieldName = stripslashes($data['fieldName']);
		$header = '&Auml;ndra inst&auml;llningar f&ouml;r f&auml;ltet "'.$fieldName.'"';
		$submit = 'Uppdatera';

	} else {
		$header = 'Skapa nytt anv&auml;ndarinfof&auml;lt';
		$submit = 'Skapa';
		$fieldName = '';
	}

	$content .= '<table width="100%" border=0 cellspacing=0 cellpadding=1 bgcolor="#000000" height="*"><tr><td>';
	$content .= '<table cellspacing=0 cellpadding=2 width="100%" border=0 bgcolor="#FFFFFF">';
	$content .= '<tr><td colspan=3><b>'.$header.'</b><br></td></tr>';
	$content .= '<tr><td>F&auml;ltnamn</td>';
	$content .= '<td>';
		$content .= '<input type="text" name="fieldname" value="'.$fieldName.'" maxlength=30>';
	$content .= '</td>';
	$content .= '<td>';
		$content .= '<input type="checkbox" name="regrequire" value="1" class="checkbox"';
		if (isset($data['regRequire']) && $data['regRequire']) $content .= ' checked';
		$content .= '> Kr&auml;v vid registrering';
	$content .= '</td></tr>';

	/* Visa bara alternativet för textfält vid ändring */
	if ((!isset($_GET['change']) && isset($data)) || (isset($data) && (($data['fieldType'] == USERDATA_TYPE_TEXT) || ($data['fieldType'] == USERDATA_TYPE_TEXTAREA)))  ) {
		$content .= '<tr><td>Defaultv&auml;rde</td>';
		$content .= '<td colspan=2>';
		$content .= '<input type="text" name="fielddefault" value="'.$data['fieldDefault'].'">';
		$content .= '</td></tr>';
	}

	$content .= '<tr><td>Typ</td><td>';
	$content .= '<select name="fieldtype">';
		$content .= '<option value="'.USERDATA_TYPE_TEXT.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_TEXT)			$content .= ' selected'; $content .= '>Text';
		$content .= '<option value="'.USERDATA_TYPE_TEXTAREA.	'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_TEXTAREA)	$content .= ' selected'; $content .= '>Textarea';
		$content .= '<option value="'.USERDATA_TYPE_CHECKBOX.	'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_CHECKBOX)	$content .= ' selected'; $content .= '>Checkbox';
		$content .= '<option value="'.USERDATA_TYPE_RADIO.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_RADIO)		$content .= ' selected'; $content .= '>Radioknappar';
		$content .= '<option value="'.USERDATA_TYPE_SELECT.		'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_SELECT)		$content .= ' selected'; $content .= '>Dropdown-lista';
		$content .= '<option value="'.USERDATA_TYPE_IMAGE.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_IMAGE)		$content .= ' selected'; $content .= '>Bild';
		$content .= '<option value="'.USERDATA_TYPE_DATE.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_DATE)			$content .= ' selected'; $content .= '>Datum-f&auml;lt';
	$content .= '</select>';
	$content .= '</td>';

	/* Visa bara alternativet för textfält vid ändring */
	if (!isset($_GET['change']) || (isset($data) && (($data['fieldType'] == USERDATA_TYPE_TEXT) || ($data['fieldType'] == USERDATA_TYPE_TEXTAREA)))  ) {
		$content .= '<td>';
		$content .= '<input type="checkbox" name="allowhtml" value="1" class="checkbox"';
		if (isset($data) && $data['allowTags']) $content .= ' checked';
		$content .= '> F&aring;r inneh&aring;lla HTML';
		$content .= '</td>';
	} else {
		$content .= '<td>&nbsp;</td>';
	}
	$content .= '</tr>';

	$content .= '<tr><td>Access</td>';
	$content .= '<td colspan=2><select name="fieldaccess">';
		$content .= '<option value="0"'; if (isset($data) && $data['fieldAccess']==0) $content .= ' selected'; $content .= '>Visas bara f&ouml;r admins';
		$content .= '<option value="1"'; if (isset($data) && $data['fieldAccess']==1) $content .= ' selected'; $content .= '>Visas f&ouml;r admins och anv&auml;ndaren';
		$content .= '<option value="2"'; if (isset($data) && $data['fieldAccess']==2) $content .= ' selected'; $content .= '>Visas f&ouml;r alla';
	$content .= '</select>';
	$content .= '</td></tr>';

	if (isset($data) && (($data['fieldType'] == USERDATA_TYPE_RADIO) || ($data['fieldType'] == USERDATA_TYPE_SELECT))) {
		$content .= '<tr><td colspan=3>&nbsp;</td></tr>';
		$list = getUserdataFieldOptions($db, $data['fieldId']);
		$content .= '<tr><td valign="top" colspan=3>'.$TEXT_ADMIN[TVAL_ADMIN_CURRENTOPTIONS].' ('.count($list).' st)</td></tr>';

		for($i=0; $i<count($list); $i++) {
			$content .= '<tr>';
			$content .= '<td>&nbsp;</td>';
			$content .= '<td>';
			$content .= '<input type="text" name="change_'.$list[$i]['optionId'].'" value="'.$list[$i]['optionName'].'">';
			$content .= '</td>';
			$content .= '<td>';
			$content .= '<input type="checkbox" name="delete_'.$list[$i]['optionId'].'" value="1" class="checkbox"> '.$TEXT_ADMIN[TVAL_ADMIN_REMOVE];
			$content .= '</td>';
			$content .= '</tr>';
		}

		$content .= '<tr><td>'.$TEXT_ADMIN[TVAL_ADMIN_ADDOPTION].'</td>';
		$content .= '<td colspan=2>';
		$content .= '<input type="text" name="optionname">';
		$content .= '</td></tr>';
	}

	$content .= '<tr><td colspan=3><input type="submit" class="button" value="'.$submit.'"></td></tr>';
	$content .= '</form></table>';
	$content .= '</td></tr></table>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Userinfo fields', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>