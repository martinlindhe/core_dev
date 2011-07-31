<?php
/**
 * Shows a login form with tabs for Register & Forgot password functions
 *
 * The handling of the result variables is handled in $this->handleAuthEvents of class.Auth_Base.php
 */

//STATUS: wip

//TODO: separate facebook code from here and move into separate view?

//TODO: fix & link to "forgot password", in core/views/session_forgot_pwd.php
//TODO: use XhtmlForm (?)

require_once('UserList.php');

$header->embedCss('
.login_box{'.
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
    echo '<div class="critical">'.t('Logins are currently not allowed.').'<br/>'.t('Please try again later.').'</div>';
    return;
}


require_once( $page->getCoreDevInclude().'../facebook-php-sdk/facebook.php');
$facebook = new Facebook(
    array(
        'appId'  => $session->facebook_app_id,
        'secret' => $session->facebook_secret,
        'cookie' => true
    )
);

$fbsession = $facebook->getSession();
if ($fbsession) {

    try {
        $fb_me = $facebook->api('/me');
    } catch (FacebookApiException $e) {
        d( $e );
        error_log($e);
        return;
    }

    $session->facebook_id = $facebook->getUser();

    echo "LOGGED IN FBID ".$session->facebook_id;
    //XXX load or create user-id for this facebook-id

$x = 'https://graph.facebook.com/'.$session->facebook_id.'?fields=email,name,picture&access_token='.$fbsession['access_token'];
$v = file_get_contents($x);
d($v);
//XXXX: has facebook email adresss
///XXX: has facebook profile picture

    echo '<a href="'.$facebook->getLogoutUrl().'"><img src="http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif"></a>';

    return;
}

$login_div = 'login_div';

echo '<div id="'.$login_div.'" class="login_box">';

if ($session->facebook_app_id && !$session->facebook_id) {
    echo '<div id="fb-root"></div>';

    $header->includeJs('http://connect.facebook.net/en_US/all.js');
    echo js_embed(
    'window.fbAsyncInit = function() {'.
        'FB.init({'.
            'appId:"'.$session->facebook_app_id.'",'.
            ($fbsession ? 'session:"'.json_encode($fbsession).'",' : ''). // don't refetch the session when PHP already has it
            'status:true,'. // check login status
            'cookie:true,'. // enable cookies to allow the server to access the session
            'xfbml:true'.   // parse XFBML
        '});'.

        // whenever the user logs in, we refresh the page
        'FB.Event.subscribe("auth.login", function() {'.
            'window.location.reload();'.
        '});'.
    '};'.

    '(function() {'.
        'var e = document.createElement("script");'.
        'e.src = document.location.protocol + "//connect.facebook.net/en_US/all.js";'.
        'e.async = true;'.
        'document.getElementById("fb-root").appendChild(e);'.
    '}());'
    );

    echo '<fb:login-button width="200" max-rows="1" perms="email">Login with Facebook</fb:login-button>';
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

$show_reg_div = false;

if (!UserList::getCount() || ($session->allow_logins && $session->allow_registrations))
    $show_reg_div = true;

if ($show_reg_div) {
    $reg_div = 'reg_div';

    $header->registerJsFunction(
    'function show_reg_form()'.
    '{'.
        'show_el("'.$reg_div.'");'.
        'hide_el("'.$login_div.'");'.
    '}'
    );

    $header->registerJsFunction(
    'function show_login_form()'.
    '{'.
        'hide_el("'.$reg_div.'");'.
        'show_el("'.$login_div.'");'.
    '}'
    );

    $x = new XhtmlComponentButton();
    $x->onClick('return show_reg_form();');
    $x->text = 'Register';
    echo $x->render();
}

echo xhtmlFormClose();

echo '</div>';


if ($show_reg_div) {
    echo '<div id="'.$reg_div.'" style="display:none;">';
    include('session_register.php');
    echo '</div>';
}

?>
