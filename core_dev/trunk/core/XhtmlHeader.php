<?php
/**
 * $Id$
 *
 * Generates a XHTML compilant header
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: rewrite "include feeds" functionality

require_once('IXMLComponent.php');
require_once('class.CoreBase.php');
require_once('LocaleHandler.php');
require_once('XmlDocumentHandler.php');  // for relurl()

class XhtmlHeader extends CoreBase implements IXMLComponent
{
    static $_instance;                  ///< singleton class

    private $title;
    private $favicon;

    private $embed_js = array();
    private $embed_css;

    private $include_js    = array();
    private $include_css   = array();
    //private $include_feed = array();

    private $meta_tags = array();
    private $opensearch    = array();

    private $reload_time   = 0;         ///< time after page load to reload the page, in seconds
    private $core_dev_root = '';        ///< web path to core_dev for ajax api calls

    private function __construct() { }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    public function handlePost($p) {}

    function setCoreDevRoot($s) { $this->core_dev_root = $s; }
    function getCoreDevRoot() { return $this->core_dev_root; }

    function setTitle($t) { $this->title = $t; }

    function setFavicon($uri)
    {
        if (substr($uri, 0, 1) != '/')
            $uri = relurl($uri);

        $this->favicon = $uri;
    }

    function setReloadTime($secs) { $this->reload_time = $secs; }

    //function includeFeed($uri) { $this->include_feed[] = $uri; }

    function includeJs($uri)
    {
        if (substr($uri, 0, 1) != '/')
            $uri = relurl($uri);

        $this->include_js[] = $uri;
    }

    function includeCss($uri)
    {
        if (substr($uri, 0, 1) == '/')
            $this->include_css[] = $uri;
        else
            $this->include_css[] = relurl($uri);
    }

    /** CSS snippets to be added inside <head> */
    function embedCss($s) { $this->embed_css .= $s; }

    /**JavaScript snippets to be added to the <body onload=""> tag */
    function embedJs($s) { $this->embed_js[] = $s; }

    function addOpenSearch($uri, $name)
    {
        if (substr($uri, 0, 1) != '/')
            $uri = relurl($uri);

        $this->opensearch[] = array('url' => $uri, 'name' => $name);
    }

    /** Set META tag */
    function setMeta($k, $s) { $this->meta_tags[ $k ] = $s; }

    /** Creates a complete XHTML header, showing rss feeds if available, etc */
    public function render()
    {
        $locale = LocaleHandler::getInstance();

        $res =
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
        '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$locale->getLanguageCode().'" lang="'.$locale->getLanguageCode().'">'.
        '<head>'."\n";

        $res .= '<style type="text/css">';

        foreach ($this->include_css as $css)
            $res .= '@import url('.$css.');';
        $res .= $this->embed_css;

        $res .= '</style>';

        $res .= '<title>'.$this->title.'</title>';

        $res .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';

        foreach ($this->meta_tags as $name => $val)
            $res .= '<meta name="'.$name.'" content="'.$val.'"/>';

/*
        foreach ($this->include_feeds as $feed) {
            //XXX: clean up feed URI's etc, make it more general
            if (!empty($feed['category']) && is_numeric($feed['category'])) $extra = '?c='.$feed['category'];
            else $extra = '';
            $res .= "\t".'<link rel="alternate" type="application/rss+xml" title="'.$feed['title'].'" href="'.$this->core_dev_root.'api/rss_'.$feed['name'].'.php'.$extra.'"/>'."\n";
        }
*/
        foreach ($this->opensearch as $os)
            $res .= '<link rel="search" type="application/opensearchdescription+xml" href="'.$os['url'].'" title="'.$os['name'].'"/>';

        if ($this->favicon)
            $res .= '<link rel="icon" type="image/png" href="'.$this->favicon.'"/>';

        foreach ($this->include_js as $uri)
            $res .= '<script type="text/javascript" src="'.$uri.'"></script>';

        $res .= '</head>';

        $res .= '<body class="yui-skin-sam"'; // required for YUI
        if ($this->embed_js)
            $res .= ' onload="'.implode('', $this->embed_js).'"';
        $res .= '>';

        if ($this->reload_time)
            $res .= js_reload($this->reload_time * 1000);

        $res .= "\n";
        return $res;
    }

}

?>
