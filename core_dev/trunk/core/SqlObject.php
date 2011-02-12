<?php

//STATUS: wip

/** reads all db columns into properties of a object */
class SqlObject
{
    /**
     * @param $q       a sql select query resulting in multiple rows, or a array of rows
     * @param $objname name of class object to load rows into
     */
    static function createObject($q, $objname)
    {
        $db = SqlHandler::getInstance();

        $list = is_array($q) ? $q : $db->pSelect($q);

        $reflect = new ReflectionClass($objname);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        $res = array();
        foreach ($list as $row)
        {
            $obj = new $objname();
            foreach ($props as $prop) {
                $n = $prop->getName();
                if (!array_key_exists($n, $row)) {
                    d( $row);
                    throw new Exception ('SqlObject not right! db column named "'.$n.'" dont exist');
                }
                $obj->$n = $row[ $n ];
            }

            $res[] = $obj;
        }

        return $res;
    }
}


?>
