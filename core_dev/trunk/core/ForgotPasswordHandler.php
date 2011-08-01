<?php
/**
 * $Id$
 */

//STATUS: wip

require_once('UserFinder.php');
require_once('SendMail.php');

class ForgotPasswordHandler
{
    protected $expire_time_email = '7d';

    protected $password_msg =
        "Hello. Someone (probably you) asked for a password reset procedure from IP @IP@

Registered username: @USERNAME@

Follow this link to set a new password:
@URL@

The link will expire in @EXPIRETIME@";

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

        $replacement = array(
            $user->getName(),
            client_ip(),
            "reset_password.php?id=".$user_id."&code=".$code,
            shortTimePeriod($this->expire_time_email)
        );

        $msg = preg_replace($pattern, $replacement, $this->password_msg);
//echo $msg;
        $mail = SendMail::getInstance();
        $mail->addRecipient($email);

        $mail->setSubject('Forgot password');
        $mail->send($msg);

        return true;
    }

}

?>
