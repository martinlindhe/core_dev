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
	private $title, $favicon;
	private $reload_time = 0;          ///< time after page load to reload the page, in seconds
	private $mimetype   = 'text/html';

	private $js, $css, $feeds, $search, $onload;

	function __construct()
	{
		$this->js     = array();
		$this->css    = array();
		$this->feeds  = array();
		$this->search = array();
		$this->onload = array();
	}

	function setTitle($t) { $this->title = $t; }
	function setFavicon($uri) { $this->favicon = $uri; }
	function setReloadTime($secs) { $this->reload_time = $secs; }
	function setMimeType($type) { $this->mimetype = $mime; }

	function addFeed($uri) { $this->feeds[] = $uri; }
	function addOpensearch($uri) { $this->search[] = $uri; }
	function addJs($uri) { $this->js[] = $uri; }
	function addCss($uri) { $this->css[] = $uri; }
	function addOnload($js) { $this->onload[] = $js; }

	/**
	 * Creates a complete XHTML header, showing rss feeds if available, etc
	 */
	function render()
	{
		global $config, $h;

		if ($this->mimetype)
			header('Content-type: '.$this->mimetype);

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
		if (count($this->onload)) {
			echo '<body class="yui-skin-sam" onload="';
			foreach ($this->onload as $row)
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
