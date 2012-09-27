<?php
/**
 * Helper class for locating users
 */

//STATUS: wip

namespace cd;

class UserFinder
{
    /** @return user id */
    static function byEmail($email)
    {
        $email = trim($email);

        if (!is_email($email))
            throw new Exception ('XXX not an email address: '.$email);

        $res = UserSetting::getList('email', $email);
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

        if ($user && $user->id)
            return $user->id;

        return false;
    }

}

?>
