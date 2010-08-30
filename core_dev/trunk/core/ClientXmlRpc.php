<?php
/**
 * $Id$
 *
 * Simple XML-RPC client class
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('HttpClient.php');

class ClientXmlRpc extends HttpClient
{
    protected $rpc_url;
    protected $user_agent = 'core_dev XML-RPC client 1.0';

    function __construct()
    {
        if (!function_exists('xmlrpc_server_create'))
            throw new Exception ('ClientXmlRpc FAIL: php5-xmlrpc not found');
    }

    function setRpcUrl($s)
    {
        if (!is_url($s))
            throw new Exception ('not a url '.$s);

        $this->rpc_url = $s;
    }

    function call($method, $params)
    {
        if (!$this->rpc_url)
            throw new Exception ('No XML-RPC server URL set');

        $this->setUrl($this->rpc_url);
        $this->setContentType('text/xml');

        $opts = array(
        'output_type' => 'xml',
        'verbosity'   => 'no_white_space',
        'escaping'    => 'non-ascii',
        'version'     => 'xmlrpc',
        'encoding'    => 'UTF-8',
        );
        $req = xmlrpc_encode_request($method, $params, $opts);
        $res = $this->post($req);

        //echo "SERVER RESPONSE\n"; d( xmlrpc_decode($res) );
        return xmlrpc_decode($res);
    }
}

?>
