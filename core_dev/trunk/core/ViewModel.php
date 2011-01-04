<?php
/**
 * $Id$
 *
 * The view model creates a scope for variables and include the file returning the parsed output
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

class ViewModel extends ArrayObject
{
    private $template;

    var $caller; ///< points to calling class

    public function __construct($template, $caller = false)
    {
        //makes this a property overloaded object
        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
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
        if (class_exists('ErrorHandler'))       $error   = ErrorHandler::getInstance();
        if (class_exists('SessionHandler'))     $session = SessionHandler::getInstance();
        if (class_exists('SqlHandler'))         $db      = SqlHandler::getInstance();
        if (class_exists('XhtmlHeader'))        $header  = XhtmlHeader::getInstance();
        if (class_exists('XmlDocumentHandler')) $page    = XmlDocumentHandler::getInstance();
        if (class_exists('LocaleHandler'))      $locale  = LocaleHandler::getInstance();

        // make reference to calling object available in the namespace of the view
        $caller = $this->caller;

        ob_start();
        include($this->template);
        return ob_get_clean();
    }
}

?>
