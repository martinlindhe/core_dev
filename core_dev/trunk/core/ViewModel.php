<?php
/**
 * $Id$
 *
 * The view model creates a scope for variables and include the file returning the parsed output
 */

class ViewModel extends ArrayObject
{
    private $template;

    public function __construct($template)
    {
        //makes this a property overloaded object
        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
        $this->template = $template;
    }

    public function render()
    {
        //available variables in the scope of the view
        $errors  = ErrorHandler::getInstance();
        $auth    = AuthHandler::getInstance();
        $session = SessionHandler::getInstance();
        $header  = XHTMLHeader::getInstance();

        ob_start();
        include($this->template);
        return ob_get_clean();
    }
}

?>
