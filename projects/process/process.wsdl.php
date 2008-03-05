<?php
	$config['no_session'] = true;	//force session "last active" update to be skipped
	require_once('config.php');
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>

<definitions name="Process"
	targetNamespace="http://example.org/process.wsdl"
	xmlns:tns="http://example.org/process.wsdl"
	xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
	xmlns:xsd="http://www.w3.org/2001/XMLSchema"
	xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"
	xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"
	xmlns="http://schemas.xmlsoap.org/wsdl/">

	<message name="fetchAndConvertRequest">
		<part name="uri" type="xsd:string"/>
		<part name="callback" type="xsd:string"/>
	</message>
	<message name="fetchAndConvertResponse">
		<part name="Result" type="xsd:integer"/>
	</message>

	<portType name="ProcessPortType">
		<operation name="fetchAndConvert">
			<input message="tns:fetchAndConvertRequest"/>
			<output message="tns:fetchAndConvertResponse"/>
		</operation>
	</portType>

	<binding name="ProcessBinding" type="tns:ProcessPortType">
		<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>

		<operation name="fetchAndConvert">
			<soap:operation soapAction="urn:#fetchAndConvert"/>
			<input>
				<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
			</input>
			<output>
				<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
			</output>
		</operation>

	</binding>

	<service name="ProcessService">
		<port name="ProcessPort" binding="tns:ProcessBinding">
			<soap:address location="<?=$config['process']['soap_server']?>"/>
		</port>
	</service>

</definitions>
