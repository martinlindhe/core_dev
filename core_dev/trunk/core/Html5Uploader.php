<?php
/**
 * $Id$
 *
 * Wrapper for js/ext/html5uploader.js
 * Verified to work with Firefox 8, Chrome 15
 */

//TODO: test with Safari (should work)

//STATUS: wip, dont work with IE (may work with IE10 when it is released)

class Html5Uploader
{
    /**
     * @param $id album id
     */
    public static function albumUploader($id)
    {
        $header = XhtmlHeader::getInstance();

        $page = XmlDocumentHandler::getInstance();

        $header->includeJs($page->getRelativeCoreDevUrl().'js/ext/html5uploader.js');

        $header->embedJsOnload(
        "new uploader('drop', 'status', '/u/upload/album/".$id."', 'list');"
        );

        $header->embedCss(
        '#box{'.
            'width:300px;'.
            'border:2px solid #454545;'.
            'border-radius:6px;'.
            '-webkit-border-radius:6px;'.
            '-moz-border-radius:6px;'.
        '}'.
        '#drop{'.
            'width:100%;'.
            'height:200px;'.
            'background-color:#E5E5E5;'.
        '}'.
        '#status{'.
            'font-size:10px;'.
            'background-color:#2B2B2B;'.
            'color:#fff;'.
            'padding:5px;'.
        '}'.
        '#list{'.
            'width:100%;'.
            'font-size:10px;'.
            'float:left;'.
            'margin-left:10px;'.
        '}'.
        '.addedIMG{'.
            'height:100px;'.
        '}'
        );

        $txt = 'Drag the images from a folder to the area below ...';

        $res =
        '<div id="box">'.
                '<div id="status">'.$txt.'</div>'.
                '<div id="drop"></div>'.
        '</div>'.
        '<div id="list"></div>';

        return $res;
    }

}

?>
