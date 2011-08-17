<?php
/**
 * $Id$
 *
 * Reads database columns into properties of objects
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: add a "delete" method

require_once('Sql.php');

class SqlObject
{
    /**
     * Creates one object $objname
     * @param $q       a sql select query resulting in one row, or a indexed array
     * @param $classname name of class object to load rows into
     */
    static function loadObject($q, $classname)
    {
        $db = SqlHandler::getInstance();

        if (!$q) {
            return false;
//            return new $classname();
//            throw new Exception ('no query');
        }

        $row = is_array($q) ? $q : $db->pSelect($q);

        if (!is_array($row))
            throw new Exception ('loadObject fail, need array of rows, got: '.$row);

        $reflect = new ReflectionClass($classname);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        $obj = new $classname();
        foreach ($props as $prop)
        {
            $n = $prop->getName();

            if (!array_key_exists($n, $row)) {
                d( $row);
                throw new Exception ('loadObject fail, db column named "'.$n.'" dont exist');
            }
            $obj->$n = $row[ $n ];
        }

        if ( count($props) != count($row))
        {
/*
            d($row);
            d( count($row ) );

            d($props);
            d( count($props ) );
*/
            throw new Exception ('loadObject fail, class '.$classname.' misses defined variables, or something!');
        }

        return $obj;
    }

    /**
     * Creates an array of $objname objects from input query/indexed array
     * @param $q       a sql select query resulting in multiple rows, or a array of rows
     * @param $classname name of class object to load rows into
     */
    static function loadObjects($q, $classname)
    {
        $list = is_array($q) ? $q : Sql::pSelect($q);

        $res = array();
        foreach ($list as $row)
            $res[] = self::loadObject($row, $classname);

        return $res;
    }

    /** XXX document */
    protected static function reflectQuery($obj, $exclude_col = '')
    {
        $reflect = new ReflectionClass($obj);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        // full list at http://dev.mysql.com/doc/refman/5.5/en/reserved-words.html
        // the list is huge, so we only try to cover common ones
        $reserved_words = array('desc', 'default');

        $vals = array();
        foreach ($props as $prop)
        {
            $col = $prop->getName();
            if ($col == $exclude_col)
                continue;

            if (in_array($col, $reserved_words))
                // escape column names for reserved SQL words
                $vals[] = '`'.$col.'`="'.Sql::escape($obj->$col).'"';
            else
                $vals[] = $col.'="'.Sql::escape($obj->$col).'"';
        }
        return $vals;
    }

    static function idExists($id, $tblname, $id_field = 'id')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($id_field))
            throw new Exception ('very bad');

        $q = 'SELECT COUNT(*) FROM '.$tblname.' WHERE '.$id_field.' = ?';

        return Sql::pSelectItem($q, 's', $id) ? true : false;
    }

    /**
     * Creates a object in a database table
     * @return insert id
     */
    static function create($obj, $tblname)
    {
        if (!is_alphanumeric($tblname))
            throw new Exception ('very bad');

        $vals = self::reflectQuery($obj);

        $q = 'INSERT INTO '.$tblname.' SET '.implode(', ', $vals);
        return SqlHandler::getInstance()->insert($q); ///XXXXXXXXXXXX use Sql::pInsert ..!!!
    }

    /**
     * If object exists with same name as field in $id_field, already in db, return false
     */
    static function storeUnique($obj, $tblname, $id_field = 'id')
    {
        if (self::idExists($obj->$id_field, $tblname, $id_field))
            return false;

        return self::create($obj, $tblname);
    }

    /**
     * If object exists with the same name as field in $id_field, update that item
     */
    static function store($obj, $tblname, $id_field = 'id')
    {
        if (SqlObject::idExists($obj->$id_field, $tblname, $id_field)) {
            sqlObject::updateId($obj, $tblname, $id_field);
            return $obj->id;
        }

        return SqlObject::create($obj, $tblname);
    }

    static function getById($id, $tblname, $classname, $id_field = 'id')
    {
        return self::getByField($id, $tblname, $classname, $id_field);
    }

    static function getByField($id, $tblname, $classname, $field_name = 'id')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($field_name))
            throw new Exception ('very bad');

        $q = 'SELECT * FROM '.$tblname.' WHERE '.$field_name.' = ?';
        $row = Sql::pSelectRow($q, 'i', $id);

        return SqlObject::loadObject($row, $classname);
    }

    static function deleteById($id, $tblname, $field_name = 'id')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($field_name))
            throw new Exception ('very bad');

        $q = 'DELETE FROM '.$tblname.' WHERE '.$field_name.' = ?';
        Sql::pDelete($q, 'i', $id);
    }

    /**
     * Fetches all items where $field_name = $value
     * @param $order 'desc', 'asc' or empty
     */
    static function getAllByField($field_name, $value, $tblname, $classname, $order_field = '', $order = 'asc')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($field_name) || !is_alphanumeric($order_field))
            throw new Exception ('very bad');

        if (!in_array(strtoupper($order), array('DESC', 'ASC')))
            throw new Exception ('odd order '.$order);

        $q = 'SELECT * FROM '.$tblname.' WHERE '.$field_name.' = ?';

        if (is_numeric($value))
            $form = 'i';
        else
            $form = 's';

        if ($order_field)
            $q .= ' ORDER BY '.$order_field.' '.strtoupper($order);

        $list = Sql::pSelect($q, $form, $value);

        return SqlObject::loadObjects($list, $classname);
    }

    static function updateId($obj, $tblname, $id_field = 'id')
    {
        if (!is_alphanumeric($tblname) || !is_alphanumeric($id_field))
            throw new Exception ('very bad');

        $vals = self::reflectQuery($obj, $id_field);

        $q = 'UPDATE '.$tblname.' SET '.implode(', ', $vals).' WHERE '.$id_field.' = '.$obj->id;
        return SqlHandler::getInstance()->update($q);  //XXXXXXXXXXx use Sql::pUpdate
    }

}

?>
