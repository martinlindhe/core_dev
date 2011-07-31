<?php
/**
 * $Id$
 *
 * Helper functions for XHTML generation
 *
  * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

require_once('LocaleHandler.php');
require_once('XhtmlHeader.php');
require_once('output_js.php');

/**
 * Creates a select-dropdown with numbers
 */
function xhtmlSelectNumeric($_name, $_min = 1, $_max = 10, $_skip = 1)
{
    if (!is_numeric($_min) || !is_numeric($_max) || !is_numeric($_skip)) return;

    $out = '<select name="'.strip_tags($_name).'">';

    for ($i = $_min; $i <= $_max; $i += $_skip)
        $out .= '<option value="'.$i.'">'.$i.'</option>';

    $out .= '</select>';

    return $out;
}

/**
 * @param $_size number of fields shown
 */
function xhtmlSelectMultiple($_name, $_arr, $_default = '', $_onchange = '')
{
    //TODO not ignore the default param

    $rnd = mt_rand();

    $out = js_embed(
    'function toggle_multi_'.$rnd.'() {'.
        'var e = document.getElementById("multi_'.$rnd.'");'.
        'if (e.multiple == true) {'.
            'e.multiple = false;'.
        '} else {'.
            'e.multiple = true;'.
        '}'.
    '}'
    );

    $out .= '<select id="multi_'.$rnd.'" name="'.strip_tags($_name).'[]"'.($_onchange ? ' onchange="'.$_onchange.'"' : '').'>';

    $out .= '<option value="0">---</option>';    //default to "0" instead of an empty string for "no option selected"
    foreach ($_arr as $id => $title)
        $out .= '<option value="'.$id.'">'.$title.'</option>';

    $out .= '</select>';

    $header = XhtmlHeader::getInstance();

    $out .= '<a href="#" onclick="toggle_multi_'.$rnd.'(); return false;" style="vertical-align: bottom;"><img src="'.$header->getCoreDevRoot().'gfx/bullet_toggle_plus.png"/></a>';

    return $out;
}

/**
 * Creates a hidden input field
 * @param $_val field value, or array of multiple values
 */
function xhtmlHidden($_name, $_val = 1)   //XXXX DEPRECATE later (used by phonecafe.se and xhtmlCheckbox(), use XhtmlComponentHidden instead
{
    $out = '';
    if (is_array($_val))
        foreach ($_val as $v)
            $out .= '<input type="hidden" name="'.$_name.'[]" value="'.$v.'"/>';
    else
        $out .= '<input type="hidden" name="'.$_name.'" value="'.$_val.'"/>';
    return $out;
}

/**
 * Creates a checkbox
 */
function xhtmlCheckbox($_name, $_title = '', $_val = 1, $_checked = false, $onclick = '')
{
    $out = '';
    if (!$onclick) $out .= xhtmlHidden($_name, 0);
    $out .= '<input type="checkbox" class="checkbox" name="'.$_name.'" value="'.$_val.'" id="lab_'.$_name.'"'.($_checked ? ' checked="checked"':'').($onclick ? ' onclick="'.$onclick.'"' : '').'/>';
    if ($_title) $out .= '<label for="lab_'.$_name.'"> '.$_title.'</label>';

    return $out;
}

/**
 * Creates a bunch of checkboxes out of an array
 *
 * @param $arr id=>name array
 * @param $prefix (optional) add prefix to checkbox id
 */
function xhtmlCheckboxArray($arr, $prefix = '')
{
    $out = '';
    foreach ($arr as $id => $title) {
        $checked = false;
        if (!empty($_POST[$prefix.$id])) $checked = true;
        $out .= xhtmlCheckbox($prefix.$id, $title, $id, $checked).'<br/>';
    }
    return $out;
}

/**
 * Creates a input field
 */
function xhtmlInput($_name, $_value = '', $_size = 0, $_maxlen = 0, $_disabled = false)  //XXX deprecate, see XhtmlInput.php
{
    if (!is_numeric($_size) || !is_numeric($_maxlen)) return false;
    return '<input type="text" name="'.$_name.'" id="'.$_name.'"'.
        (($_value || is_string($_value)) ? ' value="'.$_value.'"' : '').
        ($_size ? ' size="'.$_size.'"': '').
        ($_maxlen ? ' maxlength="'.$_maxlen.'"': '').
        ($_disabled ? ' disabled': '').
        '/>';
}

/**
 * Creates image tags
 */
function xhtmlImage($_src, $_alt = '')
{
    return '<img src="'.$_src.'"'.($_alt ? ' alt="'.$_alt.'" title="'.$_alt.'"' : '').'/>';
}

/**
 * Creates a <form> open tag
 *
 * @param $enctype set to "multipart/form-data" to handle file uploads
 */
function xhtmlForm($name = '', $action = '', $method = '', $enctype = '', $onsubmit = '')
{
    if (!$method)
        $method = 'post';

    if (!in_array($method, array('get', 'post')))
        throw new Exception ('wierd method '.$method);

    return
    '<form action="'.$action.'" method="'.$method.'"'.
    ($name ? ' id="'.$name.'" name="'.$name.'"' : '').
    ($enctype ? ' enctype="'.$enctype.'"' : '').
    ($onsubmit ? ' onsubmit="'.$onsubmit.'"' : '').
    '>';
}

/**
 * This one is kinda stupid, but added for consistency
 */
function xhtmlFormClose()
{
    return '</form>';
}

/**
 * Creates a password input field
 */
function xhtmlPassword($_name, $_value = '', $_size = 0, $_maxlen = 0)
{
    if (!is_numeric($_size) || !is_numeric($_maxlen)) return false;
    return '<input type="password" name="'.$_name.'"'.
        ($_value ? ' value="'.$_value.'"' : '').
        ($_size ? ' size="'.$_size.'"': '').
        ($_maxlen ? ' maxlength="'.$_maxlen.'"': '').
        '/>';
}

/**
 * Creates a submit button
 */
function xhtmlSubmit($_title = 'Submit', $class = 'button', $style = '')  //XXX DEPRECATE, use XhtmlComponentSubmit.php
{
    return '<input type="submit" value="'.t($_title).'"'.($class ? ' class="'.$class.'"' : '').($style ? ' style="'.$style.'"' : '').'/>';
}

/**
 * Creates a submit button
 */
function xhtmlButton($_title, $onclick = '')
{
    return '<input type="button" class="button" value="'.t($_title).'"'.($onclick ? ' onclick="'.$onclick.'"' : '').'/>';
}

/**
 * $shapes = array(
 *   array('shape' => 'rect',   'href' => 'a.html', 'coords' => array(x1,y1,x2,y2)),
 *   array('shape' => 'circle', 'href' => 'b.html', 'coords' => array(x,y,radius)),
 *   array('shape' => 'poly',   'href' => 'c.html', 'coords' => array(x1,y1,x2,y2,..,xn,yn))
 * );
 */
function xhtmlMap($shapes, $name, $id = '')
{
    $res =
    '<map name="'.$name.'" id="'.(!empty($id)?$id:'xhtmlmap_'.mt_rand()).'">';
    foreach ($shapes as $s) {
        $res .= '<area shape="'.$s['shape'].'" coords="'.implode($s['coords'], ',').'" href="'.$s['href'].'"'.(!empty($s['alt']) ? ' alt="'.$s['alt'].'" title="'.$s['alt'].'"' : '').'/>';
    }
    $res .=
    '</map>';

    return $res;
}

/**
 * Creates select-dropdown menus out of specified category
 */
function xhtmlSelectCategory__XXX_DEPRECATED($_type, $_owner = 0, $selectName = 'default', $selectedId = 0, $url = '', $varName = '', $extra = '')
{
    if (!is_numeric($_type) || !is_numeric($_owner)) return false;

    $out = '<select name="'.strip_tags($selectName).'">';

    if ($_type == CATEGORY_USERFILE) {
        if ($h->files->allow_root_level) {
            $out .= '<option value="0" onclick="location.href=\'?file_category_id=0\'">&nbsp;</option>';
        }
    } else {
        $out .= '<option value="0">&nbsp;</option>';
    }

    $shown_global_cats = false;
    $shown_my_cats = false;

    $list = getGlobalAndUserCategories($_type, $_owner);

    foreach ($list as $row) {
        if ($_type != CATEGORY_CONTACT && $_type != CATEGORY_USERDATA && $_type != CATEGORY_NEWS && $_type != CATEGORY_LANGUAGE && !$shown_global_cats && ($row['permissions']&CAT_PERM_GLOBAL) ) {
            $out .= '<optgroup label="'.t('Global categories').'">';
            $shown_global_cats = true;
        }
        if ($_type != CATEGORY_CONTACT && $_type != CATEGORY_USERDATA && $_type != CATEGORY_NEWS && $_type != CATEGORY_LANGUAGE && !$shown_my_cats && ($row['permissions']&CAT_PERM_USER)) {
            $out .= '</optgroup>';
            $out .= '<optgroup label="'.t('Your categories').'">';
            $shown_my_cats = true;
        }

        //If text is formatted like "123|Text" then 123 will be used as value for this option.
        //This is used by USERDATA_TYPE_THEME
        $data = explode('|', $row['categoryName']);
        if (!empty($data[1])) {
            $val = trim($data[0]);
            $text = trim($data[1]);
        } else {
            $val = $row['categoryId'];
            $text = $data[0];
        }

        $out .= '<option value="'.$val.'"';
        if ($selectedId == $val) $out .= ' selected="selected"';
        else if ($url) {
            if ($varName) {
                $out .= ' onclick="location.href=\''.$url.'?'.$varName.'='.$row['categoryId'].$extra.'\'"';
            } else {
                $out .= ' onclick="location.href=\''.$url.'='.$row['categoryId'].$extra.'\'"';
            }
        }
        $out .= '>'.$text;
        if ($row['permissions'] & CAT_PERM_PRIVATE) $out .= ' ('.t('Private').')';
        if ($row['permissions'] & CAT_PERM_HIDDEN) $out .= ' ('.t('Hidden').')';
        $out .= '</option>';
    }
    if ($shown_global_cats || $shown_my_cats) $out .= '</optgroup>';

    $out .= '</select>';

    return $out;
}

/**
 * Displays one of core_dev's default action buttons
 */
function coreButton($name, $link = '', $title = '')
{
    switch ($name) {
        case 'Edit':   $img = 'icon_create.png'; break;
        case 'Create': $img = 'icon_create.png'; break;
        case 'Delete': $img = 'icon_delete.png'; break;
        case 'Folder': $img = 'icon_folder.png'; break;
        case 'Add':    $img = 'icon_add.png'; break;
        case 'Error':  $img = 'icon_error.png'; break;

        default:
            echo '<h1>ERROR unknown coreButton '.$name.'</h1>';
            return;
    }

    $out = '';
    if ($link) $out .= '<a href="'.$link.'">';
    if (!$title) $title = t($name);

    $out .= '<img src="'.relurl('core_dev/gfx/'.$img).'" alt="'.$title.'" title="'.$title.'"/>';
    if ($link) $out .= '</a>';
    return $out;
}

function countryFlag($code)
{
    if (strlen($code) == 2)
        $code = country_2_to_3_letters($code);

    $code = strtoupper($code);

    $locale = LocaleHandler::getInstance();

    $title = $locale->getCountryName($code);
    if (!$title)
        throw new Exception ('unhandled country flag code '.$code);

    return '<img src="'.relurl('core_dev/gfx/flags/'.$code.'.png').'" alt="'.$title.'" title="'.$title.'"/>';
}

/**
 * Generates XML tags from an array of values
 *
 * @param $params array with params (Name=>Value) for each tag
 */
function toXmlTags($tagname, $params, $pad_before = '', $pad_after = "\n")
{
    if (!is_array($params))
        throw new Exception ('toXmlTags need array with params!');

    $res = '';
    foreach ($params as $p)
    {
        $res .= $pad_before.'<'.$tagname;

        foreach ($p as $param_name => $param_val)
            $res .= ' '.$param_name.'="'.$param_val.'"';

        $res .= '/>'.$pad_after;
    }

    return $res;
}

/**
 * Like htmlentities() but only handles predefined xml entities
 */
function xmlentities($s)
{
    $from = array('<',    '>',    "'",      '"',      '&');
    $to   = array('&lt;', '&gt;', '&apos;', '&quot;', '&amp;');

    return str_replace($from, $to, $s);
}

?>
