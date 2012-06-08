<?php
/**
 * $Id$
 *
 * Google Maps Javascript API V3
 *
 * Currently based on version "V3" of the API, which is
 * the latest release version as of 2012/02
 *
 * Tutorial:
 * http://code.google.com/apis/maps/documentation/javascript/tutorial.html
 *
 * Examples:
 * http://code.google.com/apis/maps/documentation/javascript/examples/index.html
 *
 * @author Martin Lindhe, 2011-2012 <martin@startwars.org>
 */

//STATUS: wip

//FIXME: add title text to map markers
//TODO: make default mapTypeId configurable: ROADMAP, SATELLITE, HYBRID, TERRAIN
//TODO: ability to use "css sprite" images somehow

require_once('html.php');

require_once('MapWidget.php');



class GoogleMapsJs extends MapWidget
{
    protected $lang;                    ///< 2-letter language code, eg "en". if unset, it will autodetect
    protected $region;                  ///< ("US", or "SE") in order to force maps to assume it is viewed from this region, rather than detected region

    protected $sensor = false;          ///< shall google maps try to detect location of the user? instead of specifying coordinates

    protected $width  = 500;
    protected $height = 300;

//    protected $api_key = 'AIzaSyC262ttP813tKVbb79fRHjv6oP-542KeEM';    // XXX remove, dont seem to be needed anymore? 2012-06-08

    function render()
    {
        if ($this->latitude === false || $this->longitude === false)
            throw new Exception ('initial center coords required but not set!');

        $header = XhtmlHeader::getInstance();
        $header->includeJs(
            'http://maps.google.com/maps/api/js'.
            '?sensor='.sbool($this->sensor).
//            '&amp;key='.$this->api_key.
            ($this->lang   ? '&amp;language='.$this->lang : '').
            ($this->region ? '&amp;region='.$this->region : '')
        );

        $div_id = 'gmap_'.mt_rand();

        $res =
        'var myOptions={'.
            'center:new google.maps.LatLng('.$this->latitude.','.$this->longitude.'),'.
            'zoom:'.$this->zoom.','.
            'mapTypeId:google.maps.MapTypeId.ROADMAP'.
        '};'.
        'var myMap=new google.maps.Map(document.getElementById("'.$div_id.'"),myOptions);';

        foreach ($this->markers as $idx => $m)
            $res .=
            'var mk'.$idx.'=new google.maps.Marker({'.
                'position:new google.maps.LatLng('.$m->latitude.','.$m->longitude.'),'.
                ($m->icon ? 'icon:"'.$m->icon.'",' : '').
                ($m->tooltip ? 'title:"'.$m->tooltip.'",' : '').
                ($m->zIndex ? 'zIndex:'.$m->zIndex.',' : '').
                ($m->flat ? 'flat:true,' : '').
                'map:myMap'.
            '});';

        $header->embedJsOnload($res);

        return
        '<div id="'.$div_id.'" style="width:'.$this->width.'px;height:'.$this->height.'px;"></div>';
    }

}

?>
