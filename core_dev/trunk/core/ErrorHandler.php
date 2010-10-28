<?php
/**
 * $Id$
 *
 * Handles errors
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

class ErrorHandler
{
    static $_instance; ///< singleton
    private $errors = array();

    private function __construct() { }
    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function getErrorCount() { return count($this->errors); }

    function add($s) { $this->errors[] = $s; }

    function render($clear_errors = false)
    {
        foreach ($this->errors as $e)
            echo '<div class="critical">'.$e.'</div><br/>';

        if ($clear_errors)
            $this->errors = array();
    }
}

?>
