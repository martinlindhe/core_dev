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
require_once('SqlQuery.php');

class DatabaseMysqlProfiler extends DatabaseMysql implements IDB_SQL
{
    protected $measure_start = 0;       ///< time when last profiling started
    var       $time_connect  = 0;       ///< time it took to connect to db
    var       $queries       = array(); ///< array of SqlQuery (queries executed)
    protected $debug         = false;

    function __construct()
    {
        mysqli_report(MYSQLI_REPORT_ERROR); // fails on sql syntax errors (????)
        //mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT); // all errors on, but disable exceptions (strict)
    }

    public function debug($b = true) { $this->debug = $b; }

    public function startMeasure() { $this->measure_start = microtime(true); }

    public function getErrorCount()
    {
        $cnt = 0;

        foreach ($this->queries as $q)
            if ($q->error)
                $cnt++;

        return $cnt;
    }

    public function getTotalQueryTime()
    {
        $time = 0;
        foreach ($this->queries as $q)
            $time += $q->time;

        return $time;
    }

    /**
     * Calculates the time it took to execute a query
     */
    public function &measureQuery($q)
    {
        $prof = new SqlQuery();
        $prof->query = $q;
        $prof->time = microtime(true) - $this->measure_start;
        $this->queries[] = $prof;
        if ($this->debug)
            echo $prof->render();
        return $prof;
    }

    public function connect()
    {
        $this->startMeasure();

        parent::connect();

        // calculate the time it took to connect to database
        $this->time_connect = microtime(true) - $this->measure_start;
    }

    public function insert($q)
    {
        $this->startMeasure();

        $res = parent::insert($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    public function delete($q)
    {
        $this->startMeasure();

        $res = parent::delete($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    public function getOneItem($q)
    {
        $this->startMeasure();

        $res = parent::getOneItem($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    public function getOneRow($q)
    {
        $this->startMeasure();

        $res = parent::getOneRow($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    public function getArray($q)
    {
        $this->startMeasure();

        $res = parent::getArray($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    public function getMappedArray($q) //XXX = get2dArray
    {
        $this->startMeasure();

        $res = parent::getMappedArray($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    public function get1dArray($q)
    {
        $this->startMeasure();

        $res = parent::get1dArray($q);
        $prof = &$this->measureQuery($q);

        if ($res === false)
            $prof->error = $this->db_handle->error;

        return $res;
    }

    /** Executes a prepared statement and binds parameters
     * $args[0]    = query
     * $args[1]    = prepared statement string (isii)..
     * $args[2..n] = query parameters
     */
    protected function pExecStmt($args)
    {
        if (!$args[0])
            throw new Exception ('no query');

        if (strpos($args[0], '=') !== false && strpos($args[0], '?') === false)
            if (stripos($args[0], 'SELECT') === false)
                throw new Exception ('query is not prepared: '.$args[0]);

        if (!$this->connected)
            $this->connect();

        if (! ($stmt = $this->db_handle->prepare($args[0])) )
            throw new Exception ('FAIL prepare: '.$args[0]);

        $params = array();
        for ($i = 1; $i < count($args); $i++)
            $params[] = $args[$i];

        if ($params)
            $res = call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));

        $stmt->execute();

        $prof = &$this->measureQuery($args[0]);

        if (Sql::isQueryPrepared($args[0]))
            $prof->prepared = true;

        if (isset($args[1]))
            $prof->format = $args[1];

        if (isset($args[2])) {
            for ($i = 2; $i < count($args); $i++)
                $prof->params[] = $args[$i];
        }

        if ($params && $res === false)
            $prof->error = $this->db_handle->error;

        return $stmt;
    }

}

?>
