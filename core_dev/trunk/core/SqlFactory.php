<?php
/**
 * $Id$
 *
 * SQL database factory
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
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
        switch ($driver) {
        case 'mysql': $class = 'DatabaseMysql'; break;
        case 'mssql': $class = 'DatabaseMssql'; break;
        default: throw new Exception ('Unknown driver '.$driver);
        }

        $class = $class.($profiler ? 'Profiler': '');

        require_once($class.'.php');

        if (!class_exists($class))
            throw new Exception ('Database driver not found '.$class);

        $db = new $class();

        if (!($db instanceof  IDB_SQL))
            throw new Exception('Database driver must implement IDB_SQL');

        return $db;
    }
}

?>
