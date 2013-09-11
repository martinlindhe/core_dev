<?php
/**
 * $Id$
 *
 * Maps a simple Item to a MySQL database table entry
 *
 * @author Martin Lindhe, 2013 <martin@startwars.org>
 */

namespace cd;

abstract class ItemStorageMySQL
{
    public static function getTableName() { return static::$tbl_name; }

    public static function get($id)
    {
        return SqlObject::getById($id, static::$tbl_name, get_called_class() );
    }

    public static function store($obj)
    {
        $called = get_called_class();
        if (!($obj instanceof $called))
            throw new \Exception ('not a '.$called.' object');

        return SqlObject::store($obj, static::$tbl_name, 'id');
    }

    public static function getAll()
    {
        return SqlObject::getAll(static::$tbl_name, get_called_class() );
    }

    public static function getAllByOwner($id)
    {
        return SqlObject::getAllByField('owner', $id, static::$tbl_name, get_called_class() );
    }

    public static function getAllByField($field_name, $value)
    {
        return SqlObject::getAllByField($field_name, $value, static::$tbl_name, get_called_class() );
    }
}
