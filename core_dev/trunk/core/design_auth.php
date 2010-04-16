<?php
/**
 * $Id$
 *
 * Default functions for auth drivers
 */

//STATUS: drop all code! refactor into views rendered from AuthHandler.php

require_once('output_xhtml.php');
require_once('class.User.php');

/**
 * Displays a account registration form
 *
 * @param $preId userId previously created to use, instead of creating a new id (optional)
 * @param $act_code activation code supplied to finish account creation
 * @return true if registration was successful & activation mail was sent out
 */
function showRegisterForm($preId = 0, $act_code = 0)
{
    global $h;
    if (!is_numeric($preId) || !is_numeric($act_code)) return false;

    if ($h->auth->mail_error) {
        echo '<div class="critical">'.t('An error occured sending activation mail!').'</div><br/>';
        return false;
    }

    $h->showError();

    if ($h->auth->activation_sent) {
        echo t('An email with your activation code has been sent.').'<br/>';
        echo t('Follow the link in the mail to finish your registration.').'<br/>';
        return true;
    }

    echo xhtmlForm();
    if ($preId) {
        echo '<input type="hidden" name="preId" value="'.$preId.'"/>';
    }
    echo '<table cellpadding="2">';
    echo '<tr>'.
            '<td>'.t('Username').':</td>'.
            '<td>'.xhtmlInput('register_usr', !empty($_POST['register_usr']) ? $_POST['register_usr'] : '').' '.
                '<img src="'.coredev_webroot().'gfx/icon_user.png" alt="'.t('Username').'"/>'.
            '</td>'.
            '</tr>';
    echo '<tr><td>'.t('Password').':</td><td>'.xhtmlPassword('register_pwd').' <img src="'.coredev_webroot().'gfx/icon_keys.png" alt="'.t('Password').'"/></td></tr>';
    echo '<tr><td>'.t('Repeat password').':</td><td>'.xhtmlPassword('register_pwd2').' <img src="'.coredev_webroot().'gfx/icon_keys.png" alt="'.t('Repeat password').'"/></td></tr>';
    if ($h->user->userdata) {
        showRequiredUserdataFields();
    }
    echo '</table><br/>';

    if ($act_code) {
        echo xhtmlHidden('c', $act_code);
    }
    echo xhtmlSubmit('Register');
    echo xhtmlFormClose();
    return false;
}

/**
 * Helper to change the user's current password.
 */
function changePasswordForm()
{
    global $h;
    if (!$h->session->id) return false;

    $check = false;

    if (!empty($_POST['oldpwd']) && isset($_POST['pwd1']) && isset($_POST['pwd2'])) {
        if ($this->validLogin($h->session->username, $_POST['oldpwd'])) {
            $check = Users::setPassword($h->session->id, $_POST['pwd1'], $_POST['pwd2']);
        } else {
            $session->error = t('Current password is incorrect');
        }
    }

    $h->showError();

    if (!$check) {
        echo xhtmlForm();
        echo '<table cellpadding="0" cellspacing="0" border="0">';
        echo '<tr><td>'.t('Current password').':</td><td>'.xhtmlPassword('oldpwd').'</td></tr>';
        echo '<tr><td>'.t('New password').':</td><td>'.xhtmlPassword('pwd1').'</td></tr>';
        echo '<tr><td>'.t('Repeat password').':</td><td>'.xhtmlPassword('pwd2').'</td></tr>';
        echo '<tr><td colspan="2">'.xhtmlSubmit('Change password').'</td></tr>';
        echo '</table>';
        echo xhtmlFormClose();
    } else {
        echo t('Your password has been changed successfully!');
    }
}

/**
 * Reset user's password
 *
 * @param $_id user id
 * @param $_code reset code
 * @return true on success
 */
function resetPassword($_id, $_code)
{
    global $h;
    if (!is_numeric($_id) || !is_numeric($_code)) return false;

    if (!verifyActivation(ACTIVATE_CHANGE_PWD, $_code, $_id)) {
        echo t('Activation code is invalid or expired.');
        return false;
    }

    echo '<h1>'.t('Set a new password').'</h1>';

    if (isset($_POST['reset_pwd1']) && isset($_POST['reset_pwd2'])) {
        $chk = Users::setPassword($_id, $_POST['reset_pwd1'], $_POST['reset_pwd2']);
        if ($chk) {
            echo t('Your password has been changed!');
            removeActivation(ACTIVATE_CHANGE_PWD, $_code);
            return true;
        }
    }

    echo t('Because we don\'t store the password in clear text it cannot be retrieved.').'<br/>';
    echo t('You will therefore need to set a new password for your account.').'<br/>';

    $h->showError();

    echo xhtmlForm();
    echo t('New password').': '.xhtmlPassword('reset_pwd1', '', 12).'<br/>';
    echo t('Repeat password').': '.xhtmlPassword('reset_pwd2', '', 12).'<br/>';
    echo xhtmlSubmit('Set password');
    echo xhtmlFormClose();
}

?>
