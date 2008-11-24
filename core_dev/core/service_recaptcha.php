<?php
/**
 * $Id$
 *
 * References:
 * http://recaptcha.net/apidocs/captcha/
 *
 * IMPORTANT:
 * In order to use this web service, you need to register a API key
 * at the following location:
 * http://recaptcha.net/api/getkey
 *
 */


function recaptchaEmbed($pubkey)
{
	$server = 'xx';

	//XXX "error" get parameter can also be set to embed a error message or something
	$res =
		'<script type="text/javascript" src="'.$server.'/challenge?k='.$pubkey.'"></script>'.
		'<noscript>'.
  			'<iframe src="'.$server.'/noscript?k='.$pubkey.'" height="300" width="500" frameborder="0"></iframe><br/>'.
  			'<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>'.
  			'<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>'.
		'</noscript>';
	return $res;
}


?>
