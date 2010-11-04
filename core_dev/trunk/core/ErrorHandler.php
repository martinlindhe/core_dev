<?php
/**
 * $Id$
 *
 * Stores errors in a $_SESSION array to keep them persistent
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: ok

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

    function getErrorCount()
    {
        return isset($_SESSION['e']) ? count($_SESSION['e']) : 0;
    }

    function add($s) { $_SESSION['e'][] = $s; }

    function render($clear_errors = false)
    {
        $res = '';

        foreach ($_SESSION['e'] as $e)
            $res .= '<div class="bad">'.$e.'</div><br/>';

        if ($clear_errors)
            $_SESSION['e'] = array();

        return $res;
    }
}

?>
