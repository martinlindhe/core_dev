<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@ubique.se>
 */

//STATUS: nonworking. code cleanup / rewrite + accompanying view is needed

namespace cd;

class UserActivationHandler
{
    protected $activate_msg =
        'Hello. Someone (probably you) registered an account from IP @IP@

Username: @USERNAME@
Activation code: @CODE@

Follow this link to activate your account:
<a href="@URL@">@URL@</a>

The link will expire in @EXPIRETIME@';


    /**
     * Sends a account activation mail to specified user
     *
     * @param $_id user id
     */
    function xxx_sendActivationMail($_id)
    {
        if (!is_numeric($_id))
            return false;

        $email = loadUserdataEmail($_id);
        if (!$email) return false;

        $code = generateActivationCode(ACTIVATE_EMAIL, 1000000, 9999999);
        createActivation(ACTIVATE_EMAIL, $code, $_id);

        $subj = 'Account activation';

        $pattern = array('/__USERNAME__/', '/__IP__/', '/__CODE__/', '/__URL__/', '/__EXPIRETIME__/');
        $replacement = array(
            Users::getName($_id),
            client_ip(),
            $code,
            xhtmlGetUrl("activate.php?id=".$_id."&code=".$code),
            shortTimePeriod($this->expire_time_email)
        );
        $msg = preg_replace($pattern, $replacement, $this->mail_activate_msg);

        if (!$this->SmtpSend($email, $subj, $msg)) return false;

        $this->activation_sent = true;
        return true;
    }

    /**
     * Verifies user activaction code
     *
     * @param $_id
     * @param $_code
     * @return true if success
     */
    function xxx_verifyActivationMail($_id, $_code)
    {
        if (!is_numeric($_id) || !is_numeric($_code)) return false;

        if (!verifyActivation(ACTIVATE_EMAIL, $_code, $_id)) {
            echo 'Activation code is invalid or expired.';
            return false;
        }

        Users::activate($_id);

        removeActivation(ACTIVATE_EMAIL, $_code);

        echo 'Your account has been activated.<br/>';
        echo 'You can now proceed to <a href="login.php">log in</a>.';
        return true;
    }

}

?>
