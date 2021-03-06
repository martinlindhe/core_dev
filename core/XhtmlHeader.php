<?php
/**
 * $Id$
 *
 * Generates a HTML header
 *
 * @author Martin Lindhe, 2009-2013 <martin@ubique.se>
 */

//STATUS: ok

namespace cd;

require_once('CoreBase.php');
require_once('IXmlComponent.php');
require_once('XmlDocumentHandler.php');  // for relurl()
require_once('Css3FontFace.php');
require_once('html.php');
require_once('HttpUserAgent.php');

class MetaDescription
{
    var $name;
    var $val;
}

class LinkRel
{
    var $rel_type;
    var $href;
    var $mime_type;
    var $title;
}

class XhtmlHeader extends CoreBase implements IXmlComponent
{
    static $_instance;                      ///< singleton class

    protected $title;
    protected $embed_js        = array();
    protected $embed_js_onload = array();
    protected $embed_css       = '';
    protected $css_define      = array();   ///< defines css rules
    protected $include_js      = array();
    protected $include_js_last = array();   ///< HACK to enable to include js after some js snippet has been embedded
    protected $js_functions    = array();
    protected $meta_tags       = array();
    protected $rel             = array();   ///< <link rel=""> tags for external resources: css, icon, rss, opensearch
    protected $reload_time     = 0;         ///< time after page load to reload the page, in seconds

    private function __construct() { }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /** Adds a <meta name="" content=""> tag */
    public function setMeta($name, $val)
    {
        $o = new MetaDescription();
        $o->name = $name;
        $o->val  = $val;
        $this->meta_tags[] = $o;
    }

    /** Adds a <link rel="" href=""> tag */
    public function addRel($rel_type, $href, $mime_type = '', $title = '')
    {
        $o = new LinkRel();
        $o->rel_type  = $rel_type;
        $o->href      = $href;
        $o->mime_type = $mime_type;
        $o->title     = $title;
        $this->rel[] = $o;
    }

    public function setTitle($t)
    {
        $this->title = $t;
    }

    public function setReloadTime($secs)
    {
        $this->reload_time = $secs;
    }

    /** Adds a js file include to the header */
    public function includeJs($uri)
    {
        if (substr($uri, 0, 1) != '/')
            $uri = relurl($uri);

        // dont include the same js multiple times
        if (in_array($uri, $this->include_js))
            return false;

        $this->include_js[] = $uri;
    }

    /** Adds a js file include to the end of the header */
    public function includeJsLast($uri)
    {
        if (substr($uri, 0, 1) != '/')
            $uri = relurl($uri);

        $this->include_js_last[] = $uri;
    }

    public function includeCss($uri)
    {
        if (substr($uri, 0, 1) != '/')
            $uri = relurl($uri);

        $this->addRel('stylesheet', $uri, 'text/css');
    }

    public function includeFeed($url, $title = '')
    {
        $this->addRel('alternate', $url, 'application/rss+xml', $title);
    }

    public function includeOpenSearch($url, $title)
    {
        if (substr($url, 0, 1) != '/')
            $uri = relurl($url);

        $this->addRel('search', $url, 'application/opensearchdescription+xml', $title);
    }

    public function setFavicon($uri)
    {
        if (substr($uri, 0, 1) != '/')
            $uri = relurl($uri);

        $this->addRel('icon', $uri, file_get_mime_by_suffix($uri));
    }

    /** Registers a javascript function (avoids double definitions)  */
    public function registerJsFunction($code)
    {
        $code = trim($code);
        if (substr($code, 0, 9) != 'function ')
            throw new \Exception ('wierd code: '.$code);

        $tmp = substr($code, 9);
        $fn = explode('(', $tmp);

        // detect duplicate function names
        if (isset($this->js_functions[$fn[0]]))
            if ($this->js_functions[$fn[0]] != $code)
                throw new \Exception ('js function with different code already defined: '.$fn[0]);
            else
                // dont double-embed identical functions
                return false;

        $this->js_functions[$fn[0]] = $code;
        return true;
    }

    /** CSS to be added inside <head> */
    public function embedCss($s)
    {
        $this->embed_css .= $s;
    }

    /** Registers a css block (avoids double definitions) */
    public function registerCss($code)
    {
        $code = trim($code);

        $tmp = substr($code, 9);
        $fn = explode('{', $tmp);

        // detect duplicate rule names
        if (isset($this->css_define[$fn[0]]))
            if ($this->css_define[$fn[0]] != $code)
                throw new \Exception ('css define with different code already defined: '.$fn[0]);
            else
                // dont double-embed identical rules
                return false;

        $this->css_define[$fn[0]] = $code;
        return true;
    }

    /** JavaScript to be added inside <head> (js functions is available before page load event is completed) */
    public function embedJs($s)
    {
        $this->embed_js[] = $s;
    }

    /** JavaScript to run when page loaded DOM event fires */
    public function embedJsOnload($s)
    {
        $this->embed_js_onload[] = $s;
    }

    public function render()
    {
        $res = '<head>';

        if ($this->title)
            $res .= '<title>'.$this->title.'</title>';

        // according to http://code.google.com/p/doctype-mirror/wiki/MetaCharsetAttribute, all major browsers support the short-form <meta charset>
        $res .= '<meta charset="utf-8"/>';
//        $res .= '<meta http-equiv="content-type" content="text/html;charset=utf-8"/>';   // XHTML 1.0, "long form"

        foreach ($this->meta_tags as $o)
            $res .= '<meta name="'.$o->name.'" content="'.$o->val.'"/>';

        foreach ($this->include_js as $uri)
            $res .= '<script type="text/javascript" src="'.$uri.'"></script>';

        foreach ($this->rel as $o)
            $res .=
                '<link rel="'.$o->rel_type.'"'.
                ' href="'.$o->href.'"'.
                ($o->mime_type ? ' type="'.$o->mime_type.'"' : '').
                ($o->title ? ' title="'.$o->title.'"' : '').
                '/>';

        $css = '';
        if ($this->embed_css)
            $css .= $this->embed_css;

        if ($this->css_define)
            foreach ($this->css_define as $key => $val)
                $css .= $val;

        if ($css)
            $res .= css_embed($css);

        $js = '';

        if ($this->js_functions)
            foreach ($this->js_functions as $key => $val)
                $js .= $val;

        if ($this->embed_js)
            $js .= implode('', $this->embed_js);

        if ($this->embed_js_onload)
            $js .=
            'window.onload=function()'.
            '{'.
                implode('', $this->embed_js_onload).
            '}';

        if ($js)
            $res .= js_embed($js);

        foreach ($this->include_js_last as $uri)
            $res .= '<script type="text/javascript" src="'.$uri.'"></script>';

        $res .= '</head>'."\n";

        $res .= '<body class="yui-skin-sam">'."\n"; // required for YUI

        if ($this->reload_time)
            $res .= js_reload($this->reload_time * 1000);

        return $res;
    }

}
