<?php
/**
 * $Id$
 *
 * Wrapper for built in XMLReader with helper methods
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX TODO: rename to XmlReader when we use namespaces

require_once('HttpClient.php');

class CoreXmlReader extends XMLReader
{
    function parse($data)
    {
        if (is_url($data)) {
            $http = new HttpClient($data);
            $http->setCacheTime('30m');
            $data = $http->getBody();

            //FIXME check http client return code for 404
            if (strpos($data, '<?xml ') === false) {
                throw new Exception ('RssReader->parse FAIL: cant parse feed from '.$http->getUrl() );
                return false;
            }
        }

        $this->xml($data);
    }

    function readValue()
    {
        $this->read();
        return $this->value;
    }

}

?>
