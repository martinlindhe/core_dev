<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2013 <martin@startwars.org>
 */

namespace cd;

class Css3FontFaceSrc
{
    var $format;
    var $url;
}

class Css3FontFace
{
    var $font_family;
    var $font_stretch = 'normal';
    var $font_style   = 'normal';
    var $font_weight  = 'normal';
    var $sources      = array();

    public function addSource($format, $url)
    {
        if (!in_array($format, array('woff', 'truetype')))
            throw new \Exception ('unknown format: '.$format);

        $src = new Css3FontFaceSrc();
        $src->format = $format;
        $src->url = $url;
        $this->sources[] = $src;
    }

    public function render()
    {
        $sources = array();

        foreach ($this->sources as $src)
            $sources[] = 'url('.$src->url.') format("'.$src->format.'")';

        return
        '@font-face'.
        '{'.
            'font-family:"'.$this->font_family.'";'.
            ($this->font_stretch != 'normal' ? 'font-stretch:'.$this->font_stretch.';' : '').
            ($this->font_style != 'normal' ? 'font-style:'.$this->font_style.';' : '').
            ($this->font_weight != 'normal' ? 'font-weight:'.$this->font_weight.';' : '').
            'src:'.implode(',', $sources).';'.
        '}';
    }

}
