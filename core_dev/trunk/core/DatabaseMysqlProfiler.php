<?php
/**
 * $Id$
 *
 * MySQL driver profiler class
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip
//TODO: make it a general sql profiler (reuse for ms-sql)
//XXX: somehow show return values if debug is enabled

require_once('DatabaseMysql.php');
require_once('ProfiledSqlQuery.php');

class DatabaseMysqlProfiler extends DatabaseMysql implements IDB_SQL
{
    var $measure_start = 0;       ///< time when profiling started
    var $time_connect  = 0;       ///< time it took to connect to db
    var $queries       = array(); ///< array of ProfiledSqlQuery (queries executed)
    protected $debug   = false;

    function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR); // fails on sql syntax errors (????)
        //mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT); // all errors on, but disable exceptions (strict)
    }

    function debug($b = true) { $this->debug = $b; }

    function getErrorCount()
    {
        $cnt = 0;

        foreach ($this->queries as $q)
            if ($q->error)
                $cnt++;

        return $cnt;
    }

    function getTotalQueryTime()
    {
        $time = 0;
        foreach ($this->queries as $q)
            $time += $q->time;

        return $time;
    }

    /**
     * Calculates the time it took to execute a query
     */
    private function &measureQuery($q)
    {
        $prof = new ProfiledSqlQuery();
        $prof->query = $q;
        $prof->time = microtime(true) - $this->measure_start;
        $this->queries[] = $prof;
        if ($this->debug)
            echo $prof->render();
        return $prof;
    }

    /**
     * Shows the profiler view
     */
    public function renderProfiler()
    {
        $view = new ViewModel('views/sql_profiler.php');
        return $view->render();
    }

    function connect()
    {
        $this->measure_start = microtime(true);

        parent::connect();

        // calculate the time it took to connect to database
        $this->time_connect = microtime(true) - $this->measure_start;
    }

    function insert($q)
    {
        $this->measure_start = microtime(true);

        $res = parent::insert($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function delete($q)
    {
        $this->measure_start = microtime(true);

        $res = parent::delete($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function getOneItem($q)
    {
        $this->measure_start = microtime(true);

        $res = parent::getOneItem($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function getOneRow($q)
    {
        $this->measure_start = microtime(true);

        $res = parent::getOneRow($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function getArray($q)
    {
        $this->measure_start = microtime(true);

        $res = parent::getArray($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function getMappedArray($q) //XXX = get2dArray
    {
        $this->measure_start = microtime(true);

        $res = parent::getMappedArray($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function get1dArray($q)
    {
        $this->measure_start = microtime(true);

        $res = parent::get1dArray($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function pSelect()
    {
        $args = func_get_args();

        $this->measure_start = microtime(true);

        $res = call_user_func_array(array('parent', 'pSelect'), $args);  // HACK to pass dynamic variables to parent method

        $prof = &$this->measureQuery($args[0]);
        $prof->prepared = true;

        if (isset($args[1]))
            $prof->format = $args[1];

        if (isset($args[2])) {
            $params = array();
            for ($i = 2; $i < count($args); $i++)
                $prof->params[] = $args[$i];
        }

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function pSelectItem()
    {
        $args = func_get_args();

        $this->measure_start = microtime(true);

        $res = call_user_func_array(array('parent', 'pSelectItem'), $args);  // HACK to pass dynamic variables to parent method

        $prof = &$this->measureQuery($args[0]);
        $prof->prepared = true;

        if (isset($args[1]))
            $prof->format = $args[1];

        if (isset($args[2])) {
            $params = array();
            for ($i = 2; $i < count($args); $i++)
                $prof->params[] = $args[$i];
        }

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function pSelectMapped()
    {
        $args = func_get_args();

        $this->measure_start = microtime(true);

        $res = call_user_func_array(array('parent', 'pSelectMapped'), $args);  // HACK to pass dynamic variables to parent method

        $prof = &$this->measureQuery($args[0]);
        $prof->prepared = true;

        if (isset($args[1]))
            $prof->format = $args[1];

        if (isset($args[2])) {
            $params = array();
            for ($i = 2; $i < count($args); $i++)
                $prof->params[] = $args[$i];
        }

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function pSelect1d()
    {
        $args = func_get_args();

        $this->measure_start = microtime(true);

        $res = call_user_func_array(array('parent', 'pSelect1d'), $args);  // HACK to pass dynamic variables to parent method

        $prof = &$this->measureQuery($args[0]);
        $prof->prepared = true;

        if (isset($args[1]))
            $prof->format = $args[1];

        if (isset($args[2])) {
            $params = array();
            for ($i = 2; $i < count($args); $i++)
                $prof->params[] = $args[$i];
        }

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    function pDelete()
    {
        $args = func_get_args();

        $this->measure_start = microtime(true);

        $res = call_user_func_array(array('parent', 'pDelete'), $args);  // HACK to pass dynamic variables to parent method

        $prof = &$this->measureQuery($args[0]);
        $prof->prepared = true;

        if (isset($args[1]))
            $prof->format = $args[1];

        if (isset($args[2])) {
            $params = array();
            for ($i = 2; $i < count($args); $i++)
                $prof->params[] = $args[$i];
        }

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

}

?>
