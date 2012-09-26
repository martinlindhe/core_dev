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
 * @author Martin Lindhe, 2011-2012 <martin@startwars.org>
 */

//STATUS: works

//TODO: expand popup view with links to image

namespace cd;

class YuiLightbox
{
    function render()
    {
        $header = XhtmlHeader::getInstance();

        $page = XmlDocumentHandler::getInstance();

        $header->includeJs('http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js');
        $header->includeJs($page->getRelativeCoreDevUrl().'js/ext/gallery-lightbox/gallery-lightbox-min.js');

        $header->includeCss($page->getRelativeCoreDevUrl().'js/ext/gallery-lightbox/assets/skins/sam/gallery-lightbox-skin.css');

        $js =
        'YUI().use("gallery-lightbox", "node-deprecated", '.  //XXXX: node-deprecated is added to allow it to work with > YUI 3.0.0
            'function (Y)'.
            '{'.
                'Y.Lightbox.init();'.
            '}'.
        ');';

        return js_embed($js);

    }
}

?>
