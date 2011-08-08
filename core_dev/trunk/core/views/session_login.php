<?php
/**
 * Shows a login form with tabs for Register & Forgot password functions
 */

//STATUS: wip

//TODO: make facebook javascript login code work
//TODO: use XhtmlForm (?)
//TODO cosmetic: mark input field for username or password with a color if empty in validate_login_form()

require_once('UserList.php');
require_once('SendMail.php');

if ($session->facebook_app_id)
    $session->handleFacebookLogin();

if ($session->id || $session->facebook_id)
    return;

$login_div = 'login_div';
$reg_div = 'reg_div';
$recover_div = 'recover_div';

// only show "register user" if initial setup or if config allows it
$show_reg_div = !UserList::getCount() || ($session->allow_logins && $session->allow_registrations);


// only show "recover password" if mail server is configured
$show_recover_div = SendMail::getInstance()->getServer() ? true : false;

if ($show_reg_div)
{
    // this must be included here so registration handling can happen first
    echo '<div id="'.$reg_div.'" style="display:none;">';
    include('session_register.php');
    echo '</div>';
}

if ($show_recover_div)
{
    echo '<div id="'.$recover_div.'" style="display:none;">';
    include('session_forgot_pwd.php');
    echo '</div>';
}

$header->embedCss(
'.login_box{'.
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

if (!$session->allow_logins) {
    echo '<div class="critical">Logins are currently not allowed.<br/>Please try again later.</div>';
    return;
}

echo '<div id="'.$login_div.'" class="login_box">';

if ($session->facebook_app_id && !$session->facebook_id)
{
    /*
    echo '<fb:login-button width="200" max-rows="1" perms="email">Login with Facebook</fb:login-button>';
    */

    echo '<a href="'.$session->fb_handle->getLoginUrl().'">Login with Facebook</a>';
}

$header->registerJsFunction(
'function validate_login_form(frm)'.
'{'.
    'if (!frm.login_usr.value||!frm.login_pwd.value)'.
        'return false;'.
    'return true;'.
'}'
);

echo xhtmlForm('login_form', '', '', '', 'return validate_login_form(this);');

echo '<table cellpadding="2">';
echo '<tr>'.
    '<td>'.t('Username').':</td>'.
    '<td>'.xhtmlInput('login_usr').' '.
        xhtmlImage( $page->getCoreDevRoot().'gfx/icon_user.png', t('Username')).
    '</td>'.
    '</tr>';
echo '<tr>'.
    '<td>'.t('Password').':</td>'.
        '<td>'.xhtmlPassword('login_pwd').' '.
        xhtmlImage( $page->getCoreDevRoot().'gfx/icon_keys.png', t('Password')).
        '</td>'.
    '</tr>';
echo '</table>';
echo '<br/>';
echo xhtmlSubmit('Log in', 'button', 'font-weight: bold');

if ($show_reg_div)
{
    $header->registerJsFunction(
    'function show_reg_form()'.
    '{'.
        'hide_el("'.$login_div.'");'.
        'show_el("'.$reg_div.'");'.
    '}'
    );

    $header->registerJsFunction(
    'function show_login_form()'.
    '{'.
        'hide_el("'.$reg_div.'");'.
        'hide_el("'.$recover_div.'");'.
        'show_el("'.$login_div.'");'.
    '}'
    );

    $x = new XhtmlComponentButton();
    $x->onClick('return show_reg_form();');
    $x->text = 'Register';
    $x->style = 'font-weight:bold';
    echo $x->render();

    if ($show_recover_div)
    {
        $header->registerJsFunction(
        'function show_recover_form()'.
        '{'.
            'hide_el("'.$login_div.'");'.
            'show_el("'.$recover_div.'");'.
        '}'
        );

        $x = new XhtmlComponentButton();
        $x->onClick('return show_recover_form();');
        $x->text = 'Forgot password';
        $x->style = 'font-weight:bold';
        echo $x->render();
    }
}

echo xhtmlFormClose();

echo '</div>';

?>
