<?php
/**
 * $Id$
 *
 * Shows errors from various handlers (if any)
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

class ErrorHandler
{
    static $_instance; ///< singleton

    private function __construct() { }
    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function render($clear_errors = true)
    {
        $session = SessionHandler::getInstance();
        if ($session->getError())
            echo '<div class="critical">'.$session->getError().'</div><br/>';

        $auth = AuthHandler::getInstance();
        if ($auth->getError())
            echo '<div class="critical">'.$auth->getError().'</div><br/>';

        if ($clear_errors) {
            $session->setError('');
            $auth->setError('');
        }
    }
}

?>
