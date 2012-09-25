<?php
/**
 * $Id$
 *
 * Collection of utilities to deal with FTP servers
 *
 * URL schemes:
 * ftp:// - Classic FTP
 * sftp:// - FTP over SSH (requires curl compiled --with-libssh2)
 * ftpes:// - FTP over Explicit SSL/TLS
 * ftps:// - FTP over Implicit SSL/TLS (XXX NOT SUPPORTED) - vsftp 2.0.7 support this, try it out
 *
 * http://en.wikipedia.org/wiki/FTPS
 *
 * @author Martin Lindhe, 2008-2012 <martin@startwars.org>
 */

//STATUS: ok

//FIXME: for sftp support, curl needs to be compiled with sftp support; ubuntu bug: https://bugs.launchpad.net/ubuntu/+source/curl/+bug/311029

//XXX: see curl_multi_exec() for performing multiple operations

require_once('CoreBase.php');

class FtpClient extends CoreBase
{
    private $scheme, $host;
    private $port     = 21;
    private $path     = '/';
    private $username = 'anonymous';
    private $password = 'anon@ftp.com';
    private $curl     = false; ///< curl handle
    private $timeout  = 30;    ///< timeout in seconds before giving up each command

    function __construct($url = '')
    {
        if (!extension_loaded('curl'))
            throw new exception ('php5-curl missing');

        if ($url)
            $this->setAddress($url);
    }

    function __destruct()
    {
        $this->close();
    }

    function getPath() { return $this->path; }

    /**
     * Returns a string representing the current server URL
     */
    function getUrl()
    {
        return $this->scheme.'://'.urlencode($this->username).':'.urlencode($this->password).'@'.$this->host.':'.$this->port.$this->path;
    }

    function setTimeout($n) { if (is_numeric($n)) $this->timeout = $n; }

    /**
     * @param $url "ftp://user:pwd@host:port/"
     */
    function setAddress($url) //XXX rename method
    {
        if (!$url)
            throw new Exception ('setAddress called with empty parameter');

        $p = parse_url($url);
        if (!$p)
            return false;

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
     * Translates curl errors to readable strings
     */
    function getFtpError()
    {
        // error codes: http://curl.haxx.se/libcurl/c/libcurl-errors.html
        switch (curl_errno($this->curl)) {
        case 19: return 'Zero byte transfer or weird reply to a RETR command'; //CURLE_FTP_COULDNT_RETR_FILE      XXX php 5.3 curl_error() returns "RETR response: 550", 2010-09-21
        case 78: return 'Remote file not found'; //CURLE_REMOTE_FILE_NOT_FOUND     XXX php 5.3 curl_error() returns "RETR response: 550", 2010-09-20
        default: return curl_error($this->curl).' (errno '.curl_errno($this->curl).')';
        }
    }

    /**
     * Connects to the ftp server
     */
    function connect()
    {
        if ($this->curl) return true;

        $this->curl = curl_init();

        if ($this->getDebug())
            curl_setopt($this->curl, CURLOPT_VERBOSE, true);

        switch ($this->scheme) {
        case 'ftp':  break;
        case 'sftp': break;

        case 'ftpes':
            $this->scheme = 'ftp';
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);
            curl_setopt($this->curl, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
            break;

        default:
            die('ftp class: unhandled scheme '.$this->scheme.ln());
        }

        return true;
    }

    /**
     * Closes connection to the ftp server
     */
    function close()
    {
        if (!$this->curl) return;

        if ($this->getDebug()) {
            print_r(curl_getinfo($this->curl));
            echo 'cURL error number:' .curl_errno($this->curl).ln();
            echo 'cURL error:' . curl_error($this->curl).ln();
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

        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);

        $fp = fopen($local_file, 'w');
        if (!$fp) {
            echo 'ftp->get failed to open local file for writing'.ln();
            return false;
        }
        curl_setopt($this->curl, CURLOPT_FILE, $fp);
        curl_exec($this->curl);
        fclose($fp);

        if (curl_errno($this->curl))
        {
            throw new Exception ('curl error "'.$this->getFtpError($this->curl).'" while reading '.$remote_file);

            if (!filesize($local_file))
                unlink($local_file);

            return false;
        }

        if ($this->getDebug())
            echo 'getFile md5: '.md5_file($local_file).ln();

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
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($this->curl);

        if (curl_errno($this->curl))
            throw new Exception ('ftp download error: '.curl_error($this->curl));

        if ($this->getDebug())
            echo 'getData md5: '.md5($res).ln();

        return $res;
    }

    /**
     * Stores $data on the ftp
     *
     * @param $remote_path remote path to store data in, including filename
     * @param $data content to store
     * @param $temp_file (optional) use temporary filename on remote server during upload
     */
    function putData($remote_path, $data, $tmp_name = '')
    {
        $tmp_file = tempnam('/tmp', 'cdFtp_');
        file_put_contents($tmp_file, $data);

        return $this->putFile($remote_path, $tmp_file, $tmp_name);
    }

    /**
     * Uploads a file to the ftp
     *
     * @param $remote_path destination path
     * @param $local_file path to local file
     * @param $temp_file (optional) use temporary filename on remote server during upload
     */
    function putFile($remote_file, $local_file, $temp_file = '')
    {
        if (!$this->connect()) return false;

        if (!file_exists($local_file)) {
            echo 'ftp: local file '.$local_file.' dont exist!'.ln();
            return false;
        }

        if ($this->getDebug())
            echo 'putFile md5: '.md5_file($local_file).ln();

        if ($temp_file)
            $this->setPath($temp_file);
        else
            $this->setPath($remote_file);

        curl_setopt($this->curl, CURLOPT_URL, $this->getUrl() );

        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);

        $fp = fopen($local_file, 'r');
        curl_setopt($this->curl, CURLOPT_UPLOAD, 1);
        curl_setopt($this->curl, CURLOPT_INFILE, $fp);
        curl_setopt($this->curl, CURLOPT_INFILESIZE, filesize($local_file));

        if ($temp_file) {
            if ($this->scheme == 'sftp') {
                $buf = array(
                'rename '.$temp_file.' '.$remote_file
                );
            } else {
                $buf = array(
                'RNFR '.$temp_file,
                'RNTO '.$remote_file
                );
            }

            curl_setopt($this->curl, CURLOPT_POSTQUOTE, $buf);
        }

        curl_exec($this->curl);
        fclose($fp);

        if (curl_errno($this->curl))
            throw new Exception ('ftp exec error: '.curl_error($this->curl) );

        return true;
    }

    /**
     * Returns remote directory listing
     */
    function getDir($remote_path = '')
    {
        if (!$this->connect()) return false;

        if ($remote_path) $this->setPath($remote_path);

        curl_setopt($this->curl, CURLOPT_URL, $this->getUrl() );

        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $raw = curl_exec($this->curl);

        if (curl_errno($this->curl))
            throw new Exception ('ftp download error: '.curl_error($this->curl));

        //  mode        ?   ?        ?          size    mtime      filename
        //drwxrwxr-x    2 1137     1100         2048 Apr  4  2009 slackware
        //-r--r--r--    1 1137     1100       439571 Oct  3 16:28 CHECKSUMS.md5

        $list = explode("\n", trim($raw));

        $res = array();

        foreach($list as $file)
        {
            $file = preg_split("/ /", $file, 20, PREG_SPLIT_NO_EMPTY);
            if ($file[8] == '.' || $file[8] == '..') continue;

            $row = array();
            $row['mtime'] = strtotime($file[5].' '.$file[6].' '.$file[7]);
            $row['name']  = $file[8];
            $row['size']  = $file[4];
            $row['mode']  = $file[0];

            if ($row['mode']{0} == 'd') {
                $row['is_file'] = false;
                $row['is_dir']  = true;
            } else {
                $row['is_file'] = true;
                $row['is_dir']  = false;
            }

            $res[] = $row;
        }

        return $res;
    }

}

function curl_check_protocol_support($prot)
{
    $v = curl_version();

    foreach ($v['protocols'] as $p)
        if ($p == $prot)
            return true;

    return false;
}

?>
