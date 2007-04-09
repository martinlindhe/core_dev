<?
	function calculateSum($_persNr)
	{
		$d2 = 2;
		$sum = 0;

		//klipp ut en siffra i taget...
		for ($i=0; $i<=8; $i++) {
			$d1 = intval(substr($_persNr, $i, 1));
			//echo 'd1 = '.$d1.', d2 = '. $d2. ' ... res1 = '. ($d1 * $d2).'<br/>';
			$res1 = ($d1 * $d2);

			if ($res1 >= 10) {
				$x1 = intval(substr($res1, 0, 1));
				$x2 = intval(substr($res1, 1, 1));
				$res1 = $x1 + $x2;
			}
			$sum += $res1;

			if ($d2 == 2) {
				$d2 = 1;
			} else {
				$d2 = 2; //Växla mellan 212121-212
			}
		}

		//Substract the ones place digit from 10
		$sum = 10 - intval(substr($sum, -1, 1));
		If ($sum == 10) $sum = 0;

		return $sum;
	}

	//Randomizes the last 3 digits and creates a valid control digit
	function generateLastDigits($_year, $_month, $_day, $_gender)
	{
    $persNr = substr($_year, -2) . $_month . $_day;
    if (strlen($persNr) != 6) die;

    //Randomizes the 2 first of the control digits, between 00 and 99
    $randNums = mt_rand(0, 99);
    if ($randNums < 10) $randNums = '0'.$randNums;

		//An odd number is assigned to men, an even number to women.
		$randGender = mt_rand(0, 9);
    if ($randGender % 2) { //odd number
    	if ($_gender == 2) $randGender++;		//woman have even numbers,add 1
		} else {
			if ($_gender == 1) $randGender++;		//men have odd numbers,add 1
		}
		if ($randGender > 9) $randGender = 0;

		//generate a valid checksum
		$sum = calculateSum($persNr . $randNums . $randGender);

		return $randNums . $randGender . $sum;
	}

	/*
    Public Function validateSum(ByVal changeRadios As Boolean) As Boolean

        Dim persNr, y, m, d As String
        Dim sum, controlCheck, genderCheck As Integer

        If validateInput(False) = False Then
            lblValid.Text = "Ogiltigt"
            Return False
        End If

        If Len(txtControl.Text) <> 4 Then
            lblValid.Text = "Ogiltigt"
            Return False
        End If

        //kontrollera om 4 sista siffrorna är siffror
        Try
            Dim tmp As Integer
            tmp = CInt(txtControl.Text)
        Catch e As Exception
            lblValid.Text = "Ogiltigt"
            Return False
        End Try

        genderCheck = CInt(Mid(txtControl.Text, 3, 1))
        controlCheck = CInt(Mid(txtControl.Text, 4, 1))

        If changeRadios Then
            If genderCheck Mod 2 Then 'man
                radioWoman.Checked = False
                radioMan.Checked = True
            Else 'kvinna
                radioWoman.Checked = True
                radioMan.Checked = False
            End If
        End If

        y = txtYear.Text
        m = txtMonth.Text
        d = txtDay.Text
        If Len(m) = 1 Then m = "0" & m
        If Len(d) = 1 Then d = "0" & d

        persNr = Mid(y, 3, 2) & m & d & Mid(txtControl.Text, 1, 3)

        sum = calculateSum(persNr)
        If controlCheck = sum Then

            If genderCheck Mod 2 Then
                If radioMan.Checked Then
                    lblValid.Text = "Giltigt, man"
                Else
                    lblValid.Text = "Ogiltigt, fel kön"
                End If
            Else
                If radioWoman.Checked Then
                    lblValid.Text = "Giltigt, kvinna"
                Else
                    lblValid.Text = "Ogiltigt, fel kön"
                End If
            End If

        Else
            lblValid.Text = "Ogiltigt (sista=" & sum & ")"
        End If

        Return True

    End Function
	*/

?>