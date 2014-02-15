<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011 <martin@ubique.se>
 */

// STATUS: wip

namespace cd;

require_once('UserDataFieldOption.php');

class UserDataField
{
    var $id;
    var $type;
    var $name;   ///< field name (to refer to field in code)
    var $label;  ///< field label (to show end user)

    const STRING   = 1;
    const TEXTAREA = 2;
    const EMAIL    = 3;
    const IMAGE    = 4;
    const CHECKBOX = 5;
    const RADIO    = 6;
    const AVATAR   = 7;

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

    public static function getTypes()
    {
        return array(
        self::STRING   => 'String',
        self::TEXTAREA => 'Textarea',
        self::EMAIL    => 'E-mail',
        self::IMAGE    => 'Image',
        self::CHECKBOX => 'Checkbox',
        self::RADIO    => 'Radio',  // options stored in tblSettings
        self::AVATAR   => 'Avatar', // let user select one of multiple avatar images provided by the site
        );
    }

    public function store()
    {
        return SqlObject::store($this, self::$tbl_name, 'id');
    }

}
