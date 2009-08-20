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

//TODO: add QueryZipCode (is it useful???)

require_once('class.Cache.php');

class TA_queryParams
{
	public $FromRecord;
	public $ToRecord;
	public $FindName;
	public $FindLocality;

	function __construct($name, $locality, $phone = '')
	{
		$this->FindName     = $name;
		$this->FindLocality = $locality;
		$this->FindTelephone = $phone; //if set, dont set name or locality!
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
	public $Mobiles;
	public $TeleRestriction;
	public $Gender;
	public $BirthDate;

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
		$this->Mobiles      = 1;
		$this->TeleRestriction = 1;
		$this->Gender       = 1;//XXX K=kvinna,M=Man, to M=male, F=Female
		$this->BirthDate    = 1;//XXX "1955-12-15", to unixtime
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
		$this->TargetType   = $ttype; //4 = både privatpersoner och företag
		$this->QueryParams  = new TA_queryParams($find_name, $find_place);
		$this->QueryColumns = new TA_queryColumns();
	}
}

class TA_findTelephone
{
	public $UserId;
	public $Password;
	public $TargetType;
	public $QueryParams;
	public $QueryColumns;

	function __construct($user, $password, $ttype, $telephone)
	{
		$this->UserId       = $user;
		$this->Password     = $password;
		$this->TargetType   = $ttype; //4 = både privatpersoner och företag
		$this->QueryParams  = new TA_queryParams('', '', $telephone);
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

	function showError($res)
	{
		if ($res->api_result->error_code) {
			echo "teleadress ERROR ".$res->api_result->error_code.": ".$res->api_result->error_text."\n";
			return true;
		}
		return false;
	}

	function firstResult($res, $return_first = true)
	{
		//record_list[0] = privatpersoner
		if (!empty($res->api_result->record_list[0]->record)) {
			if ($return_first && $res->api_result->count_private > 1)
				return $res->api_result->record_list[0]->record[0];
			else
				return $res->api_result->record_list[0]->record;
		}

		//[1] = företag
		if (!empty($res->api_result->record_list[1]->record)) {
			if ($return_first && $res->api_result->count_company > 1)
				return $res->api_result->record_list[1]->record[0];
			else
				return $res->api_result->record_list[1]->record;
		}

		return false;
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
		if ($res) {
			$res = unserialize($res);
		} else {
			$params->FindPerson = new TA_findPerson($this->username, $this->password, 4, $name, $place);
			$res = $this->client->Find($params);
		}

		$post = false;
		if (!$this->showError($res)) {
			$post = $this->firstResult($res, $return_first);
		}

		$this->cache->set($key, serialize($res), $this->cache_expire);
		return $post;
	}

	function findTelephone($number)
	{
		$key = 'teleadress//findtelephone//'.$number.'//first';

		$res = $this->cache->get($key);
		if ($res) {
			$res = unserialize($res);
		} else {
			$params->FindPerson = new TA_findTelephone($this->username, $this->password, 4, $number);
			$res = $this->client->Find($params);
		}

		$post = false;
		if (!$this->showError($res)) {
			$post = $this->firstResult($res);
		}

		$this->cache->set($key, serialize($res), $this->cache_expire);
		return $post;
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
