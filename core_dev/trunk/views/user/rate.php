<?php
/*
 * Frontend wrapper for YUI gallery-ratings
 *
 * https://github.com/petey/yui3-gallery
 * http://yuilibrary.com/gallery/show/ratings
 * http://www.yuiblog.com/blog/2010/04/28/gallery-ratings/
 */

require_once('Rating.php');

//FIXME: ratings:ratingChange js event  never triggers!

//XXX: make it read-only after you clicked it once
//XXX later: ability to have different scales of the rating, like 1-5, 0-5, 1-10 etc? needs a better js widget


switch ($this->view) {
case 'handle':
    // handle user rating
    // owner = item type
    // child = item id

    $type = $this->owner;
    $id = $this->child;

    $header->includeJs('http://yui.yahooapis.com/3.4.1/build/yui/yui-min.js');

    $header->includeCss( $page->getRelativeCoreDevUrl().'js/ext/gallery-ratings/assets/gallery-ratings-core.css');

    $widget_id = 'rate_'.mt_rand();

    $js =
    'YUI({'.
        'modules:{'.
        '"gallery-ratings":{'.
            //'fullpath:"'.relurl( $page->getRelativeCoreDevUrl() ).'js/ext/gallery-ratings/gallery-ratings-min.js",'.
            'fullpath:"'.relurl( $page->getRelativeCoreDevUrl() ).'js/ext/gallery-ratings/gallery-ratings.js",'.
            'requires:["base","widget"]'.
        '}'.
    '},'.

    '}).use("gallery-ratings", "event", "io-base", function(Y) {'.
        'var ratings = new Y.Ratings({'.
//            'inline: true,'.
            'skin: "small",'.
            'srcNode: "#'.$widget_id.'"'.
        '});'.

/*
        'Y.on("ratings:ratingChange",function(e){'.
            'alert("www");'.
            'var id = e.target.get("contentBox").get("id");'.
            'Y.log(id+" New Value: "+e.newVal+" was: "+e.prevVal);'.
        '});'.
*/

        'Y.on("ratings:ratingChange",function(e){'.
            'var uri = "u/rate/vote/'.$type.'/'.$id.'/" + e.newVal;'.
            'alert(uri);'.

            // Subscribe to event "io:complete", and pass an array
            // as an argument to the event handler "complete", since
            // "complete" is global.   At this point in the transaction
            // lifecycle, success or failure is not yet known.
            'Y.on("io:complete", function(id, o, args){'.
                'var id = id;'.               // Transaction ID
                'var data = o.responseText;'. // Response data
                'var args = args[1];'.
                'if (data==1) return;'.
                'alert("Voting error " + data);'.
            '});'.

            // Make request
            'var request = Y.io(uri);'.
        '});'.

    '});';

    $avg = Rating::getAverage($type, $id);

    echo
    js_embed($js).
    '<span id="'.$widget_id.'">'.round($avg, 1).'</span>';
    break;

case 'vote':
    // owner = type
    // child = item id
    // child2 = option id

    echo 'WOWOWsls';
        if (!empty($_GET['rate_vote']) && !empty($_GET['opt']))
        {
            if (!$session->id || !is_numeric($_GET['opt']))
                die('XXX');

            $page->disableDesign();

            self::addPollVote($type, $_GET['rate_vote'], $_GET['opt']);

            ob_clean(); // XXX hack.. removes previous output
            die('1');
        }
    break;

}

?>
