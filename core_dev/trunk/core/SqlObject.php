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
    static function createObject($q, $classname)
    {
        $db = SqlHandler::getInstance();

        $row = is_array($q) ? $q : $db->pSelect($q);

        $reflect = new ReflectionClass($classname);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

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
    static function createObjects($q, $classname)
    {
        $db = SqlHandler::getInstance();

        $list = is_array($q) ? $q : $db->pSelect($q);

        $res = array();
        foreach ($list as $row)
            $res[] = self::createObject($row, $classname);

        return $res;
    }

    /**
     * Stores a object in a database table
     * @return insert id
     */
    static function store($obj, $tblname)
    {
        $db = SqlHandler::getInstance();

        $reflect = new ReflectionClass($obj);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        $bad_names = array('desc', 'asc');

        $vals = array();
        foreach ($props as $prop) {
            $col = $prop->getName();
            if (in_array($col, $bad_names))
                throw new Exception ('"'.$tblname.'.'.$col.'" should be renamed, "'.$col.'" is reserved MySQL syntax');
            if ($obj->$col)
                $vals[] = $col.'="'.$db->escape($obj->$col).'"';
        }

        $q = 'INSERT INTO '.$tblname.' SET '.implode(', ', $vals);
        return $db->insert($q);
    }

}


?>
