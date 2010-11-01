<?php
/**
 * $Id$
 *
 * Renders a set of views into a XML document (XML, XHTML, VoiceXML)
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: move setCoreDevInclude to a "core_dev handler" ? or "setup handler", or "config handler" ?

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
    private $coredev_inc = '';       ///< if set, points to "/path/to/core_dev/core/"   XXXX move to own handler class?

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

    function setCoreDevInclude($path)
    {
        ///XXX peka på "/path/to/core_dev/core/" katalogen, hör egentligen inte till page handlern men den hör inte till något bra objekt... separat core-dev handler????
        if (!is_dir($path))
            throw new Exception ('path not found '.$path);

        $this->coredev_inc = $path;
    }

    function getCoreDevInclude() { return $this->coredev_inc; }

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
        ob_start();

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
        ob_end_flush();
    }

}

/** @param $dst redirects user to destination url relative to website base url */
function redir($dst)
{
    $page = XmlDocumentHandler::getInstance();
    header('Location: '.$page->getBaseUrl().$dst);
    die;
}

/** @param $url partial url to generate a url relative website base */
function relurl($url)
{
    $page = XmlDocumentHandler::getInstance();
    return $page->getBaseUrl().$url;
}

/**
 * Modifies parameters to current request URI
 * @param $p array of key=>val pairs
 */
function relurl_add($p)
{
    $page = XmlDocumentHandler::getInstance();

    $u = new Url( $page->getBaseUrl() );
    $u->setPath($_SERVER['REDIRECT_URL']);
    foreach ($p as $key => $val)
        $u->setParam($key, $val);

    return $u->get(); //XXX dont return full url, add a Url->getRelative() ?
}

function ahref($url, $title)
{
    return '<a href="'.relurl($url).'">'.$title.'</a>';
}

/** Creates "are you sure?" pages */
function confirmed($text)
{
    if (isset($_GET['cd_confirmed']))
        return true;

    echo $text.'<br/><br/>';

    echo '<a href="'.relurl_add(array('cd_confirmed'=>1)).'">Yes, I am sure</a><br/><br/>';
    echo '<a href="javascript:history.go(-1);">No, wrong button</a><br/>';
    return false;
}

?>
