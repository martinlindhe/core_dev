<?php
/**
 * Helper class for locating users
 */

//STATUS: wip

class UserFinder
{
    /** @return user id */
    static function byEmail($email)
    {
        $res = SettingsByOwner::getList(USER, 'email', $email);
        if (count($res) == 1)
            return $res[0];

        if (count($res) > 1)
            throw new Exception ('XXX multiple users with same email address');

        return false;
    }

    /** @return user id */
    static function byUsername($name)
    {
        $user = User::getByName($name);
        if ($user->id)
            return $user->id;

        return false;
    }

}

?>
