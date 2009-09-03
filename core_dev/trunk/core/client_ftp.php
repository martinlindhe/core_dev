<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with FTP/FTPES servers
 *
 * URL schemes:
 * ftp:// - Classic FTP
 * ftpes:// - FTP over Explicit SSL/TLS
 * ftps:// - FTP over Implicit SSL/TLS (XXX NOT SUPPORTED) - vsftp 2.0.7 support this, try it out
 * sftp:// - FTP over SSH (requires curl compiled --with-libssh2)
 *
 * http://en.wikipedia.org/wiki/FTPS
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//XXX for sftp support, curl needs to be recompiled with sftp support (ubuntu 9.04) https://bugs.launchpad.net/ubuntu/+source/curl/+bug/311029

class ftp
{
	var $scheme, $host, $port, $path;
	var $username, $password;
	var $curl = false; ///< curl handle
	var $debug = false;

	function __construct()
	{
		if (!function_exists('curl_init')) {
			echo "ERROR: php5-curl missing\n";
			return false;
		}
	}

	function __destruct()
	{
		if ($this->debug) {
			print_r(curl_getinfo($this->curl));
			echo "cURL error number:" .curl_errno($this->curl)."\n";
			echo "cURL error:" . curl_error($this->curl)."\n";
			print_r(curl_version());
		}

		curl_close($this->curl);
	}

	function parse_url($url)
	{
		$p = parse_url($url);
		if (!$p) return false;

		$this->scheme = $p['scheme'];

		$this->path = '/';
		$this->port = 21;
		$this->username = 'anonymous';
		$this->password = 'anon@ftp.com';

		$this->host = $p['host'];

		if (!empty($p['port'])) $this->port = $p['port'];
		if (!empty($p['path'])) $this->path = $p['path'];

		if (!empty($p['user'])) $this->username = $p['user'];
		if (!empty($p['pass'])) $this->password = $p['pass'];
		return true;
	}

	/**
	 * Returns a string representing the current URL
	 */
	function url()
	{
		return $this->scheme.'://'.urlencode($this->username).':'.urlencode($this->password).'@'.$this->host.':'.$this->port.'/'.$this->path;
	}

	function connect($url)
	{
		if (!$this->parse_url($url)) return false;
		if ($this->curl) return true;

		$this->curl = curl_init();

		if ($this->debug)
			curl_setopt($this->curl, CURLOPT_VERBOSE, true);

		switch ($this->scheme) {
		case 'ftp':
			curl_setopt($this->curl, CURLOPT_URL, $this->url() );
			break;

		case 'sftp':
			curl_setopt($this->curl, CURLOPT_URL, $this->url() );
			break;

		case 'ftpes':
			$this->scheme = 'ftp';
			curl_setopt($this->curl, CURLOPT_URL, $this->url() );
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($this->curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);
			curl_setopt($this->curl, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
			break;

		default:
			die('ftp class: unhandled scheme '.$this->scheme."\n");
		}
		return true;
	}

	/**
	 * Get a file from a FTP server
	 *
	 * @param $url ftp://usr:pwd@host/file
	 * @param $local_file write to local file (if set)
	 */
	function get($url, $local_file = '')
	{
		if (!$this->connect($url)) return false;

		curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);

		if ($local_file) {
			$fp = fopen($local_file, 'w');
			if (!$fp) {
				echo "ftp->get failed to open local file for writing\n";
				return false;
			}
			curl_setopt($this->curl, CURLOPT_FILE, $fp);
			curl_exec($this->curl);
			fclose($fp);

			if (curl_errno($this->curl)) {
				echo "File download error: ".curl_error($this->curl)."\n";
				return false;
			}

			if ($this->debug) echo 'md5: '.md5_file($local_file)."\n";

			return true;
		} else {
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($this->curl);

			if (curl_errno($this->curl)) {
				echo "File download error: ".curl_error($this->curl)."\n";
				return false;
			}

			return $res;
		}
	}

	/**
	 * Uploads a file to the ftp
	 *
	 * @param $url destination path
	 * @param $local_file path to local file
	 */
	function put($url, $local_file)
	{
		if (!$this->connect($url)) return false;

		if (!file_exists($local_file)) {
			echo "ftp: local file ".$local_file." dont exist!\n";
			return false;
		}

		if ($this->debug) echo 'md5: '.md5_file($local_file)."\n";

		$fp = fopen($local_file, 'r');
		curl_setopt($this->curl, CURLOPT_UPLOAD, 1);
		curl_setopt($this->curl, CURLOPT_INFILE, $fp);
		curl_setopt($this->curl, CURLOPT_INFILESIZE, filesize($local_file));
		curl_exec($this->curl);
		fclose($fp);

        if (curl_errno($this->curl)) {
        	echo "File upload error: ".curl_error($this->curl);
			return false;
        }

		return true;
	}








	/**
	 * Returns current working directory for the FTP connection
	 */
	function pwd()
	{
		die('implement pwd()');
		//return $this->path;
	}

	function chdir($path)
	{
		die('impĺement chdir()');
		//XXX: verify if remote path exists!
		$this->path = $path;
	}

	function mkdir($path)
	{
		die('implement mkdir()');
	}

	function rmdir($path)
	{
		die('implement rmdir()');
	}

	function chmod()
	{
		die('implement chmod()');
	}

	function nlist()
	{
		//return raw directory list of current directory
		die('implement nlist()');
	}

	function delete()
	{
		die('implement delete()');
	}

	function rename()
	{
		die('implement rename()');
	}

	function dir($remote_path)
	{
		die('implement dir()');
		$list = ftp_rawlist($this->handle, $remote_path);

		foreach ($list as $row) {
			$s = preg_split("/[\s]+/", $row, 9);

			if (strpos($s[7], ':') !== false) //"Aug 09 15:54" => "09 Aug 2009 15:54"
				$ts = strtotime($s[6].' '.$s[5].' '.date('Y').' '.$s[7]);
			else //"Aug 09 2007" => "09 Aug 2007"
				$ts = strtotime($s[6].' '.$s[5].' '.$s[7]);

			$res[] = array(
			'name'  => $s[8],
			'dir'   => $s[0]{0} == 'd',
			'size'  => $s[4],
			'chmod' => $this->chmodnum($s[0]),
			'date'  => $ts
			);
		}

		return $res;
	}

	/**
	 * Translates FTP file mode code to Unix file mode code
	 */
	function chmodnum($chmod)
	{
		$trans = array('-' => '0', 'r' => '4', 'w' => '2', 'x' => '1');
		$chmod = substr(strtr($chmod, $trans), 1);
		$array = str_split($chmod, 3);
		return array_sum(str_split($array[0])) . array_sum(str_split($array[1])) . array_sum(str_split($array[2]));
	}

}

?>
