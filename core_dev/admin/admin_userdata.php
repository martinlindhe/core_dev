<?php
/**
 * $Id$
 */

require_once('find_config.php');
$h->session->requireSuperAdmin();

$allowHTML = 0;
$regRequire = 0;

if (isset($_POST['allowhtml']))		$allowHTML = 1;
if (isset($_POST['regrequire']))	$regRequire = 1;

if (!empty($_GET['remove']) && confirmed(t('Are you sure you want to delete this userdata field?'), 'remove', $_GET['remove'])) {
	//delete userdata field
	removeUserdataField($_GET['remove']);

	require('design_admin_head.php');
	echo t('Userdata field successfully deleted!').'<br/><br/>';
	require('design_admin_foot.php');
	die;
}

require('design_admin_head.php');

echo '<h1>Userdata fields</h1>';

if (!$h->user->userdata) {
	echo 'Userdata not enabled for this application.';
	require('design_admin_foot.php');
	die;
}

//Create new field
if (isset($_GET['mode']) && ($_GET['mode'] == 'create') && isset($_POST['fieldname']) && $_POST['fieldname']) {
	$fieldType = $fieldPrivate = $fieldDefault = '';
	if (isset($_POST['fieldtype'])) $fieldType = $_POST['fieldtype'];
	if (isset($_POST['fieldprivate'])) $fieldPrivate = $_POST['fieldprivate'];
	if (isset($_POST['fielddefault'])) $fieldDefault = $_POST['fielddefault'];

	if (!addUserdataField($_POST['fieldname'], $fieldType, $fieldDefault, $allowHTML, $fieldPrivate, $regRequire)) {
		echo t('A field with the name').' '.$_POST['fieldname'].' '.t('already exists.').'<br/>';
	}
}

//Update changes
if (isset($_GET['change']) && isset($_POST['fieldname']) && isset($_POST['fieldtype']) && isset($_POST['fieldprivate'])) {

	$changeId = $_GET['change'];

	$fieldDefault = '';
	if (isset($_POST['fielddefault'])) $fieldDefault = $_POST['fielddefault'];

	//Update changes for the field
	setUserdataField($changeId, $_POST['fieldname'], $_POST['fieldtype'], $fieldDefault, $allowHTML, $_POST['fieldprivate'], $regRequire);

	//Update changes for the field-options
	$list = getCategoriesByOwner(CATEGORY_USERDATA, $changeId);

	foreach ($list as $row) {
		if (!empty($_POST['delete_'.$row['categoryId']])) {
			//Delete have highest priority
			removeCategory(CATEGORY_USERDATA, $row['categoryId']);
		} else if (!empty($_POST['change_'.$row['categoryId']]) && ($row['categoryName'] != $_POST['change_'.$row['categoryId']])) {
			//If not delete, update
			updateCategory(CATEGORY_USERDATA, $row['categoryId'], $_POST['change_'.$row['categoryId']]);
		}
	}
}

//Add new option to field
if (isset($_GET['change']) && isset($_POST['optionname']) && $_POST['optionname']) {
	$changeId = $_GET['change'];
	$optionName = $_POST['optionname'];
	if (!addCategory(CATEGORY_USERDATA, $optionName, $changeId)) {
		echo t('The option already exists.').'<br/>';
	}
}

//Move priority
if (isset($_GET['prio']) && isset($_GET['old']) && isset($_GET['new'])) {
	setUserdataFieldPriority($_GET['prio'], $_GET['old'], $_GET['new']);
}

compactUserdataFields();	//make sure the priorities are compacted
$list = getUserdataFields();

$used_image = false;
$used_email = false;
$used_theme = false;
$used_birthdate = false;
$used_birthdate_swe = false;
$used_location_swe = false;
$used_cellphone = false;
$used_gender = false;
$i = 0;

echo '<table>';
foreach ($list as $row) {

	echo '<tr class="item"><td>';
	$prio = $row['fieldPriority'];
	$prio_up = $prio-1;
	$prio_dn = $prio+1;
	if ($prio_up >= 0) {
		echo '<a href="?prio='.$row['fieldId'].'&amp;old='.$prio.'&amp;new='.$prio_up.'"><img src="'.$config['core']['web_root'].'gfx/arrow_up.png" alt="Move up"/></a>';
	}
	if ($prio_dn < count($list)) {
		echo '<a href="?prio='.$row['fieldId'].'&amp;old='.$prio.'&amp;new='.$prio_dn.'"><img src="'.$config['core']['web_root'].'gfx/arrow_down.png" alt="Move down"/></a>';
	}

	echo '&nbsp;<a href="?change='.$row['fieldId'].'">'.t('Modify').'</a><br/>';
	echo '<a href="?remove='.$row['fieldId'].'">'.t('Remove').'</a><br/>';

	if ($row['allowTags']) echo t('May contain HTML').'<br/>';
	if ($row['regRequire']) echo t('Require at registration').'<br/>';
	if ($row['private']) echo t('Private field').'<br/>';
	echo '</td>';

	echo getUserdataInput($row);

	//Allow only 1 field of these types to exist per site
	if ($row['fieldType'] == USERDATA_TYPE_IMAGE) $used_image = true;
	if ($row['fieldType'] == USERDATA_TYPE_EMAIL) $used_email = true;
	if ($row['fieldType'] == USERDATA_TYPE_THEME) $used_theme = true;
	if ($row['fieldType'] == USERDATA_TYPE_BIRTHDATE) $used_birthdate = true;
	if ($row['fieldType'] == USERDATA_TYPE_BIRTHDATE_SWE) $used_birthdate_swe = true;
	if ($row['fieldType'] == USERDATA_TYPE_LOCATION_SWE) $used_location_swe = true;
	if ($row['fieldType'] == USERDATA_TYPE_CELLPHONE) $used_cellphone = true;
	if ($row['fieldType'] == USERDATA_TYPE_GENDER) $used_gender = true;

	echo '</tr>';
}
echo '</table>';

if (isset($_GET['change'])) {
	$changeId = $_GET['change'];
	$data = getUserdataField($changeId);
	$fieldName = stripslashes($data['fieldName']);
	$header = t('Edit userdata field').' "'.$fieldName.'"';
	$submit = 'Update';
} else {
	$header = t('Create a new userdata field');
	$submit = 'Create';
	$fieldName = '';
}

echo '<div class="item">';

if (isset($_GET['change'])) {
	echo '<form name="admin_userdata" method="post" action="?change='.$_GET['change'].'">';
} else {
	echo '<form name="admin_userdata" method="post" action="?mode=create">';
}

echo '<b>'.$header.'</b><br/>';
echo t('Field name').':';
echo xhtmlInput('fieldname', $fieldName, 40, 60).'<br/>';

// Only show the default value option while editing text fields
if ((!isset($_GET['change']) && isset($data)) || (isset($data) && (($data['fieldType'] == USERDATA_TYPE_TEXT)))  ) {
	echo t('Default value').':<br/>';
	echo xhtmlInput('fielddefault', $data['fieldDefault']).'<br/>';
}

echo t('Type').': ';
echo '<select name="fieldtype">';
echo '<option value="'.USERDATA_TYPE_TEXT.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_TEXT)?' selected':'').'>'.t('Text').'</option>';
echo '<option value="'.USERDATA_TYPE_TEXTAREA.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_TEXTAREA)?' selected':'').'>'.t('Textarea').'</option>';
echo '<option value="'.USERDATA_TYPE_CHECKBOX.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_CHECKBOX)?' selected':'').'>'.t('Checkbox').'</option>';
echo '<option value="'.USERDATA_TYPE_RADIO.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_RADIO)?' selected':'').'>'.t('Radio buttons').'</option>';
echo '<option value="'.USERDATA_TYPE_SELECT.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_SELECT)?' selected':'').'>'.t('Dropdown list').'</option>';
echo '<option value="'.USERDATA_TYPE_AVATAR.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_AVATAR)?' selected':'').'>'.t('Avatar').'</option>';

if (!$used_image || (isset($data) && $data['fieldType']==USERDATA_TYPE_IMAGE)) {
	echo '<option value="'.USERDATA_TYPE_IMAGE.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_IMAGE)?' selected':'').'>'.t('Image').'</option>';
}
if (!$used_email || (isset($data) && $data['fieldType']==USERDATA_TYPE_EMAIL)) {
	echo '<option value="'.USERDATA_TYPE_EMAIL.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_EMAIL)?' selected':'').'>'.t('E-mail').'</option>';
}
if (!$used_theme || (isset($data) && $data['fieldType']==USERDATA_TYPE_THEME)) {
	echo '<option value="'.USERDATA_TYPE_THEME.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_THEME)?' selected':'').'>'.t('Theme').'</option>';
}
if (!$used_birthdate || (isset($data) && $data['fieldType']==USERDATA_TYPE_BIRTHDATE)) {
	echo '<option value="'.USERDATA_TYPE_BIRTHDATE.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_BIRTHDATE)?' selected':'').'>'.t('Birth date').'</option>';
}
if (!$used_birthdate_swe || (isset($data) && $data['fieldType']==USERDATA_TYPE_BIRTHDATE_SWE)) {
	echo '<option value="'.USERDATA_TYPE_BIRTHDATE_SWE.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_BIRTHDATE_SWE)?' selected':'').'>'.t('Birth date (Swedish)').'</option>';
}
if (!$used_location_swe || (isset($data) && $data['fieldType']==USERDATA_TYPE_LOCATION_SWE)) {
	echo '<option value="'.USERDATA_TYPE_LOCATION_SWE.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_LOCATION_SWE)?' selected':'').'>'.t('Location (Sweden)').'</option>';
}
if (!$used_cellphone || (isset($data) && $data['fieldType']==USERDATA_TYPE_CELLPHONE)) {
	echo '<option value="'.USERDATA_TYPE_CELLPHONE.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_CELLPHONE)?' selected':'').'>'.t('Cellphone').'</option>';
}
if (!$used_gender || (isset($data) && $data['fieldType']==USERDATA_TYPE_GENDER)) {
	echo '<option value="'.USERDATA_TYPE_GENDER.'"'.((isset($data) && $data['fieldType']==USERDATA_TYPE_GENDER)?' selected':'').'>'.t('Gender').'</option>';
}

echo '</select>';
echo '<br/>';

echo '<input type="checkbox" name="regrequire" id="regrequire" value="1" class="checkbox"'.(!empty($data['regRequire'])?' checked="checked"':'').'/>';
echo ' <label for="regrequire">'.t('Require at registration').'</label>';
echo '<br/>';

// Only show the text field options for text fields on edit field, not on create
if (!isset($_GET['change']) || (isset($data) && (($data['fieldType'] == USERDATA_TYPE_TEXT) || ($data['fieldType'] == USERDATA_TYPE_TEXTAREA)))  ) {
	echo '<input type="checkbox" name="allowhtml" id="allowhtml" value="1" class="checkbox"'.(!empty($data['allowTags'])?' checked="checked"':'').'/>';
	echo ' <label for="allowhtml">'.t('May contain HTML').'</label><br/>';
}

echo '<input name="fieldprivate" type="hidden" value="0"/>';
echo '<input name="fieldprivate" id="fieldprivate" type="checkbox" class="checkbox" value="1"'.(!empty($data['private'])?' checked="checked"':'').'/>';
echo ' <label for="fieldprivate">'.t('Make field private').'</label><br/>';

if (isset($data) &&
	(
		$data['fieldType'] == USERDATA_TYPE_RADIO ||
		$data['fieldType'] == USERDATA_TYPE_SELECT ||
		$data['fieldType'] == USERDATA_TYPE_AVATAR ||
		$data['fieldType'] == USERDATA_TYPE_GENDER ||
		$data['fieldType'] == USERDATA_TYPE_THEME
	) ) {

	$list = getCategoriesByOwner(CATEGORY_USERDATA, $data['fieldId']);
	echo t('Current options').' ('.count($list).' '.t('options').')<br/>';

	$i = 0;
	foreach($list as $row) {
		$i++;
		echo $i.'. <input type="text" name="change_'.$row['categoryId'].'" value="'.$row['categoryName'].'" size="40"/> ';
		echo '<input type="checkbox" name="delete_'.$row['categoryId'].'" id="delete_'.$row['categoryId'].'" value="1" class="checkbox"/>';
		echo '<label for="delete_'.$row['categoryId'].'">'.t('Delete').'</label><br/>';
	}

	if ($data['fieldType'] == USERDATA_TYPE_THEME) {
		echo '<b>Example theme rule: "default.css | Default theme"</b><br/><br/>';
	}

	echo t('Add').':<br/>';
	echo '<input type="text" name="optionname" size="40"/>';
}

echo xhtmlSubmit($submit);

echo '</form>';
echo '</div>';

require('design_admin_foot.php');
?>
