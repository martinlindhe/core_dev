<?php
/**
 * $Id$
 *
 * MySQL db driver using the PDO extension
 *
 * Perform SQL commands using the Sql class
 *
 * @author Martin Lindhe, 2007-2014 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('ISql.php');
require_once('Sql.php');
require_once('SqlQuery.php');

class DatabaseMysqlPDO implements IDB_SQL
{
    var $db_handle       = false;       ///< Internal db handle
    var $host            = 'localhost'; ///< Hostname or numeric IP address of the db server
    var $port            = 3306;        ///< Port number
    var $username        = 'root';      ///< Username to use to connect to the database
    var $password;                      ///< Password to use to connect to the database
    var $database;                      ///< Name of the database to connect to
    var $charset         = 'utf8';      ///< What character set to use

    var $profiling_enabled  = false;
    protected $measure_start = 0;       ///< time when last profiling started
    var       $time_connect  = 0;       ///< time it took to connect to db
    var       $queries       = array(); ///< array of SqlQuery (queries executed)

    protected $connected = false;       ///< Are we connected to the db?

    public function __construct()
    {
    }

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

    public function isProfilingEnabled() { return $this->profiling_enabled; }

    public function enableProfiling() { $this->profiling_enabled = true; }

    public function startMeasure() { $this->measure_start = microtime(true); }

    /**
     * Calculates the time it took to execute a query
     */
    public function &finishMeasure($q)
    {
        $prof = new SqlQuery();
        $prof->query = $q;
        $prof->time = microtime(true) - $this->measure_start;
        $this->queries[] = $prof;
        return $prof;
    }

    public function setConfig($conf)
    {
        if (!is_array($conf))
            return;

        if (!empty($conf['host']))     $this->setHost($conf['host']);
        if (!empty($conf['port']))     $this->setPort($conf['port']);
        if (!empty($conf['username'])) $this->username = $conf['username'];
        if (!empty($conf['password'])) $this->password = $conf['password'];
        if (!empty($conf['database'])) $this->database = $conf['database'];
        if (!empty($conf['charset']))  $this->charset  = $conf['charset'];
    }

    public function setHost($s)
    {
        // parse "hostname:port"
        preg_match('/([0-9a-zA-Z.]+):([0-9]+)/u', $s, $match);
        if (!empty($match[1]) && !empty($match[2])) {
            $this->host = $match[1];
            $this->port = $match[2];
        } else {
            $this->host = $s;
        }
    }

    public function setPort($n)
    {
        if (!is_numeric($n))
            throw new \Exception ('non-numeric port: '.$n);

        $this->port = $n;
    }

    function getHost()
    {
        if ($this->port && $this->port != 3306)
            return $this->host.':'.$this->port;

        return $this->host;
    }

    public function disconnect()
    {
        if ($this->connected)
            $this->db_handle->close();
    }

    /**
     * Opens a connection to MySQL database using PDO
     */
    public function connect()
    {
        if ($this->db_handle)
            return true;

        $dsn =
            'mysql:'.
            'dbname='.$this->database.';'.
            'host='.$this->host.';'.
            'port='.$this->port.';'.
            'charset='.$this->charset;

        try {
            if ($this->profiling_enabled)
                $this->startMeasure();

            $this->db_handle = new \PDO($dsn, $this->username, $this->password);

            if ($this->profiling_enabled)
                $this->time_connect = microtime(true) - $this->measure_start;

        } catch (PDOException $e) {
            die('Connection failed: '.$e->getMessage( ));
        }

        $this->connected = true;

        return true;
    }

}
