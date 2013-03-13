<?php
/**
 * $Id$
 *
 * Reads database columns into properties of objects
 *
 * @author Martin Lindhe, 2011-2013 <martin@startwars.org>
 */

//STATUS: wip

//XXX move some of the methods to Sql.php

namespace cd;

require_once('core.php');
require_once('Sql.php');

class ReflectedObject
{
    var $str;
    var $props = array();  ///< array of ReflectedProperty
    var $cols  = array();  ///< class properties / table column names
    var $vals  = array();  ///< class property / table column value
}

class SqlObject
{
    public static function stringForm($s)
    {
        if ($s && !is_string($s) && !is_numeric($s))
            throw new \Exception ('not a string: '.$s);

        if (substr($s, 0, 1) == '0')
            return 's';

        if (is_numeric($s) && strpos($s, '.') !== false)
            return 'd';

        if (numbers_only($s))
            return 'i';
        return 's';
    }

    /**
     * Creates one object $classname
     * @param $q a sql select query resulting in one row, or a indexed array
     * @param $classname name of class object to load rows into
     */
    public static function loadObject($q, $classname)
    {
        if (!$q) {
            // return new $classname();   // TODO-LATER default to always return a object of type $classname
            return false;
        }

        $list = is_array($q) ? $q : Sql::pSelect($q);

        if (!is_array($list))
            throw new \Exception ('loadObject fail, need array of rows, got: '.$list);

        if (class_exists(__NAMESPACE__.'\\'.$classname))
            $classname = __NAMESPACE__.'\\'.$classname;

        $reflect = new \ReflectionClass($classname);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);

        $found = array();
        $obj = new $classname();
        foreach ($props as $prop)
        {
            $n = $prop->getName();

            if (array_key_exists($n, $list)) {
                $obj->$n = $list[ $n ];
                $found[$n] = true;
                continue;
            }

            throw new \Exception ('class '.$classname.' expects database column named "'.$n.'" which dont exist');
        }

        if ( count($props) != count($list))
        {
            foreach ($list as $idx => $row) {
                if (!array_key_exists($idx, $found))
                    throw new \Exception ('class '.$classname.' misses define of variable '.$idx);
            }

            throw new \Exception ('class '.$classname.' ERROR - SHOULD NOT HAPPEN!');
        }

        return $obj;
    }

    /**
     * Creates an array of $objname objects from input query/indexed array
     * @param $q       a sql select query resulting in multiple rows, or a array of rows
     * @param $classname name of class object to load rows into
     */
    public static function loadObjects($q, $classname)
    {
        $list = is_array($q) ? $q : Sql::pSelect($q);

        $res = array();
        foreach ($list as $row)
            $res[] = self::loadObject($row, $classname);

        return $res;
    }

    /**
     * Helper function, useful for parsing an object for a XhtmlForm dropdown etc
     * @return id->name paired array
     */
    public static function getListAsIdNameArray($arr, $id_name = 'id', $name_name = 'name')   // XXXX rename?
    {
        $res = array();
        foreach ($arr as $o)
            $res[ $o->$id_name ] = $o->$name_name;

        return $res;
    }

    /**
     * Creates part of a sql statement out of public properties of $obj
     *
     * @param $obj
     * @param $exclude_col
     * @param $include_unset  shall unset object properties be included in result?
     * @return ReflectedObject
     */
    protected static function reflectQuery($obj, $exclude_col = '', $include_unset = true)
    {
        $reflect = new \ReflectionClass($obj);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);

        // we escape column names for reserved SQL words
        // full list at http://dev.mysql.com/doc/refman/5.5/en/reserved-words.html
        // the list is huge, so we only cover common use cases
        $reserved_words = array('desc', 'default', 'group', 'from', 'to');

        $res = new ReflectedObject();

        foreach ($props as $prop)
        {
            $col = $prop->getName();
            if ($col == $exclude_col)
                continue;

            if (!$include_unset && !$obj->$col)
                continue;

            $res->str .= self::stringForm($obj->$col);

            $res->vals[] = $obj->$col;

            if (in_array($col, $reserved_words))
                $res->cols[] = '`'.$col.'` = ?';
            else
                $res->cols[] = $col.' = ?';
        }
        //d($res);
        return $res;
    }

    public static function idExists($id, $tblname, $field_name = 'id')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($field_name))
            throw new \Exception ('very bad');

        $q =
        'SELECT COUNT(*) FROM '.$tblname.
        ' WHERE '.$field_name.' = ?';

        $form = self::stringForm($id);

        return Sql::pSelectItem($q, $form, $id) ? true : false;
    }

    /**
     * Compares the object:s set properties to table columns
     * @return true if object exists
     **/
    public static function exists($obj, $tblname)
    {
        if (!is_alphanumeric($tblname))
            throw new \Exception ('very bad');

        $reflect = self::reflectQuery($obj, '', false);

        $q =
        'SELECT COUNT(*) FROM '.$tblname.
        ' WHERE '.implode(' AND ', $reflect->cols);

        return Sql::pSelectItem($q, $reflect->str, $reflect->vals) ? true : false;
    }

    /**
     * Creates a object in a database table
     * @return insert id
     */
    public static function create($obj, $tblname)
    {
        if (!is_alphanumeric($tblname))
            throw new \Exception ('very bad');

        $reflect = self::reflectQuery($obj, '', false);

        $q = 'INSERT INTO '.$tblname.
        ' SET '.implode(', ', $reflect->cols);

        return Sql::pInsert($q, $reflect->str, $reflect->vals);
    }

    /**
     * If object exists with same name as field in $field_name, already in db, return false
     */
    public static function storeUnique($obj, $tblname)
    {
        if (self::exists($obj, $tblname))
            return false;

        return self::create($obj, $tblname);
    }

    /**
     * @param $field_name if object exists with this name, then update that item
     */
    public static function store($obj, $tblname, $field_name = 'id')
    {
        if ($obj->$field_name && SqlObject::idExists($obj->$field_name, $tblname, $field_name))
        {
//            throw new \Exception ('obj fieldname: '.$obj->$field_name.' tbl '.$tblname);

            SqlObject::updateId($obj, $tblname, $field_name);
            return $obj->$field_name;
        }

        return SqlObject::create($obj, $tblname);
    }

    public static function getById($id, $tblname, $classname, $field_name = 'id')
    {
        return self::getByField($id, $tblname, $classname, $field_name);
    }

    public static function getByField($val, $tblname, $classname, $field_name)
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($field_name))
            throw new \Exception ('very bad');

        $form = self::stringForm($val);

        $q =
         'SELECT * FROM '.$tblname.
        ' WHERE '.$field_name.' = ?';
        $row = Sql::pSelectRow($q, $form, $val);

        return SqlObject::loadObject($row, $classname);
    }

    public static function deleteById($id, $tblname, $field_name = 'id')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($field_name))
            throw new \Exception ('very bad');

        if (!is_numeric($id))
            throw new \Exception ('bad data'. $id);

        $q =
         'DELETE FROM '.$tblname.
        ' WHERE '.$field_name.' = ?';
        Sql::pDelete($q, 'i', $id);
    }

    /**
     * Fetches all items where $field_name = $value
     * @param $order 'desc', 'asc' or empty
     */
    public static function getAllByField($field_name, $value, $tblname, $classname, $order_field = '', $order = 'asc')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($field_name) || !is_alphanumeric($order_field))
            throw new \Exception ('very bad');

        if (!Sql::isValidOrder($order))
            throw new \Exception ('odd order '.$order);

        $form = self::stringForm($value);

        $q =
        'SELECT * FROM '.$tblname.' WHERE '.$field_name.' = ?'.
        ($order_field ? ' ORDER BY '.$order_field.' '.strtoupper($order) : '');

        $list = Sql::pSelect($q, $form, $value);

        return SqlObject::loadObjects($list, $classname);
    }

    public static function updateId($obj, $tblname, $field_name = 'id')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($field_name))
            throw new \Exception ('very bad');

        if (!$obj->$field_name)
        {
            d($obj);
            throw new \Exception ('eehh');
        }

        $reflect = self::reflectQuery($obj, $field_name);

        $q =
        'UPDATE '.$tblname.
        ' SET '.implode(', ', $reflect->cols).
        ' WHERE '.$field_name.' = ?';

        $reflect->str .= (is_numeric($obj->$field_name) ? 'i' : 's');
        $reflect->vals[] = $obj->$field_name;

        return Sql::pUpdate($q, $reflect->str, $reflect->vals);
    }

}
