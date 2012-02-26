<?php
/**
 * $Id$
 *
 * Wrapper for built in XMLReader with helper methods
 *
 * @author Martin Lindhe, 2010-2012 <martin@startwars.org>
 */

//STATUS: wip

//XXX TODO: rename to XmlReader when we use namespaces

require_once('HttpClient.php');

class CoreXmlReader extends XMLReader
{
    function parse($raw)
    {
        if (is_url($raw)) {
            $url = $raw;
            $h = new HttpClient($url);
            $h->setCacheTime('30m');
            $raw = $h->getBody();
//            d( $h->getResponseHeaders() );

            if ( $h->getStatus() == 404) // not found
                return false;

            if ( $h->getStatus() == 302) // redirect
            {
                $redir = $h->getResponseHeader('location');
                // echo "REDIRECT: ".$redir."\n";
                $h = new HttpClient( $redir ); //XXX: reuse previous client?
                $h->setCacheTime('30m');

                $url = $redir;
                $raw = $h->getBody();
            }

            if (strpos($raw, '<?xml ') === false) {
                echo 'http status: '.$h->getStatus()."\n";
                echo 'data len: '.strlen($raw).' bytes'."\n";
                echo dh( substr($raw, 0, 100) );
                throw new Exception ('RssReader->parse FAIL: cant parse feed from '.$url);
                return false;
            }
        }

        $this->xml($raw);
    }

    function readValue()
    {
        $this->read();
        return $this->value;
    }

}

?>
