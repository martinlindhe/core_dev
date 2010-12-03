<?php
/**
 * $Id$
 *
 * MySQL db driver using the php_mysqli extension
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

require_once('class.CoreBase.php');
require_once('ISql.php');

//STATUS: wip
//TODO soon: factorize some code from prepared-statements functions

//TODO: rewrite using PHP Data Objects: http://se.php.net/pdo

class DatabaseMysql extends CoreBase implements IDB_SQL
{
    var $db_handle       = false;       ///< Internal db handle
    var $host            = 'localhost'; ///< Hostname or numeric IP address of the db server
    var $port            = 3306;        ///< Port number
    var $username        = 'root';      ///< Username to use to connect to the database
    var $password;                      ///< Password to use to connect to the database
    var $database;                      ///< Name of the database to connect to
    var $charset         = 'utf8';      ///< What character set to use
    protected $connected = false;       ///< Are we connected to the db?

    function __construct()
    {
        mysqli_report(MYSQLI_REPORT_OFF);
    }

    function setConfig($conf)
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

    function setHost($s)
    {
        // parse "hostname:port" format
        preg_match('/([0-9a-zA-Z.]+):([0-9]+)/u', $s, $match);
        if (!empty($match[1]) && !empty($match[2])) {
            $this->host = $match[1];
            $this->port = $match[2];
        } else
            $this->host = $s;
    }

    function setPort($n)
    {
        if (!is_numeric($n))
            throw new Exception ('non-numeric port: '.$n);

        $this->port = $n;
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
        //silence warning from failed connection and display our error instead
        $this->db_handle = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);

        if ($this->db_handle->connect_error)
            die('<div class="critical">db_mysqli->connect: '.$this->db_handle->connect_error.'</div>');

        if (!$this->db_handle->set_charset($this->charset))
            die('Error loading character set '.$this->charset.': '.$this->db_handle->error);

        $this->connected = true;
    }

    /**
     * Returns a unique id for the db connection. Useful for naming TEMPORARY TABLEs
     */
    function getThreadId()
    {
        if (!$this->connected)
            $this->connect();

        return $this->db_handle->thread_id;
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
    private function real_query($q)
    {
        if (!$this->connected)
            $this->connect();

        return $this->db_handle->query($q);
    }

    /**
     * Performs a query that does a INSERT
     *
     * @param $q is the query to execute
     * @return insert_id (autoincrement primary key of table)
     */
    function insert($q)
    {
        if ($this->real_query($q))
            return $this->db_handle->insert_id;

        return false;
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

        if ($result)
            return $this->db_handle->affected_rows;

        return false;
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

        if ($result->num_rows > 1)
            throw new Exception ('DatabaseMysql::getOneItem() returned '.$result->num_rows.' rows');

        $data = $result->fetch_row();
        $result->free();

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
        if (!$result = $this->real_query($q))
            return false;

        if ($result->num_rows > 1)
            throw new Exception ('DatabaseMysql::getOneRow() returned '.$result->num_rows.' rows');

        $data = $result->fetch_array(MYSQLI_ASSOC);
        $result->free();

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
        if (!$result = $this->real_query($q))
            return false;

        $data = array();

        while ($row = $result->fetch_assoc())
            $data[] = $row;

        $result->free();

        return $data;
    }

    /**
     * Selects data, 1 column result
     * Example: SELECT val FROM t WHERE id=3
     * *        SHOW TABLES FROM mysql
     */
    function get1dArray($q)
    {
        if (!$result = $this->real_query($q))
            return false;

        $data = array();

        while ($row = $result->fetch_row())
            $data[] = $row[0];

        $result->free();

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
        if (!$result = $this->real_query($q))
            return false;

        $data = array();

        while ($row = $result->fetch_row())
            $data[ $row[0] ] = $row[1];

        $result->free();

        return $data;
    }

    /**
     * Prepared select
     *
     * @param $args[0] query
     * @param $args[1] prepare format (isdb), integer, string, double/float, binary
     * @param $args[2,3,..] variables
     *
     * STATUS: in development
     * SEE http://devzone.zend.com/article/686 for bind prepare statements
     */
    function pSelect()
    {
        if (!$this->connected)
            $this->connect();

        $args = func_get_args();

        $stmt = $this->db_handle->prepare($args[0]) or die('failed to prepare');

        $params = array();
        for ($i = 1; $i < count($args); $i++)
            $params[] = $args[$i];

        if ($params)
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));

        $stmt->execute();

        $data = array();

        $meta = $stmt->result_metadata();

        while ($field = $meta->fetch_field())
            $parameters[] = &$row[$field->name];

        call_user_func_array(array($stmt, 'bind_result'), $this->refValues($parameters));

        while ( $stmt->fetch() ) {
            $x = array();
            foreach ($row as $key => $val )
                $x[$key] = $val;

            $data[] = $x;
        }
        $meta->close();

        $stmt->close();
        return $data;
    }

    function pSelectRow()
    {
        $args = func_get_args();

        $res = call_user_func_array(array($this, 'pSelect'), $args);  // HACK to pass dynamic variables to parent method

        if (count($res) > 1)
            throw new Exception ('DatabaseMysql::pSelectRow() returned '.count($res).' rows');

        if (!$res)
            return false;

        return $res[0];
    }

    function pSelectItem()
    {
        if (!$this->connected)
            $this->connect();

        $args = func_get_args();

        $stmt = $this->db_handle->prepare($args[0]) or die('failed to prepare');

        $params = array();
        for ($i = 1; $i < count($args); $i++)
            $params[] = $args[$i];

        if ($params)
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));

        $stmt->execute();

        if ($stmt->field_count != 1)
            throw new Exception ('not 1 column result');

        $stmt->bind_result($col1);

        $data = array();
        while ($stmt->fetch())
            $data[] = $col1;

        $stmt->close();

        if (count($data) > 1)
            throw new Exception ('DatabaseMysql::pSelectItem() returned '.count($data).' rows');

        if (!$data)
            return false;

        return $data[0];
    }

    // selects 1d array
    function pSelect1d()
    {
        if (!$this->connected)
            $this->connect();

        $args = func_get_args();

        $stmt = $this->db_handle->prepare($args[0]) or die('failed to prepare');

        $params = array();
        for ($i = 1; $i < count($args); $i++)
            $params[] = $args[$i];

        if ($params)
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));

        $stmt->execute();

        $data = array();

        if ($stmt->field_count != 1)
            throw new Exception ('not 1d result');

        $stmt->bind_result($col1);

        while ($stmt->fetch())
            $data[] = $col1;

        $stmt->close();
        return $data;
    }

    /**
     * like getMappedArray(). query selects a list of key->value pairs
     */
    function pSelectMapped()
    {
        if (!$this->connected)
            $this->connect();

        $args = func_get_args();

        $stmt = $this->db_handle->prepare($args[0]) or die('failed to prepare');

        $params = array();
        for ($i = 1; $i < count($args); $i++)
            $params[] = $args[$i];

        if ($params)
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));

        $stmt->execute();

        $data = array();

        if ($stmt->field_count != 2)
            throw new Exception ('pSelectMapped requires a key->val result set');

        //2d array
        $stmt->bind_result($col1, $col2);

        while ($stmt->fetch())
            $data[ $col1 ] = $col2;

        $stmt->close();
        return $data;
    }

    // like pSelect, but returns affected rows
    function pDelete()
    {
        if (!$this->connected)
            $this->connect();

        $args = func_get_args();

        $stmt = $this->db_handle->prepare($args[0]) or die('failed to prepare');

        $params = array();
        for ($i = 1; $i < count($args); $i++)
            $params[] = $args[$i];

        if ($params)
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));

        $stmt->execute();

        $data = $stmt->affected_rows;

        $stmt->close();
        return $data;
    }

    // like pDelete
    function pUpdate()
    {
        $args = func_get_args();
        return call_user_func_array(array($this, 'pDelete'), $args);  // HACK to pass dynamic variables to parent method
    }

    // like pDelete, but returns insert id
    function pInsert()
    {
        $args = func_get_args();
        $res = call_user_func_array(array($this, 'pDelete'), $args);  // HACK to pass dynamic variables to parent method
        if ($res == 1)
            return $this->db_handle->insert_id;
        else
            throw new Exception ('insert fail');
    }

    private function refValues($arr)
    {
        if (!php_min_ver('5.3'))
            return $arr;

        // reference is required for PHP 5.3+
        $refs = array();
        foreach ($arr as $key => $val)
            $refs[$key] = &$arr[$key];

        return $refs;
    }


    /**
     * Shows the config view
     */
    public function renderConfig()
    {
        $view = new ViewModel('views/mysql_config.php');
        return $view->render();
    }

}

?>
