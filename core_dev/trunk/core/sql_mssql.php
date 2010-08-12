<?php
/**
 * $Id$
 *
 * MSSQL db driver using the php_mssql extension
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

require_once('class.CoreBase.php');
require_once('ISql.php');

//STATUS: wip
//TODO: update getNumArray()
//TODO: add something like mysql's getThreadId()

//TODO: rewrite using PHP Data Objects: http://se.php.net/pdo

class DatabaseMssql extends CoreBase implements IDB_SQL
{
    var $db_handle       = false;       ///< Internal db handle
    var $host            = 'localhost'; ///< Hostname or numeric IP address of the db server
    var $port            = 1433;        ///< Port number
    var $username        = 'root';      ///< Username to use to connect to the database
    var $password        = '';          ///< Password to use to connect to the database
    var $database        = '';          ///< Name of the database to connect to
    var $charset         = 'utf8';      ///< What character set to use
    protected $connected = false;       ///< Are we connected to the db?

    function setConfig($conf)
    {
        if (!is_array($conf))
            return;

        if (!empty($conf['host']))     $this->host     = $conf['host'];
        if (!empty($conf['port']))     $this->port     = $conf['port'];
        if (!empty($conf['username'])) $this->username = $conf['username'];
        if (!empty($conf['password'])) $this->password = $conf['password'];
        if (!empty($conf['database'])) $this->database = $conf['database'];
        if (!empty($conf['charset']))  $this->charset  = $conf['charset'];
    }

    function disconnect()
    {
        if ($this->connected)
            mssql_close($this->db_handle);
    }

    /**
     * Opens a connection to MySQL database
     */
    function connect()
    {
        //silence warning from failed connection and display our error instead
        $this->db_handle = mssql_pconnect($this->host.':'.$this->port, $this->username, $this->password, true);

        if (!$this->db_handle)
            die('<div class="critical">db_mssql->connect: Connection Error</div>');

//        if (!$this->db_handle->set_charset($this->charset))
//            die('Error loading character set '.$this->charset.': '.$this->db_handle->error);

        if (!$this->selectDatabase($this->database)) {
            die('Error selecting database');
        }

        $this->connected = true;
    }

    function selectDatabase($dbname)
    {
        $this->database = $dbname;
        return mssql_select_db($this->database, $this->db_handle);
    }

    /**
     * Escapes the string for use in MSSQL queries, only need to escape "'"
     * Do NOT use " in queries for string encapsulation
     * http://php.net/manual/en/function.mssql-query.php
     *
     * @param $q the query to escape
     * @return escaped query
     */
    function escape($q)
    {
        return str_replace("'","''",$q);
    }

    /**
     * Executes a SQL a query, opens db connection if required
     *
     * @param $q the query to execute
     * @return result
     */
    private function real_query($q)
    {
        if (!$this->connected)
            $this->connect();

        return mssql_query($q, $this->db_handle);
    }

    /**
     * Performs a query that does a INSERT
     *
     * @param $q is the query to execute
     * @return insert_id (autoincrement primary key of table)
     */
    function insert($q)
    {
        $result = $this->real_query($q);

        $ret_id = 0;

        if ($result) {
            $query = 'select SCOPE_IDENTITY() AS last_insert_id';
            $query_result = mssql_query($query, $this->db_handle) or die('Query failed, no insert id: '.$query);

            $query_result = mssql_fetch_object($query_result);

            $ret_id = $query_result->last_insert_id;
            mssql_free_result($query_result);
        }

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
        $result = $this->real_query($q);

        $affected_rows = false;

        if ($result) {
            $affected_rows = mssql_rows_affected($this->db_handle);
        }

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
     * Selects one column of one row of data
     * Example: SELECT a FROM t WHERE id=1 (where id is distinct)
     *
     * @param $q is the query to execute
     * @return one column-result only
     */
    function getOneItem($q)
    {
        if (!$result = $this->real_query($q))
            return false;

        if (mssql_rows_affected($this->db_handle) > 1) {
            echo "ERROR: DB_MSSQL::getOneItem() returned ".mssql_rows_affected($this->db_handle)." rows!\n";
            if ($this->getDebug()) echo "Query: ".$q."\n";
            die;
        }

        $data = mssql_fetch_row($result);
        mssql_free_result($result);

        if (!$data) return false;
        return $data[0];
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
            return false;
        }

        if (mssql_rows_affected($db->handle) > 1) {
            echo "ERROR: DB_MSSQL::getOneRow() returned ".mssql_rows_affected($this->db_handle)." rows!\n";
            if ($this->getDebug()) echo "Query: ".$q."\n";
            die;
        }

        $data = mssql_fetch_array($result, MSSQL_ASSOC);
        mssql_free_result($result);

        return $data;
    }

    /**
     * Selects data
     * Example: SELECT * FROM t
     *
     * @param $q is the query to execute
     * @return an array with the results, with columns as array indexes
     */
    function getArray($q)
    {
        if (!$result = $this->real_query($q)) {
            return false;
        }

        $data = array();

        while ($row = mssql_fetch_assoc($result)) {
            $data[] = $row;
        }

        mssql_free_result($result);

        return $data;
    }

    /**
     * Selects data, 1 column result
     * Example: SELECT val FROM t WHERE id=3
     */
    function get1dArray($q)
    {
        if (!$result = $this->real_query($q)) {
            return false;
        }

        $data = array();

        while ($row = mssql_fetch_row($result))
            $data[] = $row[0];

        mssql_free_result($result);

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
        if (!$result = $this->real_query($q)) {
            return false;
        }

        $data = array();

        while ($row = mssql_fetch_row($result))
            $data[ $row[0] ] = $row[1];

        mssql_free_result($result);


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
        die('fixme update getNumArray');
    }

    /**
     * Shows the config view
     */
    public function renderConfig()
    {
        die('IMPLEMENT');
        $view = new ViewModel('views/mysql_config.php');
        return $view->render();
    }

}

?>
