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
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * TODO: recaptchaVerify() returns a 2nd line of fail reason, which could be displayed to the user
 */

require_once('output_http.php');

define('RECAPTCHA_API',     'http://api.recaptcha.net');
define('RECAPTCHA_API_SSL', 'https://api-secure.recaptcha.net');
define('RECAPTCHA_VERIFY',  'http://api-verify.recaptcha.net/verify');

/**
 * Verifies a recaptcha
 *
 * @param $priv_key private recaptcha key
 * @return true on success
 */
function recaptchaVerify($priv_key)
{
	if (!isset($_POST['recaptcha_challenge_field']) || !isset($_POST['recaptcha_response_field'])) return false;

	$params = array (
		'privatekey' => $priv_key,
		'remoteip' => $_SERVER['REMOTE_ADDR'],
		'challenge' => $_POST['recaptcha_challenge_field'],
		'response' => $_POST['recaptcha_response_field']
	);

	$res = http_post(RECAPTCHA_VERIFY, $params);
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
function recaptchaShow($pub_key, $ssl = true)
{
	$server = ($ssl ? RECAPTCHA_API_SSL : RECAPTCHA_API);

	$res =
		'<script type="text/javascript" src="'.$server.'/challenge?k='.$pub_key.'"></script>'.
		'<noscript>'.
  			'<iframe src="'.$server.'/noscript?k='.$pub_key.'" height="300" width="500" frameborder="0"></iframe><br/>'.
  			'<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>'.
  			'<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>'.
		'</noscript>';
	return $res;
}

?>
