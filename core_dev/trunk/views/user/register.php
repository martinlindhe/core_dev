<?php
/**
 * Register user view
 *
 * DIRECTLY INCLUDED FROM session_login.php
 */

//STATUS: wip

//XXX XHR för att se om användarnamn är ledigt
//XXX js som visuellt visar password strength & "dont match" medans man skriver
//TODO: send account activation mail
//XXX use XhtmlForm class, it needs a way to show the images first. also needs a way to show multiple buttons

require_once('UserList.php');

$superadmin_reg = !UserList::getCount();

if (!$superadmin_reg && !$session->allow_registrations)
    return;

// Handle new user registrations
if (isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2']))
{
    $reg = UserHandler::getInstance();

    $user_id = $reg->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2']);

    if ($user_id)
    {
        if ($superadmin_reg) {
            if (!UserGroup::getAll()) {
                // If no UserGroup:s exist, create them
                UserGroup::create('Webmasters', 1);
                UserGroup::create('Admins', 2);
                $sadmin_id = UserGroup::create('Super Admins', 3);
            } else {
                $grp = UserGroup::getByName('Super Admins');
                $sadmin_id = $grp['groupId'];
            }

            // Add this user to Super Admin group
            UserGroupHandler::addToGroup($user_id, $sadmin_id);
        }

        if ($session->login($_POST['register_usr'], $_POST['register_pwd']))
            $session->showStartPage();
    }

    // after form submit failed, put focus back to the register form <div> to show error
    $header->embedJsOnload('show_reg_form();');
}

$header->embedCss(
'.register_box{'.
    'font-size:14px;'.
    'border:1px solid #aaa;'.
    'min-width:280px;'.
    'color:#000;'.
    'background-color:#ddd;'.
    'padding:10px;'.
    'border-radius:15px 15px 15px 15px;'.      //css3
    '-moz-border-radius:15px 15px 15px 15px;'. //ff
'}'
);

echo '<div id="login_register_layer" class="register_box">';

echo '<b>Register new account</b><br/><br/>';
if ($superadmin_reg)
    echo '<div class="critical">The account you create now will be the super administrator account.</div><br/>';


echo xhtmlForm('reg_frm', '', '', '', 'return validate_reg_form(this);');
echo '<table cellpadding="2">';
echo '<tr>'.
    '<td>'.t('Username').':</td>'.
    '<td>'.xhtmlInput('register_usr', !empty($_POST['register_usr']) ? $_POST['register_usr'] : '').' '.
        xhtmlImage( $page->getRelativeCoreDevUrl().'gfx/icon_user.png', t('Username')).
    '</td>'.
    '</tr>';
echo '<tr><td>'.t('Password').':</td>'.
    '<td>'.xhtmlPassword('register_pwd').' '.
        xhtmlImage( $page->getRelativeCoreDevUrl().'gfx/icon_keys.png', t('Password')).
    '</td>'.
    '</tr>';
echo '<tr><td>'.t('Again').':</td>'.
    '<td>'.xhtmlPassword('register_pwd2').' '.
        xhtmlImage( $page->getRelativeCoreDevUrl().'gfx/icon_keys.png', t('Repeat password')).
    '</td>'.
    '</tr>';

echo '</table><br/>';

echo xhtmlSubmit('Register', 'button', 'font-weight:bold');

$x = new XhtmlComponentButton();
$x->text = t('Cancel');
$x->onClick('return show_login_form();');
//$x->style = 'font-weight:bold';
echo $x->render();

echo xhtmlFormClose();

echo '</div>';

?>
