<?php
/**
 * $Id$
 *
 * The view model creates a scope for variables and include the file returning the parsed output
 *
 * @author Martin Lindhe, 2010-2012 <martin@ubique.se>
 */

namespace cd;

require_once('IXmlComponent.php');

class ViewModel extends \ArrayObject implements IXmlComponent
{
    private $template;

    var $caller; ///< points to calling class

    public function __construct($template, $caller = false)
    {
        //makes this a property overloaded object
        parent::__construct(array(), \ArrayObject::ARRAY_AS_PROPS);
        $this->template = $template;

        $this->caller = $caller;
    }

    /**
     * Allows the user to register variables available in the view model
     */
    function registerVar($name, $val)
    {
        $this->$name = $val;
    }

    public function render()
    {
        //available variables in the scope of the view
        if (class_exists('\cd\ErrorHandler'))       $error   = ErrorHandler::getInstance();
        if (class_exists('\cd\SessionHandler'))     $session = SessionHandler::getInstance();
        if (class_exists('\cd\SqlHandler'))         $db      = SqlHandler::getInstance();
        if (class_exists('\cd\XhtmlHeader'))        $header  = XhtmlHeader::getInstance();
        if (class_exists('\cd\XmlDocumentHandler')) $page    = XmlDocumentHandler::getInstance();
        if (class_exists('\cd\LocaleHandler'))      $locale  = LocaleHandler::getInstance();
        if (class_exists('\cd\TempStore'))          $temp    = TempStore::getInstance();

        // make reference to calling object available in the namespace of the view
        $caller = $this->caller;

        $file = $page->getCoreDevPath().$this->template;

        if (!file_exists($file)) {
            // if not built in view, look in app dir
            $file = $this->template;
            if (!file_exists($file))
                throw new \Exception ('cannot find '.$this->template);
        }

        ob_start();
        require($file);
        return ob_get_clean();
    }
}
