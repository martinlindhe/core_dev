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
 * @author Martin Lindhe, 2010-2014 <martin@ubique.se>
 */

//STATUS: wip

//REQUIRES PHP 5.4+

namespace cd;

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
            throw new \Exception ('no query');

        $db = SqlHandler::getInstance();
        $db->connect();

        if ($db->isProfilingEnabled())
            $db->startMeasure();

        if (! ($stmt = $db->db_handle->prepare($args[0])) )
            throw new \Exception ('FAIL prepare: '.$args[0]);

        if (isset($args[2]) && is_array($args[2]))
        {
            $x = array();
            for ($i = 0; $i < count($args[2]); $i++)
                $x[] = $args[2][$i];

            for ($i = 0; $i < count($x); $i++)
                $args[2+$i] = $x[$i];
         }


        for ($i = 1; $i < count($args)-1; $i++) {
            //echo "binding arg ".$i.", ".$args[$i+1]."<br>\n";
            $stmt->bindValue($i, $args[$i+1]);
        }

        $res = $stmt->execute();
        if (!$res) {
            $err = $stmt->errorInfo();

            $s = $err[0].' '.$err[2].' (execute failed: '.$args[0].')';

            if (!empty($args[1]))
                $s .= ' ('.$args[1].')';
            throw new \Exception ($s);
        }

        if ($db->isProfilingEnabled())
        {
            $prof = &$db->finishMeasure($args[0]);

            $prof->prepared = true;

            if (isset($args[1]))
                $prof->format = $args[1];

            if (isset($args[2]))
                for ($i = 2; $i < count($args); $i++)
                    $prof->params[] = $args[$i];

            if ($res === false)
                $prof->error = $db->db_handle->error;
        }

        return $stmt;
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

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function pStoredProc()
    {
        $stmt = self::pExecStmt( func_get_args() );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function pSelectRow()
    {
        $stmt = self::pExecStmt( func_get_args() );

        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($res) > 1) {
//            d( func_get_args() );
            throw new \Exception ('returned '.count($res).' rows');
        }

        if (!$res)
            return false;

        return $res[0];
    }

    public static function pSelectItem()
    {
        $stmt = self::pExecStmt( func_get_args() );

        if ($stmt->columnCount() != 1)
            throw new \Exception ('expected 1 column result, got '.$stmt->field_count.' columns');


        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($res) != 1 || count($res[0]) != 1) {
            if (count($res) == 0)
                return 0;

            d($res);
            throw new \Exception ("fail res");
        }

        return array_shift($res[0]);
    }

    /** selects 1d array */
    /*
    public static function pSelect1d()
    {
        $stmt = self::pExecStmt( func_get_args() );

        $data = array();

        if ($stmt->field_count != 1)
            throw new \Exception ('not 1d result');

        $stmt->bind_result($col1);

        while ($stmt->fetch())
            $data[] = $col1;

        $stmt->free_result();
        $stmt->close();

        return $data;
    }*/

    /** like getMappedArray(). query selects a list of key->value pairs */
    /*
    public static function pSelectMapped()
    {
        $stmt = self::pExecStmt( func_get_args() );

        $data = array();

        if ($stmt->field_count != 2)
            throw new \Exception ('result is not 2 fields wide, requires a key->val result set');

        // 2d array
        $stmt->bind_result($col1, $col2);

        while ($stmt->fetch())
            $data[ $col1 ] = $col2;

        $stmt->free_result();
        $stmt->close();

        return $data;
    }*/


    /** returns number of affected rows */
    public static function pDelete()
    {
        $stmt = self::pExecStmt( func_get_args() );
        return $stmt->rowCount();
    }

    public static function pTruncate()
    {
        $stmt = self::pExecStmt( func_get_args() );
        return $stmt->rowCount();
    }

    public static function pUpdate()
    {
        $stmt = self::pExecStmt( func_get_args() );
        return $stmt->rowCount();
    }

    public static function pSet()
    {
        $stmt = self::pExecStmt( func_get_args() );
        return $stmt->rowCount();
    }

    /** returns insert id */
    public static function pInsert()
    {
        $stmt = self::pExecStmt( func_get_args() );

        if ($stmt->rowCount() != 1) {
            $args = func_get_args();
            throw new \Exception ('insert fail: '.$args[0]);
        }

        $db = SqlHandler::getInstance();
        return $db->db_handle->lastInsertId();
    }

}
