<?php
/**
 * $Id$
 *
 * Renders a set of views into a XML document (XML, XHTML, VoiceXML)
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('class.CoreBase.php');

class XmlDocumentHandler extends CoreBase
{
    static $_instance;               ///< singleton

    private $design_head;
    private $design_foot;
    private $enable_design = true;
    private $mimetype = 'text/html';
    private $base_url = '';
    private $attachment_name;

    var $objs = array();  ///< IXMLComponent objects

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setMimeType($s) { $this->mimetype = $s; }

    /**
     * Sets base URL for current website
     * @param $s base url, e.g. http://www.example.com/  (with ending / )
     */
    function setBaseUrl($s) { $this->base_url = $s; }

    function getBaseUrl() { return $this->base_url; }

    /**
     * Sends HTTP headers that prompts the client browser to download the page content with given name
     */
    function sendAttachment($s) { $this->attachment_name = $s; }

    /**
     * Specifies php scripts to include for additional design
     */
    function designHead($n) { $this->design_head = $n; }
    function designFoot($n) { $this->design_foot = $n; }

    /**
     * Removes XhtmlHeader, designHead & designFoot for this request
     */
    function disableDesign() { $this->enable_design = false; }

    /**
     * Send http headers to disable browser cache
     */
    private function noCache()
    {
        //FIXME: are these all needed for modern browsers?
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D,d M YH:i:s').' GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
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
/*
        if (!empty($_POST))
            foreach ($this->objs as $obj)
                $obj->handlePost($_POST);
*/

        if ($this->mimetype)
            header('Content-Type: '.$this->mimetype);

        //prompts the user to save the file
        if ($this->attachment_name) {
            $this->noCache();
            header('Content-Disposition: attachment; filename="'.$this->attachment_name.'"');
        }

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
            if (is_string($obj)) {
                //XXX hack to allow any text to be attached
                echo $obj;
                continue;
            }

            $rc = new ReflectionClass($obj);
            /*
            if (!$rc->implementsInterface('IXMLComponent'))
                throw new exception('Attached object '.get_class($obj).' dont implement IXMLComponent');
            */

            if (!$rc->hasMethod('render'))
                throw new Exception('Attached object '.get_class($obj).' dont implement render()');

            echo $obj->render();
        }

        if ($this->enable_design && class_exists('SqlHandler')) { //&& $session->isAdmin) {
            $db = SqlHandler::getInstance();

            if ($db instanceof DatabaseMySQLProfiler)
                echo $db->renderProfiler();
        }

        if ($this->enable_design) {
            if ($this->design_foot) {
                $view = new ViewModel($this->design_foot);
                echo $view->render();
            }

            //XXX <body> and <html> tags is opened in XhtmlHeader->render()
            echo "\n".'</body></html>';
        }
    }

}

?>
