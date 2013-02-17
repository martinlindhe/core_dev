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
 * SqlHandler::addInstance($db, 'mysql-db');      //always add the main db instance first
 * $db2 = SqlFactory::factory('mssql');
 * SqlHandler::addInstance($db2, 'microsoft-db');
 * # ...
 * $db  = SqlHandler::getInstance();  //will always return the first registered instance
 * $db2 = SqlHandler::getInstance(1); //will always return the 2:nd registered instance
 * ...
 * $db  =  SqlHandler::getInstance('microsoft-db');  // will always return the instance named "microsoft-db"
 *
 * @author Martin Lindhe, 2010-2013 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('sql_misc.php');

class SqlInstance
{
    var $obj;
    var $name;
}

class SqlHandler
{
    private static $_instances = array(); ///< array of SqlInstance objects

    private function __construct() {}     ///< singleton

    private function __clone() {}         ///< singleton: prevent cloning of class

    /**
     * Registers a database object to the instance pool
     * @return instance index
     */
    public static function addInstance($obj, $name = '')
    {
        if (!$name)
            $name = 'default'.count(self::$_instances);

        foreach (self::$_instances as $i)
            if ($i->name == $name)
                throw new \Exception ('duplicate db instance name '.$name);

        $i = new SqlInstance();
        $i->obj = $obj;
        $i->name = $name;
        self::$_instances[] = $i;

        return count(self::$_instances) - 1;
    }

    /**
     * @param $s instance id or name
     */
    public static function getInstance($s = 0)
    {
        if (is_numeric($s) && !empty(self::$_instances[ $s ]))
            return self::$_instances[ $s ]->obj;

        foreach (self::$_instances as $i)
            if ($i->name == $s)
                return $i->obj;

        throw new \Exception ('No sql instance registered');
    }

}

?>
