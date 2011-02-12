<?php
/**
 * Register user view
 */

//STATUS: wip

//TODO: send account activation mail

require_once('UserList.php');

$allow_superadmin_reg = !UserList::getCount();

if ($allow_superadmin_reg || ($session->allow_logins && $session->allow_registrations))
{

    $header->embedCss('
    .register_box {
     font-size: 14px;
     border: 1px solid #aaa;
     min-width: 280px;
     color: #000;
     background-color: #DDD;
     padding: 10px;
     border-radius:15px 15px 15px 15px; /*css3*/
     -moz-border-radius:15px 15px 15px 15px; /*ff*/
    }');

    // Handle new user registrations
    if (isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2']))
    {
        $reg = RegisterHandler::getInstance();

        if (!$reg->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2']))
            return;

        $session->login($_POST['register_usr'], $_POST['register_pwd']);
    }

    echo '<div class="register_box">';

    echo '<div id="login_register_layer">';

    echo '<b>'.t('Register new account').'</b><br/><br/>';
    if ($allow_superadmin_reg)
        echo '<div class="critical">'.t('The account you create now will be the super administrator account.').'</div><br/>';

    echo xhtmlForm();
    echo '<table cellpadding="2">';
    echo '<tr>'.
        '<td>'.t('Username').':</td>'.
        '<td>'.xhtmlInput('register_usr', !empty($_POST['register_usr']) ? $_POST['register_usr'] : '').' '.
            xhtmlImage( $header->getCoreDevRoot().'gfx/icon_user.png', t('Username')).
        '</td>'.
        '</tr>';
    echo '<tr><td>'.t('Password').':</td>'.
        '<td>'.xhtmlPassword('register_pwd').' '.
            xhtmlImage( $header->getCoreDevRoot().'gfx/icon_keys.png', t('Password')).
        '</td>'.
        '</tr>';
    echo '<tr><td>'.t('Again').':</td>'.
        '<td>'.xhtmlPassword('register_pwd2').' '.
            xhtmlImage( $header->getCoreDevRoot().'gfx/icon_keys.png', t('Repeat password')).
        '</td>'.
        '</tr>';

    echo '</table><br/>';

    echo xhtmlSubmit('Register', 'button', 'font-weight: bold');

    echo xhtmlFormClose();
    echo '</div>';

    echo '</div>';
}

?>
