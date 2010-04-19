<?php
/**
 * $Id$
 *
 * Renders a set of views into a XML document (XML, XHTML, VoiceXML)
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: WIP

//require_once('XHTMLHeader.php');
//require_once('XHTMLMenu.php');

require_once('class.CoreBase.php');

class XMLDocumentHandler extends CoreBase
{
    static $_instance;               ///< singleton

    private $design_head;
    private $design_foot;
    private $mimetype = 'text/html';

    var $objs = array();  ///< IXMLComponent objects

    private function __construct()
    {
        $this->handleEvents();
    }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function setMimeType($type) { $this->mimetype = $mime; }

    /**
     * Specifies php scripts to include for additional design
     */
    function designHead($n) { $this->design_head = $n; }
    function designFoot($n) { $this->design_foot = $n; }

    /**
     * Attaches a controller object to the main body
     */
    function attach($obj)
    {
        $this->objs[] = $obj;
    }

    private function handleEvents()
    {
        $auth = AuthHandler::getInstance();
        $auth->handleEvents();

        $session = SessionHandler::getInstance();
        $session->handleEvents();
    }

    function render()
    {
/*
        if (!empty($_POST))
            foreach ($this->objs as $obj)
                $obj->handlePost($_POST);
*/

        if ($this->mimetype)
            header('Content-type: '.$this->mimetype);

        $header = XHTMLHeader::getInstance();
        echo $header->render();

        if ($this->design_head) {
            $view = new ViewModel($this->design_head);
            echo $view->render();
        }

        if ($this->getError())
            echo xhtmlError($this->getError());

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

        if ($this->design_foot) {
            $view = new ViewModel($this->design_foot);
            echo $view->render();
        }
    }
}

?>
