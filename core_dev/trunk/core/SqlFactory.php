<?php
/**
 * $Id$
 *
 * SQL database factory
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

class SqlFactory
{
    /**
     * Returns a SQL database object
     * @param $type <string> type of database handler (mysql)
     * @param $conf <array> config options
     * @param $profiler <bool> set to true to profile SQL performance
     */
    public static function factory($driver = 'mysql', $conf = '', $profiler = false)
    {
        if (!require_once('sql_'.$driver.($profiler ? '_profiler': '').'.php'))
            throw new Exception('DatabaseFactory: Unknown driver '.$driver);

        switch(strtolower($driver)) {
        case 'mysql':

            //XXX more elegant way to select profiler?
            return $profiler ? DatabaseMySQLProfiler::getInstance($conf) : DatabaseMySQL::getInstance($conf);
        }
    }

}

?>
