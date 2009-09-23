<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with FTP servers
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

//FIXME ubuntu: for sftp support, curl needs to be recompiled with sftp support (ubuntu 9.04)
//      https://bugs.launchpad.net/ubuntu/+source/curl/+bug/311029

//TODO implement functions when needed, such as mkdir(), rmdir(), delete()

class ftp
{
	private $scheme, $host;
	private $port = 21, $path = '/';
	private $username = 'anonymous';
	private $password = 'anon@ftp.com';

	private $curl  = false; ///< curl handle
	private $debug = false;

	function __construct($address = '')
	{
		if (!function_exists('curl_init')) {
			echo "ERROR: php5-curl missing".dln();
			return false;
		}

		$this->setAddress($address);
	}

	function __destruct()
	{
		$this->close();
	}

	/**
	 * Returns a string representing the current server URL
	 */
	function getUrl()
	{
		return $this->scheme.'://'.urlencode($this->username).':'.urlencode($this->password).'@'.$this->host.':'.$this->port.$this->path;
	}

	function setDebug($bool = true) { $this->debug = $bool; }

	/**
	 * @param $address "ftp://user:pwd@host:port/"
	 */
	function setAddress($address)
	{
		$p = parse_url($address);
		if (!$p) return false;

		$this->scheme = $p['scheme'];
		$this->host   = $p['host'];

		if (!empty($p['port'])) $this->port = $p['port'];
		if (!empty($p['path'])) $this->setPath($p['path']);

		if (!empty($p['user'])) $this->username = $p['user'];
		if (!empty($p['pass'])) $this->password = $p['pass'];
		return true;
	}

	function setPath($remote_path)
	{
		//XXX: verify if remote path exists!
		if (substr($remote_path, 0, 1) == '/')
			$this->path = $remote_path;
		else
			$this->path = '/'.$remote_path;
	}

	/**
	 * Connects to the server
	 */
	function connect()
	{
		if ($this->curl) return true;

		$this->curl = curl_init();

		if ($this->debug)
			curl_setopt($this->curl, CURLOPT_VERBOSE, true);

		switch ($this->scheme) {
		case 'ftp':  break;
		case 'sftp': break;

		case 'ftpes':
			$this->scheme = 'ftp';
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($this->curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);
			curl_setopt($this->curl, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
			break;

		default:
			die('ftp class: unhandled scheme '.$this->scheme.dln());
		}

		return true;
	}

	/**
	 * Closes connection to the ftp server
	 */
	function close()
	{
		if (!$this->curl) return;

		if ($this->debug) {
			print_r(curl_getinfo($this->curl));
			echo "cURL error number:" .curl_errno($this->curl).dln();
			echo "cURL error:" . curl_error($this->curl).dln();
			print_r(curl_version());
		}

		curl_close($this->curl);
		$this->curl = false;
	}

	/**
	 * Get a file from a FTP server
	 *
	 * @param $url ftp://usr:pwd@host/file
	 * @param $local_file write to local file
	 */
	function getFile($remote_file, $local_file)
	{
		if (!$this->connect()) return false;

		$this->setPath($remote_file);

		curl_setopt($this->curl, CURLOPT_URL, $this->getUrl() );

		curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);

		$fp = fopen($local_file, 'w');
		if (!$fp) {
			echo "ftp->get failed to open local file for writing".dln();
			return false;
		}
		curl_setopt($this->curl, CURLOPT_FILE, $fp);
		curl_exec($this->curl);
		fclose($fp);

		if (curl_errno($this->curl)) {
			echo "ftp download error: ".curl_error($this->curl).dln();
			return false;
		}

		if ($this->debug) echo 'getFile md5: '.md5_file($local_file).dln();

		return true;
	}

	/**
	 * Returns remote file as a data string
	 */
	function getData($remote_file)
	{
		if (!$this->connect()) return false;

		$this->setPath($remote_file);

		curl_setopt($this->curl, CURLOPT_URL, $this->getUrl() );

		curl_setopt($this->curl, CURLOPT_TIMEOUT, 30);

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec($this->curl);

		if (curl_errno($this->curl)) {
			echo "ftp download error: ".curl_error($this->curl).dln();
			return false;
		}

		if ($this->debug) echo 'getData md5: '.md5($res).dln();

		return $res;
	}

	/**
	 * Stores $data on the ftp
	 *
	 * @param $remote_path remote path to store data in, including filename
	 * @param $data content to store
	 */
	function putData($remote_path, $data)
	{
		$tmp_file = tempnam('/tmp', 'cdFtp_');
		file_put_contents($tmp_file, $data);

		return $this->putFile($remote_path, $tmp_file);
	}

	/**
	 * Uploads a file to the ftp
	 *
	 * @param $remote_path destination path
	 * @param $local_file path to local file
	 */
	function putFile($remote_file, $local_file)
	{
		if (!$this->connect()) return false;

		if (!file_exists($local_file)) {
			echo "ftp: local file ".$local_file." dont exist!".dln();
			return false;
		}

		if ($this->debug) echo 'putFile md5: '.md5_file($local_file).dln();

		$this->setPath($remote_file);
		curl_setopt($this->curl, CURLOPT_URL, $this->getUrl() );

		$fp = fopen($local_file, 'r');
		curl_setopt($this->curl, CURLOPT_UPLOAD, 1);
		curl_setopt($this->curl, CURLOPT_INFILE, $fp);
		curl_setopt($this->curl, CURLOPT_INFILESIZE, filesize($local_file));
		curl_exec($this->curl);
		fclose($fp);

        if (curl_errno($this->curl)) {
        	echo "ftp upload error: ".curl_error($this->curl).dln();
			return false;
        }

		return true;
	}

	/**
	 * Renames a file or directory on the server
	 */
	function rename($remote_src, $remote_dst)
	{
		if (!$this->connect()) return false;

		$this->setPath($remote_src);
		curl_setopt($this->curl, CURLOPT_URL, $this->getUrl() );

		if ($this->scheme == 'sftp') {
			$buf = array(
			"rename ".$remote_src." ".$remote_dst
			);
		} else {
			$buf = array(
			"RNFR ".$remote_src,
			"RNTO ".$remote_dst
			);
		}

		curl_setopt($this->curl, CURLOPT_POSTQUOTE, $buf);
		curl_exec($this->curl);

        if (curl_errno($this->curl)) {
        	echo "ftp rename error: ".curl_error($this->curl).dln();
			return false;
        }
		return true;
	}

}

?>
