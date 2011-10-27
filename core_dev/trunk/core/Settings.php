<?php
/**
 * $Id
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: use SqlObject stuff

require_once('constants.php');

class Settings
{
    var $id;
    var $owner;
    var $category;
    var $name;
    var $value;
    var $type;    ///< use numbers from 50 and up for application specific types
    var $time_saved;

    public static function get($type, $owner, $name, $default = '')
    {
        $q =
        'SELECT value FROM tblSettings'.
        ' WHERE owner = ? AND type = ? AND name = ?';
        $res = Sql::pSelectRow($q, 'iis', $owner, $type, $name);

        if ($res) return $res['value'];
        return $default;
    }

    public static function set($type, $owner, $name, $val)
    {
        $q =
        'SELECT id FROM tblSettings'.
        ' WHERE owner = ? AND type = ? AND name = ?';

        if (Sql::pSelectItem($q, 'iis', $owner, $type, $name)) {
            $q =
            'UPDATE tblSettings SET time_saved = NOW(), value = ?'.
            ' WHERE owner = ? AND type = ? AND name = ?';
            Sql::pUpdate($q, 'siis', $val, $owner, $type, $name);
        } else {
            $q =
            'INSERT INTO tblSettings SET time_saved = NOW(),'.
            'owner = ?, type = ?, name = ?, value = ?';
            Sql::pInsert($q, 'iiss', $owner, $type, $name, $val);
        }
        return true;
    }

    public static function delete($type, $owner, $name)
    {
        throw new Exception ('XXX should work just not testd!');

        $q =
        'DELETE FROM tblSettings'.
        ' WHERE owner = ? AND type = ? AND name = ?';
        Sql::pDelete($q, 'iiis', $owner, $type, $name);
        return true;
    }

    public static function getById($id)
    {
        $q =
        'SELECT value FROM tblSettings'.
        ' WHERE id = ?';
        return Sql::pSelectItem($q, 'i', $id);
    }

}






class Settings__DEPRECATED
{
    /**
     * @return ownerId of the setting with the unique value $val
     */
    function getOwner($name, $val)
    {
        $q =
        'SELECT ownerId FROM tblSettings'.
        ' WHERE categoryId = ? AND settingType = ? AND settingName = ? AND settingValue = ?';
        return Sql::pSelectItem($q, 'iiss', $this->category, $this->type, $name, $val);
    }

    function setOwner($n) { if (is_numeric($n)) $this->owner = $n; }

    function getTimeSaved($name, $val)
    {
        $q =
        'SELECT timeSaved FROM tblSettings'.
        ' WHERE categoryId = ? AND settingType = ? AND settingName = ? AND settingValue = ?';
        return Sql::pSelectItem($q, 'iiss', $this->category, $this->type, $name, $val);
    }

    /**
     * @return 2d array of all settings for owner
     */
    function getAll()
    {
        $q =
        'SELECT settingId, settingName, settingValue, categoryId'.
        ' FROM tblSettings WHERE settingType = ? AND ownerId = ?';
        return Sql::pSelect($q, 'ii', $this->type, $this->owner);
    }

}

?>
