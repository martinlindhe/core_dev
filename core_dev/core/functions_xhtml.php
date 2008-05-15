<?php
/**
 * $Id$
 *
 * Helper functions for rapid XHTML generation
 * 
 * \disclaimer This file is a required component of core_dev
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('functions_defaults.php');

/**
 * Creates a complete XHTML header, showing rss feeds if available, etc
 * Uses the following global variables, if they are set:
 *
 * $title			- <title> of current page. set the default title with $config['session']['default_title']		FIXME rename
 * $meta_rss[]		- array of rss feeds to expose for current page
 * $meta_js[]		- array of javascript files that needs to be included for current page
 * $body_onload[] - array of js function(s) to call on load
 */
function createXHTMLHeader()
{
	global $config, $session, $files, $title, $meta_rss, $meta_js, $meta_search, $body_onload;

	if (!$title && !empty($config['default_title'])) $title = $config['default_title'];

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
	echo '<head>';
		echo '<title>'.$title.'</title>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';
		echo '<link rel="stylesheet" href="'.$config['core']['web_root'].'css/core.css" type="text/css"/>';

		if (!empty($config['my_themes'])) $theme_dir = $config['my_themes'];
		else $theme_dir = $config['core']['web_root'].'css/themes/';
		if (!empty($session)) echo '<link rel="stylesheet" href="'.$theme_dir.$session->theme.'" type="text/css"/>';
		echo '<link rel="stylesheet" href="'.$config['app']['web_root'].'css/site.css" type="text/css"/>';

		if ($meta_rss) {
			foreach ($meta_rss as $feed) {
				if (!empty($feed['category']) && is_numeric($feed['category'])) $extra = '?c='.$feed['category'].getProjectPath();
				else $extra = getProjectPath(0);
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
			//fixme: highlitar inte wiki parametrar t.ex "Wiki:Hello", "Blog:243"
			if ($cur == $url) echo '<li class="'.$current_class.'">';
			else echo '<li>';

			if ($url) echo '<a href="'.($url[0] != '/' ? getProjectPath(3) : '').$url.'">'.$text.'</a>';
			else echo $text;
			echo '</li>';
		}
	echo '</ul>';
}

/**
 * Helper function for generating "pagers", splitting up listings of content on several pages
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
 * Helper function to generate select-dropdown menus out of specified category
 */
function getCategoriesSelect($_type, $_owner = 0, $selectName = 'default', $selectedId = 0, $url = '', $varName = '', $extra = '')
{
	global $config;
	if (!is_numeric($_type) || !is_numeric($_owner)) return false;

	$content = '<select name="'.strip_tags($selectName).'">';

	if ($_type == CATEGORY_USERFILE) {
		$content .= '<option value="0" onclick="location.href=\'?file_category_id=0\'">&nbsp;</option>';
	} else {
		$content .= '<option value="0">&nbsp;</option>';
	}

	$shown_global_cats = false;
	$shown_my_cats = false;

	$list = getGlobalAndUserCategories($_type, $_owner);

	foreach ($list as $row) {
		if ($_type != CATEGORY_CONTACT && $_type != CATEGORY_USERDATA && $_type != CATEGORY_NEWS && $_type != CATEGORY_LANGUAGE && !$shown_global_cats && ($row['permissions']&CAT_PERM_GLOBAL) ) {
			$content .= '<optgroup label="'.t('Global categories').'">';
			$shown_global_cats = true;
		}
		if ($_type != CATEGORY_CONTACT && $_type != CATEGORY_USERDATA && $_type != CATEGORY_NEWS && $_type != CATEGORY_LANGUAGE && !$shown_my_cats && ($row['permissions']&CAT_PERM_USER)) {
			$content .= '</optgroup>';
			$content .= '<optgroup label="'.t('Your categories').'">';
			$shown_my_cats = true;
		}

		//If text is formatted like "123|Text" then 123 will be used as value for this option
		$data = explode('|', $row['categoryName']);
		if (!empty($data[1])) {
			$val = $data[0];
			$text = $data[1];
		} else {
			$val = $row['categoryId'];
			$text = $data[0];
		}

		$content .= '<option value="'.$val.'"';
		if ($selectedId == $val) $content .= ' selected="selected"';
		else if ($url) {
			if ($varName) {
				$content .= ' onclick="location.href=\''.$url.'?'.$varName.'='.$row['categoryId'].$extra.'\'"';
			} else {
				$content .= ' onclick="location.href=\''.$url.'='.$row['categoryId'].$extra.'\'"';
			}
		}
		$content .= '>'.$text;
		if ($row['permissions'] & CAT_PERM_PRIVATE) $content .= ' ('.t('Private').')';
		if ($row['permissions'] & CAT_PERM_HIDDEN) $content .= ' ('.t('Hidden').')';
		$content .= '</option>';
	}
	if ($shown_global_cats || $shown_my_cats) $content .= '</optgroup>';

	$content .= '</select>';

	return $content;
}

?>
