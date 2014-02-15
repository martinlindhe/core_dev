<?php
/**
 * $Id$
 *
 * SQL database factory
 *
 * @author Martin Lindhe, 2010-2011 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('SqlHandler.php');
require_once('DatabaseMysqlPDO.php');

class SqlFactory
{
    /**
     * Returns a SQL database object
     * @param $type <string> type of database handler (mysql)
     * @param $profiler <bool> set to true to profile SQL performance
     */
    public static function factory($driver = 'mysql', $profiler = false)
    {
        $driver = ucfirst($driver);

        switch ($driver) {
        case 'Mysql': $class = 'DatabaseMysqlPDO'; break;
        case 'Mssql': $class = 'DatabaseMssql'; break;
        default: throw new \Exception ('Unknown driver '.$driver);
        }

        require_once($class.'.php');

        $class = '\\cd\\'.$class;

        if (!class_exists($class))
            throw new \Exception ('Database driver not found '.$class);

        $db = new $class();

        if ($profiler) {
            $db->enableProfiling();
        }

        if (!($db instanceof IDB_SQL))
            throw new \Exception('Database driver must implement IDB_SQL');

        return $db;
    }
}
