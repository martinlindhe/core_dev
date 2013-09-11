<?php
/**
 * Shows a login form with tabs for Register & Forgot password functions
 */

//STATUS: wip

//TODO: make facebook javascript login code work
//TODO cosmetic: mark input field for username or password with a color if empty in validate_login_form()

namespace cd;

require_once('UserList.php');
require_once('SendMail.php');

require_once('XhtmlForm.php');

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
    include('register.php');
    echo '</div>';
}

if ($show_recover_div)
{
    echo '<div id="'.$recover_div.'" style="display:none;">';
    include('forgot_pwd.php');
    echo '</div>';
}

// include js validation snippets
$view = new ViewModel('views/core/js_validation.php');
echo $view->render();

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
    // XXXX javascript login button dont work in chrome due to error at 2011.08.08:
    // Unsafe JavaScript attempt to access frame with URL http://static.ak.fbcdn.net/connect/xd_proxy.php?... from frame with URL http://styggvar.dyndns.org:81/textfeed/. Domains, protocols and ports must match.

    // echo '<fb:login-button width="200" max-rows="1" perms="email">Login with Facebook</fb:login-button>';

    echo '<a href="'.$session->fb_handle->getLoginUrl().'">Login with Facebook</a>';
}

$header->registerJsFunction(
'function check_login(frm)'.
'{'.
    'if (!frm.login_usr.value||!frm.login_pwd.value)'.
        'return false;'.
    'return true;'.
'}'
);

function loginHandler($p)
{
    $session = SessionHandler::getInstance();
    if ($session->id)
    {
        dp('HACK user '.$session->name.' ('.$session->id.') tried to login user '.$p['usr']);
        return false;
    }

    if ($session->login($p['usr'], $p['pwd']))
        $session->showStartPage();

    return true;
}

$form = new XhtmlForm('login');
$form->cssTable('');

$u_img = new XhtmlComponentImage();
$u_img->src = $page->getRelativeCoreDevUrl().'gfx/icon_user.png';

$i = new XhtmlComponentInput();
$i->name = 'usr';

$form->add($i, t('Username'), $u_img);

$p_img = new XhtmlComponentImage();
$p_img->src = $page->getRelativeCoreDevUrl().'gfx/icon_keys.png';

$i = new XhtmlComponentPassword();
$i->name = 'pwd';

$form->add($i, t('Password'), $p_img);


$form->addSubmit('Log in', 'font-weight:bold');
$form->setFocus('usr');
$form->onSubmit('return check_login(this);');
$form->setHandler(__NAMESPACE__.'\loginHandler');
echo $form->render();

$header->registerJsFunction(
'function show_login_form()'.
'{'.
    ($show_reg_div     ? 'hide_el("'.$reg_div.'");' : '').
    ($show_recover_div ? 'hide_el("'.$recover_div.'");' : '').
    'show_el("'.$login_div.'");'.
'}'
);

if ($show_reg_div)
{
    $header->registerJsFunction(
    'function show_reg_form()'.
    '{'.
        'hide_el("'.$login_div.'");'.
        'show_el("'.$reg_div.'");'.
    '}'
    );

    $x = new XhtmlComponentButton();
    $x->onClick('return show_reg_form();');
    $x->text = t('Register');
    $x->style = 'font-weight:bold';
    echo $x->render();
}

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
    $x->text = t('Forgot password');
    $x->style = 'font-weight:bold';
    echo $x->render();
}

echo '</div>';

?>
