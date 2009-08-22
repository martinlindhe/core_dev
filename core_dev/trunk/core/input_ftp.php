<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with FTP/FTPES servers
 *
 * upload and download has been verified to work with a ~10MB file (over ftp and ftpes)
 *
 * URL schemes:
 * ftp:// - Classic FTP
 * ftpes:// - FTP over Explicit SSL/TLS
 * ftps:// - FTP over Implicit SSL/TLS (XXX NOT SUPPORTED) - vsftp 2.0.7 support this, try it out
 * sftp:// - SSH FTP (XXX not supported here!!!)
 *
 * http://en.wikipedia.org/wiki/FTPS
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//XXX: rename to "client_ftp.php", it is both input & output...

class ftp
{
	//XXX to support implicit SSL we might need to do a full ftp implementation, or maybe curl can help us?
	//    more "full" ftp implementation: http://kacke.de/php_samples/source.php?f=ftp.cls.php

	var $scheme, $host, $port, $path;
	var $username = 'anonymous';
	var $password = 'anon@ftp.com';

	var $handle = false, $pipes;

	function __construct()
	{
	}

	function __destruct()
	{
		if (!$this->handle) return;

		ftp_close($this->handle);

		fclose($this->pipes[0]);
		fclose($this->pipes[1]);
	}

	function parse_url($url)
	{
		$p = parse_url($url);
		if (!$p) return false;

		$this->scheme = $p['scheme'];

		$this->path = '/';
		$this->port = 21;

		$this->host = $p['host'];

		if (!empty($p['port'])) $this->port = $p['port'];
		if (!empty($p['path'])) $this->path = $p['path'];

		if (!empty($p['user'])) $this->username = $p['user'];
		if (!empty($p['pass'])) $this->password = $p['pass'];
		return true;
	}

	function connect($url)
	{
		if ($this->handle) return true;
		if (!$this->parse_url($url)) return false;

		switch ($this->scheme) {
		case 'ftp':   $this->handle = ftp_connect($this->host, $this->port); break;
		case 'ftpes': $this->handle = ftp_ssl_connect($this->host, $this->port); break;
		default: die('ftp class: unhandled scheme '.$this->scheme);
		}
		if (!$this->handle) return false;

		if (!ftp_login($this->handle, $this->username, $this->password)) return false;

		$this->pipes = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
		stream_set_blocking($this->pipes[1], 0);
		return true;
	}

	/**
	 * Returns current working directory for the FTP connection
	 */
	function getcwd()
	{
		return $this->path;
	}

	function chdir($path)
	{
		//XXX: verify if remote path exists!
		$this->path = $path;
	}

	function dir($remote_path)
	{
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

	function is_dir($path)
	{
		die('FIXME implement');
		$x = $this->dir($path); //XXX list 1 directory below path. needs path parsing, explode by /
		print_r($x);
	}

	function is_file($path)
	{
		die('FIXME implement');
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

	/**
	 * Get a file from a FTP server
	 * @param $url ftp://usr:pwd@host/file
	 */
	function get($url)
	{
		if (!$this->connect($url)) return false;

		$data = '';

		$ret = ftp_nb_fget($this->handle, $this->pipes[0], $this->path, FTP_BINARY);

		while ($ret == FTP_MOREDATA && !feof($this->pipes[1])) {
			$r = fread($this->pipes[1], 8192);
			if (!$r) break;
			$data .= $r;
			$ret = ftp_nb_continue($this->handle);
		}

		while (!feof($this->pipes[1])) {
			$r = fread($this->pipes[1], 8192);
			if (!$r) break;
			$data .= $r;
		}

		if ($ret != FTP_FINISHED) return false;
		return $data;
	}

	/**
	 * Uploads a file to the ftp
	 *
	 * @param $local_file path to local file
	 * @param $remote_file path to remote file
	 */
	function put($local_file, $remote_file)
	{
		if (!$this->handle) return false;

		return ftp_put($this->handle, $remote_file, $local_file, FTP_BINARY);
	}

}

?>
