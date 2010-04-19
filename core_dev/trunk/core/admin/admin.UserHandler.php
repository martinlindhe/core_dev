<?php
/**
 * $Id$
 *
 * Class to deal with creating and modifying a user
 */

//STATUS: wip

class UserHandler
{
    private $id;
    private $name;

    function __construct($id = 0)
    {
        if (is_numeric($id)) {

            $user = new User($id);
            $this->id   = $user->getId();
            $this->name = $user->getName();
        }
    }

    function getId() { return $this->id; }
    function getName() { return $this->name; }

    function create($username, $usermode)
    {
        global $db;
        if (!is_numeric($usermode)) return false;

        $q = 'INSERT INTO tblUsers SET userName="'.$db->escape($username).'",userMode='.$usermode.',timeCreated=NOW()';
        $this->id   = $db->insert($q);
        $this->name = $username;

        dp('Created user '.$this->id.' with usermode '.$usermode);

        return $this->id;
    }

    /**
     * Marks specified user as "deleted"
     */
    function remove()
    {
        global $db;

        $q = 'UPDATE tblUsers SET timeDeleted=NOW() WHERE userId='.$this->id;
        $db->update($q);
    }


/*
    function setUserName($username)
    {
        global $db;

        $q = 'UPDATE tblUsers SET userName="'.$db->escape($username).'" WHERE userId='.$this->id;
        $db->update($q);
    }
*/
    /**
     * Sets a new password for the user
     *
     * @param $_id user id
     * @param $_pwd password to set
     */
    function setPassword($_pwd)
    {
        global $db;

        $auth = AuthHandler::getInstance();
        $q = 'UPDATE tblUsers SET userPass="'.sha1( $this->id.sha1( $auth->getEncryptKey() ).sha1($_pwd) ).'" WHERE userId='.$this->id;
        $db->update($q);

        return true;
    }

    /**
     * Set user mode to $_mode
     */
    function setMode($usermode)
    {
        global $db;
        if (!is_numeric($usermode)) return false;

        $q = 'UPDATE tblUsers SET userMode='.$usermode.' WHERE userId='.$this->id;
        $db->update($q);

        dp('Changed usermode for user '.$this->id.' to '.$usermode);
        return true;
    }

}

?>
