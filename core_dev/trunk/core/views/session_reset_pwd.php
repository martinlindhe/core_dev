<?php
/**
 *
 */

//STATUS: works (???)

//TODO: use js from views/js_validation.php to check password
//XXX use XhtmlForm?

require_once('ForgotPasswordHandler.php');

if (!$this->token)
    return;

$duration = ForgotPasswordHandler::getInstance()->getExpireTime();
if (Token::isExpired('activation_code', $this->token, $duration))
{
    echo 'The token is no longer valid.';
    return;
}

$user_id = Token::getOwner('activation_code', $this->token);

if (!$user_id)
    throw new Exception ('token dont exist');

if ($session->id && $user_id != $session->id)
    throw new Exception ('HACKER stop doing that!');

if ($session->id)
    echo '<div class="critical">You are already logged in! Are you sure you want to reset your password?</div>';

if (isset($_POST['reset_pwd']) && isset($_POST['reset_pwd2']))
{
    /// TODO reuse code from register user
    if ($_POST['reset_pwd'] == $_POST['reset_pwd2']) {
        UserHandler::setPassword($user_id, $_POST['reset_pwd']);
        $session->login($user->name, $_POST['reset_pwd']);
        echo '<div class="okay">Your password has been reset. You have been logged in.</div>';

        // delete consumed token
        Token::delete($user_id, 'activation_code');
        return;
    } else
        $error->add('The passwords dont match');
}

echo $error->render(true);

echo 'Reset password for user <b>'.$user->name.'</b>';

$header->registerJsFunction(
'function validate_reset_pwd_form(frm)'.
'{'.
    'if (!frm.reset_pwd.value||!frm.reset_pwd2.value)'.
        'return false;'.
//    'if (frm.reset_pwd.value!=frm.reset_pwd2.value)'.
//        'return false;'.
    'return true;'.
'}'
);

//XXXX use XhtmlForm class, it needs a way to show the images first
echo xhtmlForm('reg_frm', '', '', '', 'return validate_reset_pwd_form(this);');
echo '<table cellpadding="2">';
echo '<tr><td>'.t('New password').':</td>'.
    '<td>'.xhtmlPassword('reset_pwd').' '.
        xhtmlImage( $page->getCoreDevRoot().'gfx/icon_keys.png', t('Password')).
    '</td>'.
    '</tr>';
echo '<tr><td>'.t('Again').':</td>'.
    '<td>'.xhtmlPassword('reset_pwd2').' '.
        xhtmlImage( $page->getCoreDevRoot().'gfx/icon_keys.png', t('Repeat password')).
    '</td>'.
    '</tr>';

echo '</table><br/>';

echo xhtmlSubmit('Reset password', 'button', 'font-weight:bold');
echo xhtmlFormClose();

?>
