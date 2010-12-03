<?php

//STATUS: not used & not working



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



?>
