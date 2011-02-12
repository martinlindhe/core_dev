<?php

//STATUS: wip

/** reads all db columns into properties of a object */
class SqlObject
{
    /**
     * @param $q       a sql select query resulting in multiple rows
     * @param $objname name of class object to load rows into
     */
    static function createObject($q, $objname)
    {
        $db = SqlHandler::getInstance();

        $reflect = new ReflectionClass($objname);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        $res = array();
        foreach ($db->getArray($q) as $row)
        {
            $obj = new $objname();
            foreach ($props as $prop) {
                $n = $prop->getName();
                $obj->$n = $row[ $n ];
            }

            $res[] = $obj;
        }

        return $res;
    }
}


?>
