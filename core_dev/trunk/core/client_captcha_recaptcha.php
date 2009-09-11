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
 * at the following location:
 * http://recaptcha.net/api/getkey
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

//TODO: recaptchaVerify() returns a 2nd line of fail reason, which could be displayed to the user

class captcha_recaptcha
{

	private $api_url        = 'http://api.recaptcha.net';
	private $api_url_ssl    = 'https://api-secure.recaptcha.net';
	private $api_url_verify = 'http://api-verify.recaptcha.net/verify';

	private $pub_key, $priv_key;

	function setPubKey($k) { $this->pub_key = $k; }
	function setPrivKey($k) { $this->priv_key = $k; }

	/**
	 * Verifies a recaptcha
	 *
	 * @param $priv_key private recaptcha key
	 * @return true on success
	 */
	function verify()
	{
		if (!isset($_POST['recaptcha_challenge_field']) || !isset($_POST['recaptcha_response_field'])) return false;

		$params = array (
			'privatekey' => $this->priv_key,
			'remoteip' => client_ip(),
			'challenge' => $_POST['recaptcha_challenge_field'],
			'response' => $_POST['recaptcha_response_field']
		);

		$res = http_post($this->api_url_verify, $params);
		$answers = explode("\n", $res['body']);
		if (trim($answers[0]) == 'true') return true;
		return false;
	}

	/**
	 * Embeds a recaptcha on your website
	 *
	 * @param $pub_key public recaptcha key
	 * @param $ssl use SSL to connect to recaptcha.net
	 * @return HTML code to display recaptcha
	 */
	function render($ssl = true)
	{
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
