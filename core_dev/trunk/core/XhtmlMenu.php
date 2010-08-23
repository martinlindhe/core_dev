<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('class.CoreBase.php');

class XhtmlMenu extends CoreBase
{
    private $items       = array();
    private $css_all     = '';
    private $css_current = '';

    public function add($title, $link)
    {
        $this->items[] = array('title'=>$title, 'link'=>$link);
    }

    function setCss($class, $current = '')
    {
        $this->css_all = $class;
        $this->css_current = $current;
    }

    public function render()
    {
        $cur = $_SERVER['REQUEST_URI'];
        $res = '<ul class="'.$this->css_all.'">';

        foreach ($this->items as $item)
        {
            $l = strlen($item['link']);
            if ($this->css_current && $item['link'] == $cur || ($l > 1 && substr($cur, 0, $l) == $item['link']))
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
