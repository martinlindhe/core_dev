<?php
/**
 * $Id$
 *
 * Tutorial:
 * http://code.google.com/apis/maps/documentation/javascript/tutorial.html
 *
 * Examples:
 * http://code.google.com/apis/maps/documentation/javascript/examples/index.html
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip

//FIXME: add title text to map markers

//XXXX: map div messes up page

//TODO: make mapTypeId configurable: ROADMAP, SATELLITE, HYBRID, TERRAIN
//TODO: docs mentioned a version param to force a exact version of the api for production sites, use this to match a working implementation!

require_once('output_js.php');
require_once('output_css.php');
require_once('Coordinate.php');

class GoogleMapMarker extends Coordinate
{
    var $tooltip;
    var $icon;
}

class GoogleMapsJs
{
    protected $latitude;
    protected $longitude;
    protected $lang;                    ///< 2-letter language code, eg "en". if unset, it will autodetect
    protected $region;                  ///< ("US", or "SE") in order to force maps to assume it is viewed from this region, rather than detected region

    protected $detect_location = false; ///< shall google maps try to detect location of the user? .. XXX WHY??? whats the benefit of setting this?
    protected $zoom = 1;                ///< 1 (whole world) to 20 (max zoom)

    protected $width;
    protected $height;
    protected $markers = array();

    function __construct($lat = 0, $lng = 0)
    {
        $this->latitude  = $lat;
        $this->longitude = $lng;
    }

    function setZoomLevel($n) { $this->zoom = $n; }
    function setWidth($n) { $this->width = $n; }
    function setHeight($n) { $this->height = $n; }


    function addMarkers($arr)
    {
        if (!is_array($arr))
            throw new Exception ('not array');

        foreach ($arr as $o)
        {
            if (!($o instanceof GoogleMapMarker))
                throw new Exception ('need GoogleMapMarker');

            $this->markers[] = $o;
        }
    }

    function render()
    {
        $url =
        'http://maps.google.com/maps/api/js?sensor='.sbool($this->detect_location).
        ($this->lang   ? '&language='.$this->lang : '').
        ($this->region ? '&region='.$this->region : '');

        $header = XhtmlHeader::getInstance();
        $header->includeJs($url);

        $div_id = 'gm_map_'.mt_rand();

         $header->embedCss(
        '#'.$div_id.' {'.
            ($this->width  ? 'width:'.css_size($this->width).';' : '' ).
            ($this->height ? 'height:'.css_size($this->height).';' : '').
        '}'
        );

        $res =
        'var ll = new google.maps.LatLng('.$this->latitude.','.$this->longitude.');'.
        'var myOptions = {'.
            'center: ll,'.
            'zoom: '.$this->zoom.','.
            'mapTypeId: google.maps.MapTypeId.ROADMAP'.
        '};'.
        'var myMap = new google.maps.Map(document.getElementById("'.$div_id.'"), myOptions);';

        if ($this->markers)
            foreach ($this->markers as $idx => $m)
                $res .=
                'var mk'.$idx.' = new google.maps.Marker({'.
                    'position: new google.maps.LatLng('.$m->latitude.','.$m->longitude.'),'.
                    ($m->icon ? 'icon: "'.$m->icon.'",' : '').
                    ($m->tooltip ? 'title: "'.$m->tooltip.'",' : '').
                    'map: myMap'.
                '});'."\n";

        return
        '<div id="'.$div_id.'"/>'.
        js_embed($res);
    }

}

?>
