<?php
/**
 * Test script for functions_validate_ssn.php
 */

require_once('../core/functions_validate_ssn.php');

$male_ssn = '740524-5593'; //valid male ssn (randomized)
$female_ssn = '770911-8884'; //valid female ssn (randomized)

$chk = SsnValidateSwedish($male_ssn, SSN_GENDER_UNKNOWN);
if ($chk !== true) echo "FAIL 1\n";
else echo "PASS 1\n";

$chk = SsnValidateSwedish($male_ssn, SSN_GENDER_MALE);
if ($chk !== true) echo "FAIL 2\n";
else echo "PASS 2\n";

$chk = SsnValidateSwedish($male_ssn, SSN_GENDER_FEMALE);
if ($chk === true) echo "FAIL 3\n";
else echo "PASS 3\n";

$chk = SsnValidateSwedish($female_ssn, SSN_GENDER_MALE);
if ($chk === true) echo "FAIL 4\n";
else echo "PASS 4\n";

$chk = SsnValidateSwedish($female_ssn, SSN_GENDER_FEMALE);
if ($chk !== true) echo "FAIL 5\n";
else echo "PASS 5\n";

?>
