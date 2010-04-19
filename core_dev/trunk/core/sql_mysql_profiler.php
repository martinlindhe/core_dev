<?php
/**
 * $Id$
 *
 * MySQL driver profiler class
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip XXX make it a general sql profiler, split out showConfig() and showStatus() to views connected to the mysql driver

//XXX: theres some pointless overrides here like the constructor & getInstance, because we extend a singleton class (is that wrong?)
//TODO: make showProfile, showStatus, showConfig into separate views

require_once('sql_mysql.php');

class DatabaseMysqlProfiler extends DatabaseMySQL implements IDB_SQL
{
    var $time_initial = 0;       ///< profiler: microtime for db instance
    var $time_measure = 0;       ///< profiler: time when profiling started
    var $time_connect = 0;       ///< profiler: time it took to connect to db
    var $time_spent   = array(); ///< Used internally for the SQL profiler
    var $queries_cnt  = 0;       ///< Used internally for the SQL profiler
    var $queries      = array(); ///< Used internally for the SQL profiler
    var $query_error  = array(); ///< Used internally for the SQL profiler

    function __construct()
    {
        $this->time_initial = microtime(true);
    }

    function getErrorCount() { return count($query_error); }

    /**
     * Renders the object using a view script
     */
    public function renderProfiler()
    {
        $view = new ViewModel('views/sql_profiler.php');
        return $view->render();
    }

    function connect()
    {
        $this->measure_time();
        parent::connect();
        $this->measure_connect();
    }

    function insert($q)
    {
        $this->measure_time();
        $res = parent::insert($q);
        $this->measure_query($q);

        if ($res === false)
            $this->profileError($q, $this->db_handle->error);

        return $res;
    }

    function delete($q)
    {
        $this->measure_time();
        $res = parent::delete($q);
        $this->measure_query($q);

        if ($res === false)
            $this->profileError($q, $this->db_handle->error);

        return $res;
    }

    function getOneItem($q)
    {
        $this->measure_time();
        $res = parent::getOneItem($q);
        $this->measure_query($q);

        if ($res === false)
            $this->profileError($q, $this->db_handle->error);

        return $res;
    }

    function getOneRow($q)
    {
        $this->measure_time();
        $res = parent::getOneRow($q);
        $this->measure_query($q);

        if ($res === false)
            $this->profileError($q, $this->db_handle->error);

        return $res;
    }

    function getArray($q)
    {
        $this->measure_time();
        $res = parent::getArray($q);
        $this->measure_query($q);

        if ($res === false)
            $this->profileError($q, $this->db_handle->error);

        return $res;
    }

    function getMappedArray($q)
    {
        $this->measure_time();
        $res = parent::getMappedArray($q);
        $this->measure_query($q);

        if ($res === false)
            $this->profileError($q, $this->db_handle->error);

        return $res;
    }

















    /**
     * Shows MySQLi driver status
     */
    function showStatus()
    {
        echo 'Host info: '.$this->db_handle->host_info.'<br/>';
        echo 'Connection character set: '.$this->db_handle->character_set_name().'<br/>';
        echo 'Last error: '.$this->db_handle->error.'<br/>';
        echo 'Last errno: '.$this->db_handle->errno;
    }

    /**
     * Shows current settings
     */
    function showConfig()
    {
        echo '<div class="item">';
        echo '<h2>Current database configuration</h2>';
        echo 'DB driver: '.$this->driver.'<br/>';
        echo 'Server version: '.$this->db_handle->server_info.'<br/>';
        echo 'Client version: '.$this->db_handle->client_info.'<br/>';
        echo 'Host: '.$this->host.':'.$this->port.'<br/>';
        echo 'Login: '.$this->username.':'.($this->password ? $this->password : '(blank)').'<br/>';
        echo 'Database: '.$this->database.'<br/>';
        echo 'Configured charset: '.$this->charset;
        echo '</div><br/>';

        echo '<div class="item">';
        echo '<h2>DB host features</h2>';
        $db_time = $this->getOneItem('SELECT NOW()');
        echo 'DB time: '.$db_time.' (webserver time: '.now().')<br/>';
        echo '</div><br/>';

        echo '<div class="item">';
        echo '<h2>DB driver specific settings</h2>';
        $this->showDriverStatus();
        echo '</div><br/>';

        echo '<div class="item">';
        if ($this->dialect == 'mysql') {
            //Show MySQL query cache settings
            $data = $this->getMappedArray('SHOW VARIABLES LIKE "%query_cache%"');
            if ($data['have_query_cache'] == 'YES') {
                echo '<h2>MySQL query cache settings</h2>';
                echo 'Type: '. $data['query_cache_type'].'<br/>';        //valid values: ON, OFF or DEMAND
                echo 'Size: '. formatDataSize($data['query_cache_size']).' (total size)<br/>';
                echo 'Limit: '. formatDataSize($data['query_cache_limit']).' (per query)<br/>';
                echo 'Min result unit: '. formatDataSize($data['query_cache_min_res_unit']).'<br/>';
                echo 'Wlock invalidate: '. $data['query_cache_wlock_invalidate'].'<br/><br/>';

                //Current query cache status
                $data = $this->getMappedArray('SHOW STATUS LIKE "%Qcache%"', 'Variable_name', 'Value');
                echo '<h2>MySQL query cache status</h2>';
                echo 'Hits: '. formatNumber($data['Qcache_hits']).'<br/>';
                echo 'Inserts: '. formatNumber($data['Qcache_inserts']).'<br/>';
                echo 'Queries in cache: '. formatNumber($data['Qcache_queries_in_cache']).'<br/>';
                echo 'Total blocks: '. formatNumber($data['Qcache_total_blocks']).'<br/>';
                echo '<br/>';
                echo 'Not cached: '. formatNumber($data['Qcache_not_cached']).'<br/>';
                echo 'Free memory: '. formatDataSize($data['Qcache_free_memory']).'<br/>';
                echo '<br/>';
                echo 'Free blocks: '. formatNumber($data['Qcache_free_blocks']).'<br/>';
                echo 'Lowmem prunes: '. formatNumber($data['Qcache_lowmem_prunes']);
            } else {
                echo '<h2>MySQL query cache is disabled</h2>';
            }
        }
        echo '</div>';
    }

    /**
     * Saves time for profiling current action (connect, execute query, ...)
     */
    function measure_time()
    {
        $this->time_measure = microtime(true);
    }

    /**
     * Calculates the time it took to connect to database
     */
    function measure_connect()
    {
        $this->time_connect = microtime(true) - $this->time_measure;
    }

    /**
     * Calculates the time it took to execute a query
     */
    function measure_query($q)
    {
        $this->time_spent[ $this->queries_cnt ] = microtime(true) - $this->time_measure;
        $this->queries[ $this->queries_cnt ] = $q;
        $this->queries_cnt++;
    }

    /**
     * Stores profiling information about a failed query execution
     *
     * @param $time_started is microtime from when the execution of this query begun
     * @param $q is the query being profiled
     * @param $err is the error message returned by the db driver in use
     */
    function profileError($q, $err)
    {
        $this->query_error[ $this->queries_cnt ] = $err;
        $this->measure_query($q);
    }

}
?>
