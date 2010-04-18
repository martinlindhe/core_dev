<?php
/**
 * $Id$
 *
 * Purpose:
 * To return a single handle for the database connection to be used in the scope of the application.
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip, i have no idea how to do this more elegant, this will do for now, hopefully

class SqlHandler
{
    static $_instance;

    private function __construct() //singleton class
    {
    }

    public static function setInstance($obj)
    {
        self::$_instance = $obj;
    }

    public static function getInstance()
    {
        return self::$_instance;
    }

    private function __construct()
    {
    }
}

?>
