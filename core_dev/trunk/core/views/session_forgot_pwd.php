<?php
/**
 *
 */

//STATUS: xxx

//TODO!: need a core_dev internal handler to handle the link from the email


require_once('ForgotPasswordHandler.php');


if ($session->id)
    return;

if (isset($_POST['forgot_pwd']))
{
    $check = new ForgotPasswordHandler();

    if (!$check->sendMail($_POST['forgot_pwd']))
        $error->add = 'The specified email address does not match any registered user.';
    else {
        echo 'A email has been sent to your mail address with instructions how to reclaim your account.';
        return;
    }
}



echo '<div id="forgot_pwd_layer">';

echo 'Enter the e-mail address used when registering your account.<br/><br/>';
echo 'You will recieve an e-mail with a link to follow,<br/>';
echo 'where you can set a new password.<br/><br/>';

echo xhtmlForm();

echo
    '<table cellpadding="2">'.
    '<tr>'.
        '<td>E-mail:</td>'.
        '<td>'.xhtmlInput('forgot_pwd', '', 26).' '.
        xhtmlImage( $header->getCoreDevRoot().'gfx/icon_mail.png', t('E-mail')).
        '</td>'.
    '</tr>'.
    '</table><br/>';

echo xhtmlSubmit('Forgot password', 'button', 'font-weight: bold');
echo xhtmlFormClose();

echo '</div>';

?>
