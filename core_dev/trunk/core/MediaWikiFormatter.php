<?php
/**
 * $Id$
 *
 * Renders MediaWiki markup as HTML
 *
 * MediaWiki formatting code based on
 * http://johbuc6.coconia.net/doku.php/mediawiki2html_machine/code
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: wip... used by MediaWikiClient

class MediaWikiFormatter
{
    protected $url;          ///< needed to expand full url:s

    /** @return Base URL to the MediaWiki installation */
    public function getBaseUrl()
    {
        $url = new Url($this->url);

        if (substr($url->getPath(), 0, 6) != '/wiki/')
            throw new Exception ('not a mediawiki url: '.$this->url);

        $url->setPath('/wiki/');
        return $url->render();
    }

    /** Attempt to format MediaWiki markup code */
    public function format($html, $page_url)
    {
        $this->url = $page_url;

        $html = str_replace('&ndash;', '-', $html);
        $html = str_replace('&quot;', '"', $html);
        $html = preg_replace('/\&amp;(nbsp);/', '&${1};', $html);

        //formatting
        // bold
        $html = preg_replace('/\'\'\'([^\n\']+)\'\'\'/', '<strong>${1}</strong>', $html);
        // emphasized
        $html = preg_replace('/\'\'([^\'\n]+)\'\'?/', '<em>${1}</em>', $html);
        //interwiki links
        $html = preg_replace_callback('/\[\[([^\|\n\]:]+)[\|]([^\]]+)\]\]/', array($this, 'helper_interwikilinks'), $html);
        // without text
        $html = preg_replace_callback('/\[\[([^\|\n\]:]+)\]\]/', array($this, 'helper_interwikilinks'), $html);

        //$html = preg_replace('/{{([^}]+)+}}/', 'Interwiki: ${1}+${2}+${3}', $html);
        $html = preg_replace('/{{([^\|\n\}]+)([\|]?([^\}]+))+\}\}/', 'Interwiki: ${1} &raquo; ${3}', $html);
        // Template
        //$html = preg_replace('/{{([^}]*)}}/', ' ', $html);
        // categories
        //$html = preg_replace('/\[\[([^\|\n\]]+)([\|]([^\]]+))?\]\]/', '', $html);
        $html = preg_replace('/\[\[([^\|\n\]]{2})([\:]([^\]]+))?\]\]/', 'Translation: ${1} &raquo; ${3}', $html);
        $html = preg_replace('/\[\[([^\|\n\]]+)([\:]([^\]]+))?\]\]/', 'Category: ${1} - ${2}', $html);
        // image
        $html = preg_replace('/\[\[([^\|\n\]]+)([\|]([^\]]+))+\]\]/', 'Image: ${0}+${1}+${2}+${3}', $html);

        //links
        //$html = preg_replace('/\[([^\[\]\|\n\': ]+)\]/', '<a href="${1}">${1}</a>', $html);
        $html = preg_replace_callback('/\[([^\[\]\|\n\': ]+)\]/', array($this, 'helper_externlinks'), $html);
        // with text
        //$html = preg_replace('/\[([^\[\]\|\n\' ]+)[\| ]([^\]\']+)\]/', '<a href="${1}">${2}</a>', $html);
        $html = preg_replace_callback('/\[([^\[\]\|\n\' ]+)[\| ]([^\]\']+)\]/', array($this, 'helper_externlinks'), $html);

        // allowed tags
        $html = preg_replace('/&lt;(\/?)(small|sup|sub|u)&gt;/', '<${1}${2}>', $html);

        $html = preg_replace('/\n*&lt;br *\/?&gt;\n*/', "\n", $html);
        $html = preg_replace('/&lt;(\/?)(math|pre|code|nowiki)&gt;/', '<${1}pre>', $html);
        $html = preg_replace('/&lt;!--/', '<!--', $html);
        $html = preg_replace('/--&gt;/', ' -->', $html);

        // headings
        for ($i=7; $i>0; $i--)
            $html = preg_replace(
                '/\n+[=]{'.$i.'}([^=]+)[=]{'.$i.'}\n*/',
                '<h'.$i.'>${1}</h'.$i.'>',
                $html
            );

        //lists
        $html = preg_replace(
            '/(\n[ ]*[^#* ][^\n]*)\n(([ ]*[*]([^\n]*)\n)+)/',
            '${1}<ul>'."\n".'${2}'.'</ul>'."\n",
            $html
        );
        $html = preg_replace(
            '/(\n[ ]*[^#* ][^\n]*)\n(([ ]*[#]([^\n]*)\n)+)/',
            '${1}<ol>'."\n".'${2}'.'</ol>'."\n",
            $html
        );
        $html = preg_replace('/\n[ ]*[\*#]+([^\n]*)/', '<li>${1}</li>', $html);

        $html = preg_replace('/----/', '<hr />', $html);

        // line breaks
        $html = preg_replace('/[\n\r]{4}/', '<br/><br/>', $html);
        $html = preg_replace('/[\n\r]{2}/', '<br/>', $html);

        $html = preg_replace('/[>]<br\/>[<]/', '><', $html);

        return $html;
    }

    protected function helper_externlinks($matches)
    {
        $target = $matches[1];
        $text = empty($matches[2])?$matches[1]:$matches[2];
        return '<a href="'.$target.'" target="_blank">'.$text.'</a>';
    }

    protected function helper_interwikilinks($matches)
    {
        $target = $matches[1];
        $text = empty($matches[2])?$matches[1]:$matches[2];
        return '<a  href="'.$this->getBaseUrl().$target.'" target="_blank">'.$text.'</a>';
    }
}

?>
