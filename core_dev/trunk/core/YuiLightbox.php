<?php
/**
 * $Id$
 *
 * Wrapper for http://projects.sophomoredev.com/yui-gallery-lightbox
 *
 * You need to add rel="lightbox" to links that you want Lightbox to open
 * Grouping: You just need to add a grouping to the rel attribute with the grouping in brackets.
 *           So rel="lightbox" becomes rel="lightbox[grouping]"
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org> 
 */
 
//STATUS: works
 
class YuiLightbox
{
    function render()
    {
        $header = XhtmlHeader::getInstance();
        
        $page = XmlDocumentHandler::getInstance();

        $header->includeJs('http://yui.yahooapis.com/3.0.0/build/yui/yui-min.js');
        $header->includeJs($page->getCoreDevRoot().'js/ext/gallery-lightbox/gallery-lightbox-min.js'); 

        $header->includeCss($page->getCoreDevRoot().'js/ext/gallery-lightbox/assets/skins/sam/gallery-lightbox-skin.css');
        
        $js =
        'YUI().use("gallery-lightbox", '.
            'function (Y)'.
            '{'.
                'Y.Lightbox.init();'.
            '}'.
        ');';

        return js_embed($js);
        
    }
}

?>
