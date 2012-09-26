<?php
/**
 * $Id$
 *
 * Implements a OpenSearch compatible search engine
 *
 * http://www.opensearch.org/Specifications/OpenSearch/1.1
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: ability to embed icon in the xml
//TODO: ask browser to cache content

namespace cd;

require_once('XhtmlComponent.php');

class XhtmlComponentOpenSearch extends XhtmlComponent
{
    var $url;  ///< relative link to the script handling searches on the server including search parameter, example: "search.php?s="
    var $icon; ///< (optional) url to icon resource

    function render()
    {
        if (!$this->url)
            throw new Exception ('no url set');

        $page = XmlDocumentHandler::getInstance();
        $page->disableDesign(); //remove XhtmlHeader, designHead & designFoot for this request
        $page->setMimeType('application/xml');       // or "application/opensearchdescription+xml"

        if (!is_url($this->icon))
            $this->icon = $page->getUrl().$this->icon;

        if (!is_url($this->url))
            $this->url = $page->getUrl().$this->url;

        return
        '<?xml version="1.0" encoding="UTF-8"?>'.
            '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">'.
            '<ShortName>'.$this->name.'</ShortName>'.
//            '<Description>'.$this->name.'</Description>'.
            ($this->icon ? '<Image height="16" width="16" type="image/x-icon">'.$this->icon.'</Image>' : '').
            '<Url type="text/html" template="'.$this->url.'{searchTerms}"/>'.
        '</OpenSearchDescription>';
    }

}

?>
