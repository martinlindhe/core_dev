<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2013 <martin@ubique.se>
 */

//STATUS: wip

//TODO: use SqlObject stuff
//TODO: add data type (string(binary), int, bool, double) in order to improve automatic handling

namespace cd;

require_once('constants.php');

class Setting
{
    var $id;
    var $owner;
    var $category;
    var $name;
    var $value;
    var $type;    ///< use numbers from 50 and up for application specific types
    var $time_saved;

    protected static $tbl_name = 'tblSettings';

    public static function get($type, $owner, $name, $default = '')
    {
        $q =
        'SELECT value FROM '.self::$tbl_name.
        ' WHERE owner = ? AND type = ? AND name = ?';
        $res = Sql::pSelectRow($q, 'iis', $owner, $type, $name);

        if ($res) return $res['value'];
        return $default;
    }

    public static function set($type, $owner, $name, $val)
    {
        $q =
        'SELECT id FROM '.self::$tbl_name.
        ' WHERE owner = ? AND type = ? AND name = ?';

        if (Sql::pSelectItem($q, 'iis', $owner, $type, $name)) {
            $q =
            'UPDATE '.self::$tbl_name.
            ' SET time_saved = NOW(), value = ?'.
            ' WHERE owner = ? AND type = ? AND name = ?';
            Sql::pUpdate($q, 'siis', $val, $owner, $type, $name);
        } else {
            $q =
            'INSERT INTO '.self::$tbl_name.
            ' SET time_saved = NOW(),'.
            'owner = ?, type = ?, name = ?, value = ?';
            Sql::pInsert($q, 'iiss', $owner, $type, $name, $val);
        }
        return true;
    }

    /** Toggle value between 0 and 1 */
    public static function toggle($type, $owner, $name)
    {
        $val = self::get($type, $owner, $name, 0);

        self::set($type, $owner, $name, $val ? 0 : 1);
    }

    public static function delete($type, $owner, $name)
    {
        $q =
        'DELETE FROM '.self::$tbl_name.
        ' WHERE owner = ? AND type = ? AND name = ?';
        Sql::pDelete($q, 'iis', $owner, $type, $name);
        return true;
    }

    public static function getById($type, $id)
    {
        $q =
        'SELECT value FROM '.self::$tbl_name.
        ' WHERE type = ? AND id = ?';
        return Sql::pSelectItem($q, 'ii', $type, $id);
    }

    /**
     * @return 2d array of all settings for owner
     */
    public static function getAll($type, $owner)
    {
        $q =
        'SELECT id, name, value, category'.
        ' FROM '.self::$tbl_name.
        ' WHERE type = ? AND owner = ?';
        return Sql::pSelect($q, 'ii', $type, $owner);
    }

    /**
     * Used by Token class
     * @return ownerId of the setting with the unique value $val
     */
    public static function getOwner($type, $name, $val)
    {
        $q =
        'SELECT owner FROM '.self::$tbl_name.
        ' WHERE type = ? AND name = ? AND value = ?';
        return Sql::pSelectItem($q, 'iss', $type, $name, $val);
    }

    /**
     * Used by Token class
     */
    public static function getTimeSaved($type, $name, $val)
    {
        $q =
        'SELECT time_saved FROM '.self::$tbl_name.
        ' WHERE type = ? AND name = ? AND value = ?';
        return Sql::pSelectItem($q, 'iss', $type, $name, $val);
    }

}
