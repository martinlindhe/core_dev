<?php
/**
 * Shows a login form with tabs for Register & Forgot password functions
 *
 * The handling of the result variables is handled in $this->handleAuthEvents of class.Auth_Base.php
 */

//STATUS: need cleanup, move non-view code out
//TODO: re-add "forgot password" feature
//TODO: use XhtmlForm (?)

$header->addCss('
.login_box {
 font-size: 14px;
 border: 1px solid #aaa;
 min-width: 280px;
 color: #000;
 background-color: #DDD;
 padding: 10px;
 border-radius:15px 15px 15px 15px; /*css3*/
 -moz-border-radius:15px 15px 15px 15px; /*ff*/
}');

echo js_embed(
//Makes element with name "n" invisible in browser
'function hide_element(n)'.
'{'.
    'var e = document.getElementById(n);'.
    'e.style.display = "none";'.
'}'.
//Makes element with name "n" visible in browser
'function show_element(n)'.
'{'.
    'var e = document.getElementById(n);'.
    'e.style.display = "";'.
'}'
);

echo '<div class="login_box">';

$tab = 'login';    //default tab show login form

$allow_superadmin_reg = false;

$userlist = new UserList();

if (!$userlist->getCount()) {
    $allow_superadmin_reg = true;
    $tab = 'register';
}

/*
$forgot_pwd = false;
if ($h->user->userdata) {
    $forgot_pwd = getUserdataFieldIdByType(USERDATA_TYPE_EMAIL);
}

//Check for "forgot password" request, POST to any page with 'forgot_pwd' set
if ($forgot_pwd && !$h->session->id && isset($_POST['forgot_pwd'])) {
    $check = $h->auth->handleForgotPassword($_POST['forgot_pwd']);
    if (!$check) {
        $h->error = t('The specified email address does not match any registered user.');
    }
    $tab = 'forgot_pwd';
}

*/

if (isset($_POST['register_usr'])) {
    $tab = 'register';
}

echo $error->render();

echo '<div id="login_form_layer"'.($tab!='login'?' style="display: none;"':'').'>';
if (!$auth->allow_logins) {
    echo '<div class="critical">'.t('Logins are currently not allowed.').'<br/>'.t('Please try again later.').'</div>';
}
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
if (($auth->allow_logins && $auth->allow_registrations) || $allow_superadmin_reg) {
    echo xhtmlButton('Register', "hide_element('login_form_layer'); show_element('login_register_layer')");
}
/*
if ($forgot_pwd) {
    echo xhtmlButton('Forgot password', "hide_element('login_form_layer'); show_element('login_forgot_pwd_layer')");
}
*/
echo xhtmlFormClose();
echo '</div>';

if (($auth->allow_logins && $auth->allow_registrations) || $allow_superadmin_reg) {
    echo '<div id="login_register_layer"'.($tab!='register'?' style="display: none;"':'').'>';
/*
    if ($auth->activation_sent) {
        echo t('An email with your activation code has been sent.').'<br/>';
        echo t('Follow the link in the mail to complete your registration.').'<br/>';

        echo 'You can also enter activation code here to finish:<br/>';
        echo '<form method="post" action="">';
        echo '<input type="text" size="10"/>';
        echo '<input type="submit" class="button" value="Complete registration"/>';
        echo '</form>';

    } else {
*/
        echo '<b>'.t('Register new account').'</b><br/><br/>';
        if ($allow_superadmin_reg) {
            echo '<div class="critical">'.t('The account you create now will be the super administrator account.').'</div><br/>';
        }

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
/*
        if ($user->userdata) {
            showRequiredUserdataFields();
        }
*/
        echo '</table><br/>';

        if (!$allow_superadmin_reg) {
            echo xhtmlButton('Log in', "hide_element('login_register_layer'); show_element('login_form_layer')");
        }
        echo xhtmlSubmit('Register', 'button', 'font-weight: bold');
        /*if ($forgot_pwd) {
            echo xhtmlButton('Forgot password', "hide_element('login_register_layer'); show_element('login_forgot_pwd_layer')");
        }*/
            echo xhtmlFormClose();
        echo '</div>';
//    }
}
/*
if ($forgot_pwd) {
    echo '<div id="login_forgot_pwd_layer"'.($tab!='forgot_pwd'?' style="display: none;"':'').'>';

    //XXX FIXME how to read resetpwd_sent
    //if ($this->resetpwd_sent) {
    //    echo t('A email has been sent to your mail address with instructions how to reclaim your account.');
    //} else
    {
        echo xhtmlForm();
        echo 'Enter the e-mail address used when registering your account.<br/><br/>';
        echo 'You will recieve an e-mail with a link to follow,<br/>';
        echo 'where you can set a new password.<br/><br/>';
        echo '<table cellpadding="2">';
        echo '<tr><td>'.getUserdataFieldName($forgot_pwd).':</td><td>'.xhtmlInput('forgot_pwd', '', 26).' <img src="'.coredev_webroot().'gfx/icon_mail.png" alt="'.t('E-mail').'"/></td></tr>';
        echo '</table><br/>';

        echo xhtmlButton('Log in', "hide_element('login_forgot_pwd_layer'); show_element('login_form_layer')");
        echo xhtmlButton('Register', "hide_element('login_forgot_pwd_layer'); show_element('login_register_layer')");
        echo xhtmlSubmit('Forgot password', 'button', 'font-weight: bold');
        echo xhtmlFormClose();
    }
    echo '</div>';
}
*/

echo '</div>';

?>
