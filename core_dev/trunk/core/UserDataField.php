<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

// STATUS: early wip

require_once('UserDataFieldOption.php');

class UserDataField
{
    var $id;
    var $type;
    var $name;

    const STRING   = 1;
    const TEXTAREA = 2;
    const EMAIL    = 3;
    const IMAGE    = 4;
    const RADIO    = 6;

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
        self::STRING   => 'String',
        self::TEXTAREA => 'Textarea',
        self::EMAIL    => 'E-mail',
        self::IMAGE    => 'Image',
//        5 => 'Checkbox',
        self::RADIO    => 'Radio',  // options stored in tblSettings ??!?=!
        );
    }

}

?>
