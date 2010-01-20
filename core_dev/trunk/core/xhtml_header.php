<?php
/**
 * $Id$
 *
 * Generates a XHTML compilant header
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

require_once('core.php');
require_once('output_xhtml.php');

//STATUS: ok
//XXX: remove usage of getProjectPath

interface CoreHeader
{
	public function render();
}

class xhtml_header implements CoreHeader
{
	private $title, $favicon;
	private $js            = array();
	private $css           = array();
	private $feeds         = array();
	private $search        = array();
	private $onload        = array();
	private $keywords      = array();

	private $reload_time   = 0;          ///< time after page load to reload the page, in seconds
	private $mimetype      = 'text/html';
	private $core_dev_root = '';         ///< web path to core_dev for ajax api calls

	function __construct()
	{
		$this->core_dev_root = coredev_webroot();
	}

	function setTitle($t) { $this->title = $t; }
	function setFavicon($uri) { $this->favicon = $uri; }
	function setReloadTime($secs) { $this->reload_time = $secs; }
	function setMimeType($type) { $this->mimetype = $mime; }

	function addFeed($uri) { $this->feeds[] = $uri; }
	function addJs($uri) { $this->js[] = $uri; }
	function addCss($uri) { $this->css[] = $uri; }
	function addOnload($js) { $this->onload[] = $js; }

	function addOpenSearch($uri, $name = 'Search box')
	{
		$arr = array('url' => $uri, 'name' => $name);
		$this->search[] = $arr;
	}

	/**
	 * Adds META keywords tags
	 */
	function addKeyword($w)
	{
		if (is_array($w))
			foreach ($w as $t)
				$this->keywords[] = $t;
		else
			$this->keywords[] = $w;
	}

	/**
	 * Creates a complete XHTML header, showing rss feeds if available, etc
	 */
	public function render()
	{
		global $h;

		if ($this->mimetype)
			header('Content-type: '.$this->mimetype);

		$res =
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
		"\n".
		'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'.
		'<head>'.
		'<title>'.$this->title.'</title>'.
		'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';

		if ($this->keywords)
			$res .= '<meta name="keywords" content="'.implode(',',$this->keywords).'"/>';

		$res .= '<link rel="stylesheet" type="text/css" href="'.$this->core_dev_root.'css/core.css"/>';

		foreach ($this->css as $css)
			$res .= '<link rel="stylesheet" type="text/css" href="'.$css.'"/>';

		foreach ($this->feeds as $feed) {
			//XXX: clean up feed URI's etc, make it more general
			if (!empty($feed['category']) && is_numeric($feed['category'])) $extra = '?c='.$feed['category'];
			else $extra = '';
			$res .= "\t".'<link rel="alternate" type="application/rss+xml" title="'.$feed['title'].'" href="'.$this->core_dev_root.'api/rss_'.$feed['name'].'.php'.$extra.'"/>'."\n";
		}

		foreach ($this->search as $search)
			$res .= '<link rel="search" type="application/opensearchdescription+xml" href="'.$search['url'].'" title="'.$search['name'].'"/>';

		if ($this->favicon)
			$res .= '<link rel="icon" type="image/png" href="'.$this->favicon.'"/>';

		//XXX: make theme path configurable
		$theme_dir = $this->core_dev_root.'css/themes/';

		if (!empty($h->session) && $theme_dir)
			$res .= '<link rel="stylesheet" type="text/css" href="'.$theme_dir.$h->session->theme.'"/>';

		$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/coredev.js"></script>';
		//$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/swfobject.js"></script>';

		/*
		$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/ajax.js"></script>';
		$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/fileareas.js"></script>';
		$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/chat_1on1.js"></script>';
		$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/ext/prototype.js"></script>';
		$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/ext/scriptaculous.js?load=builder,effects,dragdrop,controls,slider"></script>';
		$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/ext/cropper.js"></script>';
		*/

		if (!empty($h->files) && $h->files->allow_rating) //XXX fixme: let files class register this one
			$res .= '<script type="text/javascript" src="'.$this->core_dev_root.'js/rate.js"></script>';

		foreach ($this->js as $uri)
			$res .= '<script type="text/javascript" src="'.$uri.'"></script>';

		$res .= '</head>';

		$res .= '<body class="yui-skin-sam"'; // required for YUI
		if (count($this->onload)) {
			$res .= ' onload="';
			foreach ($this->onload as $onload)
				$res .= $onload;
		}
		$res .= '">';

		$res .= '<script type="text/javascript">';
		//XXX rename _ext_core to _core_api since its url to coredev api
		$res .= 'var _ext_ref="'.getProjectPath(2).'",_ext_core="'.$this->core_dev_root.'api/";';
		$res .= '</script>';

		if ($this->reload_time)
			$res .= jsReload($this->reload_time * 1000);

		$res .= "\n";
		return $res;
	}

}

?>
