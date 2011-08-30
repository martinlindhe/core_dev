<?php
/**
 *
 */

//STATUS: wip

require_once('ForgotPasswordHandler.php');

if ($session->id)
    return;

$header->embedCss(
'.forgot_pwd_box{'.
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

echo '<div id="forgot_pwd_layer" class="forgot_pwd_box">';


echo '<b>Forgot password</b><br/><br/>';

if (isset($_POST['forgot_pwd']))
{
    $header->embedJsOnload('show_recover_form();');

    if (!ForgotPasswordHandler::getInstance()->sendMail($_POST['forgot_pwd'])) {
        $error->add('The specified email address does not match any registered user.');
    } else {
        echo 'A email has been sent to your mail address with instructions how to reclaim your account.';
        echo '</div>';
        return;
    }
}

echo $error->render(true);

echo 'Enter the e-mail address used when registering your account.<br/><br/>';
echo 'You will recieve an e-mail with a link to follow,<br/>';
echo 'where you can set a new password.<br/><br/>';

echo xhtmlForm();

echo
    '<table cellpadding="2">'.
    '<tr>'.
        '<td>E-mail:</td>'.
        '<td>'.xhtmlInput('forgot_pwd', '', 26).' '.
        xhtmlImage( $page->getCoreDevRoot().'gfx/icon_mail.png', t('E-mail')).
        '</td>'.
    '</tr>'.
    '</table><br/>';

echo xhtmlSubmit('Forgot password', 'button', 'font-weight: bold');

$x = new XhtmlComponentButton();
$x->text = t('Cancel');
$x->onClick('return show_login_form();');
//$x->style = 'font-weight:bold';
echo $x->render();

echo xhtmlFormClose();

echo '</div>';

?>
