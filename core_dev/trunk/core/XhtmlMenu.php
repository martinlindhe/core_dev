<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2012 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('CoreBase.php');
require_once('network.php');   // for get_protocol()

class XhtmlMenu extends CoreBase
{
    private $items       = array();
    private $css_all     = '';
    private $css_current = '';

    public function add($title, $link, $style = '')
    {
        $this->items[] = array('title'=>$title, 'link'=>relurl($link), 'style'=>$style);
    }

    public function spacer()
    {
        $this->items[] = array();
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
            if (!$item) {
                $res .= '<li>&nbsp;</li>';
                continue;
            }

            if ($this->css_current && $item['link'] == $cur)
                $res .= '<li class="'.$this->css_current.'"';
            else
                $res .= '<li';

            $res .= ($item['style'] ? ' style="'.$item['style'].'"' : '').'>';

            if ($item['link'])
                $res .= '<a href="'.$item['link'].'">'.htmlentities($item['title'], ENT_QUOTES, 'UTF-8').'</a>';
            else
                $res .= $item['title'];

            $res .= '</li>';
        }
        $res .= '</ul>';
        return $res;
    }
}

?>
