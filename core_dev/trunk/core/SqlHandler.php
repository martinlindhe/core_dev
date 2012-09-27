<?php
/**
 * $Id$
 *
 * This is a singleton class with a twist. Its purpose is to return
 * a single handle for the database connection to be used in the scope
 * of the application, but also to function as a "database instance handler".
 *
 * In normal usage pattern, it will only contain one database handle,
 * but if you need to work with additional databases in your app, you
 * can just register them using the addInstance() method.
 *
 * # Using multiple databases
 * $db  = SqlFactory::factory('mysql');
 * SqlHandler::addInstance($db);      //always add the main db instance first
 * $db2 = SqlFactory::factory('mssql');
 * SqlHandler::addInstance($db2);
 * # ...
 * $db  = SqlHandler::getInstance();  //will always return the first registered instance
 * $db2 = SqlHandler::getInstance(1); //will always return the 2:nd registered instance
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('sql_misc.php');

class SqlHandler
{
    static $_instances = array();

    private function __construct() { } //singleton

    private function __clone() {}      //singleton: prevent cloning of class

    /**
     * Registers a database object to the instance pool
     * @return instance index
     */
    public static function addInstance($obj)
    {
        self::$_instances[] = $obj;
        return count(self::$_instances) - 1;
    }

    public static function getInstance($num = 0)
    {
        if (empty(self::$_instances[ $num ]))
            throw new \Exception ('No sql instance registered');
//            return false;

        return self::$_instances[ $num ];
    }

}

?>
