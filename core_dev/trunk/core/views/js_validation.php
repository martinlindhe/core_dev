<?php
/**
 * Utility functions used to validate various input forms
 *
 * javascript based on http://www.webcheatsheet.com/javascript/form_validation.php
 */

$h = UserHandler::getInstance();

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
    '} else if (fld.value.length < '.$h->getUsernameMinlen().') {'.
        'fld.style.background = "Yellow";'.
        'e = "The username is too short (min '.$h->getUsernameMinlen().' characters).\n";'.
    '} else if (fld.value.length > '.$h->getUsernameMaxlen().') {'.
        'fld.style.background = "Yellow";'.
        'e = "The username is too long (max '.$h->getUsernameMaxlen().' characters).\n";'.
    '} else if (illegal.test(fld.value)) {'.
        'fld.style.background = "Yellow";'.
        'e = "The username contains illegal characters.\n";'.
    '} else {'.
        'fld.style.background = "White";'.
    '}'.
    'return e;'.
'}'
);

$header->registerJsFunction(
'function validate_pwd(fld,fld2)'.
'{'.
    'var e="";'.
    'var illegal=/[\W_]/;'. // allow only letters and numbers

    'if (fld.value == "") {'.
        'fld.style.background = "Yellow";'.
        'e = "You didn\'t enter a password.\n";'.
    '} else if (fld.value.length < '.$h->getPasswordMinlen().') {'.
        'e = "The password is too short, minimum '.$h->getPasswordMinlen().' chars.\n";'.
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

?>
