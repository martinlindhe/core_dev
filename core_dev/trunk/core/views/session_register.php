<?php
/**
 * Register user view
 *
 * DIRECTLY INCLUDED FROM session_login.php
 *
 * javascript based on http://www.webcheatsheet.com/javascript/form_validation.php
 */

//STATUS: wip

// XXXX XHR för att se om användarnamn är ledigt


// XXXX js som visuellt visar password strength & "dont match" medans man skriver


//XXXX efter form submit, sätt åter fokus på register form div:en

//TODO: send account activation mail

require_once('UserList.php');

$superadmin_reg = !UserList::getCount();

if (!$superadmin_reg && !$session->allow_registrations)
    return;

// Handle new user registrations
if (isset($_POST['register_usr']) && isset($_POST['register_pwd']) && isset($_POST['register_pwd2']))
{
    $reg = RegisterHandler::getInstance();

    if ($reg->register($_POST['register_usr'], $_POST['register_pwd'], $_POST['register_pwd2']))
        $session->login($_POST['register_usr'], $_POST['register_pwd']);
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

$header->registerJsFunction(
'function validate_reg_form(frm)'.
'{'.
    'var reason = validate_usr(frm.register_usr);'.
    'reason += validate_pwd(frm.register_pwd, frm.register_pwd2);'.
//    'reason += validateEmail(frm.email);'.

    'if (reason != "") {'.
        'alert("Some fields need correction:\n" + reason);'.
        'return false;'.
    '}'.

    'return true;'.
'}'
);

$header->registerJsFunction(
'function validate_empty(fld)'.
'{'.
    'var e="";'.

    'if (fld.value.length == 0) {'.
        'fld.style.background = "Yellow";'.
        'e = "The required field has not been filled in.\n"'.
    '} else {'.
        'fld.style.background = "White";'.
    '}'.
    'return e;'.
'}'
);

$header->registerJsFunction(
'function validate_usr(fld)'.
'{'.
    'var e="";'.
    'var illegal=/\W/;'. // allow letters, numbers, and underscores

    'if (fld.value == "") {'.
        'fld.style.background = "Yellow";'.
        'e = "You didn\'t enter a username.\n";'.
    '} else if ((fld.value.length < 5) || (fld.value.length > 15)) {'.
        'fld.style.background = "Yellow";'.
        'e = "The username is the wrong length.\n";'.
    '} else if (illegal.test(fld.value)) {'.
        'fld.style.background = "Yellow";'.
        'e = "The username contains illegal characters.\n";'.
    '} else {'.
        'fld.style.background = "White";'.
    '}'.
    'return e;'.
'}'
);

$register = RegisterHandler::getInstance();

$header->registerJsFunction(
'function validate_pwd(fld,fld2)'.
'{'.
    'var e="";'.
    'var illegal=/[\W_]/;'. // allow only letters and numbers

    'if (fld.value == "") {'.
        'fld.style.background = "Yellow";'.
        'e = "You didn\'t enter a password.\n";'.
    '} else if (fld.value.length < '.$register->getPasswordMinlen().') {'.
        'e = "The password is too short, minimum '.$register->getPasswordMinlen().' chars.\n";'.
        'fld.style.background = "Yellow";'.
    '} else if (illegal.test(fld.value)) {'.
        'e = "The password contains illegal characters.\n";'.
        'fld.style.background = "Yellow";'.
    '} else if (!((fld.value.search(/(a-z)+/)) && (fld.value.search(/(0-9)+/)))) {'.
        'e = "The password must contain at least one numeral.\n";'.
        'fld.style.background = "Yellow";'.
    '} else if (fld.value != fld2.value) {'.
        'e = "The passwords dont match.\n";'.
    '} else {'.
        'fld.style.background = "White";'.
    '}'.
    'return e;'.
'}'
);

$header->registerJsFunction(
'function trim(s)'.
'{'.
    'return s.replace(/^\s+|\s+$/, "");'.
'}');

$header->registerJsFunction(
'function validate_email(fld)'.
'{'.
    'var e="";'.
    'var email_match=/^[^@]+@[^@.]+\.[^@]*\w\w$/;'.
    'var illegal=/[\(\)\<\>\,\;\:\\\"\[\]]/;'.

    'if (fld.value == "") {'.
        'fld.style.background = "Yellow";'.
        'e = "You didn\'t enter an email address.\n";'.
    '} else if (!email_match.test(trim(fld.value))) {'.
        'fld.style.background = "Yellow";'.
        'e = "Please enter a valid email address.\n";'.
    '} else if (fld.value.match(illegal)) {'.
        'fld.style.background = "Yellow";'.
        'e = "The email address contains illegal characters.\n";'.
    '} else {'.
        'fld.style.background = "White";'.
    '}'.
    'return e;'.
'}'
);

echo '<div id="login_register_layer" class="register_box">';

echo '<b>Register new account</b><br/><br/>';
if ($superadmin_reg)
    echo '<div class="critical">The account you create now will be the super administrator account.</div><br/>';


//XXXX use XhtmlForm class, it needs a way to show the images first. also needs a way to show multiple buttons
echo xhtmlForm('reg_frm', '', '', '', 'return validate_reg_form(this);');
echo '<table cellpadding="2">';
echo '<tr>'.
    '<td>'.t('Username').':</td>'.
    '<td>'.xhtmlInput('register_usr', !empty($_POST['register_usr']) ? $_POST['register_usr'] : '').' '.
        xhtmlImage( $page->getCoreDevRoot().'gfx/icon_user.png', t('Username')).
    '</td>'.
    '</tr>';
echo '<tr><td>'.t('Password').':</td>'.
    '<td>'.xhtmlPassword('register_pwd').' '.
        xhtmlImage( $page->getCoreDevRoot().'gfx/icon_keys.png', t('Password')).
    '</td>'.
    '</tr>';
echo '<tr><td>'.t('Again').':</td>'.
    '<td>'.xhtmlPassword('register_pwd2').' '.
        xhtmlImage( $page->getCoreDevRoot().'gfx/icon_keys.png', t('Repeat password')).
    '</td>'.
    '</tr>';

echo '</table><br/>';

echo xhtmlSubmit('Register', 'button', 'font-weight:bold');

$x = new XhtmlComponentButton();
$x->text = 'Cancel';
$x->onClick('return show_login_form();');
//$x->style = 'font-weight:bold';
echo $x->render();

echo xhtmlFormClose();

echo '</div>';

?>
