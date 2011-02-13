<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('CoreBase.php');
require_once('network.php'); //For get_protocol()

class XhtmlMenu extends CoreBase
{
    private $items       = array();
    private $css_all     = '';
    private $css_current = '';

    public function add($title, $link)
    {
        if (!get_protocol($link) && substr($link, 0, 1) != '/') {
            $page = XmlDocumentHandler::getInstance();
            $link = $page->getRelativeUrl().$link;
        }
        $this->items[] = array('title'=>$title, 'link'=>$link);
    }

    function setCss($class, $current = '')
    {
        $this->css_all = $class;
        $this->css_current = $current;
    }

    public function render()
    {
        $cur = urldecode($_SERVER['REQUEST_URI']);

        $res = '<ul class="'.$this->css_all.'">';

        foreach ($this->items as $item)
        {
            if ($this->css_current && $item['link'] == $cur)
                $res .= '<li class="'.$this->css_current.'">';
            else
                $res .= '<li>';

            if ($item['link']) $res .= '<a href="'.$item['link'].'">'.$item['title'].'</a>';
            else $res .= $item['title'];
            $res .= '</li>';
        }
        $res .= '</ul>';
        return $res;
    }
}

?>
