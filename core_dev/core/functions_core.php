<?php
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

require_once('functions_locale.php');	//for translations

	/**
	 * Debug function. Prints out variable $v
	 *
	 * \param $v variable of any type to display
	 * \return nothing
   */
	function d($v)
	{
		if (is_string($v)) echo htmlentities($v);
		else {
			if (extension_loaded('xdebug')) var_dump($v);	//xdebug's var_dump is awesome
			else {
				echo '<pre>';
				print_r($v);
				echo '</pre>';
			}
		}
	}

	/**
	 * Debug function. Prints $m to Apache log file
	 */
	function dp($m)
	{
		global $config;

		error_log($m);
		if (!empty($config['debug'])) {
			error_log($m, 3, '/var/tmp/core_dev.log');
		}
	}

	/**
	 * Helper function to include core function files
	 *
	 * \param $file filename to include
	 */
	function require_core($file)
	{
		global $config;
		require_once($config['core']['fs_root'].'core/'.$file);
	}

	/* loads all active plugins */
	function loadPlugins()
	{
		global $config;

		if (empty($config['plugins'])) return;

		foreach ($config['plugins'] as $plugin) {
			require_once($config['core']['fs_root'].'plugins/'.$plugin.'/plugin.php');
		}
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

	/* Returns the project's path as a "project name" identifier. in a webroot hierarchy if scripts are
			run from the / path it will return nothing, else the directory name of the directory script are run from */
	function getProjectPath($_amp = 1)
	{
		global $config;

		if ($_amp == 3) return $config['app']['web_root'];

		if (!empty($_GET['pr'])) {
			$proj_name = basename(strip_tags($_GET['pr']));
		} else {
			$project_path = dirname($_SERVER['SCRIPT_NAME']);
			$pos = strrpos($project_path, '/');
			$proj_name = substr($project_path, $pos+1);
		}
		
		if ($proj_name == 'admin') $proj_name = '';

		if ($proj_name) {
			switch ($_amp) {
				case 0: return '?pr='.$proj_name;
				case 1: return '&amp;pr='.$proj_name;
				case 2: return '&pr='.$proj_name;
			}
		}
		return '';
	}
?>
