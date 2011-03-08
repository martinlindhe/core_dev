<?php
/**
 * $Id$
 *
 * Renders a set of views into a XML document (XML, XHTML, VoiceXML)
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: move setCoreDevInclude to a "core_dev handler" ? or "setup handler", or "config handler" ?

require_once('CoreBase.php');
require_once('LocaleHandler.php');
require_once('Url.php');

class XmlDocumentHandler extends CoreBase
{
    static $_instance;                       ///< singleton

    private $design_head;
    private $design_foot;
    private $enable_design   = true;
    private $enable_headers  = true;         ///< send http headers?
    private $allow_frames    = false;        ///< allow this document to be framed using <frame> or <iframe> ?
    private $cache_duration  = 0;            ///< number of seconds to allow browser client to cache this result
    private $mimetype        = '';           ///< "text/html" should be "application/xhtml+xml" but IE8 still cant even understand such a page
    private $Url;                            ///< Url object
    private $attachment_name;                ///< name of file attachment (force user to save file)
    private $inline_name;                    ///< name of inlined file (will set correct name if user chooses to save file)
    private $coredev_inc;                    ///< if set, points to "/path/to/core_dev/core/"   XXXX move to own handler class?
    private $upload_root;                    ///< root directory for file uploads
    private $app_root;                       ///< application root directory, currently only used to locate favicon.png for auto conversion to favicon.ico
    private $ts_initial;                     ///< used to measure page load time

    private $objs = array();                 ///< IXmlComponent objects

    private function __clone() {}      //singleton: prevent cloning of class

    private function __construct()
    {
        $this->ts_initial = microtime(true);
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /** @return relative URL for current website */
    function getRelativeUrl() { return $this->Url->getPath(); }

    /** @return full base/root URL to website */
    function getUrl() { return $this->Url->get(); }

    /** @return domain name part of base URL to website */
    function getHostName() { return $this->Url->getHost(); }

    function getCoreDevInclude()
    {
        if (!$this->coredev_inc)
            throw new Exception ('setCoreDevInclude not configured');

        return $this->coredev_inc;
    }

    function getApplicationRoot() { return $this->app_root; }
    function getUploadRoot() { return $this->upload_root; }
    function getMimeType() { return $this->mimetype; }

    function getStartTime() { return $this->ts_initial; }

    function setUploadRoot($s)
    {
        if (!is_dir($s))
            throw new Exception ('setUploadRoot: directory dont exist: '.$s);

        $this->upload_root = realpath($s);
    }

    function setApplicationRoot($s = './')
    {
        if (!is_dir($s))
            throw new Exception ('setApplicationRoot: directory dont exist: '.$s);

        $this->app_root = realpath($s);
    }

    function setMimeType($s) { $this->mimetype = $s; }

    function setCoreDevInclude($path)
    {
        ///XXX peka på "/path/to/core_dev/core/" katalogen, hör egentligen inte till page handlern men den hör inte till något bra objekt... separat core-dev handler????
        if (!is_dir($path))
            throw new Exception ('path not found '.$path);

        $this->coredev_inc = $path;
    }

    /**
     * Sets base/root URL for current website
     * @param $s base url, e.g. http://www.example.com/  (with ending / )
     */
    function setUrl($s) { $this->Url = new Url($s); }

    /**
     * Sends HTTP headers that prompts the client browser to download the page content with given name
     */
    function setAttachmentName($s) { $this->attachment_name = basename($s); }
    function setInlineName($s) { $this->inline_name = basename($s); }

    /**
     * Specifies php scripts to include for additional design
     */
    function designHead($n) { $this->design_head = $n; }
    function designFoot($n) { $this->design_foot = $n; }

    /** Disables automatic render of XhtmlHeader, designHead & designFoot */
    function disableDesign() { $this->enable_design = false; }

    /** Disables headers being set automatically */
    function disableHeaders() { $this->enable_headers = false; }

    /** How long (in seconds) should the browser client cache this page? */
    function setCacheDuration($n) { $this->cache_duration = $n; }

    /**
     * Send http headers
     */
    private function sendHeaders()
    {
        if (!$this->enable_headers)
            return;

        if (!$this->mimetype)
            $this->mimetype = 'text/html';

        header('Content-Type: '.$this->mimetype);

        if ($this->attachment_name)
            header('Content-Disposition: attachment; filename="'.$this->attachment_name.'"');
        else if ($this->inline_name)
            header('Content-Disposition: inline; filename="'.$this->inline_name.'"');

        // see http://www.mnot.net/cache_docs/
        header('Cache-Control: '.
            ($this->cache_duration ? 'max-age='.$this->cache_duration : 'no-cache').
            ', must-revalidate');

        // XSS prevention, forbids this document to be embedded in a frame from an
        // external source, see https://developer.mozilla.org/en/the_x-frame-options_response_header
        header('X-Frame-Options: '.($this->allow_frames ? 'SAMEORIGIN' : 'DENY') );

        // XSS prevention, specifies valid sources for inclusion of javascript files,
        // see https://developer.mozilla.org/en/Introducing_Content_Security_Policy
        header("X-Content-Security-Policy: allow 'self' yui.yahooapis.com");
    }

    /**
     * Attaches a controller object to the main body
     */
    function attach($obj)
    {
        $this->objs[] = $obj;
    }

    function render()
    {
        ob_start();

        $out = '';

        if ($this->enable_design && $this->design_head) {
            $view = new ViewModel($this->design_head);
            $out .= $view->render();
        }

        foreach ($this->objs as $obj)
        {
            if (!$obj)
                continue;

            if (is_string($obj)) {
                //XXX hack to allow any text to be attached
                $out .= $obj;
                continue;
            }

            if (!is_object($obj))
                throw new Exception ('not an object: '.$obj);

            $rc = new ReflectionClass($obj);

            if (!$rc->implementsInterface('IXmlComponent'))
                throw new exception('Attached '.get_class($obj).' dont implement IXmlComponent');

            if (!$rc->hasMethod('render'))
                throw new Exception('Attached '.get_class($obj).' dont implement render()');

            $out .= $obj->render();
        }

        if ($this->enable_design) {
            if ($this->design_foot) {
                $view = new ViewModel($this->design_foot);
                $out .= $view->render();
            }

            $view = new ViewModel('views/page_profiler.php');
            $out .= $view->render();
        }

        $this->sendHeaders();

        $x = ob_get_contents();
        if ($x)
            throw new Exception ('XXX should not happen '.$x);

        $lang = LocaleHandler::getInstance()->getLanguageCode();

        echo
        '<?xml version="1.0" encoding="UTF-8"?>'."\n".
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n".
        '<html'.
            ' xml:lang="'.$lang.'"'.
            ' lang="'.$lang.'"'.
            ' xmlns="http://www.w3.org/1999/xhtml">'."\n";

        if ($this->enable_design)
            echo XhtmlHeader::getInstance()->render();

        echo $out;

        //XXX <body> tag is opened in XhtmlHeader->render()
        if ($this->enable_design)
            echo '</body>';

        echo '</html>';
    }

}

?>
