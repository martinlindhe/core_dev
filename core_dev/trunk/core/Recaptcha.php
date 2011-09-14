<?php
/**
 * $Id$
 *
 * References:
 * hhttp://www.google.com/recaptcha/
 * http://code.google.com/p/recaptcha/
 *
 * IMPORTANT:
 * In order to use this web service, you need to register a API key
 * from http://www.google.com/recaptcha/
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('XhtmlComponent.php');
require_once('ErrorHandler.php');
require_once('HttpClient.php');
require_once('html.php');

class RecaptchaConfig
{
    static $_instance;             ///< singleton

    private $pub_key, $priv_key;

    private function __construct() { }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /* @param $k public recaptcha key */
    public function setPublicKey($k) { $this->pub_key = $k; }

    /* @param $k private recaptcha key */
    public function setPrivateKey($k) { $this->priv_key = $k; }

    public function getPublicKey() { return $this->pub_key; }
    public function getPrivateKey() { return $this->priv_key; }

}

class Recaptcha extends XhtmlComponent
{
    private $api_url        = 'http://www.google.com/recaptcha/api';
    private $api_url_ssl    = 'https://www.google.com/recaptcha/api';
    private $api_url_verify = 'http://www.google.com/recaptcha/api/verify';

    /**
     * Verifies a recaptcha
     *
     * @param $priv_key private recaptcha key
     * @return true on success
     */
    public function verify()
    {
        $error = ErrorHandler::getInstance();

        $conf = RecaptchaConfig::getInstance();

        if (empty($_POST['recaptcha_challenge_field']) || empty($_POST['recaptcha_response_field']))
        {
            $error->add('No captcha answer given.');
            return false;
        }

        if (!$conf->getPublicKey() || !$conf->getPrivateKey() )
            die('ERROR - Get Recaptcha API key at http://recaptcha.net/api/getkey');

        $params = array (
            'privatekey' => $conf->getPrivateKey(),
            'remoteip'   => client_ip(),
            'challenge'  => $_POST['recaptcha_challenge_field'],
            'response'   => $_POST['recaptcha_response_field']
        );

        $http = new HttpClient($this->api_url_verify);
        $res = $http->post($params);

        $answers = explode("\n", $res);

        if (trim($answers[0]) == 'true') return true;

        switch ($answers[1]) {
        case 'incorrect-captcha-sol': $e = 'Incorrect captcha solution'; break;
        default: $e = 'untranslated error: '.$answers[1];
        }

        $error->add($e);

        return false;
    }

    /**
     * Embeds a recaptcha on your website
     *
     * @param $ssl use SSL to connect to recaptcha.net
     * @return HTML code to display recaptcha
     */
    public function render($ssl = true)
    {
        $conf = RecaptchaConfig::getInstance();

        if (!$conf->getPublicKey() || !$conf->getPrivateKey() )
            die('ERROR - You need a Recaptcha API key');

        $server = $ssl ? $this->api_url_ssl : $this->api_url;

        $locale = LocaleHandler::getInstance();

        switch ($locale->get()) {
        case 'swe': // translation was submitted to recaptcha google group: http://groups.google.com/group/recaptcha/browse_thread/thread/78a677ea59626024
            $opts =
            'custom_translations : {'.
                'instructions_visual : "Skriv in de två orden:",'.
                'instructions_audio : "Skriv in vad du hör:",'.
                'play_again : "Spela ljudet igen",'.
                'cant_hear_this : "Ladda ner ljudfil som MP3",'.
                'visual_challenge : "Se en visuell captcha",'.
                'audio_challenge : "Lyssna på en ljud-captcha",'.
                'refresh_btn : "Ladda en ny captcha",'.
                'help_btn : "Hjälp",'.
                'incorrect_try_again : "Fel svar. Försök igen.",'.
            '},';
            break;

        // http://code.google.com/intl/sv-SE/apis/recaptcha/docs/customization.html    some languages is supported already
        case 'eng': $opts = 'lang : "en", '; break;
        default:
            throw new Exception ('recaptcha translation missing');
        }

        return
        js_embed('var RecaptchaOptions = { '.$opts.' };').

        '<script type="text/javascript" src="'.$server.'/challenge?k='.$conf->getPublicKey().'"></script>'.

        '<noscript>'.
            '<iframe src="'.$server.'/noscript?k='.$conf->getPublicKey().'" height="300" width="500" frameborder="0"></iframe><br/>'.
            '<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>'.
            '<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>'.
        '</noscript>';
    }

}

?>
