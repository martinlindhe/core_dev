<?
	/*
		todo: kräv bekräftelse innan ett fält tas bort!
	*/
	
	require_once('find_config.php');

	$session->requireAdmin();

	$allowHTML = 0;
	$regRequire = 0;

	if (isset($_POST['allowhtml']))		$allowHTML = 1;
	if (isset($_POST['regrequire']))	$regRequire = 1;

	if (isset($_GET['id'])) {
		$fieldId = $_GET['id'];
	}

	/* Remove field */
	if (!empty($_GET['remove'])) {
		removeUserdataField($_GET['remove']);
	}

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	
	/* Create new field */
	if (isset($_GET['mode']) && ($_GET['mode'] == 'create') && isset($_POST['fieldname']) && $_POST['fieldname']) {
		$fieldType = $fieldAccess = $fieldDefault = '';
		if (isset($_POST['fieldtype'])) $fieldType = $_POST['fieldtype'];
		if (isset($_POST['fieldaccess'])) $fieldAccess = $_POST['fieldaccess'];
		if (isset($_POST['fielddefault'])) $fieldDefault = $_POST['fielddefault'];

		if (!addUserdataField($_POST['fieldname'], $fieldType, $fieldDefault, $allowHTML, $fieldAccess, $regRequire)) {
			echo 'A field with the name '.$_POST['fieldname'].' already exists<br/>';
		}
	}

	/* Update changes */

	if (isset($_GET['change']) && isset($_POST['fieldname']) && isset($_POST['fieldtype']) && isset($_POST['fieldaccess'])) {

		$changeId	= $_GET['change'];

		$fieldName = $_POST['fieldname'];
		$fieldType = $_POST['fieldtype'];
		$fieldAccess = $_POST['fieldaccess'];
		$fieldDefault = '';
		if (isset($_POST['fielddefault'])) $fieldDefault = $_POST['fielddefault'];

		/* Update changes for the field */
		setUserdataField($changeId, $fieldName, $fieldType, $fieldDefault, $allowHTML, $fieldAccess, $regRequire);

		/* Update changes for the field-options */
		$list = getCategoriesByOwner(CATEGORY_USERDATA, $changeId);

		foreach ($list as $row) {
			if (!empty($_POST['delete_'.$row['categoryId']])) {
				/* Delete have highest priority */
				removeCategory(CATEGORY_USERDATA, $row['categoryId']);
			} else if (!empty($_POST['change_'.$row['categoryId']]) && ($row['categoryName'] != $_POST['change_'.$row['categoryId']])) {
				/* If not delete, update */
				updateCategory(CATEGORY_USERDATA, $row['categoryId'], $_POST['change_'.$row['categoryId']]);
			}
		}
	}

	/* Add new option to field */
	if (isset($_GET['change']) && isset($_POST['optionname']) && $_POST['optionname']) {
		$changeId	 = $_GET['change'];
		$optionName  = $_POST['optionname'];
		if (!addCategory(CATEGORY_USERDATA, $optionName, $changeId)) {
			echo 'The option already exists<br/>';
		}
	}

	/* Move priority */
	if (isset($_GET['prio']) && isset($_GET['old']) && isset($_GET['new'])) {
		setUserdataFieldPriority($_GET['prio'], $_GET['old'], $_GET['new']);
	}

	compactUserdataFields();	//make sure the priorities are compacted
	$list = getUserdataFields();

	//for ($i=0; $i<count($list); $i++) {
	foreach ($list as $row) {
		echo '<table bgcolor="#FFFFFF">';
		echo '<tr><td width="38%" valign="top">';

		$fieldName = stripslashes($row['fieldName']);
		$prio = $row['fieldPriority'];
		$prio_up = $prio-1;
		$prio_dn = $prio+1;
		if ($prio_up >= 0) {
			echo '<a href="?prio='.$row['fieldId'].'&amp;old='.$prio.'&amp;new='.$prio_up.getProjectPath().'"><img src="/gfx/arrow_up.png" alt="Move up"/></a>';
		}
		if ($prio_dn < count($list)) {
			echo '<a href="?prio='.$row['fieldId'].'&amp;old='.$prio.'&amp;new='.$prio_dn.getProjectPath().'"><img src="/gfx/arrow_down.png" alt="Move down"/></a>';
		}

		echo '&nbsp;<a href="?change='.$row['fieldId'].getProjectPath().'">'.$fieldName.'</a><br/>';
		echo '<a href="?remove='.$row['fieldId'].getProjectPath().'">Remove</a><br/>';

		if ($row['allowTags']) echo 'May contain HTML<br/>';
		if ($row['regRequire']) echo 'Require at registration<br/>';

		echo 'Field will be displayed to ';
		switch ($row['fieldAccess']) {
			case 0: echo 'only admin'; break;
			case 1: echo 'admin and user'; break;
			case 2: echo 'everyone'; break;
		}
		echo '</td>';

		echo '<td valign="top">'.getUserdataInput($row).'</td>';

		echo '</tr>';
		echo '</table>';
		echo '<br/>';
	}

	if (isset($_GET['change'])) {
		$changeId = $_GET['change'];
		echo '<form name="admin_userdata" method="post" action="?change='.$changeId.getProjectPath().'">';
	} else {
		echo '<form name="admin_userdata" method="post" action="?mode=create'.getProjectPath().'">';
	}

	if (isset($_GET['change'])) {
		$changeId	 = $_GET['change'];
		$data = getUserdataField($changeId);
		$fieldName = stripslashes($data['fieldName']);
		$header = 'Edit userdata field "'.$fieldName.'"';
		$submit = 'Update';
	} else {
		$header = 'Create a new userdata field';
		$submit = 'Create';
		$fieldName = '';
	}

	echo '<table bgcolor="#FFFFFF">';
	echo '<tr><td colspan=3><b>'.$header.'</b><br/></td></tr>';
	echo '<tr><td>Field name</td>';
	echo '<td>';
		echo '<input type="text" name="fieldname" value="'.$fieldName.'" maxlength="30"/>';
	echo '</td>';
	echo '<td>';
		echo '<input type="checkbox" name="regrequire" id="regrequire" value="1" class="checkbox"'.(!empty($data['regRequire'])?' checked="checked"':'').'/>';
		echo ' <label for="regrequire">Require at registration</label>';
	echo '</td></tr>';

	/* Visa bara alternativet för textfält vid ändring */
	if ((!isset($_GET['change']) && isset($data)) || (isset($data) && (($data['fieldType'] == USERDATA_TYPE_TEXT) || ($data['fieldType'] == USERDATA_TYPE_TEXTAREA)))  ) {
		echo '<tr><td>Default value</td>';
		echo '<td colspan=2>';
		echo '<input type="text" name="fielddefault" value="'.$data['fieldDefault'].'"/>';
		echo '</td></tr>';
	}

	echo '<tr><td>Type</td><td>';
	echo '<select name="fieldtype">';
		echo '<option value="'.USERDATA_TYPE_TEXT.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_TEXT)			echo ' selected'; echo '>Text';
		echo '<option value="'.USERDATA_TYPE_TEXTAREA.	'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_TEXTAREA)	echo ' selected'; echo '>Textarea';
		echo '<option value="'.USERDATA_TYPE_CHECKBOX.	'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_CHECKBOX)	echo ' selected'; echo '>Checkbox';
		echo '<option value="'.USERDATA_TYPE_RADIO.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_RADIO)			echo ' selected'; echo '>Radioknappar';
		echo '<option value="'.USERDATA_TYPE_SELECT.		'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_SELECT)		echo ' selected'; echo '>Dropdown-lista';
		echo '<option value="'.USERDATA_TYPE_IMAGE.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_IMAGE)			echo ' selected'; echo '>Bild';
		echo '<option value="'.USERDATA_TYPE_DATE.			'"'; if (isset($data) && $data['fieldType']==USERDATA_TYPE_DATE)			echo ' selected'; echo '>Datum-f&auml;lt';
	echo '</select>';
	echo '</td>';

	/* Visa bara alternativet för textfält vid ändring */
	if (!isset($_GET['change']) || (isset($data) && (($data['fieldType'] == USERDATA_TYPE_TEXT) || ($data['fieldType'] == USERDATA_TYPE_TEXTAREA)))  ) {
		echo '<td>';
		echo '<input type="checkbox" name="allowhtml" id="allowhtml" value="1" class="checkbox"'.(!empty($data['allowTags'])?' checked="checked"':'').'/>';
		echo ' <label for="allowhtml">May contain HTML</label>';
		echo '</td>';
	} else {
		echo '<td>&nbsp;</td>';
	}
	echo '</tr>';

	echo '<tr><td>Access</td>';
	echo '<td colspan=2><select name="fieldaccess">';
		echo '<option value="0"'; if (isset($data) && $data['fieldAccess']==0) echo ' selected'; echo '>Only show to admins';
		echo '<option value="1"'; if (isset($data) && $data['fieldAccess']==1) echo ' selected'; echo '>Show to admins and the user';
		echo '<option value="2"'; if (isset($data) && $data['fieldAccess']==2) echo ' selected'; echo '>Show to everyone';
	echo '</select>';
	echo '</td></tr>';

	if (isset($data) && (($data['fieldType'] == USERDATA_TYPE_RADIO) || ($data['fieldType'] == USERDATA_TYPE_SELECT))) {
		echo '<tr><td colspan=3>&nbsp;</td></tr>';
		$list = getCategoriesByOwner(CATEGORY_USERDATA, $data['fieldId']);
		echo '<tr><td valign="top" colspan=3>Current options ('.count($list).' st)</td></tr>';

		foreach($list as $row) {
			echo '<tr>';
			echo '<td>&nbsp;</td>';
			echo '<td>';
			echo '<input type="text" name="change_'.$row['categoryId'].'" value="'.$row['categoryName'].'"/>';
			echo '</td>';
			echo '<td>';
			echo '<input type="checkbox" name="delete_'.$row['categoryId'].'" id="delete_'.$row['categoryId'].'" value="1" class="checkbox"/>';
			echo '<label for="delete_'.$row['categoryId'].'">Delete</label>';
			echo '</td>';
			echo '</tr>';
		}

		echo '<tr><td>Add</td>';
		echo '<td colspan=2>';
		echo '<input type="text" name="optionname"/>';
		echo '</td></tr>';
	}

	echo '<tr><td colspan=3><input type="submit" class="button" value="'.$submit.'"/></td></tr>';
	echo '</table>';

	echo '</form>';

	require($project.'design_foot.php');
?>