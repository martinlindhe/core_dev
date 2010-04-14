<?php
/**
 * $Id: db_mysqli.php 4995 2010-03-29 15:17:37Z ml $
 *
 * MySQL db driver using the php_mysqli extension
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('db_base.php');

class db_mssql extends db_base
{
    /**
     * Destructor
     */
    function __destruct()
    {
        if ($this->connected) mssql_close($this->db_handle);
    }

    /**
     * Opens a connection to MySQL database
     */
    function connect()
    {
        parent::measure_time();

        //MSSQL defaults
        if (!$this->host)     $this->host     = 'localhost';
        if (!$this->port)     $this->port     = 1433;
        if (!$this->username) $this->username = 'root';

        //silence warning from failed connection and display our error instead
        $this->db_handle = mssql_pconnect($this->host.':'.$this->port, $this->username, $this->password, true);

        if (!$this->db_handle)
            die('<div class="critical">db_mssql->connect: Conncection Error</div>');

//      if (!$this->db_handle->set_charset($this->charset))
//          die('Error loading character set '.$this->charset.': '.$this->db_handle->error);

        $this->connected = true;
        $this->driver    = 'mssql';
        $this->dialect   = 'mssql';
//      $this->server_version = $this->db_handle->server_info;
//      $this->client_version = $this->db_handle->client_info;

        if (!$this->selectDatabase($this->database)) {
            die('Error selecting database');
        }

        parent::measure_connect();
    }

    /**
     * Shows MSSQL driver status
     */
    function status()
    {
        echo 'NO INFO';
/*      echo 'Host info: '.$this->db_handle->host_info.'<br/>';
        echo 'Connection character set: '.$this->db_handle->character_set_name().'<br/>';
        echo 'Last error: '.$this->db_handle->error.'<br/>';
        echo 'Last errno: '.$this->db_handle->errno;
*/
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
    function real_query($q)
    {
        if (!$this->connected) $this->connect();
        return mssql_query($q, $this->db_handle);
    }

    /**
     * Executes a SQL query
     *
     * @param $q the query to execute
     * @return result
     */
    function query($q)
    {
        parent::measure_time();

        $result = $this->real_query($q);

        if (!$result) {
            if ($this->getDebug()) $this->query_error[ $this->queries_cnt ] = mssql_get_last_message();
        }

        parent::measure_query($q);

        return $result;
    }

    /**
     * For SQL INSERT queries
     *
     * @param $q the query to execute
     * @return insert_id
     */
    function insert($q)
    {
        parent::measure_time();

        $result = $this->real_query($q);

        $ret_id = 0;

        if ($result) {
            $query = 'select SCOPE_IDENTITY() AS last_insert_id';
            $query_result = mssql_query($query, $this->db_handle) or die('Query failed, no insert id: '.$query);
                                       
            $query_result = mssql_fetch_object($query_result);
           
            $ret_id = $query_result->last_insert_id;;
            mssql_free_result($query_result);
        } else {
            if ($this->getDebug()) $this->query_error[ $this->queries_cnt ] = mssql_get_last_message();
        }

        parent::measure_query($q);

        return $ret_id;
    }

    /**
     * For SQL DELETE queries
     *
     * @param $q the query to execute
     * @return number of rows affected
     */
    function delete($q)
    {
        parent::measure_time();

        $result = $this->real_query($q);

        $affected_rows = false;

        if ($result) {
            $affected_rows = mssql_rows_affected($this->db_handle);
        } else {
            if ($this->getDebug()) $this->query_error[ $this->queries_cnt ] = mssql_get_last_message();
        }

        parent::measure_query($q);

        return $affected_rows;
    }

    /**
     * For SQL SELECT queries who returns array of data
     *
     * @param $q the query to execute
     * @return result
     */
    function getArray($q)
    {
        parent::measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug()) $this->profileError($q, mssql_get_last_message());
            return array();
        }

        $data = array();

        while ($row = mssql_fetch_assoc($result)) {
            $data[] = $row;
        }

        mssql_free_result($result);

        parent::measure_query($q);

        return $data;
    }

    /**
     * For SQL SELECT queries who returns multiple rows with 1 column of data
     *
     * @param $q the query to execute
     * @return result
     */
    function get1dArray($q)
    {
        parent::measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug()) $this->profileError($q, mssql_get_last_message());
            return array();
        }

        $data = array();

        while ($row = mssql_fetch_row($result))
            $data[] = $row[0];

        mssql_free_result($result);

        parent::measure_query($q);

        return $data;
    }

    /**
     * For SQL SELECT queries who returns mapped array of data
     *
     * @param $q the query to execute
     * @return result
     */
    function getMappedArray($q)
    {
        parent::measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug()) $this->profileError($q, mssql_get_last_message());
            return array();
        }

        $data = array();

        while ($row = mssql_fetch_row($result))
            $data[ $row[0] ] = $row[1];

        mssql_free_result($result);

        parent::measure_query($q);

        return $data;
    }

    /**
     * For SQL SELECT queries who returns array of data with numerical index
     *
     * @param $q the query to execute
     * @return result
     */
    function getNumArray($q)
    {
        parent::measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug()) $this->profileError($q, mssql_get_last_message());
            return array();
        }

        $data = array();

        while ($row = mssql_fetch_row($result))
            $data[] = $row;

        mssql_free_result($result);

        parent::measure_query($q);

        return $data;
    }

    /**
     * For SQL SELECT queries who returns one row of data
     *
     * @param $q the query to execute
     * @return result
     */
    function getOneRow($q)
    {
        parent::measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug()) $this->profileError($q, mssql_get_last_message());
            return array();
        }

        if (mssql_rows_affected($db->handle) > 1) {
            echo "ERROR: DB_MSSQL::getOneRow() returned ".mssql_rows_affected($this->db_handle)." rows!\n";
            if ($this->getDebug()) echo "Query: ".$q."\n";
            die;
        }

        $data = mssql_fetch_array($result, MSSQL_ASSOC);
        mssql_free_result($result);

        parent::measure_query($q);

        return $data;
    }

    /**
     * For SQL SELECT queries who returns one entry of data
     *
     * @param $q the query to execute
     * @return result
     */
    function getOneItem($q)
    {
        parent::measure_time();

        if (!$result = $this->real_query($q)) {
            if ($this->getDebug()) $this->profileError($q, mssql_get_last_message());
            return '';
        }

        if (mssql_rows_affected($db->handle) > 1) {
            echo "ERROR: DB_MSSQL::getOneItem() returned ".mssql_rows_affected($this->db_handle)." rows!\n";
            if ($this->getDebug()) echo "Query: ".$q."\n";
            die;
        }

        $data = mssql_fetch_row($result);
        mssql_free_result($result);

        parent::measure_query($q);

        if (!$data) return false;
        return $data[0];
    }

    /**
     * Lock table from reading
     *
     * @param $t table to lock
     * 
     * FIXME
     */
    function lock($t)
    {
        $this->query('LOCK TABLES '.$t.' READ');
    }

    /**
     * Unlock tables
     * 
     * FIXME
     */
    function unlock()
    {
        $this->query('UNLOCK TABLES');
    }

    /**
     * Returns true if a database with this name already exists
     * 
     * FIXME
     */
    function findDatabase($dbname)
    {
        $list = $this->getArray('SHOW DATABASES');

        foreach ($list as $row)
            if ($row['Database'] == $dbname) return true;

        return false;
    }

    function selectDatabase($dbname)
    {
        $this->database = $dbname;
        return mssql_select_db($this->database, $this->db_handle);
    }

    /**
     * 
     * FIXME
     */
    function createDatabase($dbname, $charset = 'utf8')
    {
        if ($this->findDatabase($dbname)) return false;

        $q = 'CREATE DATABASE '.$dbname.' CHARACTER SET utf8';
        return $this->query($q);
    }

    /**
     * Returns true if a table with this name already exists
     * 
     * FIXME
     */
    function findTable($tblname)
    {
        $list = $this->getNumArray('SHOW TABLES FROM '.$this->database);

        foreach ($list as $row)
            if ($row[0] == $tblname) return true;

        return false;
    }

    /**
     * 
     * FIXME
     */
    function createTable($tblname, $layout, $charset = 'utf8')
    {
        $parsed = $this->parseLayout($layout);

        $q = "CREATE TABLE ".$tblname." (\n";
        $key_pri = '';
        foreach ($parsed as $col) {
            $q .= $col['Field'].' '.$col['Type'];
            switch ($col['Null']) {
            case 'NO': $q .= ' NOT NULL'; break;
            case 'YES': $q .= ' NULL'; break;
            }
            $q .= ($col['Default'] ? " default '".$col['Default']."'" : "");
            $q .= ($col['Extra'] ? ' '.$col['Extra'] : '').",\n";

            if ($col['Key'] == 'PRI') $key_pri = $col['Field'];
        }
        if ($key_pri) {
            $q .= "PRIMARY KEY (".$key_pri.")\n";
        }
        $q .= ") ENGINE=MyISAM DEFAULT CHARSET=".$charset."\n";
        return $this->query($q);
    }

    /**
     * 
     * FIXME
     */
    function verifyTable($tblname, $layout, $charset = 'utf8')
    {
        $list = $this->getArray('DESCRIBE '.$this->database.'.'.$tblname);
        if (!$list) return false;

        $parsed = $this->parseLayout($layout);

        if ($list == $parsed) return true;
        return false;
    }

    /**
     * 
     * FIXME
     */
    function parseLayout($layout)
    {
        $res = array();
        $idx = 0;
        foreach ($layout as $name=>$col) {
            $res[$idx]['Field'] = $name;
            $res[$idx]['Type'] = '';
            $res[$idx]['Null'] = 'YES';
            $res[$idx]['Key'] = '';
            $res[$idx]['Default'] = '';
            $res[$idx]['Extra'] = '';
            foreach ($col as $prop) {
                $ex = explode(':',$prop);

                switch ($ex[0]) {
                case 'key':
                    $res[$idx]['Key'] = 'PRI';
                    break;

                case 'extra':
                    $res[$idx]['Extra'] = $ex[1];
                    break;

                case 'default':
                    $res[$idx]['Default'] = $ex[1];
                    break;

                case 'null':
                    $res[$idx]['Null'] = $ex[1];
                    break;

                case 'datetime':
                case 'text':
                case 'smallint':
                case 'tinyint':
                case 'bigint':
                    $res[$idx]['Type'] = $ex[0].(!empty($ex[1]) ? '('.$ex[1].')' : '').(!empty($ex[2]) ? ' '.$ex[2] : '');
                    break;

                default:
                    echo "createTable: unknown prop ".$ex[0]."\n";
                }
            }
            $idx++;
        }
        return $res;
    }

}


?>
