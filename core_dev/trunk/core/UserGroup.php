<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2012 <martin@startwars.org>
 */

//STATUS: wip

//XXXX TODO rework this as a static class & map to tblUserGroups

require_once('User.php');

class UserGroup
{
    private $name;
    private $id;
    private $level = 0; ///< user level
    private $info;

    private $time_created;
    private $creator_id;

    function __construct($id = 0)
    {
        if ($id)
            $this->loadById($id);
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }
    function getInfo() { return $this->info; }
    function getLevel() { return $this->level; }
    function getTimeCreated() { return $this->time_created; }

    function getCreatorName()
    {
        $u = User::get($this->creator_id);
        if (!$u)
            return false;

        return $u->name;
    }

    function getLevelDesc()
    {
        return getUserLevelName( $this->level );
    }

    function setName($s) { $this->name = $s; }
    function setInfo($s) { $this->info = $s; }
    function setLevel($n) { if (is_numeric($n)) $this->level = $n; }

    function loadById($n)
    {
        if (!is_numeric($n)) return false;

        $q = 'SELECT * FROM tblUserGroups WHERE groupId = ?';
        $row = Sql::pSelectRow($q, 'i', $n);
        $this->loadFromSql($row);
    }

    public static function getByName($s)
    {
        $q = 'SELECT * FROM tblUserGroups WHERE name = ?';
        return Sql::pSelectRow($q, 's', $s);
    }

    function loadFromSql($row)
    {
        $this->name         = $row['name'];
        $this->id           = $row['groupId'];
        $this->level        = $row['level'];
        $this->info         = $row['info'];
        $this->time_created = ts($row['timeCreated']);
        $this->creator_id   = $row['createdBy'];
    }

    /**
     * @return array of User objects for all group members
     */
    function getMembers()
    {
        if (!$this->id)
            throw new Exception ('no group id set');

        $res = array();

        $q = 'SELECT userId FROM tblGroupMembers WHERE groupId = ?';
        foreach (Sql::pSelect1d($q, 'i', $this->id) as $uid)
            $res[] = User::get($uid);

        return $res;
    }

    function save()
    {
        $session = SessionHandler::getInstance();

        if (!$this->id) {
            $q = 'SELECT groupId FROM tblUserGroups WHERE name = ?';
            $this->id = Sql::pSelectItem($q, 's', $this->name);
        }

        if ($this->id) {
            $q = 'UPDATE tblUserGroups SET name = ?, info = ?, level = ? WHERE groupId = ?';
            Sql::pUpdate($q, 'ssii', $this->name, $this->info, $this->level, $this->id);
        } else {
            $q = 'INSERT INTO tblUserGroups SET createdBy = ?, timeCreated = NOW(), name = ?, info = ?, level = ?';
            $this->id = Sql::pInsert($q, 'issi', $session->id, $this->name, $this->info,$this->level);
        }

        return $this->id;
    }

    static function getAll()
    {
        $arr = array();

        foreach (Sql::pSelect('SELECT * FROM tblUserGroups') as $row)
        {
            $item = new UserGroup();
            $item->loadFromSql($row);

            $arr[] = $item;
        }

        return $arr;
    }

    static function create($name, $level)
    {
        $session = SessionHandler::getInstance();

        $q = 'INSERT INTO tblUserGroups SET createdBy = ?, timeCreated = NOW(), name = ?, level = ?';
        return Sql::pInsert($q, 'isi', $session->id, $name, $level);
    }

    /**
     * @return array of id=>name pairs
     */
    static function getIndexedList()
    {
        $res = array();

        foreach (self::getAll() as $i)
            $res[ $i->getId() ] = $i->getName();

        return $res;
    }


}

?>
