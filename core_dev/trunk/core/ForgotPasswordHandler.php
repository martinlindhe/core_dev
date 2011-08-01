<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: dont create a new token if one exists and is still valid
//TODO: dont send out new email if token already exists and was saved less than 30 minutes ago

require_once('UserFinder.php');
require_once('SendMail.php');

class ForgotPasswordHandler
{
    static $_instance;             ///< singleton

    protected $expire_time_email = '7d';

    protected $password_msg =
        'Hello. Someone (probably you) asked for a password reset procedure from IP @IP@

Registered username: @USERNAME@

Follow this link to set a new password:
<a href="@URL@">@URL@</a>

The link will expire in @EXPIRETIME@';

    private function __construct() { }
    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    function getExpireTime() { return $this->expire_time_email; }

    /**
     * Looks up user supplied email address / alias and sends a mail
     *
     * @param $email email address or username
     */
    function sendMail($in)
    {
        $in = trim($in);

        if (is_email($in))
            $user_id = UserFinder::byEmail($in);
        else
            $user_id = UserFinder::byUsername($in);

        $error = ErrorHandler::getInstance();

        if (!$user_id) {
            $error->add('Invalid email address or username');
            return false;
        }

        $user = new User($user_id);
        $email = $user->getEmail();
        if (!$email)
            throw new Exception ('entered email not found');

        $tok = new Token();
        $tok->setOwner($user_id);
        $code = $tok->generate('activation_code');

        $pattern = array(
            '/@USERNAME@/',
            '/@IP@/',
            '/@URL@/',
            '/@EXPIRETIME@/'
        );

        $page = XmlDocumentHandler::getInstance();

        $url = $page->getUrl().'coredev/reset_password/'.$code;

        $replacement = array(
            $user->getName(),
            client_ip(),
            $url,
            shortTimePeriod($this->expire_time_email)
        );

        $msg = preg_replace($pattern, $replacement, $this->password_msg);
//d($msg);
        $mail = SendMail::getInstance();
        $mail->addRecipient($email);

        $mail->setSubject('Forgot password');
        $mail->send($msg);

        return true;
    }

}

?>
