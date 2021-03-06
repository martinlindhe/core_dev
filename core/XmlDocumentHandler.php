<?php
/**
 * $Id$
 *
 * Renders a set of views into a XML document (XHTML, HTML5)
 *
 * @author Martin Lindhe, 2010-2013 <martin@ubique.se>
 */

//STATUS: wip

//XXX: move facebook stuff out of here?

namespace cd;

require_once('CoreBase.php');
require_once('LocaleHandler.php');
require_once('Url.php');

class XmlDocumentHandler extends CoreBase
{
    static $_instance;                       ///< singleton

    protected $html_mode     = 'xhtml';      ///< Defaults to XHTML 1.0 Transitional

    private $design_head;
    private $design_foot;
    private $enable_design          = true;
    private $enable_html_headers    = true;  ///< send HTML headers?
    private $enable_http_headers    = true;  ///< send http headers?
    private $enable_profiler        = false; ///< embed page profiler?
    private $allow_internal_framing = false; ///< allow this document to be framed using <frame> or <iframe> from same origin
    private $allow_external_framing = false; ///< allow this document to be framed by any origin
    private $cache_duration  = 0;            ///< seconds to allow browser client to cache this result
    private $mimetype;
    private $Url;                            ///< Url object
    private $attachment_name;                ///< name of file attachment (force user to save file)
    private $inline_name;                    ///< name of inlined file (will set correct name if user chooses to save file)
    private $coredev_inc;                    ///< if set, points to "/path/to/core_dev/core/"   XXXX move to own handler class?
    private $coredev_root;                   ///< web path to core_dev for ajax api calls
    private $upload_root;                    ///< root directory for file uploads
    private $app_root;                       ///< application root directory, currently only used to locate favicon.png for auto conversion to favicon.ico
    private $ts_initial;                     ///< used to measure page load time
    private $language_code = 'sv';           /// FIXME make this unset by default & force it to be set in config.php

    private $xmlns = array();                ///< registered XML namespaces

    private $objs = array();                 ///< IXmlComponent objects

    private function __clone() {}            ///< singleton: prevent cloning of class

    private function __construct()           ///< singleton
    {
        $this->Url = new Url('http://XmlDocumentHandler.url.default');
        $this->ts_initial = microtime(true);
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /** Registers a XML namespace for definition in the <html> tag */
    function registerXmlNs($ns, $uri)
    {
        $this->xmlns[ $ns ] = $uri;
    }

    /**
     * Sets base/root URL for current website
     * @param $s base url, e.g. http://www.example.com/  (with ending / )
     */
    function setUrl($s)
    {
        $this->Url = new Url($s);

        if (!$this->coredev_root)
            $this->setRelativeCoreDevUrl('core_dev/');
    }

    public function setHtmlMode($s)
    {
        $this->html_mode = $s;
    }

    /** @return relative URL for current website */
    function getRelativeUrl() { return $this->Url->getPath(); }

    /** @return full base/root URL to website */
    function getUrl() { return $this->Url->get(); }

    /** @return domain name part of base URL to website */
    function getHostName() { return $this->Url->getHost(); }

    /** @return "http" or "https" */
    function getScheme() { return $this->Url->getScheme(); }

    /** @return web root of core_dev installation */
    function getRelativeCoreDevUrl() { return $this->coredev_root; }

    function setRelativeCoreDevUrl($s)
    {
        if (substr($s, 0, 1) != '/')
            $s = $this->getRelativeUrl().$s;

        $this->coredev_root = $s;
    }

    /** @return full url to core_dev root */
    function getCoreDevUrl()
    {
        $t = new Url( $this->getUrl() );
        $t->setPath( $this->getRelativeCoreDevUrl() );
        return $t->get();
    }

    /** @return full path to upload directory */
    function getUploadPath() { return $this->upload_root; }

    function setUploadPath($s)
    {
        if (!is_dir($s))
            throw new \Exception ('setUploadPath: directory dont exist: '.$s);

        $this->upload_root = realpath($s);
    }

    /** @return full path to application directory */
    function getApplicationPath() { return $this->app_root; }

    function setApplicationPath($s = './')
    {
        if (!is_dir($s))
            throw new \Exception ('setApplicationPath: directory dont exist: '.$s);

        $this->app_root = realpath($s);
    }

    /** @return full path to core_dev installation */
    function getCoreDevPath()
    {
        if (!$this->coredev_inc)
            throw new \Exception('setCoreDevPath not configured');

        return $this->coredev_inc;
    }

    function setCoreDevPath($path)
    {
        $real = realpath($path);

        if (!is_dir($real))
            throw new \Exception ('path not found '.$path.' (expanded to '.$real.')');

        // make sure this function returns path name with ending /
        $this->coredev_inc = $real.'/';
    }

    function getStartTime() { return $this->ts_initial; }

    function getMimeType() { return $this->mimetype; }

    function setMimeType($s)
    {
        switch ($s) {
        case 'text/plain':
            // removes XhtmlHeader, designHead & designFoot for this request
            $this->disableDesign();
            break;
        }

        $this->mimetype = $s;
    }

    /**
     * Sends HTTP headers that prompts the client browser to download the page content with given name
     */
    function setAttachmentName($s) { $this->attachment_name = basename($s); }
    function setInlineName($s) { $this->inline_name = basename($s); }

    /**
     * Specifies php scripts to include for additional design
     */
    function designHead($s) { $this->design_head = $s; }
    function designFoot($s) { $this->design_foot = $s; }

    function internalFraming($b) { $this->allow_internal_framing = $b; }
    function externalFraming($b) { $this->allow_external_framing = $b; }

    /** Disables automatic render of XhtmlHeader, designHead & designFoot */
    function disableDesign() { $this->enable_design = false; }

    /** Disables HTTP headers being set automatically */
    function disableHeaders() { $this->enable_http_headers = false; }    /// TODO: deprecate, use disableHttpHeaders()
    function disableHttpHeaders() { $this->enable_http_headers = false; }

    /** Disables HTML headers being set automatically */
    function disableHtmlHeaders() { $this->enable_html_headers = false; }

    function enableProfiler($b = true) { $this->enable_profiler = $b; }

    /** How long (in seconds) should the browser client cache this page? */
    function setCacheDuration($n) { $this->cache_duration = $n; }

    /**
     * Send http headers
     */
    private function sendHeaders()
    {
        if (!$this->mimetype)
            $this->mimetype = 'text/html; charset=utf-8';

        if (!$this->enable_http_headers)
            return;

        header('Content-Type: '.$this->mimetype);

        if ($this->attachment_name)
            header('Content-Disposition: attachment; filename="'.$this->attachment_name.'"');
        else if ($this->inline_name)
            header('Content-Disposition: inline; filename="'.$this->inline_name.'"');

        // see http://www.php.net/manual/en/function.session-cache-limiter.php
        // and http://www.mnot.net/cache_docs/
        if ($this->cache_duration)
        {
            session_cache_expire( $this->cache_duration / 60); // in minutes
            session_cache_limiter('private');
        }
        else
        {
            session_cache_limiter('nocache');
        }

        // IE8, Fiefox 3.6: "Clickjacking Defense" (XSS prevention), Forbids this document to be embedded in a frame from
        // an external source, see https://developer.mozilla.org/en/the_x-frame-options_response_header
        // and http://blogs.msdn.com/b/ie/archive/2009/01/27/ie8-security-part-vii-clickjacking-defenses.aspx
        if (!$this->allow_external_framing)
            header('X-Frame-Options: '.($this->allow_internal_framing ? 'SAMEORIGIN' : 'DENY') );

        // IE8: "XSS Filter"
        // see http://blogs.msdn.com/b/ie/archive/2008/07/01/ie8-security-part-iv-the-xss-filter.aspx
        header('X-XSS-Protection: 1; mode=block');

        // Firefox 4: XSS prevention, specifies valid sources for inclusion of javascript files,
        // see https://developer.mozilla.org/en/Introducing_Content_Security_Policy
        // DISABLED FOR NOW! we need to eliminate inline javascript due to base restriction "No inline scripts will execute":
        // https://wiki.mozilla.org/Security/CSP/Specification#Base_Restrictions

//        header("X-Content-Security-Policy: allow 'self' http://yui.yahooapis.com http://connect.facebook.net");
    }

    /**
     * Attaches a controller object to the main body
     */
    function attach($obj)
    {
        if ( !($obj instanceof IXmlComponent))
            throw new \Exception( get_class($obj).' dont implement IXmlComponent');

        $this->objs[] = $obj;
    }

    /**
     * Attaches a controller object to the beginning of the main body
     */
    function prepend($obj)
    {
        if ( !($obj instanceof IXmlComponent))
            throw new \Exception( get_class($obj).' dont implement IXmlComponent');

        array_unshift($this->objs, $obj);
    }

    function render()
    {
        $head = '';
        $foot = '';

        if ($this->enable_design)
        {
            if ($this->design_head) {
                $view = new ViewModel($this->design_head);
                $head .= $view->render();
            }

            $view = new ViewModel('views/core/required_js.php');
            $head .= $view->render();

            if ($this->design_foot) {
                $view = new ViewModel($this->design_foot);
                $foot .= $view->render();
            }
        }

        $main = '';

        foreach ($this->objs as $obj)
        {
           $main .= $obj->render();
        }

        if ($this->enable_design && $this->enable_profiler) {
            $view = new ViewModel('views/profiler/page.php');
            $main .= $view->render();
        }

        $this->sendHeaders();

        $header = XhtmlHeader::getInstance();

        $enable_fb = false;

        if ($this->enable_design && class_exists('\cd\SessionHandler') && SessionHandler::getInstance()->facebook_app_id)
        {
            $enable_fb = true;

            $header->includeJs( $this->getScheme().'://connect.facebook.net/en_US/all.js');

            $this->registerXmlNs('fb', 'http://www.facebook.com/2008/fbml');

            $header->embedJs(
            'window.fbAsyncInit = function() {'.
                'FB.init({'.
                    'appId:"'.SessionHandler::getInstance()->facebook_app_id.'",'.
                    'status:true,'. // fetch fresh status
                    'cookie:true,'. // enable cookie support
                    'xfbml:true,'.  // parse XFBML tags
                    'channelUrl:"'.$this->getUrl().'c/fbchannel",'. // channel.html file
            //        'oauth:true'.   // enable OAuth 2.0   XXX dont work with stable Chrome at 2011.08.08
                '});'.
            '};'.

            '(function() {'.
                'var e = document.createElement("script"); e.async = true;'.
                'e.src = document.location.protocol + "//connect.facebook.net/en_US/all.js";'.
                'e.async = true;'.
                'document.getElementById("fb-root").appendChild(e);'.
            '}());'
            );
        }

        if ($this->enable_html_headers)
        {
            switch ($this->html_mode) {
            case 'xhtml': // XHTML 1.0 Transitional
                echo
                '<!DOCTYPE html'.
                ' PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'.
                ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n".
                '<html'.
                ' xmlns="http://www.w3.org/1999/xhtml"';
                break;

            case 'html5': // HTML 5
                echo
                '<!DOCTYPE html>'."\n".
                '<html';
    //            ' manifest="cache.manifest"'.   // The "HTML5" manifest="cache.manifest" property on the <html> tag is how the browser knows that we want to cache this web page offline.
                break;
            }

//            ' xml:lang="'.$this->language_code.'" lang="'.$this->language_code.'"'

            foreach ($this->xmlns as $name => $uri)
                echo ' xmlns:'.$name.'="'.$uri.'"';

            echo '>'."\n";

            echo $header->render();

            if ($enable_fb)
                echo '<div id="fb-root"></div>'; // required for Facebook API
        }

        if ($this->enable_design)
            echo $head;

        echo $main;

        if ($this->enable_design)
            echo $foot;

        if ($this->enable_design) {
            echo '</body>';        // <body> tag is opened in XhtmlHeader->render()
            echo '</html>';
        }
    }

}
