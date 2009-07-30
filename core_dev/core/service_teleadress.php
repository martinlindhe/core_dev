<?php
/**
 * $Id$
 *
 * References
 * ---
 * http://api.teleadress.se/
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('class.Cache.php');

class TA_queryParams
{
	public $FromRecord;
	public $ToRecord;
	public $FindName;
	public $FindLocality;

	function __construct($name, $locality)
	{
		$this->FindName     = $name;
		$this->FindLocality = $locality;
		$this->FromRecord   = 1;
		$this->ToRecord     = 10;
	}
}

class TA_queryColumns
{
	public $FirstName;
	public $LastName;
	public $StreetName;
	public $StreetNumber;
	public $ZipCode;
	public $Locality;
	public $Telephones;

	function __construct()
	{
		//columns to get in the response:
		$this->FirstName    = 1;
		$this->LastName     = 1;
		$this->StreetName   = 1;
		$this->StreetNumber = 1;
		$this->ZipCode      = 1;
		$this->Locality     = 1;
		$this->Telephones   = 1;
	}
}

class TA_findPerson
{
	public $UserId;
	public $Password;
	public $TargetType;
	public $QueryParams;
	public $QueryColumns;

	function __construct($user, $password, $ttype, $find_name, $find_place)
	{
		$this->UserId       = $user;
		$this->Password     = $password;
		$this->TargetType   = $ttype; //4 = ???
		$this->QueryParams  = new TA_queryParams($find_name, $find_place);
		$this->QueryColumns = new TA_queryColumns();
	}
}

class Teleadress
{
	var $username = 'test';
	var $password = 'TEST';
	var $api_url  = 'http://api.teleadress.se/WSDL/nnapiwebservice.wsdl';

	var $client   = false;
	var $cache    = false;
	var $cache_expire = 86400; ///<  expire time in seconds for local cache (24h default)

	function __construct()
	{
		$this->client = new SoapClient($this->api_url, array( "trace" => 1, "exceptions" => 0 ) );
		$this->cache  = new cache();
		//$this->cache->debug = true;
	}

	/**
	 * Finds adress and phone number for a person
	 *
	 * @param $name name to search for
	 * @param $place location/cityname
	 * @param $return_first if true, only returns the first result
	 */
	function findPerson($name, $place = '', $return_first = true)
	{
		$name  = strtolower($name);
		$place = strtolower($place);
		$key = 'teleadress//findperson//'.$name.'//'.$place.($return_first ? '//first' : '//all');

		$res = $this->cache->get($key);
		if ($res) return unserialize($res);

		$params->FindPerson = new TA_findPerson($this->username, $this->password, 4, $name, $place);
		$res = $this->client->Find($params);

		if (empty($res->api_result->record_list[0]->record)) $res = false;

		if ($return_first)
			$res = $res->api_result->record_list[0]->record[0];
		else
			$res = $res->api_result->record_list[0]->record;

		$this->cache->set($key, serialize($res), $this->cache_expire);
		return $res;
	}
}


/*
class zipcode {
	public $User;
	public $Password;
	public $ZipCode;
	public $LocalityName;
	public $CountyCode;
	public $CommunityCode;
}

$par = new zipcode;
$par->User = $username;
$par->Password =$password;
$par->ZipCode = "";
$par->LocalityName = "Sollentuna";

$params->zipcode = $par;
//echo var_dump($params) . "\n";

$client = new SoapClient("http://api.teleadress.se/WSDL/nnapiwebservice.wsdl", array( "trace" => 1,"exceptions" => 0 ) );

# echo var_dump($client) . "<br/>";
//$functions = $client->__getFunctions();
//print_r($functions);
//$types = $client->__getTypes();
//print_r($types);

$response = $client->QueryZipCode($params);
print_r($response);
*/

?>
