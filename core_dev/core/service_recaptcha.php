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

require_once('output_http.php');

define('RECAPTCHA_API',     'http://api.recaptcha.net');
define('RECAPTCHA_API_SSL', 'https://api-secure.recaptcha.net');
define('RECAPTCHA_VERIFY',  'http://api-verify.recaptcha.net/verify');

function recaptchaEmbed($pub_key, $ssl = true)
{
	$server = ($ssl ? RECAPTCHA_API_SSL : RECAPTCHA_API);

	//XXX "error" get parameter can also be set to embed a error message or something (didnt see it displayed in the recaptcha so ignoring it)
	$res =
		'<script type="text/javascript" src="'.$server.'/challenge?k='.$pub_key.'"></script>'.
		'<noscript>'.
  			'<iframe src="'.$server.'/noscript?k='.$pub_key.'" height="300" width="500" frameborder="0"></iframe><br/>'.
  			'<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>'.
  			'<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>'.
		'</noscript>';
	return $res;
}

function recaptchaVerify($priv_key, $challenge, $response)
{
	if (empty($priv_key) || empty($challenge) || empty($response)) return false;

	$params = array (
		'privatekey' => $priv_key,
		'remoteip' => $_SERVER['REMOTE_ADDR'],
		'challenge' => $challenge,
		'response' => $response
	);

	$res = http_post(RECAPTCHA_VERIFY, $params);
	$answers = explode("\n", $res['body']);

	if (trim($answers[0]) == 'true') return true;

	echo "Error: ".$answers[1]."\n";
	return false;
}

?>
