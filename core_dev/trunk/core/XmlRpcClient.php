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

class XmlRpcClient extends HttpClient
{
    protected $user_agent = 'core_dev XML-RPC client 1.0';

    function __construct($s = '')
    {
        if (!function_exists('xmlrpc_server_create'))
            throw new Exception ('XmlRpcClient FAIL: php5-xmlrpc not found');

        parent::__construct($s);
    }

    function call($method, $params)
    {
        if (!$this->Url->get() )
            throw new Exception ('No XML-RPC server URL set');

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
