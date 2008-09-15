<?php
/**
 * $Id$
 *
 * Helper functions for rapid XHTML generation
 *
 * \disclaimer This file is a required component of core_dev
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * Creates a complete XHTML header, showing rss feeds if available, etc
 * Uses the following global variables, if they are set:
 *
 * $title			- <title> of current page. set the default title with $config['session']['default_title']		FIXME rename
 * $meta_rss[]		- array of rss feeds to expose for current page
 * $meta_js[]		- array of javascript files that needs to be included for current page
 * $meta_css[]		- array of css files that needs to be included for current page
 * $body_onload[]	- array of js function(s) to call on load
 */
function createXHTMLHeader()
{
	global $config, $session, $files, $title, $meta_rss, $meta_js, $meta_css, $meta_search, $body_onload;

	if (!$title && !empty($config['default_title'])) $title = $config['default_title'];

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
	echo '<head>';
	echo '<title>'.$title.'</title>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';

	echo '<link rel="stylesheet" type="text/css" href="'.$config['core']['web_root'].'css/core.css"/>';

	if ($meta_css) {
		foreach ($meta_css as $css) {
			echo '<link rel="stylesheet" type="text/css" href="'.$css.'"/>';
		}
	}

	if (!empty($config['my_themes'])) $theme_dir = $config['my_themes'];
	else $theme_dir = $config['core']['web_root'].'css/themes/';
	if (!empty($session)) echo '<link rel="stylesheet" type="text/css" href="'.$theme_dir.$session->theme.'"/>';

	if ($meta_rss) {
		foreach ($meta_rss as $feed) {
			if (!empty($feed['category']) && is_numeric($feed['category'])) $extra = '?c='.$feed['category'];
			else $extra = '';
			echo "\t".'<link rel="alternate" type="application/rss+xml" title="'.$feed['title'].'" href="'.$config['core']['web_root'].'api/rss_'.$feed['name'].'.php'.$extra.'"/>'."\n";
		}
	}
	if ($meta_search) {
		foreach ($meta_search as $search) {
			echo '<link rel="search" type="application/opensearchdescription+xml" href="'.$search['url'].'" title="'.$search['name'].'"/>';
		}
	}

	//echo '<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>';
	echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/ajax.js"></script>';
	echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/swfobject.js"></script>';
	echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/functions.js"></script>';
	echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/fileareas.js"></script>';
		echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/chat_1on1.js"></script>';

	echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/ext/prototype.js"></script>';
	echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/ext/scriptaculous.js?load=builder,effects,dragdrop,controls,slider"></script>';

	echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/ext/cropper.js"></script>';
	if (!empty($files) && $files->allow_rating) {
		echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/rate.js"></script>';
	}

	if ($meta_js) {
		foreach ($meta_js as $script) {
			echo '<script type="text/javascript" src="'.$script.'"></script>';
		}
	}

	echo '</head>';
	if ($body_onload) {

		echo '<body onload="';
		foreach ($body_onload as $row) {
			echo $row;
		}
		echo '">';
	} else {
		echo '<body>';
	}
	echo '<script type="text/javascript">';
	echo 'var _ext_ref="'.getProjectPath(2).'",_ext_core="'.$config['core']['web_root'].'api/";';
	echo '</script>';
}

/**
 * Takes an array of menu entries and creates a <ul><li>-style menu
 */
function createMenu($menu_arr, $class = 'ulli_menu', $current_class = 'ulli_menu_current')
{
	$cur = basename($_SERVER['SCRIPT_NAME']);

	echo '<ul class="'.$class.'">';
		foreach($menu_arr as $url => $text) {

			//if ($cur == $url || isset($_GET[str_replace('?','',$url)])) echo '<li class="'.$current_class.'">';
			//FIXME: highlitar inte wiki parametrar t.ex "Wiki:Hello", "Blog:243"
			if ($cur == $url) echo '<li class="'.$current_class.'">';
			else echo '<li>';

			if ($url) echo '<a href="'.($url[0] != '/' ? getProjectPath(3) : '').$url.'">'.$text.'</a>';
			else echo $text;
			echo '</li>';
		}
	echo '</ul>';
}

/**
 * Helper to generate "pagers", splitting up listings of content on several pages
 *
 * Reads the 'p' get parameter for current page
 * Example: $pager = makePager(102, 25);		will create a pager for total of 102 items with 25 items per page
 *
 * \return Returns a $pager array with some properties filled
 */
function makePager($_total_cnt, $_items_per_page, $_add_value = '')
{
	global $config;

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
		$pager['head'] .= '<img src="'.$config['core']['web_root'].'gfx/arrow_prev.png" alt="'.t('Previous').'" width="11" height="12"/></a>';
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
		$pager['head'] .= '<img src="'.$config['core']['web_root'].'gfx/arrow_next.png" alt="'.t('Next').'" width="11" height="12"/></a>';
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
	for ($i = $_min; $i <= $_max; $i += $_skip) {
		$out .= '<option value="'.$i.'">'.$i.'</option>';
	}
	$out .= '</select>';

	return $out;
}

/**
 * Creates a select-dropdown from a indexed array
 */
function xhtmlSelectArray($_name, $_arr, $_default = 0, $_onchange = '')
{
	$out = '<select name="'.strip_tags($_name).'"'.($_onchange ? ' onchange="'.$_onchange.'"' : '').'>';
	foreach ($_arr as $id => $title) {
		$out .= '<option value="'.$id.'"'.($_default == $id ? ' selected':'').'>'.$title.'</option>';
	}
	$out .= '</select>';

	return $out;
}

/**
 * Helper to create a hidden input field
 */
function xhtmlHidden($_name, $_val)
{
	$out = '<input type="hidden" name="'.$_name.'" value="'.$_val.'"/>';
	return $out;
}

/**
 * Helper to create a checkbox
 */
function xhtmlCheckbox($_name, $_title = '', $_val = 1, $_checked = false)
{
	$out = xhtmlHidden($_name, 0);
	$out .= '<input type="checkbox" class="checkbox" name="'.$_name.'" value="'.$_val.'" id="lab_'.$_name.'"'.($_checked ? ' checked':'').'/>';
	if ($_title) $out .= '<label for="lab_'.$_name.'"> '.$_title.'</label>';

	return $out;
}

/**
 * Helper to create a bunch of checkboxes out of an array
 */
function xhtmlCheckboxArray($_arr, $all_checked = false)
{
	$out = '';
	foreach ($_arr as $id => $title) {
		$out .= '<input type="checkbox" class="checkbox" name="'.$id.'" value="1" id="lab_'.$id.'"'.($all_checked ? ' checked':'').'/>';
		$out .= '<label for="lab_'.$id.'"> '.$title.'</label><br/>';
	}
	return $out;
}

/**
 * Helper to create a bunch of radio buttons out of an array
 */
function xhtmlRadioArray($_name, $_arr, $_default = '')
{
	$out = '';
	foreach ($_arr as $id => $title) {
		$out .= '<input type="radio" class="radio" name="'.$_name.'" value="'.$id.'" id="lab_'.$id.'"'.
			($_default == $id ? ' checked' : '').'/>';
		$out .= '<label for="lab_'.$id.'"> '.$title.'</label><br/>';
	}
	return $out;
}

/**
 * Helper to create a input field
 */
function xhtmlInput($_name, $_value = '', $_size = 0, $_maxlen = 0)
{
	if (!is_numeric($_size) || !is_numeric($_maxlen)) return false;
	return '<input type="text" name="'.$_name.'"'.
		($_value ? ' value="'.$_value.'"' : '').
		($_size ? ' size="'.$_size.'"': '').
		($_maxlen ? ' maxlength="'.$_maxlen.'"': '').
		'/>';
}

/**
 * Helper to create a file upload field
 */
function xhtmlFile($_name)
{
	return '<input type="file" name="'.$_name.'"/>';
}


/**
 * Helper to create a <form> open tag
 */
function xhtmlForm($name = '', $action = '', $method = 'post', $enctype = '')
{
	return '<form action="'.$action.'" method="'.$method.'"'.($name ? ' name="'.$name.'"' : '').($enctype ? ' enctype="'.$enctype.'"' : '').'>';
}

/**
 * This one is kinda stupid, but added for consistency
 */
function xhtmlFormClose()
{
	return '</form>';
}

/**
 * Helper to create a password input field
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

function xhtmlTextarea($_name, $_value = '', $_width = 0, $_height = 0)
{
	if (!is_numeric($_width) || !is_numeric($_height)) return false;
	return '<textarea name="'.$_name.'"'.
		($_width ? ' cols="'.$_width.'"' : '').
		($_height ? ' rows="'.$_height.'"' : '').
		'>'.$_value.'</textarea>';
}

/**
 * Helper to create a submit button
 */
function xhtmlSubmit($_title, $class = 'button', $style = '')
{
	return '<input type="submit" value="'.t($_title).'"'.($class ? ' class="'.$class.'"' : '').($style ? ' style="'.$style.'"' : '').'/>';
}

/**
 * Helper to create a submit button
 */
function xhtmlButton($_title, $onclick = '')
{
	return '<input type="button" class="button" value="'.t($_title).'"'.($onclick ? ' onclick="'.$onclick.'"' : '').'/>';
}

/**
 * Helper to create a table out of a named array and/or callback function
 *
 * \param $arr is a usual named array
 * \param $heads is array('Användare' => 'userId', 'Senast aktiv' => 'timeLastActive')
 * \param $callback is funct name to call to customize each row
 */
function xhtmlTable($arr, $heads = '', $callback = '')
{
	$out = '<table>';
	if (is_array($heads)) {
		$out .= '<tr>';
		foreach ($heads as $t => $x) {
			$out .= '<th>'.$t.'</th>';
		}
		$out .= '</tr>';
	}

	$i = 0;
	foreach ($arr as $row) {
		if (function_exists($callback)) {
			$out .= call_user_func($callback, $row, &$i);
			$i++;
		} else if (is_array($heads)) {
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
 * Helper to generate select-dropdown menus out of specified category
 */
function getCategoriesSelect($_type, $_owner = 0, $selectName = 'default', $selectedId = 0, $url = '', $varName = '', $extra = '')
{	//FIXME: rename to xhtmlSelectCategory()
	global $config, $files;
	if (!is_numeric($_type) || !is_numeric($_owner)) return false;

	$out = '<select name="'.strip_tags($selectName).'">';

	if ($_type == CATEGORY_USERFILE) {
		if ($files->allow_root_level) {
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
 * Helper to display one of core_dev's default action buttons
 */
function coreButton($name, $dst = '')
{
	global $config;

	switch ($name) {
		case 'Edit': $src = ''; break;
		case 'Create': $src = ''; break;
		case 'Delete': $src = 'icon_delete.png'; break;

		default:
			echo '<h1>ERROR unknown coreButton '.$name.'</h1>';
			return;
	}

	$out = '';
	if ($dst) $out .= '<a href="'.$dst.'">';
	//FIXME: make path configurable, so user can override core_dev icon set
	$out .= '<img src="'.$config['core']['web_root'].'gfx/'.$src.'" alt="'.t($name).'" title="'.t($name).'"/>';
	if ($dst) $out .= '</a>';
	return $out;
}


/**
 * Implements a OpenSearch compatible search engine
 *
 */
function xhtmlOpenSearch($url, $name, $icon_url = '')
{
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">';
		echo '<ShortName>'.$name.'</ShortName>';
		echo '<Description>'.$name.'</Description>';

		if ($icon_url) {
			echo '<Image height="16" width="16" type="image/x-icon">'.$icon_url.'</Image>';
		}

		echo '<Url type="text/html" template="'.$url.'{searchTerms}"/>';

		//FIXME: implement search suggestion support:
		//echo	'<Url type="application/x-suggestions+json" method="get" template="http://en.wikipedia.org/w/api.php?action=opensearch&amp;search={searchTerms}&amp;namespace=0"/>';
	echo '</OpenSearchDescription>';
}
?>
