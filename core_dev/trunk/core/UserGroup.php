<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

class UserGroup
{
    private $name;
    private $id;
    private $level = 0; ///< user level
    private $info;

    function __construct($id = 0)
    {
        if ($id)
            $this->loadById($id);
    }

    static function getUserlevels()
    {
        return array(
        USERLEVEL_NORMAL     => 'Normal',
        USERLEVEL_WEBMASTER  => 'Webmaster',
        USERLEVEL_ADMIN      => 'Admin',
        USERLEVEL_SUPERADMIN => 'Super Admin',
        );
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }
    function getInfo() { return $this->info; }
    function getLevel() { return $this->level; }

    function getLevelDesc()
    {
        $x = $this->getUserlevels();
        return $x[ $this->level ];
    }

    function setName($s) { $this->name = $s; }
    function setInfo($s) { $this->info = $s; }
    function setLevel($n) { if (is_numeric($n)) $this->level = $n; }

    function loadById($n)
    {
        if (!is_numeric($n)) return false;

        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUserGroups WHERE groupId='.$n;
        $row = $db->getOneRow($q);
        $this->loadFromSql($row);
    }

    function loadFromSql($row)
    {
        $this->name  = $row['name'];
        $this->id    = $row['groupId'];
        $this->level = $row['level'];
        $this->info  = $row['info'];
    }

    function save()
    {
        $db = SqlHandler::getInstance();

        if (!$this->id) {
            $q = 'SELECT groupId FROM tblUserGroups WHERE name="'.$db->escape($this->name).'"';
            $this->id = $db->getOneItem($q);
        }

        if ($this->id) {
            $q = 'UPDATE tblUserGroups SET name="'.$db->escape($this->name).'",info="'.$db->escape($this->info).'",level='.$this->level.' WHERE groupId='.$this->id;
            $db->update($q);
        } else {
            $q = 'INSERT INTO tblUserGroups SET name="'.$db->escape($this->name).'",info="'.$db->escape($this->info).'",level='.$this->level;
            $this->id = $db->insert($q);
        }

        return $this->id;
    }

}

?>
