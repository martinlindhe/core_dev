<?php
/**
 * $Id$
 *
 * Helper class for creating SOAP (WSDL) XML interfaces
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//XXXX: SOAP här är helt fel namn den borde heta WSDL_Generator !!!!

class SOAP
{
	public $interface_name = 'Undefined';
	public $interface_url = '';

	private $messages = array();

	function __construct($interface_name, $interface_url)
	{
		$this->interface_name = $interface_name;
		$this->interface_url = $interface_url;
	}

	function message($name, $params = array())
	{
		$this->messages[$name] = $params;
	}

	//see http://en.wikipedia.org/wiki/Web_Services_Description_Language
	function output()
	{
		header('Content-type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

		echo '<definitions name="'.$this->interface_name.'"
				targetNamespace="http://example.org/'.$this->interface_name.'.wsdl"
				xmlns:tns="http://example.org/'.$this->interface_name.'.wsdl"
				xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
				xmlns:xsd="http://www.w3.org/2001/XMLSchema"
				xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"
				xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
				xmlns="http://schemas.xmlsoap.org/wsdl/">';

		//Describe function parameter datatypes and return datatypes
		foreach ($this->messages as $name => $params) {
			echo '<message name="'.$name.'Request">';
				foreach ($params as $part => $type) {
					if ($part == 'response') continue;
					echo '<part name="'.$part.'" type="xsd:'.$type.'"/>';
				}
			echo '</message>';

			echo '<message name="'.$name.'Response">';
				if (!$params['response']) $res_type = 'integer';
				else $res_type = $params['response'];
				echo '<part name="Result" type="xsd:'.$res_type.'"/>';
			echo '</message>';
		}

		//Describe what <message> responds to a specific <operation> input and output
		echo '<portType name="'.$this->interface_name.'PortType">';
			foreach ($this->messages as $operation => $params) {
				echo '<operation name="'.$operation.'">';
					echo '<input message="tns:'.$operation.'Request"/>';
					echo '<output message="tns:'.$operation.'Response"/>';
				echo '</operation>';
			}
		echo '</portType>';

		//Describe how to encode data for each <operation> input and output
		echo '<binding name="'.$this->interface_name.'Binding" type="tns:'.$this->interface_name.'PortType">';
			echo '<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>';
			foreach ($this->messages as $operation => $params) {
				echo '<operation name="'.$operation.'">';
					echo '<soap:operation soapAction="urn:#'.$operation.'"/>';
					echo '<input><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>';
					echo '<output><soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>';
				echo '</operation>';
			}
		echo '</binding>';

		//Describe URL for the service
		echo '<service name="'.$this->interface_name.'Service">';
			echo '<port name="'.$this->interface_name.'Port" binding="tns:'.$this->interface_name.'Binding">';
				echo '<soap:address location="'.$this->interface_url.'"/>';
			echo '</port>';
		echo '</service>';

		echo '</definitions>';
	}

}
