<?php
/**
 * $Id$
 *
 * Simple XML-RPC client class
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('HttpClient.php');

class XmlRpcClient extends HttpClient
{
    protected $user_agent = 'core_dev XML-RPC client 1.0';

    function __construct($s = '')
    {
        if (!extension_loaded('xmlrpc'))
            throw new Exception ('php5-xmlrpc not found');

        parent::__construct($s);
    }

    function call($method, $params, $debug = false)
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

        if ($debug)
            echo "CLIENT REQUEST: ".$req."\n";

        $res = $this->post($req);

        if ($debug) {
            echo "SERVER RESPONSE: ";
            d( xmlrpc_decode($res) );
        }

        return xmlrpc_decode($res);
    }
}

?>
