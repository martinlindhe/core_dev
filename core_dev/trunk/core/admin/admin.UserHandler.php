<?php
/**
 * $Id$
 *
 * Class to deal with registering, editing, removing, validating, activating etc a user
 */

//STATUS: draft

class UserHandler
{
    function registerUser($username, $mode)
    {
        global $db;
        if (!is_numeric($mode)) return false;

        $q = 'INSERT INTO tblUsers SET userName="'.$db->escape($username).'",userMode='.$mode.',timeCreated=NOW()';
        return $db->insert($q);
    }

    function updateUser($id, $username, $mode)
    {
        global $db;
        if (!is_numeric($id) || !is_numeric($mode)) return false;

        $q = 'UPDATE tblUsers SET userName="'.$db->escape($username).'",userMode='.$_mode.',timeCreated=NOW() WHERE userId='.$id;
        $db->update($q);
    }

    /**
     * Sets a new password for the user
     *
     * @param $_id user id
     * @param $_pwd1 password to set
     * @param $_pwd2 password to compare with (optional)
     */
    function setPassword($_id, $_pwd1, $_pwd2 = '', $key = '')
    {
        global $db, $h;
        if (!is_numeric($_id)) return false;
        /* This function is referenced in the Auth-class too, but in that
         * context you cant use the $auth-object, since it's itself.
         * If $key is empty, the reference wasn't from the Auth-class
         * so therefore use the key from the Auth-object instead.
         */
        if (empty($key)) $key = $h->auth->sha1_key;

        if ($_pwd2) {
            if (strlen($_pwd1) < 4) {
                $h->error = t('Password must be at least 4 characters long');
                return false;
            }

            if ($_pwd1 != $_pwd2) {
                $h->error = t('The passwords doesnt match');
                return false;
            }
        }
//FIXME move password algorithm out of here!!!
        $q = 'UPDATE tblUsers SET userPass="'.sha1( $_id.sha1($key).sha1($_pwd1) ).'" WHERE userId='.$_id;
        $db->update($q);
        return true;
    }

    /**
     * Set user mode to $_mode
     */
    function setMode($_id, $_mode)
    {
        global $db;
        if (!is_numeric($_id) || !is_numeric($_mode)) return false;

        $q = 'UPDATE tblUsers SET userMode='.$_mode.' WHERE userId='.$_id;
        $db->update($q);

//dp('Changed usermode for '.Users::getName($_id).' to '.$_mode);    //FIXME lookup from Session->userModes
        return true;
    }

}

?>
