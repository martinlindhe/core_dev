<?php
/**
 * $Id$
 *
 * MySQL driver profiler class
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip
//TODO: make it a general sql profiler (reuse for ms-sql)

require_once('sql_mysql.php');

class DatabaseMysqlProfiler extends DatabaseMySQL implements IDB_SQL
{
    var $ts_initial   = 0;       ///< microtime for db instance
    var $time_measure = 0;       ///< time when profiling started
    var $time_connect = 0;       ///< time it took to connect to db
    var $time_spent   = array(); ///< time spent for each query
    var $queries_cnt  = 0;       ///< number of queries executed
    var $queries      = array(); ///< queries executed
    var $query_error  = array(); ///< query error messages

    function __construct()
    {
        $this->ts_initial = microtime(true);
    }

    function getErrorCount() { return count($this->query_error); }

    /**
     * Saves time for profiling current action (connect, execute query, ...)
     */
    private function measureStart()
    {
        $this->time_measure = microtime(true);
    }

    /**
     * Calculates the time it took to connect to database
     */
    private function measureConnect()
    {
        $this->time_connect = microtime(true) - $this->time_measure;
    }

    /**
     * Calculates the time it took to execute a query
     */
    private function measureQuery($q)
    {
        $this->time_spent[ $this->queries_cnt ] = microtime(true) - $this->time_measure;
        $this->queries[ $this->queries_cnt ] = $q;
        $this->queries_cnt++;
    }

    /**
     * Stores profiling information about a failed query execution
     *
     * @param $q is the query being profiled
     * @param $err is the error message returned by the db driver in use
     */
    private function addError($q, $err)
    {
        $this->query_error[ $this->queries_cnt-1 ] = $err;
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
        $this->measureStart();
        parent::connect();
        $this->measureConnect();
    }

    function insert($q)
    {
        $this->measureStart();
        $res = parent::insert($q);
        $this->measureQuery($q);

        if ($res === false)
            $this->addError($q, $this->db_handle->error);

        return $res;
    }

    function delete($q)
    {
        $this->measureStart();
        $res = parent::delete($q);
        $this->measureQuery($q);

        if ($res === false)
            $this->addError($q, $this->db_handle->error);

        return $res;
    }

    function getOneItem($q)
    {
        $this->measureStart();
        $res = parent::getOneItem($q);
        $this->measureQuery($q);

        if ($res === false)
            $this->addError($q, $this->db_handle->error);

        return $res;
    }

    function getOneRow($q)
    {
        $this->measureStart();
        $res = parent::getOneRow($q);
        $this->measureQuery($q);

        if ($res === false)
            $this->addError($q, $this->db_handle->error);

        return $res;
    }

    function getArray($q)
    {
        $this->measureStart();
        $res = parent::getArray($q);
        $this->measureQuery($q);

        if ($res === false)
            $this->addError($q, $this->db_handle->error);

        return $res;
    }

    function getMappedArray($q)
    {
        $this->measureStart();
        $res = parent::getMappedArray($q);
        $this->measureQuery($q);

        if ($res === false)
            $this->addError($q, $this->db_handle->error);

        return $res;
    }

    function get1dArray($q)
    {
        $this->measureStart();
        $res = parent::get1dArray($q);
        $this->measureQuery($q);

        if ($res === false)
            $this->addError($q, $this->db_handle->error);

        return $res;
    }
/*
    function pSelect()
    {
        $this->measureStart();
        $res = parent::pSelect(); //XXX how this works with extra params?
        $this->measureQuery($q);

        if ($res === false)
            $this->addError($q, $this->db_handle->error);

        return $res;
    }
*/

}
?>
