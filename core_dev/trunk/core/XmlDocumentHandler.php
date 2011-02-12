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
    private $mimetype        = 'text/html';  ///< should be "application/xhtml+xml" but IE8 still cant even understand such a page
    private $Url;                            ///< Url object
    private $attachment_name;                ///< name of file attachment (force user to save file)
    private $inline_name;                    ///< name of inlined file (will set correct name if user chooses to save file)
    private $coredev_inc;                    ///< if set, points to "/path/to/core_dev/core/"   XXXX move to own handler class?
    private $upload_root;                    ///< root directory for file uploads
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
            throw new Exception ('core_dev include not set');

        return $this->coredev_inc;
    }

    function getUploadRoot() { return $this->upload_root; }
    function getMimeType() { return $this->mimetype; }

    function getStartTime() { return $this->ts_initial; }

    function setUploadRoot($s)
    {
        if (!is_dir($s))
            throw new Exception ('setUploadRoot: directory dont exist: '.$s);

        $this->upload_root = $s;
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

        if ($this->mimetype)
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

/*
        if (!empty($_POST))
            foreach ($this->objs as $obj)
                $obj->handlePost($_POST);
*/

        $this->sendHeaders();

        if ($this->enable_design) {
            $header = XhtmlHeader::getInstance();
            echo $header->render();

            if ($this->design_head) {
                $view = new ViewModel($this->design_head);
                echo $view->render();
            }
        }
/*
        //XXX should we really show errors on top of every page?
        $error = ErrorHandler::getInstance();
        echo $error->render();
*/
        foreach ($this->objs as $obj)
        {
            if (!$obj)
                continue;

            if (is_string($obj)) {
                //XXX hack to allow any text to be attached
                echo $obj;
                continue;
            }

            if (!is_object($obj))
                throw new Exception ('not an object: '.$obj);

            $rc = new ReflectionClass($obj);
            /*
            if (!$rc->implementsInterface('IXmlComponent'))
                throw new exception('Attached object '.get_class($obj).' dont implement IXmlComponent');
            */

            if (!$rc->hasMethod('render'))
                throw new Exception('Attached object '.get_class($obj).' dont implement render()');

            echo $obj->render();
        }

        if ($this->enable_design) {
            if (class_exists('SqlHandler')) {
                $db = SqlHandler::getInstance();

                if ($db instanceof DatabaseMySQLProfiler)
                    echo $db->renderProfiler();
            }

            if (class_exists('TempStore')) {
                $store = TempStore::getInstance();
                echo $store->renderStatus();
            }

            echo $this->renderPageLoad();

            if ($this->design_foot) {
                $view = new ViewModel($this->design_foot);
                echo $view->render();
            }

            //XXX <body> and <html> tags is opened in XhtmlHeader->render()
            echo "\n".'</body></html>';
        }

        ob_end_flush();
    }

    function renderPageLoad()
    {
        $view = new ViewModel('views/page_profiler.php');
        return $view->render();
    }

}

?>
