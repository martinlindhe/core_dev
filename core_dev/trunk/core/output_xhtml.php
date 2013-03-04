<?php
/**
 * $Id$
 *
 * Helper functions for XHTML generation
 *
  * @author Martin Lindhe, 2007-2013 <martin@startwars.org>
 */

//WIP - deprecate all functions, see html.php

namespace cd;

require_once('LocaleHandler.php');
require_once('XhtmlHeader.php');
require_once('html.php');


/**
 * Creates a hidden input field
 * @param $_val field value, or array of multiple values
 */
function xhtmlHidden($_name, $_val = 1)   //XXXX DEPRECATE later (used by phonecafe.se, use XhtmlComponentHidden instead
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
{ /// XXXX DEPRECATE later!!
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
        throw new \Exception ('wierd method '.$method);

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
 * Generates XML tags from an array of values
 *
 * @param $params array with params (Name=>Value) for each tag
 */
function toXmlTags($tagname, $params, $pad_before = '', $pad_after = "\n")
{
    if (!is_array($params))
        throw new \Exception ('toXmlTags need array with params!');

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
