<?php
/**
 * $Id$
 *
 * Wrapper around the ios-add2home javascript
 *
 * https://github.com/cubiq/add-to-homescreen
 * http://cubiq.org/add-to-home-screen
 *
 * @author Martin Lindhe, 2012 <martin@startwars.org>
 */

namespace cd;

class iOSAddToHomeJs
{
    public static function render()
    {
        if (!HttpUserAgent::isIOS())
            return;

        $header = XhtmlHeader::getInstance();

        $header->embedJs(
        'var addToHomeConfig='.
        '{'.
            'touchIcon:true,'. // show <link rel="apple-touch-icon"> image in popup?
            'lifespan:10000,'. // = 10s
            'message:"Lägg till denna app på din %device: tryck på %icon och sedan <strong>Lägg till på hemskärmen</strong>."'.
            // 'returningVisitor:true,'.
            // 'expire:2,'.
            // 'animationIn:"bubble",'.
            // 'animationOut:"drop",'.
        '};'
        );

        $header->includeCss('core_dev/js/ext/ios-add2home/assets/add2home.css');
        $header->includeJsLast('core_dev/js/ext/ios-add2home/add2home.js');
    }

}

?>
