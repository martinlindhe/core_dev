<?php
/**
 * $Id$
 *
 * Wrapper for built in XMLReader with helper methods
 *
 * @author Martin Lindhe, 2010-2012 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('HttpClient.php');

class XmlReader extends \XMLReader
{
    function parse($raw)
    {
        // TODO XmlReader should not handle HTTP protocol details

        if (is_url($raw)) {
            $url = $raw;
            $h = new HttpClient($url);
//            $h->setCacheTime('30m');
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

            // prepend XML header if nonexistent
            if (strpos($raw, '<?xml ') === false)
                $raw = '<?xml version="1.0"?>'.$raw;
        }

        if (!$this->xml($raw)) {
            if (isset($url))
                throw new \Exception ("Failed to parse XML from ".$url);

            throw new \Exception ("Failed to parse XML");
        }
    }

    function readValue()
    {
        $this->read();
        return $this->value;
    }

}

?>
