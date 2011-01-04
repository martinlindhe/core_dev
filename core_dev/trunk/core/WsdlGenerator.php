<?php
/**
 * $Id$
 *
 * Helper class for creating SOAP (WSDL) XML interfaces
 *
 * Documentation:
 * http://en.wikipedia.org/wiki/Web_Services_Description_Language
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

class WsdlGenerator
{
    public $interface_name = '';
    public $interface_url  = '';

    private $messages = array();

    function __construct($interface_name, $interface_path)
    {
        $page = XmlDocumentHandler::getInstance();

        $this->interface_name = $interface_name;
        $this->interface_url  = $page->getUrl().$interface_path;
    }

    function message($name, $params = array())
    {
        $this->messages[$name] = $params;
    }

    function render()
    {
        header('Content-type: text/xml');
        $res = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

        $res .=
        '<definitions name="'.$this->interface_name.'"
            targetNamespace="http://example.org/'.$this->interface_name.'.wsdl"
            xmlns:tns="http://example.org/'.$this->interface_name.'.wsdl"
            xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"
            xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
            xmlns="http://schemas.xmlsoap.org/wsdl/">';

        //Describe function parameter datatypes and return datatypes
        foreach ($this->messages as $name => $params) {
            $res .= '<message name="'.$name.'Request">';
                foreach ($params as $part => $type) {
                    if ($part == 'response') continue;
                    $res .= '<part name="'.$part.'" type="xsd:'.$type.'"/>';
                }
            $res .= '</message>';

            $res .= '<message name="'.$name.'Response">';
                if (!$params['response']) $res_type = 'integer';
                else $res_type = $params['response'];
                $res .= '<part name="Result" type="xsd:'.$res_type.'"/>';
            $res .= '</message>';
        }

        //Describe what <message> responds to a specific <operation> input and output
        $res .= '<portType name="'.$this->interface_name.'PortType">';

            foreach ($this->messages as $operation => $params)
                $res .= '<operation name="'.$operation.'">'.
                    '<input message="tns:'.$operation.'Request"/>'.
                    '<output message="tns:'.$operation.'Response"/>'.
                '</operation>';

        $res .= '</portType>';

        //Describe how to encode data for each <operation> input and output
        $res .= '<binding name="'.$this->interface_name.'Binding" type="tns:'.$this->interface_name.'PortType">';
            $res .= '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>';

            foreach ($this->messages as $operation => $params)
                $res .=
                '<operation name="'.$operation.'">'.
                    '<soap:operation soapAction="urn:#'.$operation.'"/>'.
                    '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>'.
                    '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>'.
                '</operation>';

        $res .= '</binding>';

        //Describe URL for the service
        $res .=
        '<service name="'.$this->interface_name.'Service">'.
            '<port name="'.$this->interface_name.'Port" binding="tns:'.$this->interface_name.'Binding">'.
                '<soap:address location="'.$this->interface_url.'"/>'.
            '</port>'.
        '</service>';

        $res .= '</definitions>';

        return $res;
    }

}

?>
