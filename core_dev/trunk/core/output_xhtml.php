<?php
/**
 * $Id$
 *
 * Helper functions for rapid XHTML generation
 *
  * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('locale.php');
require_once('xhtml_header.php');

/**
 * Takes an array of menu entries and creates a <ul><li>-style menu
 */
function xhtmlMenu($menu_arr, $class = 'ulli_menu', $current_class = 'ulli_menu_current')
{
	$cur = basename($_SERVER['SCRIPT_NAME']);

	$res = '<ul class="'.$class.'">';
	foreach ($menu_arr as $url => $text) {
		//FIXME: highlitar inte wiki parametrar t.ex "Wiki:Hello", "Blog:243"
		if ($cur == $url) $res .= '<li class="'.$current_class.'">';
		else $res .= '<li>';

		if ($url) $res .= '<a href="'.xhtmlGetUrl($url).'">'.$text.'</a>';
		else $res .= $text;
		$res .= '</li>';
	}
	$res .= '</ul>';
	return $res;
}

/**
 * Generates "pagers", splitting up listings of content on several pages
 *
 * Reads the 'p' get parameter for current page
 * Example: $pager = makePager(102, 25);		will create a pager for total of 102 items with 25 items per page
 *
 * @return $pager array with some properties filled
 */
function makePager($_total_cnt, $_items_per_page = 0, $_add_value = '')
{
	if (!$_items_per_page) $_items_per_page = $_total_cnt;

	$pager['page'] = 1;
	$pager['items_per_page'] = $_items_per_page;
	if (!empty($_GET['p']) && is_numeric($_GET['p'])) $pager['page'] = $_GET['p'];

	$pager['tot_pages'] = ceil($_total_cnt / $_items_per_page);
	if ($pager['tot_pages'] < 1) $pager['tot_pages'] = 1;
	$pager['head'] = t('Page').' '.$pager['page'].' '.t('of').' '.$pager['tot_pages'].' ('.t('displaying').' '.t('total').' '.$_total_cnt.' '.t('items').')<br/><br/>';

	$pager['index'] = ($pager['page']-1) * $pager['items_per_page'];
	$pager['limit'] = ' LIMIT '.$pager['index'].','.$pager['items_per_page'];

	if ($pager['tot_pages'] <= 1) return $pager;

	if ($pager['page'] > 1) {
		$pager['head'] .= '<a href="'.URLadd('p', $pager['page']-1, $_add_value).'">';
		$pager['head'] .= '<img src="'.coredev_webroot().'gfx/arrow_prev.png" alt="'.t('Previous').'" width="11" height="12"/></a>';
	}
	if ($pager['tot_pages'] <= 10) {
		for ($i=1; $i <= $pager['tot_pages']; $i++) {
			$pager['head'] .= ($i==$pager['page']?'<b>':'').' <a href="'.URLadd('p', $i, $_add_value).'">'.$i.'</a> '.($i==$pager['page']?'</b>':'');
		}
	} else {
		//extended pager for lots of pages

		for ($i=1; $i <= 3; $i++) {
			$pager['head'] .= ($i==$pager['page']?'<b>':'').' <a href="'.URLadd('p', $i, $_add_value).'">'.$i.'</a> '.($i==$pager['page']?'</b>':'');
		}
		if ($pager['page'] > 1) {
			$pager['head'] .= ' ... ';
		}

		if ($pager['page'] >= 2 && $pager['page'] < $pager['tot_pages']) {
			for ($i=$pager['page']-2; $i <= $pager['page']+2; $i++) {
				if ($i > $pager['tot_pages'] || $i == 0 || $i == 1 || $i == 2 || $i == 3
				 || $i == $pager['tot_pages']-2 || $i == $pager['tot_pages']-1
				 || $i == $pager['tot_pages']) continue;
				$pager['head'] .= ($i==$pager['page']?'<b>':'').' <a href="'.URLadd('p', $i, $_add_value).'">'.$i.'</a> '.($i==$pager['page']?'</b>':'');
			}
		}

		if ($pager['page'] < $pager['tot_pages']) {
			$pager['head'] .= ' ... ';
		}
		for ($i=$pager['tot_pages']-2; $i <= $pager['tot_pages']; $i++) {
			$pager['head'] .= ($i==$pager['page']?'<b>':'').' <a href="'.URLadd('p', $i, $_add_value).'">'.$i.'</a> '.($i==$pager['page']?'</b>':'');
		}
	}

	if ($pager['page'] < $pager['tot_pages']) {
		$pager['head'] .= '<a href="'.URLadd('p', $pager['page']+1, $_add_value).'">';
		$pager['head'] .= '<img src="'.coredev_webroot().'gfx/arrow_next.png" alt="'.t('Next').'" width="11" height="12"/></a>';
	}

	$pager['head'] .= '<br/>';
	return $pager;
}

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
 * Creates a select-dropdown from a indexed array
 */
function xhtmlSelectArray($_name, $_arr, $_default = 0, $_onchange = '')
{
	$out = '<select name="'.strip_tags($_name).'"'.($_onchange ? ' onchange="'.$_onchange.'"' : '').'>';

	$out .= '<option value="0">---</option>';	//default to "0" instead of an empty string for "no option selected"

	foreach ($_arr as $id => $title)
		$out .= '<option value="'.$id.'"'.($_default == $id ? ' selected':'').'>'.$title.'</option>';

	$out .= '</select>';

	return $out;
}

/**
 * Creates a hidden input field
 */
function xhtmlHidden($_name, $_val = 1)
{
	$out = '<input type="hidden" name="'.$_name.'" value="'.$_val.'"/>';
	return $out;
}

/**
 * Creates a checkbox
 */
function xhtmlCheckbox($_name, $_title = '', $_val = 1, $_checked = false, $onclick = '')
{
	$out = '';
	if (!$onclick) $out .= xhtmlHidden($_name, 0);
	$out .= '<input type="checkbox" class="checkbox" name="'.$_name.'" value="'.$_val.'" id="lab_'.$_name.'"'.($_checked ? ' checked':'').($onclick ? ' onclick="'.$onclick.'"' : '').'/>';
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
 * Creates a bunch of radio buttons out of an array
 */
function xhtmlRadioArray($_name, $_arr, $_default = '')
{
	$out = '';
	foreach ($_arr as $id => $title) {
		$out .= '<input type="radio" class="radio" name="'.$_name.'" value="'.$id.'" id="lab_'.$id.'"'.($_default == $id ? ' checked' : '').'/>';
		$out .= '<label for="lab_'.$id.'"> '.$title.'</label><br/>';
	}
	return $out;
}

/**
 * Creates a input field
 */
function xhtmlInput($_name, $_value = '', $_size = 0, $_maxlen = 0, $_disabled = false)
{
	if (!is_numeric($_size) || !is_numeric($_maxlen)) return false;
	return '<input type="text" name="'.$_name.'" id="'.$_name.'"'.
		($_value ? ' value="'.$_value.'"' : '').
		($_size ? ' size="'.$_size.'"': '').
		($_maxlen ? ' maxlength="'.$_maxlen.'"': '').
		($_disabled ? ' disabled': '').
		'/>';
}

/**
 * Creates a file upload field
 */
function xhtmlFile($_name)
{
	return '<input type="file" name="'.$_name.'"/>';
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
 */
function xhtmlForm($name = '', $action = '', $method = 'post', $enctype = '')
{
	return '<form action="'.$action.'" method="'.$method.'"'.($name ? ' id="'.$name.'" name="'.$name.'"' : '').($enctype ? ' enctype="'.$enctype.'"' : '').'>';
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
 * Creates a xhtml textarea
 */
function xhtmlTextarea($_name, $_value = '', $_width = 0, $_height = 0)
{
	if (!is_numeric($_width) || !is_numeric($_height)) return false;
	return '<textarea name="'.$_name.'" id="'.$_name.'"'.
		($_width ? ' cols="'.$_width.'"' : '').
		($_height ? ' rows="'.$_height.'"' : '').
		'>'.$_value.'</textarea>';
}

/**
 * Creates a submit button
 */
function xhtmlSubmit($_title = 'Submit', $class = 'button', $style = '')
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
	'<map name="'.$name.'" id="'.(!empty($id)?$id:'xhtmlmap_'.mt_rand(1,9999999)).'">';
	foreach ($shapes as $s) {
		$res .= '<area shape="'.$s['shape'].'" coords="'.implode($s['coords'], ',').'" href="'.$s['href'].'"'.(!empty($s['alt']) ? ' alt="'.$s['alt'].'" title="'.$s['alt'].'"' : '').'/>';
	}
	$res .=
	'</map>';

	return $res;
}

/**
 * Creates a table out of a named array and/or callback function
 *
 * @param $arr is a normal $list array
 * @param $heads is array('User' => 'userId', 'Last active' => 'timeLastActive')
 * @param $callback is funct name to call to customize each row
 */
function xhtmlTable($arr, $heads = '', $callback = '')
{
	$out = '<table>';

	$heads_idx = false;

	if (is_array($heads)) {
		$out .= '<tr>';

		if (key($heads)) $heads_idx = true;
		else $heads_idx = false;

		foreach ($heads as $t => $x) {
			if ($heads_idx) $out .= '<th>'.$t.'</th>';
			else $out .= '<th>'.$x.'</th>';
		}
		$out .= '</tr>';
	}

	$i = 0;
	foreach ($arr as $row) {
		if (function_exists($callback)) {
			$out .= call_user_func($callback, $row, &$i);
			$i++;
		} else if (is_array($heads) && $heads_idx) {
			$out .= '<tr>';
			foreach ($heads as $t => $x) {
				$out .= '<td>';
				if ($x == 'userId') {
					$out .= Users::link($row['userId'], $row['userName']);
				} else {
					$out .= $row[ $x ];
				}
				$out .= '</td>';
			}
			$out .= '</tr>';
		}
	}

	$out .= '</table>';
	return $out;
}

/**
 * Creates select-dropdown menus out of specified category
 */
function xhtmlSelectCategory($_type, $_owner = 0, $selectName = 'default', $selectedId = 0, $url = '', $varName = '', $extra = '')
{
	global $h;
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
		case 'Edit':   $img = 'icon_edit.png'; break;
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
	//FIXME: make path configurable, so user can override core_dev icon set
	$out .= '<img src="'.coredev_webroot().'gfx/'.$img.'" alt="'.$title.'" title="'.$title.'"/>';
	if ($link) $out .= '</a>';
	return $out;
}

/**
 * Implements a OpenSearch compatible search engine
 *
 * @param $url relative link to the script handling searches on the server including search parameter
 *             example: "search.php?s="
 * @param $name name of search engine
 * @param $icon (optional) url to icon resource
 */
function xhtmlOpenSearch($script, $name, $icon = '')
{
	//header('Content-type: application/opensearchdescription+xml');
	header('Content-type: application/xml');

	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">';
		echo '<ShortName>'.$name.'</ShortName>';
		echo '<Description>'.$name.'</Description>';

		if ($icon)
			echo '<Image height="16" width="16" type="image/x-icon">'.xhtmlGetUrl($icon).'</Image>';

		echo '<Url type="text/html" template="'.xhtmlGetUrl($script).'{searchTerms}"/>';

	echo '</OpenSearchDescription>';
}

/**
 * Creates a full url to the currently executed script, only usable in browser sessions
 *
 * @param $script (optional) if unset, returns currently executing script including GET parameters
 */
function xhtmlGetUrl($script = '') //XXX see prop_Url.php for more advanced url manipluation
{
	$default_port = 0;
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		$scheme = 'https';
		$default_port = 443;
	} else {
		$scheme = 'http';
		$default_port = 80;
	}

	$port = '';
	if ($_SERVER['SERVER_PORT'] != $default_port)
		$port = ':'.$_SERVER['SERVER_PORT'];

	if (substr($script, 0, 4) == 'http')
		return $script;

	if (strpos($script, '/') !== false)
		$path = $script;
	else if (substr($script, 0, 1) == '?')
		$path = $_SERVER['PHP_SELF'].$script; // append parameters
	else if ($script) {
		if (dirname($_SERVER['PHP_SELF']) == '/')
			$path = '/'.$script;
		else
			$path = dirname($_SERVER['PHP_SELF']).'/'.$script;
	}
	else {
		$path = $_SERVER['PHP_SELF'];
		if (!empty($_SERVER['QUERY_STRING']))
			$path .= '?'.$_SERVER['QUERY_STRING'];
	}

	$extern_url = $scheme.'://'.$_SERVER['SERVER_NAME'].$port.$path;
	return $extern_url;
}

/**
 * Generates Javascript style arrays
 */
function jsArray($name, $list) {

	$res =
	'<script type="text/javascript">'.
	$name.' = ['."\n";

	foreach ($list as $l)
	{
		$res .= '{ ';

		foreach ($l as $key => $val)
			$res .= $key.': '.(is_numeric($val) ? $val : '"'.$val.'"').', ';

		$res .= '},'."\n";
	}
	$res .=
	'];'.
	'</script>';

	return $res;
}

/**
 * @param $ms reload time in milliseconds (1/1000th second)
 */
function jsReload($ms)
{
	if (!is_numeric($ms)) return false;

	$res =
	'<script type="text/javascript">'.
	'setTimeout("location.reload();", '.$ms.');'.
	'</script>';

	return $res;
}

/**
 * Generates XML tags from an array of values
 *
 * @param $params array with params (Name=>Value) for each tag
 */
function toXmlTags($tagname, $params)
{
	if (!is_array($params)) die('toXmlTags need array with params!');

	$res = '';
	foreach ($params as $p)
	{
		$res .= '<'.$tagname;

		foreach ($p as $param_name => $param_val)
			$res .= ' '.$param_name.'="'.$param_val.'"';

		$res .= '/>'."\n";
	}

	return $res;
}

?>
