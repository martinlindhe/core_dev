<?php
/**
 * $Id$
 *
 * Generates a XHTML 1.x compilant header
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: ok

//FIXME all code should use registerJsFunction instead of embedJs!!!

require_once('CoreBase.php');
require_once('IXmlComponent.php');
require_once('XmlDocumentHandler.php');  // for relurl()
require_once('output_js.php');

class FeedDescription
{
    var $title;
    var $url;
}

class OpenSearchDescription extends FeedDescription { }

class MetaDescription
{
    var $name;
    var $val;
}

class XhtmlHeader extends CoreBase implements IXmlComponent
{
    static $_instance;                     ///< singleton class

    protected $title;
    protected $favicon;

    protected $embed_js        = array();
    protected $embed_js_onload = array();
    protected $embed_css;

    protected $include_js      = array();
    protected $include_css     = array();
    protected $include_feed    = array();

    protected $js_functions    = array();

    protected $meta_tags       = array();
    protected $opensearch      = array();

    protected $reload_time     = 0;        ///< time after page load to reload the page, in seconds

    private function __construct() { }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    public function handlePost($p) {}

    function getFavicon() { return $this->favicon; }

    function setTitle($t) { $this->title = $t; }

    function setFavicon($uri)
    {
        if (substr($uri, 0, 1) != '/')
            $uri = relurl($uri);

        $this->favicon = $uri;
    }

    function setReloadTime($secs) { $this->reload_time = $secs; }

    /** Adds a js file include to the header */
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

    function includeFeed($url, $title = '')
    {
        $o = new FeedDescription();
        $o->title = $title;
        $o->url   = $url;

        $this->include_feed[] = $o;
    }

    function includeOpenSearch($url, $title)
    {
        if (substr($url, 0, 1) != '/')
            $uri = relurl($url);

        $o = new OpenSearchDescription();
        $o->title = $title;
        $o->url   = $url;

        $this->opensearch[] = $o;
    }

    /** Registers a javascript function */
    function registerJsFunction($code)
    {
        $code = trim($code);
        if (substr($code, 0, 9) != 'function ')
            throw new Exception ('wierd code: '.$code);

        $tmp = substr($code, 9);
        $fn = explode('(', $tmp);

        // detect duplicate function names
        if (isset($this->js_functions[$fn[0]]) && $this->js_functions[$fn[0]] != $code)
            throw new Exception ('XXX js function with different code already defined: '.$fn[0]);

        $this->js_functions[$fn[0]] = $code;
    }

    /** CSS snippets to be added inside <head> */
    function embedCss($s = '') { $this->embed_css .= $s; }

    /** JavaScript snippets to be added inside <head> (js functions is available before page load event is completed) */
    function embedJs($s) { $this->embed_js[] = $s; }

    /** JavaScript snippets to be added to the <body onload=""> tag (js code will execute when page loads) */
    function embedJsOnload($s) { $this->embed_js_onload[] = $s; }

    /** Set META tag */
    function setMeta($name, $val)
    {
        // TODO: see if meta tag already exists
        $o = new MetaDescription();
        $o->name = $name;
        $o->val  = $val;
        $this->meta_tags[] = $o;
    }

    /** Detects User Agent and adjusts output accordingly */
    private function adjustApperance()
    {
        // XXX maybe this is useless?

        // XXX TODO use HttpUserAgent to detect browsers... class dont detect mobile browsers yet
/*
        // ios, android
        $this->setMeta('viewport', 'width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;');

        // also supported by Android devices running software version 1.5 (Cupcake) or newer:
        //<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

        // enable full-screen mode
        $this->setMeta('apple-mobile-web-app-capable', 'yes');

        // controls the appearance of the status bar in full-screen mode
        $this->setMeta('apple-mobile-web-app-status-bar-style', 'black');
*/
    }

    public function render()
    {
        $this->adjustApperance();

        $res = '<head>';

        if ($this->title)
            $res .= '<title>'.$this->title.'</title>';

        $res .= '<meta http-equiv="content-type" content="text/html;charset=utf-8"/>';

        foreach ($this->meta_tags as $o)
            $res .= '<meta name="'.$o->name.'" content="'.$o->val.'"/>';

        foreach ($this->include_css as $css)
            $res .= '<link rel="stylesheet" href="'.$css.'"/>';

        if ($this->favicon)
            $res .= '<link rel="icon" type="'.file_get_mime_by_suffix($this->favicon).'" href="'.$this->favicon.'"/>';

        foreach ($this->include_js as $uri)
            $res .= '<script type="text/javascript" src="'.$uri.'"></script>';

        foreach ($this->include_feed as $o)
            $res .= '<link rel="alternate" type="application/rss+xml" href="'.$o->url.'" title="'.$o->title.'"/>';

        foreach ($this->opensearch as $o)
            $res .= '<link rel="search" type="application/opensearchdescription+xml" href="'.$o->url.'" title="'.$o->title.'"/>';

        // margin and padding on body element can introduce errors in determining element position and are not recommended
        // height:100% is needed for google maps js widget
        $res .= '<style type="text/css">'.
        'html{'.
            'height:100%'.
        '}'.
        'body{'.
            'height:100%;'.
            'margin:0;'.
            'padding:0'.
        '}'.
        $this->embed_css.
        '</style>';

        $js = '';

        if ($this->embed_js)
            $js .= implode('', $this->embed_js);

        if ($this->js_functions)
            foreach ($this->js_functions as $key => $val)
                $js .= $val;

        if ($js)
            $res .= js_embed($js);

        $res .= '</head>';

        $res .= '<body class="yui-skin-sam"'; // required for YUI
        if ($this->embed_js_onload)
            $res .= ' onload="'.implode('', $this->embed_js_onload).'"';
        $res .= '>'."\n";

        if ($this->reload_time)
            $res .= js_reload($this->reload_time * 1000);

        return $res;
    }

}

?>
