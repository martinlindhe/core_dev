<?php
/**
 * Shows a login form with tabs for Register & Forgot password functions
 *
 * The handling of the result variables is handled in $this->handleAuthEvents of class.Auth_Base.php
 */

//STATUS: wip

//TODO: separate "register user" from here and move into separate view

//TODO: fix & link to "forgot password", in core/views/session_forgot_pwd.php
//TODO: use XhtmlForm (?)

require_once('UserList.php');

$header->embedCss('
.login_box {'.
 'font-size: 14px;'.
 'border: 1px solid #aaa;'.
 'min-width: 280px;'.
 'color: #000;'.
 'background-color: #DDD;'.
 'padding: 10px;'.
 'border-radius:15px 15px 15px 15px;'.      //css3
 '-moz-border-radius:15px 15px 15px 15px;'. //ff
'}'
);

if (!$session->allow_logins) {
    echo '<div class="critical">'.t('Logins are currently not allowed.').'<br/>'.t('Please try again later.').'</div>';
    return;
}


echo '<div class="login_box">';

echo '<div id="login_form_layer">';

echo xhtmlForm('login_form');

echo '<table cellpadding="2">';
echo '<tr>'.
    '<td>'.t('Username').':</td>'.
    '<td>'.xhtmlInput('login_usr').' '.
        xhtmlImage( $header->getCoreDevRoot().'gfx/icon_user.png', t('Username')).
    '</td>'.
    '</tr>';
echo '<tr>'.
    '<td>'.t('Password').':</td>'.
        '<td>'.xhtmlPassword('login_pwd').' '.
        xhtmlImage( $header->getCoreDevRoot().'gfx/icon_keys.png', t('Password')).
        '</td>'.
    '</tr>';
echo '</table>';
echo '<br/>';
echo xhtmlSubmit('Log in', 'button', 'font-weight: bold');
echo xhtmlFormClose();
echo '</div>';


if (!UserList::getCount() || ($session->allow_logins && $session->allow_registrations)) {
    echo ahref('coredev/view/session_register', 'Register');
}

echo '</div>';

?>
