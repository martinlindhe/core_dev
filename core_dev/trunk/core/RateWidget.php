<?php
/**
 * $Id$
 *
 * Frontend wrapper for YUI gallery-ratings
 *
 * http://yuilibrary.com/gallery/show/ratings
 * http://www.yuiblog.com/blog/2010/04/28/gallery-ratings/
 *
 */

//STATUS: hackish.. .
//XXX TODO: upgrade to a more finished version of the widget (css on yahoo server etc?)

/**

echo RateWidget::render(BLOG, 74);

*/


require_once('PollWidget.php');

class RateWidget extends PollWidget
{
    /** Count current average of the rating */
    static function getAvgRating($type, $id)
    {
        $q = 'SELECT AVG(value) FROM tblRatings WHERE type = ? AND owner = ?';
        return SqlHandler::getInstance()->pSelectItem($q, 'ii', $type, $id);
    }

    static function render($type, $id)
    {
        $session = SessionHandler::getInstance();

        if (!empty($_GET['rate_vote']) && !empty($_GET['opt']))
        {
            if (!$session->id || !is_numeric($_GET['opt']))
                die('XXX');

            $page = XmlDocumentHandler::getInstance();
            $page->disableDesign();

            self::addPollVote($type, $_GET['rate_vote'], $_GET['opt']);

            ob_clean(); // XXX hack.. removes previous output
            die('1');
        }

        $header = XhtmlHeader::getInstance();

        $header->includeJs('http://yui.yahooapis.com/3.3.0/build/yui/yui-min.js');

///XXX will css+gfx be hosted on yahoo? is here too: https://github.com/petey/yui3-gallery/blob/master/src/gallery-ratings/assets/gallery-ratings.css
        $header->includeCss('http://peterpeterson.net/gallery-ratings/assets/gallery-ratings.css');

        $widget_id = 'rate_'.mt_rand();

//XXX: make it read-only after you clicked it once

        $res =
        'YUI({'.
            'gallery: "gallery-2010.04.14-19-47"'.
        '}).use("gallery-ratings", "event", function(Y) {'.
            'var ratings = new Y.Ratings({'.
//                'inline: true,'.
//                'skin: "small,"'.
                'srcNode: "#'.$widget_id.'"'.
            '});'.
            'ratings.render();'.

            'Y.on("ratings:ratingChange",function(e){'.

                'YUI().use("io-base", function(Y) {'.
                    'var uri = "?rate_vote=" + '.$id.' + "&opt=" + e.newVal;'.

                    // Define a function to handle the response data
                    'function complete(id, o, args) {'.
                        'var id = id;'.               // Transaction ID
                        'var data = o.responseText;'. // Response data
                        'var args = args[1];'.
                        'if (data==1) return;'.
                        'alert("Voting error " + data);'.
                    '};'.

                    // Subscribe to event "io:complete", and pass an array
                    // as an argument to the event handler "complete", since
                    // "complete" is global.   At this point in the transaction
                    // lifecycle, success or failure is not yet known.
                    'Y.on("io:complete", complete, Y, ["lorem", "ipsum"]);'.

                    // Make request
                    'var request = Y.io(uri);'.
                '});'.

            '});'.

        '});';

        $avg = self::getAvgRating($type, $id);

        return
        js_embed($res).
        '<span id="'.$widget_id.'">'.round($avg, 1).'</span>';
    }

}

?>
