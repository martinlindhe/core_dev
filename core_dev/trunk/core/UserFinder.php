<?php
/**
 * Helper class for locating users
 */

//STATUS: early wip

class UserFinder
{
    /** @return user id */
    static function byEmail($email)
    {
        $res = SettingsByOwner::getList(USER, 'email', $email);
        if (count($res) == 1)
            return $res[0];

        return false;
    }

    /** @return user id */
    static function byUsername($name)
    {
        throw new Exception ('FIXME implement byUsername');
    }

}

?>
