<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

// STATUS: early wip

class UserDataField
{
    var $id;
    var $type;
    var $name;

    protected static $tbl_name = 'tblUserDataField';

    public static function get($id)
    {
        return SqlObject::getById($id, self::$tbl_name, __CLASS__);
    }

    public static function getAll()
    {
        $q =
        'SELECT * FROM '.self::$tbl_name.
        ' ORDER BY name ASC';
        return SqlObject::loadObjects($q, __CLASS__);
    }

    public static function store($obj)
    {
        return SqlObject::store($obj, self::$tbl_name, 'id');
    }

    public static function getTypes()
    {
        return array(
        1 => 'String',
        2 => 'Textarea',
        3 => 'E-mail',
//        4 => 'Image',
//        5 => 'Checkbox',
//        6 => 'Radio',  // options stored in tblSettings ??!?=!
        );
    }

}

?>
