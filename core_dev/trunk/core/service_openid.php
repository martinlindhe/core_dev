<?php
/**
 * $Id$
 *
 * OpenID 2.0 implementation
 *
 * Currently supports OpenID authentication through the following providers:
 * google.com
 *
 *
 * Google OpenID:
 * http://code.google.com/apis/accounts/docs/OpenID.html
 * http://groups.google.com/group/google-federated-login-api
 *
 * Yahoo OpenID:
 * http://developer.yahoo.com/openid/
 * http://developer.yahoo.com/openid/faq.html
 *
 * References:
 * http://openid.net/
 * http://openid.net/specs/openid-authentication-2_0.html
 * http://openid.net/specs/openid-attribute-exchange-1_0.html
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * TODO: yahoo openid
 * Your site must publish a discoverable XRDS document listing all the valid return_to URLs for your Realm.
 * An excellent writeup describing how to do this can can be found here:
 * Why Yahoo! says your OpenID site's identity is not confirmed:
 * http://blog.nerdbank.net/2008/06/why-yahoo-says-your-openid-site.html
 *
 * TODO - READ THE FOLLOWING:
 * http://www.plaxo.com/api/openid_recipe
 *
 * TODO: facebook openid
 *
 * TODO: microsoft OpenId (will be available in 2009)
 * TODO: myspace openid (not yet available)
 *
 * TODO: do AOL have openid too?
 */

//STATUS: this code is working but needs a cleanup

require_once('output_xhtml.php');

define('OPENID_GOOGLE_XRDS',  'https://www.google.com/accounts/o8/id');	//XXX this should be parsed to get login URL, its currently static on google but that might change
define('OPENID_GOOGLE_LOGIN', 'https://www.google.com/accounts/o8/ud');

/**
 * XXX
 */
function openidLogin($site_url)
{
	//FIXME php maps GET parameter with . in it to _, example: "openid.ns" => $_GET['openid_ns'], can this be disabled??

	/*
	 http://projects.localhost/openid.php
 			?openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0
			&openid.mode=id_res
			&openid.op_endpoint=https%3A%2F%2Fwww.google.com%2Faccounts%2Fo8%2Fud
			&openid.response_nonce=2008-11-25T13%3A07%3A28ZyUCwx4n3gmUeYw
			&openid.return_to=http%3A%2F%2Fprojects.localhost%2Fopenid.php
			&openid.assoc_handle=AOQobUctv7u8lBNS9czmmriBnnyuKsKCaO-cCSm5K3trwFcShQoTZ2xM
			&openid.signed=op_endpoint%2Cclaimed_id%2Cidentity%2Creturn_to%2Cresponse_nonce%2Cassoc_handle
			&openid.sig=wedYQY22Kh%2FhGKSkFhlkJtk%2BQS0%3D
			&openid.identity=https%3A%2F%2Fwww.google.com%2Faccounts%2Fo8%2Fid%3Fid%3DAItOawnqet4MjcGaTLcdu5wONrN_e4sRqpd3mNc
			&openid.claimed_id=https%3A%2F%2Fwww.google.com%2Faccounts%2Fo8%2Fid%3Fid%3DAItOawnqet4MjcGaTLcdu5wONrN_e4sRqpd3mNc
	*/

	//XXX A relying party application should be prepared to accept responses as both GETs and as POSTs.
	if (!empty($_GET['openid_mode'])) {
		if ($_GET['openid_mode'] == 'id_res') {
			echo "google openid succeeded!\n\n";

			echo "get:\n\n";
			print_r($_GET);
			echo "\n\npost:\n\n";
			print_r($_POST);

			echo "claimed openid  : ".$_GET['openid_claimed_id']."\n";
			if (!empty($_GET['openid_ext1_value_email'])) {
				echo "email address is: ".$_GET['openid_ext1_value_email']."\n";
			}

			return true;
		} else {
			echo "openid auth failed!\n\n";
			return false;
		}
	}

	if (!empty($_POST['core_openid_url'])) {

		$p = parse_url($site_url);
		$realm = $p['scheme'].'://'.$p['host'].(!empty($p['port']) ? ':'.$p['port'] : '');

		$params = array (
			'openid.ns'              => 'http://specs.openid.net/auth/2.0',                   //required
			'openid.claimed_id'      => 'http://specs.openid.net/auth/2.0/identifier_select', //optional
			'openid.identity'        => 'http://specs.openid.net/auth/2.0/identifier_select', //optional
			'openid.return_to'       => $site_url,                                            //required
			'openid.mode'            => 'checkid_setup',                                      //required
			'openid.realm'           => $realm,                                               //optional

			//for email exchange:
			'openid.ns.ext1'         => 'http://openid.net/srv/ax/1.0',
			'openid.ext1.mode'       => 'fetch_request',
			'openid.ext1.type.email' => 'http://axschema.org/contact/email',
			'openid.ext1.required'   => 'email'
		);

		header('Location: '.OPENID_GOOGLE_LOGIN.'?'.http_encode_params($params) );
		die;
	}

	echo xhtmlForm();
	echo xhtmlInput('core_openid_url', 'http://google.com').'<br/>';
	//XXX click image to select that OpenID supplier, as in http://sourceforge.net/account/login.php

	echo xhtmlImage('http://google.com/favicon.ico', 'Sign in with Google');
	//echo xhtmlImage('http://blogger.com/favicon.ico', 'Sign in with Blogger');
	//echo xhtmlImage('http://yahoo.com/favicon.ico', 'Sign in with Yahoo');
	echo '<br/><br/>';

	echo xhtmlSubmit('Log in');
	echo xhtmlFormClose();
	return false;
}

/**
 * Encodes parameters to a HTTP GET/POST request
 * For GET requests in URL
 * For POST requests with "Content-Type: application/x-www-form-urlencoded"
 */
function http_encode_params($params)
{
	//XXX only used with service_openid.php, can "http_build_query" be used instead?
	$res = '';
	foreach ($params as $key => $val) {
		$res .= $key.'='.urlencode($val).'&';
	}

	return substr($res, 0, strlen($res)-1);
}


?>
