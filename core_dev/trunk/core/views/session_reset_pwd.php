<?php
/**
 *
 */

require_once('ForgotPasswordHandler.php');

if (!$this->token)
    return;

$tok = new Token();

$duration = ForgotPasswordHandler::getInstance()->getExpireTime();
if ($tok->isExpired('activation_code', $this->token, $duration))
{
    echo 'The token is no longer valid.';
    return;
}

$user_id = $tok->getOwner('activation_code', $this->token);

if (!$user_id)
    throw new Exception ('token dont exist');

if ($session->id && $user_id != $session->id)
    throw new Exception ('HACKER stop doing that!');

if ($session->id)
    echo '<div class="critical">You are already logged in! Are you sure you want to reset your password?</div>';

$user = new User($user_id);

if (isset($_POST['reset_pwd']) && isset($_POST['reset_pwd2']))
{
    /// TODO reuse code from register user
    if ($_POST['reset_pwd'] == $_POST['reset_pwd2']) {
        $user->setPassword($_POST['reset_pwd']);
        $session->login($user->name, $_POST['reset_pwd']);
        echo '<div class="okay">Your password has been reset. You have been logged in.</div>';

        // delete consumed token
        $tok->setOwner($user_id);
        $tok->delete('activation_code');
        return;
    } else
        throw new Exception ('passwords dont match');
}


echo 'Reset password for user <b>'.$user->name.'</b>';

$header->registerJsFunction(
'function validate_reset_pwd_form(frm)'.
'{'.
    'if (!frm.reset_pwd.value||!frm.reset_pwd2.value)'.//XXX  use js from session_register to check password
        'return false;'.
    'if (frm.reset_pwd.value!=frm.reset_pwd2.value)'.
        'return false;'.
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

?>
