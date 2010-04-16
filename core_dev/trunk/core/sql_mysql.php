<?php
/**
 * $Id: db_mysql.php 201 2010-04-07 19:28:04Z ml $
 *
 * MySQL db driver using the php_mysqli extension
 * Implemented as a singleton class
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//require_once('core_Base.php');
require_once('ISql.php');

//STATUS: wip
//TODO: move all measure_*() to db_mysql_profiler parent methods

class DatabaseMySQL extends CoreBase implements IDB_SQL
{
    private static $_instance;       ///< singleton instance

    var $db_handle    = false; ///< Internal db handle
    var $host         = '';    ///< Hostname or numeric IP address of the db server
    var $port         = 0;     ///< Port number
    var $username     = '';    ///< Username to use to connect to the database
    var $password     = '';    ///< Password to use to connect to the database
    var $database     = '';    ///< Name of the database to connect to
    var $charset      = 'utf8';///< What character set to use
    protected $connected    = false; ///< Are we connected to the db?

    /**
     * Constructor. Initializes db driver and connects to the database
     *
     * @param $settings is array with DB-specific settings
     */
    private function __construct($conf) //singleton: requires private constructor
    {
        global $config;

        $this->time_initial = microtime(true);

        if (!is_array($conf))
            return;

        if (!empty($conf['host']))     $this->host     = $conf['host'];
        if (!empty($conf['port']))     $this->port     = $conf['port'];
        if (!empty($conf['username'])) $this->username = $conf['username'];
        if (!empty($conf['password'])) $this->password = $conf['password'];
        if (!empty($conf['database'])) $this->database = $conf['database'];
        if (!empty($conf['charset']))  $this->charset  = $conf['charset'];
    }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance($param = '')
    {
        if ( !(self::$_instance instanceof self) )
            self::$_instance = new self($param);

        return self::$_instance;
    }

    /**
     * Destructor
     */
    function __destruct()
    {
        $this->disconnect();
    }

    function disconnect()
    {
        if ($this->connected)
            $this->db_handle->close();
    }

    /**
     * Opens a connection to MySQL database
     */
    function connect()
    {
        //MySQL defaults
        if (!$this->host)     $this->host     = 'localhost';
        if (!$this->port)     $this->port     = 3306;
        if (!$this->username) $this->username = 'root';

        //silence warning from failed connection and display our error instead
        if (!$this->db_handle = @new mysqli($this->host, $this->username,
                                            $this->password, $this->database, $this->port)) {
            throw new Exception("Could not connect to the database");
            return false;
        }

        if ($this->db_handle->connect_error)
            die('<div class="critical">db_mysqli->connect: '.$this->db_handle->connect_error.'</div>');

        if (!$this->db_handle->set_charset($this->charset))
            die('Error loading character set '.$this->charset.': '.$this->db_handle->error);

        $this->connected = true;
    }

    /**
     * Escapes a string for use in queries
     *
     * @param $q is the query to escape
     * @return the escaped string, taking db-connection locale into account
     */
    function escape($q)
    {
        //db handle is needed to use the escape function
        if (!$this->connected)
            $this->connect();

        return $this->db_handle->real_escape_string($q);
    }

    /**
     * Executes a SQL a query, opens db connection if required
     *
     * @param $q the query to execute
     * @return result
     */
    function real_query($q)
    {
        if (!$this->connected)
            $this->connect();

        return $this->db_handle->query($q);
    }

    /**
     * Performs a general query. Should only be used with special commands that don't return anything.
     * Use the other functions for common SQL operations such as select, insert, update, delete
     * Example: LOCK TABLES
     *
     * @param $q is the query to execute
     * @return the result of the query, if anything
     */
    /*function query($q)
    {
        $this->measure_time();

        $result = $this->real_query($q);

        if (!$result && $this->getDebug())
            $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;

        $this->measure_query($q);

        return $result;
    }*/

    /**
     * Performs a query that does a INSERT
     *
     * @param $q is the query to execute
     * @return insert_id (autoincrement primary key of table)
     */
    function insert($q)
    {
        $this->measure_time();

        $result = $this->real_query($q);

        $ret_id = 0;

        if ($result) {
            $ret_id = $this->db_handle->insert_id;
        } else {
            if ($this->getDebug())
                $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
        }

        $this->measure_query($q);

        return $ret_id;
    }

    /**
     * Performs a query that does a REPLACE
     *
     * @param $q is the query to execute
     * @return the number of rows affected
     */
    function replace($q) { return $this->insert($q); }

    /**
     * Performs a query that does a DELETE
     * Example: DELETE FROM t WHERE id=1
     *
     * @param $q is the query to execute
     * @return the number of rows affected
     */
    function delete($q)
    {
        $this->measure_time();

        $result = $this->real_query($q);

        $affected_rows = false;

        if ($result) {
            $affected_rows = $this->db_handle->affected_rows;
        } else {
            if ($this->getDebug())
                $this->query_error[ $this->queries_cnt ] = $this->db_handle->error;
        }

        $this->measure_query($q);

        return $affected_rows;
    }

    /**
     * Performs a query that does a UPDATE
     * Example: UPDATE t SET n=1
     *
     * @param $q is the query to execute
     * @return the number of rows affected
     */
    function update($q) { return $this->delete($q); }

    /**
     * Selects data
     * Example: SELECT * FROM t
     *
     * @param $q is the query to execute
     * @return an array with the results, with columns as array indexes
     */
    function getArray($q)
    {
        $this->measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug())
                $this->profileError($q, $this->db_handle->error);
            return array();
        }

        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $result->free();

        $this->measure_query($q);

        return $data;
    }

    /**
     * Selects data, 1 column result
     * Example: SELECT val FROM t WHERE id=3
     */
    function get1dArray($q)
    {
        $this->measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug())
                $this->profileError($q, $this->db_handle->error);
            return array();
        }

        $data = array();

        while ($row = $result->fetch_row())
            $data[] = $row[0];

        $result->free();

        $this->measure_query($q);

        return $data;
    }

    /**
     * Selects data
     * Example: SHOW VARIABLES LIKE "%cache%"
     *
     * @param $q is the query to execute
     * @return an array with the results mapped as key => value
     */
    function getMappedArray($q)
    {
        $this->measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug())
                $this->profileError($q, $this->db_handle->error);
            return array();
        }

        $data = array();

        while ($row = $result->fetch_row())
            $data[ $row[0] ] = $row[1];

        $result->free();

        $this->measure_query($q);

        return $data;
    }

    /**
     * Selects data
     * Example: SELECT textRow FROM t
     *
     * @param $q is the query to execute
     * @return an 1-dimensional array with a numeric index
     */
    function getNumArray($q)
    {
        $this->measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug())
                $this->profileError($q, $this->db_handle->error);
            return array();
        }

        $data = array();

        while ($row = $result->fetch_row())
            $data[] = $row;

        $result->free();

        $this->measure_query($q);

        return $data;
    }

    /**
     * Selects one row of data
     * Example: SELECT * FROM t WHERE id=1 (where id is distinct)
     *
     * @param $q is the query to execute
     * @return one row-result with columns as array indexes
     */
    function getOneRow($q)
    {
        if (!$result = $this->real_query($q)) {

            if ($this->getDebug())
                $this->setError( $this->db_handle->error.': '.$q );
            else
                $this->setError( $this->db_handle->error );

            return array();
        }

        if ($result->num_rows > 1) {
            echo "ERROR: DB_MySQLi::getOneRow() returned ".$result->num_rows." rows!\n";
            if ($this->getDebug())
                echo "Query: ".$q."\n";
            die;
        }

        $data = $result->fetch_array(MYSQLI_ASSOC);

        $result->free();

        return $data;
    }

    /**
     * Selects one column of one row of data
     * Example: SELECT a FROM t WHERE id=1 (where id is distinct)
     *
     * @param $q is the query to execute
     * @return one column-result only
     */
    function getOneItem($q)
    {
        $this->measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug())
                $this->profileError($q, $this->db_handle->error);
            return '';
        }

        if ($result->num_rows > 1) {
            echo "ERROR: DB_MySQLi::getOneItem() returned ".$result->num_rows." rows!\n";
            if ($this->getDebug())
                echo "Query: ".$q."\n";
            die;
        }

        $data = $result->fetch_row();
        $result->free();

        $this->measure_query($q);

        if (!$data) return false;
        return $data[0];
    }

}

?>
