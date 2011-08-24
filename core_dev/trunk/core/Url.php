<?php
/**
 * $Id$
 *
 * Location object, contains a URI resource
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: good

require_once('CoreProperty.php');
require_once('network.php'); //for is_url(), scheme_default_port(), url_query()

class Url extends CoreProperty
{
    private $scheme = 'http';
    private $host;
    private $port;
    private $path;
    private $params  = array();
    private $username, $password; ///< for HTTP AUTH

    function setUsername($username) { $this->username = $username; }
    function setPassword($password) { $this->password = $password; }

    function getScheme()   { return $this->scheme; }
    function getUsername() { return $this->username; }
    function getPassword() { return $this->password; }
    function getParams() { return $this->params; }

    /**
     * @param $safe outputs URL in a safe format (& => &amp;)
     * @return the full url as a string
     */
    function get($safe = false)
    {
        if (!$this->scheme || !$this->host)
            return false;

        $port = '';
        if ($this->port && scheme_default_port($this->scheme) != $this->port)
            $port = ':'.$this->port;

        $res = $this->scheme.'://'.($this->username ? $this->username.':'.$this->password.'@' : '').$this->host.$port.$this->getPath($safe);

        return $res;
    }

    function getHost()
    {
        if ($this->port &&
            ($this->scheme == 'http'  && $this->port != 80) ||
            ($this->scheme == 'https' && $this->port != 443)
        )
            return $this->host.':'.$this->port;

        return $this->host;
    }

    /** @return url path excluding hostname */
    function getPath($safe = false)
    {
        $res = $this->path;

        if (!empty($this->params))
            if ($safe)
                $res .= '?'.htmlspecialchars(url_query($this->params, '&'));
            else
                $res .= '?'.url_query($this->params, '&');

        return $res;
    }

    function render() { return $this->get(); }

    /**
     * @param $text string to substitute location with
     * @return the url as a html tag
     */
    function asHtml($text = '')
    {
        if (!$text)
            $text = $this->get();

        return '<a href="'.$this->get().'">'.$text.'</a>';
    }

    private function reset()
    {
        $this->scheme = 'http';
        $this->host   = '';
        $this->port   = '';
        $this->path   = '';
        $this->params = array();
    }

    /**
     * Parses url and initializes the object for this url
     */
    function set($url)
    {
        if (!$url)
            return false;

        if (!is_url($url))
            throw new Exception ('not a url: '.$url);

        $this->reset();

        $parsed = parse_url($url);

        if (!$parsed['scheme'])
            return false;

        if (empty($parsed['path'])) $parsed['path'] = '/';

        $this->scheme = $parsed['scheme'];
        $this->host   = $parsed['host'];

        if (!empty($parsed['port']))
            $this->port = $parsed['port'];

        $this->path   = $parsed['path'];

        if (!empty($parsed['query']))
            parse_str($parsed['query'], $this->params);

        return true;
    }

    function setPath($path)
    {
        $parsed = parse_url($path);

        $this->path = $parsed['path'];

        if (!empty($parsed['query']))
            parse_str($parsed['query'], $this->params);
    }

    function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return true;
    }

    /**
     * Adds/sets a parameter to the url
     */
    function setParam($name, $val = false)
    {
        unset($this->params[$name]);
        $this->params[$name] = $val;
    }

    function getParam($name)
    {
        if (isset($this->params[$name]))
            return $this->params[$name];

        return false;
    }

    /**
     * Removes a parameter from the url
     */
    function removeParam($name)
    {
        foreach ($this->params as $n=>$val)
            if ($name == $n)
                unset($this->params[$n]);
    }

}

?>
