<?php
/**
 * $Id$
 *
 * Utility class for writing prepared SQL statements
 * It always grabs current SqlHandler::getInstance(),
 * so you can write code like:
 *
 *   $id = Sql:pInsert($q, 'i', $val);
 *
 * Prepared statement format:
 *  (isdb), integer, string, double/float, binary
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//REQUIRES PHP 5.3+

class Sql
{
    /** Executes a prepared statement and binds parameters
     * @param $args[0]    sql query
     * @param $args[1]    prepared statement string (isdb)..
     * @param $args[2..n] query parameters, or all params is an array in $args[2]
     */
    protected static function pExecStmt($args)
    {
        if (!$args[0])
            throw new Exception ('no query');

        if (!self::isQueryPrepared($args[0]))
            throw new Exception ('query is not prepared: ... '.$args[0]);

        $db = SqlHandler::getInstance();
        $db->connect();

        if ($db instanceof DatabaseMysqlProfiler)
            $db->startMeasure();

        if (! ($stmt = $db->db_handle->prepare($args[0])) )
            throw new Exception ('FAIL prepare: '.$args[0]);

        $params = array();
        if (isset($args[2]) && is_array($args[2]))
        {
            $x = array();
            for ($i = 0; $i < count($args[2]); $i++)
                $x[] = $args[2][$i];

            for ($i = 0; $i < count($x); $i++)
                $args[2+$i] = $x[$i];
        }

        for ($i = 1; $i < count($args); $i++)
            $params[] = $args[$i];

        if ($params)
            $res = call_user_func_array(array($stmt, 'bind_param'), self::refValues($params));

        if (!$stmt->execute())
            throw new Exception ('query failed: '.$args[0]);

        if ($db instanceof DatabaseMysqlProfiler)
        {
            $prof = &$db->measureQuery($args[0]);  // XXXX rename to finishMeasure()

            $prof->prepared = true;

            if (isset($args[1]))
                $prof->format = $args[1];

            if (isset($args[2]))
                for ($i = 2; $i < count($args); $i++)
                    $prof->params[] = $args[$i];

            if ($params && $res === false)
                $prof->error = $db->db_handle->error;
        }

        return $stmt;
    }

    public static function isQueryPrepared($q)
    {
        $s = $q;
        do
        {
            $p = strpos($s, '=');
            if ($p === false)
                return true;

            $x1 = substr($s, $p+1);
            $x2 = substr( trim($x1), 0, 1);

            $old_s = $s;
            $s = substr($s, $p+1);

            if (is_numeric($x2) || $x2 == '"')
            {
                throw new Exception ('XXX: query is not prepared: (val is '.$x2.') ... '.$old_s);

                echo 'XXX: query is not prepared: (val is '.$x2.') ... '.$old_s.ln();
                return false;
            }

        } while ($s);

        return true;
    }

    /**
     * Escapes a string for use in queries
     *
     * @param $q is the query to escape
     * @return the escaped string, taking db-connection locale into account
     */
    public static function escape($q)
    {
        //db handle is needed to use the escape function... XXX, REALLY?, WHY???
        $db = SqlHandler::getInstance();
        $db->connect();

        return $db->db_handle->real_escape_string($q);
    }

    /**
     * @return true if $order is a valid sort order
     */
    public static function isValidOrder($order)
    {
        $valid = array('DESC', 'ASC');

        if (!in_array(strtoupper($order), $valid))
            return false;

        return true;
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
    public static function pSelect()
    {
        $stmt = self::pExecStmt( func_get_args() );

        $data = array();

        $meta = $stmt->result_metadata();

        while ($field = $meta->fetch_field())
            $parameters[] = &$row[$field->name];

        call_user_func_array(array($stmt, 'bind_result'), self::refValues($parameters));

        while ($stmt->fetch())
        {
            $x = array();
            foreach ($row as $key => $val)
                $x[$key] = $val;

            $data[] = $x;
        }

        $meta->close();

        $stmt->close();

        return $data;
    }

    public static function pSelectRow()
    {
        $res = call_user_func_array('Sql::pSelect', func_get_args() );  // HACK to pass dynamic variables to parent method

        if (count($res) > 1) {
//            d( func_get_args() );
            throw new Exception ('DatabaseMysql::pSelectRow() returned '.count($res).' rows');
        }

        if (!$res)
            return false;

        return $res[0];
    }

    public static function pSelectItem()
    {
        $stmt = self::pExecStmt( func_get_args() );

        if ($stmt->field_count != 1)
            throw new Exception ('expected 1 column result, got '.$stmt->field_count.' columns');

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

    /** selects 1d array */
    public static function pSelect1d()
    {
        $stmt = self::pExecStmt( func_get_args() );

        $data = array();

        if ($stmt->field_count != 1)
            throw new Exception ('not 1d result');

        $stmt->bind_result($col1);

        while ($stmt->fetch())
            $data[] = $col1;

        $stmt->close();
        return $data;
    }

    /** like getMappedArray(). query selects a list of key->value pairs */
    public static function pSelectMapped()
    {
        $stmt = self::pExecStmt( func_get_args() );

        $data = array();

        if ($stmt->field_count != 2)
            throw new Exception ('pSelectMapped requires a key->val result set');

        // 2d array
        $stmt->bind_result($col1, $col2);

        while ($stmt->fetch())
            $data[ $col1 ] = $col2;

        $stmt->close();
        return $data;
    }


    /** like pSelect, but returns affected rows */
    public static function pDelete()
    {
        $stmt = self::pExecStmt( func_get_args() );

        $data = $stmt->affected_rows;

        $stmt->close();
        return $data;
    }

    /** like pDelete */
    public static function pUpdate()
    {
        $args = func_get_args();
        return call_user_func_array(array('self', 'pDelete'), $args);  // HACK to pass dynamic variables to parent method
    }

    /** like pDelete, but returns insert id */
    public static function pInsert()
    {
        $args = func_get_args();
        $res = call_user_func_array(array('self', 'pDelete'), $args);  // HACK to pass dynamic variables to parent method

        if ($res != 1)
            throw new Exception ('insert fail: '.$args[0]);

        $db = SqlHandler::getInstance();
        return $db->db_handle->insert_id;
    }

    /** HACK needed for some reason */
    protected static function refValues($arr)
    {
        // COMPAT: PHP 5.3+ is required for references
        $refs = array();
        foreach ($arr as $key => $val)
            $refs[$key] = &$arr[$key];

        return $refs;
    }

}

?>
