<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2011
 */

class VcardAddress
{
    var $first_name;
    var $last_name;
    var $street;     ///< street name + street number + entrance
    var $zipcode;
    var $city;
    var $country;    ///< 3-letter country code
    var $gender;     ///< "M", or "F", or blank
    var $birthdate;  ///< in "YYYY-MM-DD" form
    var $cellphone;
    var $homephone;
}

?>
