<?php

//STATUS: wip

/** reads all db columns into properties of a object */
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

        $row = is_array($q) ? $q : $db->pSelect($q);

        $reflect = new ReflectionClass($classname);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        $res = array();

        if (!is_array($row))
            throw new Exception ('need array of row, got: '.$row);

        $obj = new $classname();
        foreach ($props as $prop) {
            $n = $prop->getName();
            if (!array_key_exists($n, $row)) {
                d( $row);
                throw new Exception ('SqlObject not right! db column named "'.$n.'" dont exist');
            }
            $obj->$n = $row[ $n ];
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
        $db = SqlHandler::getInstance();

        $list = is_array($q) ? $q : $db->pSelect($q);

        $res = array();
        foreach ($list as $row)
            $res[] = self::loadObject($row, $classname);

        return $res;
    }

    static function idExists($id, $tblname, $id_field = 'id')
    {
        $db = SqlHandler::getInstance();
        $q = 'SELECT COUNT(*) FROM '.$tblname.' WHERE '.$id_field.' = ?';

        return $db->pSelectItem($q, 's', $id) ? true : false;
    }

    /**
     * Creates a object in a database table
     * @return insert id
     */
    static function create($obj, $tblname)
    {
        $db = SqlHandler::getInstance();

        $vals = self::reflectQuery($obj);

        $q = 'INSERT INTO '.$tblname.' SET '.implode(', ', $vals);
        return $db->insert($q);
    }

    /**
     * If object was with same name as field in $id_field, already in db, return false
     */
    static function storeUnique($obj, $tblname, $id_field)
    {
        if (self::idExists($obj->$id_field, $tblname, $id_field))
            return false;

        return self::create($obj, $tblname);
    }

    static function updateId($obj, $tblname, $id_field = 'id')
    {
        $db = SqlHandler::getInstance();

        $vals = self::reflectQuery($obj, $id_field);

        $q = 'UPDATE '.$tblname.' SET '.implode(', ', $vals).' WHERE '.$id_field.' = '.$obj->id;
        return $db->update($q);
    }

    protected static function reflectQuery($obj, $exclude_col = '')
    {
        $db = SqlHandler::getInstance();

        $reflect = new ReflectionClass($obj);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        $bad_names = array('desc', 'asc');

        $vals = array();
        foreach ($props as $prop)
        {
            $col = $prop->getName();
            if ($col == $exclude_col)
                continue;

            if (in_array($col, $bad_names))
                throw new Exception ('"'.$tblname.'.'.$col.'" should be renamed, "'.$col.'" is reserved MySQL syntax');

            $vals[] = $col.'="'.$db->escape($obj->$col).'"';
        }
        return $vals;
    }
}


?>
