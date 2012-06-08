<?php
/**
 * $Id$
 *
 * Uses UpenLayers JavaScript widget for OpenStreetMap embeddable map
 *
 * http://www.openlayers.org/
 * http://wiki.openstreetmap.org/wiki/OpenLayers_Simple_Example
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

//STATUS: works

require_once('MapWidget.php');

class OpenStreetMapClient extends MapWidget
{
    function render()
    {
        $div_id = 'omap_'.mt_rand();

        $header = XhtmlHeader::getInstance();
        $header->includeJsLast('core_dev/js/ext/openlayers/OpenLayers.js'); // version 2.11

        $js =
        'map=new OpenLayers.Map("'.$div_id.'");'.
        'map.addLayer(new OpenLayers.Layer.OSM());'.

        'var ll=new OpenLayers.LonLat('.$this->longitude.','.$this->latitude.')'.
            '.transform('.
            'new OpenLayers.Projection("EPSG:4326"),'. // transform from WGS 1984
            'map.getProjectionObject()'.               // to Spherical Mercator Projection
            ');'.

        'map.setCenter(ll,'.$this->zoom.');';

        $header->embedJsOnload($js);

        return
        '<div id="'.$div_id.'" style="width:'.$this->width.'px;height:'.$this->height.'px;"></div>';
    }

}

?>
