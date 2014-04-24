<?php
/**
 * @author Martin Lindhe, 2011-2014 <martin@ubique.se>
 */

///XXXX: make interface IXhtmlComponent instead?

namespace cd;

require_once('CoreBase.php');

abstract class XhtmlComponent extends CoreBase
{
    var $name;

    abstract function render();

    function __construct($s = '')
    {
        $this->name = $s;
    }

    function setName($s) { $this->name = $s; }
    function getName() { return $this->name; }
}

// standard html components:
require_once('XhtmlComponentA.php');
require_once('XhtmlComponentAudio.php');
require_once('XhtmlComponentButton.php');
require_once('XhtmlComponentCheckbox.php');
require_once('XhtmlComponentDropdown.php');
require_once('XhtmlComponentFile.php');
require_once('XhtmlComponentHidden.php');
require_once('XhtmlComponentIframe.php');
require_once('XhtmlComponentImage.php');
require_once('XhtmlComponentInput.php');
require_once('XhtmlComponentListbox.php');
require_once('XhtmlComponentOpenSearch.php');
require_once('XhtmlComponentPassword.php');
require_once('XhtmlComponentRadio.php');
require_once('XhtmlComponentSubmit.php');
require_once('XhtmlComponentText.php');
require_once('XhtmlComponentTextarea.php');


// YUI-javascript enhanced components:
require_once('YuiAutocomplete.php');
require_once('YuiDate.php');
require_once('YuiDateInterval.php');
require_once('YuiDatePopup.php');
require_once('YuiRichedit.php');

// other enhanced components:
require_once('Recaptcha.php');
