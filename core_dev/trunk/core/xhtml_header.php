<?php
/**
 * $Id$
 *
 * Class to generate XHTML compilant header
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('output_xhtml.php');

//XXX: remove usage of getProjectPath

class xhtml_header
{
	var $title       = '';
	var $favicon     = '';
	var $reload_time = 0;    ///< time after page load to reload the page, in seconds
	var $set_mime    = true; ///< set the page mime-type to text/html

	var $feeds  = array();
	var $search = array();
	var $js     = array();
	var $css    = array();
	var $onLoad = array();

	/**
	 * <title> of current page
	 */
	function title($t)
	{
		$this->title = $t;
	}

	/**
	 * Sets URI of favicon to use
	 */
	function favicon($uri)
	{
		$this->favicon = $uri;
	}

	/**
	 * Adds a RSS feed to expose for current page
	 */
	function addFeed($uri)
	{
		$this->feeds[] = $uri;
	}

	/**
	 * Adds a OpenSearch search engine to expose for current page
	 */
	function opensearch($uri)
	{
		$this->search[] = $uri;
	}

	/**
	 * Adds a JS file that needs to be included for current page
	 */
	function js($uri)
	{
		$this->js[] = $uri;
	}

	/**
	 * Adds a CSS file that needs to be included for current page
	 */
	function css($uri)
	{
		$this->css[] = $uri;
	}

	/**
	 * Javascript functions/code to execute on page load
	 */
	function onload($js)
	{
		$this->onLoad[] = $js;
	}

	/**
	 * Adds Javascript to reload the page after a period of time
	 * @param $secs seconds until reload
	 */
	function reload_time($secs)
	{
		$this->reload_time = $secs;
	}

	/**
	 * Creates a complete XHTML header, showing rss feeds if available, etc
	 */
	function render()
	{
		global $config, $h;

		if ($this->set_mime)
			header('Content-type: text/html');

		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
		echo '<head>';
		echo '<title>'.$this->title.'</title>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';

		if (!empty($config['core']['web_root']))
			echo '<link rel="stylesheet" type="text/css" href="'.$config['core']['web_root'].'css/core.css"/>';

		foreach ($this->css as $css)
			echo '<link rel="stylesheet" type="text/css" href="'.$css.'"/>';

		foreach ($this->feeds as $feed) {
			//XXX: clean up feed URI's etc, make it more general
			if (!empty($feed['category']) && is_numeric($feed['category'])) $extra = '?c='.$feed['category'];
			else $extra = '';
			echo "\t".'<link rel="alternate" type="application/rss+xml" title="'.$feed['title'].'" href="'.$config['core']['web_root'].'api/rss_'.$feed['name'].'.php'.$extra.'"/>'."\n";
		}

		foreach ($this->search as $search)
			echo '<link rel="search" type="application/opensearchdescription+xml" href="'.$search['url'].'" title="'.$search['name'].'"/>';

		if ($this->favicon)
			echo '<link rel="icon" type="image/png" href="'.$this->favicon.'"/>';

		$theme_dir = '';
		if (!empty($config['my_themes'])) $theme_dir = $config['my_themes'];
		else if (!empty($config['core']['web_root'])) $theme_dir = $config['core']['web_root'].'css/themes/';

		if (!empty($h->session) && $theme_dir)
			echo '<link rel="stylesheet" type="text/css" href="'.$theme_dir.$h->session->theme.'"/>';

		if (!empty($config['core']['web_root'])) {
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/coredev.js"></script>';
			//echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/swfobject.js"></script>';

			/*
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/ajax.js"></script>';
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/fileareas.js"></script>';
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/chat_1on1.js"></script>';
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/ext/prototype.js"></script>';
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/ext/scriptaculous.js?load=builder,effects,dragdrop,controls,slider"></script>';
			echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/ext/cropper.js"></script>';
			*/

			if (!empty($h->files) && $h->files->allow_rating) //XXX fixme: let files class register this one
				echo '<script type="text/javascript" src="'.$config['core']['web_root'].'js/rate.js"></script>';
		}

		foreach ($this->js as $uri)
			echo '<script type="text/javascript" src="'.$uri.'"></script>';

		echo '</head>';
		if (count($this->onLoad)) {
			echo '<body class="yui-skin-sam" onload="';
			foreach ($this->onLoad as $row)
				echo $row;
			echo '">';
		} else {
			echo '<body class="yui-skin-sam">';
		}

		if (function_exists('getProjectPath') && !empty($config['core']['web_root'])) {
			echo '<script type="text/javascript">';
			echo 'var _ext_ref="'.getProjectPath(2).'",_ext_core="'.$config['core']['web_root'].'api/";';
			echo '</script>';
		}

		if ($this->reload_time) {
			echo '<script type="text/javascript">';
			echo 'setTimeout("location.reload();", '.($this->reload_time*1000).');';
			echo '</script>';
		}

	}

}

?>
