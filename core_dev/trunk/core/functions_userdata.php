<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

throw new Exception ('DEPRECATED'); // SEE UserDataType.php


require_once('atom_categories.php');    //for multi-choise userdata types
require_once('network.php');    //for is_email()
require_once('validate_ssn.php');    //to validate swedish ssn's
require_once('ZipLocation.php');    //for location datatyper
require_once('output_xhtml.php');


/* Userdata field types */
define('USERDATA_TYPE_TEXT',                1);
define('USERDATA_TYPE_CHECKBOX',            2);
define('USERDATA_TYPE_RADIO',               3);
define('USERDATA_TYPE_SELECT',              4);
define('USERDATA_TYPE_TEXTAREA',            5);
define('USERDATA_TYPE_IMAGE',               6); //UNIQUE: Used as presentation picture
define('USERDATA_TYPE_BIRTHDATE_SWE',       7); //UNIQUE: Swedish date of birth, with last-4-digits control check
define('USERDATA_TYPE_EMAIL',               8); //UNIQUE: text string holding a email address
define('USERDATA_TYPE_THEME',               9); //UNIQUE: select-dropdown in display. contains user preferred theme (.css file)
define('USERDATA_TYPE_LOCATION_SWE',        10);//UNIQUE: location gadget,user inputs zipcode which maps to "län" and "ort"
define('USERDATA_TYPE_CELLPHONE',           11);//UNIQUE: cellphone number
define('USERDATA_TYPE_AVATAR',              12);//UNIQUE: avatar is radiobutton list but with images
define('USERDATA_TYPE_BIRTHDATE',           13);//UNIQUE: Date of birth
define('USERDATA_TYPE_GENDER',              14);//UNIQUE: radiobutton list-selector for gender
define('USERDATA_TYPE_VIDEOPRES',           15);//UNIQUE: videopresentation (FIXME: IMPLEMENT!!!)

/**
 * Returns a input field from the passed data, used together with editUserdataSettings()
 */
function getUserdataInput($row, $fill = false)
{
    $fieldId = $row['fieldId'];
    if (isset($row['value'])) {
        $value = stripslashes($row['value']);    //doesnt nessecary exist
    } else if (!empty($row['settingValue'])) {
        $value = stripslashes($row['settingValue']);
    } else if ($fill) {
        //look for post data
        if (!empty($_POST['userdata_'.$fieldId])) $value = $_POST['userdata_'.$fieldId];
    }

    if (!isset($value)) {
        //for default values in admin display
        $value = stripslashes($row['fieldDefault']);
    }

    switch ($row['fieldType']) {
        case USERDATA_TYPE_EMAIL:
        case USERDATA_TYPE_TEXT:
            $result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
            $result .= xhtmlInput('userdata_'.$fieldId, $value, 20, 50);
            if ($row['fieldType'] == USERDATA_TYPE_EMAIL) {
                $result .= ' '.xhtmlImage(coredev_webroot().'gfx/icon_mail.png', t('E-mail')).'<br/>';
                //$result .= '<div id="email_valid_'.$fieldId.'">dskksks</div>';    //XXX show email input status (invalid, taken)

            }
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
        case USERDATA_TYPE_GENDER:
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
            $result .= xhtmlSelectCategory(CATEGORY_USERDATA, $fieldId, 'userdata_'.$fieldId, $value);
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

        case USERDATA_TYPE_BIRTHDATE:
            $result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
            $d = $m = $y = '';
            if ($value) {
                $y = date('Y', strtotime($row['settingValue']));
                $m = date('m', strtotime($row['settingValue']));
                $d = date('d', strtotime($row['settingValue']));
            } else if (isset($_POST['userdata_'.$fieldId.'_year'])) {
                if (is_numeric($_POST['userdata_'.$fieldId.'_year'])) $y = $_POST['userdata_'.$fieldId.'_year'];
                if (is_numeric($_POST['userdata_'.$fieldId.'_month'])) $m = $_POST['userdata_'.$fieldId.'_month'];
                if (is_numeric($_POST['userdata_'.$fieldId.'_day'])) $d = $_POST['userdata_'.$fieldId.'_day'];
            }

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
            $result .= '</td>';
            break;

        case USERDATA_TYPE_BIRTHDATE_SWE:
            $result = '<td>'.stripslashes($row['fieldName']).':</td><td>';
            $d = $m = $y = $chk = '';

            if ($value) {
                $result .= date('Y-m-d', strtotime($row['settingValue']));
            } else {
                if (isset($_POST['userdata_'.$fieldId.'_year'])) {
                    if (is_numeric($_POST['userdata_'.$fieldId.'_year'])) $y = $_POST['userdata_'.$fieldId.'_year'];
                    if (is_numeric($_POST['userdata_'.$fieldId.'_month'])) $m = $_POST['userdata_'.$fieldId.'_month'];
                    if (is_numeric($_POST['userdata_'.$fieldId.'_day'])) $d = $_POST['userdata_'.$fieldId.'_day'];
                    if (is_numeric($_POST['userdata_'.$fieldId.'_chk'])) $chk = $_POST['userdata_'.$fieldId.'_chk'];
                }

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

                $result .= '<input type="text" name="userdata_'.$fieldId.'_chk" value="'.$chk.'" size="4" maxlength="4"/>';
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
            die('FATAL: unhandled userdata type in getUserdataInput(): '.$row['fieldType']);
    }

    return $result;
}

/**
 * Returns a input field from the passed data, used by Users::search()
 */
function getUserdataSearch($row)
{
    switch ($row['fieldType']) {
        case USERDATA_TYPE_IMAGE:
            $result  = '<td colspan="2"><input name="userdata_'.$row['fieldId'].'" id="userdata_'.$row['fieldId'].'" type="checkbox" value="1" class="checkbox"/>';
            $result .= ' <label for="userdata_'.$row['fieldId'].'">'.t('Has image').'</label></td>';
            break;

        case USERDATA_TYPE_LOCATION_SWE:
            $result = '<td>'.ZipLocation::regionSelect().'</td>';
            $result .= '<td><div id="ajax_cities"></div></td>';
            break;

        case USERDATA_TYPE_BIRTHDATE:
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
                if (!is_email($_POST['userdata_'.$row['fieldId']])) return t('The email entered is not valid!');
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

        switch ($row['fieldType']) {
            case USERDATA_TYPE_BIRTHDATE:
            case USERDATA_TYPE_BIRTHDATE_SWE:
                //swedish ssn was already verified in verifyRequiredUserdataFields()
                $born = mktime(0, 0, 0,
                    $_POST['userdata_'.$row['fieldId'].'_month'],
                    $_POST['userdata_'.$row['fieldId'].'_day'],
                    $_POST['userdata_'.$row['fieldId'].'_year']
                );
                $val = sql_datetime($born);
                break;

            case USERDATA_TYPE_LOCATION_SWE:
                saveSetting(SETTING_USERDATA, 0, $userId, 'city', ZipLocation::cityId($_POST['userdata_'.$row['fieldId']]));
                saveSetting(SETTING_USERDATA, 0, $userId, 'region', ZipLocation::regionId($_POST['userdata_'.$row['fieldId']]));
                $val = $_POST['userdata_'.$row['fieldId']];
                break;

            default:
                if (empty($_POST['userdata_'.$row['fieldId']])) continue;
                $val = $_POST['userdata_'.$row['fieldId']];
                break;
        }

        saveSetting(SETTING_USERDATA, 0, $userId, $row['fieldId'], $val);
    }
}

/**
 * Helper to display userdata content
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
        case USERDATA_TYPE_GENDER:
        case USERDATA_TYPE_THEME:
            $val = getCategoryName(CATEGORY_USERDATA, $result);
            break;

        case USERDATA_TYPE_IMAGE:
            if (!$result) return false;
            // TODO: Make this an optional setting
            if (isInQueue($result, MODERATION_PRES_IMAGE)) return false;
            $val = showThumb($result, $settingName, 270, 200);
            break;

        default:
            $val = $result;
    }

    return $val;
}

/**
 * Renders html for editing all tblSettings field for current user
 *
 * @return nothing
 */
function editUserdataSettings($_userid = '')
{
    global $h;
    if (empty($_userid)) $_userid = $h->session->id;

    $list = readAllUserdata($_userid);
    if (!$list) return;

    echo '<div class="settings">';
    echo xhtmlForm('edit_settings_frm', '', 'post', 'multipart/form-data');
    echo xhtmlHidden('edit_settings_check', 1);
    echo '<table>';
    foreach ($list as $row) {
        if (!empty($_POST['edit_settings_check'])) {
            switch ($row['fieldType']) {
                case USERDATA_TYPE_IMAGE:
                    if (!empty($_POST['userdata_'.$row['fieldId'].'_remove'])) {
                        $h->files->deleteFile($row['settingValue']);
                        $row['settingValue'] = 0;
                    } else if (isset($_FILES['userdata_'.$row['fieldId']])) { // FIXME: Gör så att handleUpload klarar av att ta userId som parameter
                        $row['settingValue'] = $h->files->handleUpload($_FILES['userdata_'.$row['fieldId']], FILETYPE_USERDATA, $row['fieldId']);
                    }
                    break;

                case USERDATA_TYPE_EMAIL:
                    if (empty($_POST['userdata_'.$row['fieldId']])) break;
                    if (!is_email($_POST['userdata_'.$row['fieldId']])) {
                        echo '<div class="critical">'.t('The email entered is not valid!').'</div>';
                    } else {
                        $chk = findUserByEmail($_POST['userdata_'.$row['fieldId']]);
                        if ($chk && $chk != $_userid) {
                            echo '<div class="critical">'.t('The email entered already taken!').'</div>';
                        } else {
                            $row['settingValue'] = $_POST['userdata_'.$row['fieldId']];
                        }
                    }
                    break;

                case USERDATA_TYPE_BIRTHDATE:
                    if (empty($_POST['userdata_'.$row['fieldId'].'_year'])) break;
                    $born = mktime(0, 0, 0,
                        $_POST['userdata_'.$row['fieldId'].'_month'],
                        $_POST['userdata_'.$row['fieldId'].'_day'],
                        $_POST['userdata_'.$row['fieldId'].'_year']
                    );
                    $row['settingValue'] = sql_datetime($born);
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
                        $h->session->log('User entered invalid swedish zipcode: '.$_POST['userdata_'.$row['fieldId']], LOGLEVEL_WARNING);
                    } else {
                        saveSetting(SETTING_USERDATA, 0, $_userid, 'city', ZipLocation::cityId($_POST['userdata_'.$row['fieldId']]));
                        saveSetting(SETTING_USERDATA, 0, $_userid, 'region', ZipLocation::regionId($_POST['userdata_'.$row['fieldId']]));
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
            saveSetting(SETTING_USERDATA, 0, $_userid, $row['fieldId'], $row['settingValue']);
        }

        echo '<tr>'.getUserdataInput($row).'</tr>';
    }
    echo '</table>';
    echo xhtmlSubmit('Save');
    echo xhtmlFormClose();
    echo '</div>';
}

?>
