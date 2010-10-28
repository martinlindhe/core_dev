<?php
/**
 * $Id$
 *
 * References:
 * http://recaptcha.net/apidocs/captcha/
 * http://code.google.com/p/recaptcha/
 *
 * IMPORTANT:
 * In order to use this web service, you need to register a API key
 * at the following location: http://recaptcha.net/api/getkey
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('ErrorHandler.php');
require_once('Captcha.php');
require_once('HttpClient.php');

class CaptchaRecaptcha extends Captcha
{
    static $_instance;             ///< singleton

    private $api_url        = 'http://api.recaptcha.net';
    private $api_url_ssl    = 'https://api-secure.recaptcha.net';
    private $api_url_verify = 'http://api-verify.recaptcha.net/verify';

    private $pub_key, $priv_key;

    private function __construct() { }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /*
    * @param $k public recaptcha key
    */
    function setPubKey($k) { $this->pub_key = $k; }

    /*
    * @param $k private recaptcha key
    */
    function setPrivKey($k) { $this->priv_key = $k; }

    /**
     * Verifies a recaptcha
     *
     * @param $priv_key private recaptcha key
     * @return true on success
     */
    function verify()
    {
        $error = ErrorHandler::getInstance();

        if (empty($_POST['recaptcha_challenge_field']) || empty($_POST['recaptcha_response_field']))
        {
            $error->add('No captcha answer given.');
            return false;
        }

        if (!$this->pub_key || !$this->priv_key)
            die('ERROR - Get Recaptcha API key at http://recaptcha.net/api/getkey');

        $params = array (
            'privatekey' => $this->priv_key,
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
    function render($ssl = true)
    {
        if (!$this->pub_key || !$this->priv_key)
            die('ERROR - Get Recaptcha API key at http://recaptcha.net/api/getkey');

        $server = ($ssl ? $this->api_url_ssl : $this->api_url);

        return
        '<script type="text/javascript" src="'.$server.'/challenge?k='.$this->pub_key.'"></script>'.
        '<noscript>'.
            '<iframe src="'.$server.'/noscript?k='.$this->pub_key.'" height="300" width="500" frameborder="0"></iframe><br/>'.
            '<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>'.
            '<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>'.
        '</noscript>';
    }
}

?>
