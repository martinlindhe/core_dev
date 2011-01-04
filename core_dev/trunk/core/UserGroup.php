<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

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
        $u = new User($this->creator_id);
        return $u->getName();
    }

    function getLevelDesc()
    {
        $x = User::getUserLevels();
        return $x[ $this->level ];
    }

    function setName($s) { $this->name = $s; }
    function setInfo($s) { $this->info = $s; }
    function setLevel($n) { if (is_numeric($n)) $this->level = $n; }

    function loadById($n)
    {
        if (!is_numeric($n)) return false;

        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUserGroups WHERE groupId = ?';
        $row = $db->pSelectRow($q, 'i', $n);
        $this->loadFromSql($row);
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

        $db = SqlHandler::getInstance();

        $res = array();

        $q = 'SELECT userId FROM tblGroupMembers WHERE groupId='.$this->id;
        foreach ($db->get1dArray($q) as $uid)
            $res[] = new User($uid);

        return $res;
    }

    function save()
    {
        $db = SqlHandler::getInstance();

        $session = SessionHandler::getInstance();

        if (!$this->id) {
            $q = 'SELECT groupId FROM tblUserGroups WHERE name="'.$db->escape($this->name).'"';
            $this->id = $db->getOneItem($q);
        }

        if ($this->id) {
            $q = 'UPDATE tblUserGroups SET name="'.$db->escape($this->name).'",info="'.$db->escape($this->info).'",level='.$this->level.' WHERE groupId='.$this->id;
            $db->update($q);
        } else {
            $q = 'INSERT INTO tblUserGroups SET createdBy='.$session->id.',timeCreated=NOW(),name="'.$db->escape($this->name).'",info="'.$db->escape($this->info).'",level='.$this->level;
            $this->id = $db->insert($q);
        }

        return $this->id;
    }

    function render()
    {
        $view = new ViewModel('views/admin_UserGroup.php', $this);
        return $view->render();
    }

}

?>
