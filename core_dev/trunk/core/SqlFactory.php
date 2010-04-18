<?php
/**
 * $Id$
 *
 * SQL database factory
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('SqlHandler.php');

class SqlFactory
{
    /**
     * Returns a SQL database object
     * @param $type <string> type of database handler (mysql)
     * @param $profiler <bool> set to true to profile SQL performance
     */
    public static function factory($driver = 'mysql', $profiler = false)
    {
        if (!require_once('sql_'.$driver.($profiler ? '_profiler': '').'.php'))
            throw new Exception('DatabaseFactory: Unknown driver '.$driver);

        $targetClass = 'Database'.strtolower($driver).($profiler ? 'Profiler' : '');

        if (!class_exists($targetClass))
            throw new Exception ('Database driver not found '.$targetClass);

        $rc = new ReflectionClass($targetClass);
        if (!$rc->implementsInterface('IDB_SQL'))
            throw new Exception('Database driver must implement IDB_SQL');

        return new $targetClass();
    }
}

?>
