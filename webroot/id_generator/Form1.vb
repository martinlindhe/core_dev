'minor todo:exit om man trycker ESC

Public Class Form1

    'The personal identity number consists of 10 digits and a hyphen.
    'The first six correspond to the person's birthday, in YYMMDD form.
    'They are followed by a hyphen. The seventh through ninth are a serial number.
    'An odd number is assigned to men, an even number to women. In some counties,
    'such as Stockholm, they have started using 12 digit numbers to allow YYYYMMDD.
    'This format is also used on official Swedish ID-cards.

    'The tenth digit is a checksum which was introduced in 1967 when the system was computerized.
    'Originally, when the personal identity number was introduced, it had nine digits and
    'the seventh and eighth denoted the county (Swedish: län) in which the subject was born.
    'It was replaced with the current system in 1990.

    'People over the age of 100 replace the hyphen with a plus sign.

    'To calculate the checksum, multiply the individual digits in the identity number with 212121-212.
    'The resulting products (a two digit product, such as 16, would be converted to 1 + 6) are
    'added together. The checksum is 10 minus the ones place digit in this sum.

    Public Function calculateSum(ByVal persNr As String) As Integer

        Dim i, d1, d2, x1, x2, sum As Integer
        Dim res1 As String

        d2 = 2
        sum = 0

        'klipp ut en siffra i taget...
        For i = 1 To 9
            d1 = CInt(Mid(persNr, i, 1))
            res1 = CStr(d1 * d2)

            If Len(res1) = 2 Then
                x1 = CInt(Mid(res1, 1, 1))
                x2 = CInt(Mid(res1, 2, 1))
                res1 = x1 + x2
            End If
            sum = sum + res1

            If d2 = 2 Then d2 = 1 Else d2 = 2 'Växla mellan 212121-212
        Next

        'Substract the ones place digit from 10
        sum = 10 - CInt(Microsoft.VisualBasic.Right(CStr(sum), 1))
        If sum = 10 Then sum = 0

        Return (sum)

    End Function

    Public Function randomizeChecksum()

        'Randomizes the last 3 digits and creates a valid control digit

        Dim persNr, randNums As String
        Dim randGender, sum As Integer

        If validateInput(True) = False Then Return (0)

        persNr = Mid(txtYear.Text, 3, 2) & txtMonth.Text & txtDay.Text

        'Randomizes the 2 first of the control digits, between 00 and 99
        randNums = CStr(Int((99 * Rnd()) + 0))
        If Len(randNums) = 1 Then randNums = "0" & randNums

        'An odd number is assigned to men, an even number to women.
        randGender = Int(9 * Rnd() + 0) 'random between 0 and 9
        If randGender Mod 2 Then 'odd number
            If radioWoman.Checked Then randGender = randGender + 1 'woman have even numbers,add 1
        Else
            If radioMan.Checked Then randGender = randGender + 1 'men have odd numbers,add 1
        End If
        If randGender = 10 Then randGender = 0

        'piece it together
        persNr = persNr & randNums & CStr(randGender)

        'generate a valid checksum
        sum = calculateSum(persNr)

        'Update display of calculated control code
        txtControl.Text = randNums & CStr(randGender) & CStr(sum)

    End Function

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

        'kontrollera om 4 sista siffrorna är siffror
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


    Public Function validateInput(ByVal showMsg As Boolean) As Boolean

        Dim y, m, d As Integer

        txtYear.Text = Trim(txtYear.Text)
        txtMonth.Text = Trim(txtMonth.Text)
        txtDay.Text = Trim(txtDay.Text)
        txtControl.Text = Trim(txtControl.Text)

        If txtYear.Text = "" Then
            If showMsg Then MsgBox("Ange ett årtal", MsgBoxStyle.Critical)
            Return False
        End If

        If txtMonth.Text = "" Then
            If showMsg Then MsgBox("Ange en månad", MsgBoxStyle.Critical)
            Return False
        End If

        If txtDay.Text = "" Then
            If showMsg Then MsgBox("Ange en dag", MsgBoxStyle.Critical)
            Return False
        End If

        Try
            y = CInt(txtYear.Text)
        Catch e As Exception
            If showMsg Then MsgBox("Ogiltiga tecken i angivet år", MsgBoxStyle.Critical)
            Return False
        End Try

        Try
            m = CInt(txtMonth.Text)
        Catch e As Exception
            If showMsg Then MsgBox("Ogiltiga tecken i angiven månad", MsgBoxStyle.Critical)
            Return False
        End Try

        Try
            d = CInt(txtDay.Text)
        Catch e As Exception
            If showMsg Then MsgBox("Ogiltiga tecken i angiven dag", MsgBoxStyle.Critical)
            Return False
        End Try

        If y < 1900 Or y > Today.Year Then
            If showMsg Then MsgBox("Årtalet måste vara en siffra mellan 1900 och " & Today.Year, MsgBoxStyle.Critical)
            Return False
        End If

        If m = 0 Or m > 12 Then
            If showMsg Then MsgBox("Månaden måste vara en siffra mellan 01 och 12", MsgBoxStyle.Critical)
            Return False
        End If

        If d = 0 Then
            If showMsg Then MsgBox("Ogiltig dag", MsgBoxStyle.Critical)
            Return False
        End If
        'kontrollerar dagen utifrån max antal dagar i aktuell månad
        If d > System.DateTime.DaysInMonth(1900 + y, m) Then
            Dim birthDay As New System.DateTime(1900 + y, m, 1, 0, 0, 0)
            If showMsg Then MsgBox(Format(birthDay, "MMMM yyyy") & " har bara " & System.DateTime.DaysInMonth(1900 + y, m) & " dagar", MsgBoxStyle.Critical)
            Return False
        End If

        If showMsg Then
            'snyggar till siffrorna
            If m < 10 Then txtMonth.Text = "0" & CStr(m) Else txtMonth.Text = CStr(m)
            If d < 10 Then txtDay.Text = "0" & CStr(d) Else txtDay.Text = CStr(d)
        End If

        Return True

    End Function

    Private Sub btnRandom_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnRandom.Click

        randomizeChecksum()

    End Sub

    Private Sub Form1_KeyDown(ByVal sender As Object, ByVal e As System.Windows.Forms.KeyEventArgs) Handles Me.KeyDown

        'fixme:triggrar inte
        MsgBox("ss")
        If e.KeyCode = Keys.Escape Then
            MsgBox("esc")
        End If

    End Sub

    Private Sub Form1_Load(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles MyBase.Load

        ' Initialize the random-number generator.
        Randomize()
        validateSum(True)

    End Sub

    Private Sub txtYear_KeyDown(ByVal sender As Object, ByVal e As System.Windows.Forms.KeyEventArgs) Handles txtYear.KeyDown
        If e.KeyCode = Keys.Enter Then randomizeChecksum()
    End Sub

    Private Sub txtYear_TextChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles txtYear.TextChanged
        validateSum(True)
    End Sub

    Private Sub txtMonth_KeyDown(ByVal sender As Object, ByVal e As System.Windows.Forms.KeyEventArgs) Handles txtMonth.KeyDown
        If e.KeyCode = Keys.Enter Then randomizeChecksum()
    End Sub

    Private Sub txtMonth_TextChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles txtMonth.TextChanged
        validateSum(True)
    End Sub

    Private Sub txtDay_KeyDown(ByVal sender As Object, ByVal e As System.Windows.Forms.KeyEventArgs) Handles txtDay.KeyDown
        If e.KeyCode = Keys.Enter Then randomizeChecksum()
    End Sub

    Private Sub txtDay_TextChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles txtDay.TextChanged
        validateSum(True)
    End Sub

    Private Sub txtControl_TextChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles txtControl.TextChanged
        validateSum(True)
    End Sub

    Private Sub radioWoman_CheckedChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles radioWoman.CheckedChanged
        validateSum(False)
    End Sub

    Private Sub radioMan_CheckedChanged(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles radioMan.CheckedChanged
        validateSum(False)
    End Sub

    Private Sub btnInfo_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles btnInfo.Click

        Dim genderCode, i As Integer

        'visar msgbox med info om giltigt personnummer
        If validateInput(True) = False Then
            Return
        End If

        If validateSum(False) = False Then
            MsgBox("Personnummret är ogiltigt", MsgBoxStyle.Critical)
            Return
        End If

        Dim persNr, genderName As String
        persNr = Mid(txtYear.Text, 3, 2) & txtMonth.Text & txtDay.Text & txtControl.Text

        'verifiera checksumman
        Dim sum As Integer
        sum = calculateSum(Mid(persNr, 1, 9))
        If sum <> CInt(Mid(persNr, 10, 1)) Then
            MsgBox("Felaktig checksumma!", MsgBoxStyle.Critical)
            Return
        End If

        'räkna ut hur många år och dagar gammal personen är
        If CInt(txtYear.Text) > Today.Year Then
            MsgBox("Födelseåret är i framtiden", MsgBoxStyle.Critical)
            Return
        End If

        Dim birthDay As New System.DateTime(CInt(txtYear.Text), CInt(txtMonth.Text), CInt(txtDay.Text), 0, 0, 0)

        Dim diff As System.TimeSpan
        diff = Now.Date - birthDay

        'loopa igenom och räkna skottår
        Dim years, days As Integer
        days = diff.Days
        years = 0
        For i = CInt(txtYear.Text) + 1 To Today.Year

            If System.DateTime.IsLeapYear(i) Then
                If days >= 366 Then
                    days = days - 366
                    years = years + 1
                End If
            Else
                If days >= 365 Then
                    days = days - 365
                    years = years + 1
                End If
            End If

        Next

        Dim birthLeapYear As String
        If System.DateTime.IsLeapYear(CInt(txtYear.Text)) Then birthLeapYear = " (skottår!)" Else birthLeapYear = ""

        genderCode = CInt(Mid(txtControl.Text, 3, 1))
        If genderCode Mod 2 Then
            genderName = "man"
        Else
            genderName = "kvinna"
        End If

        'visar i ÅÅMMDD-XXXX format eftersom det är vanligast förekommande
        persNr = Mid(txtYear.Text, 3, 2) & txtMonth.Text & txtDay.Text & "-" & txtControl.Text
        MsgBox("Personnummer: " & persNr & Chr(13) & "Födelsedag: " & Format(birthDay, "dddd") & " den " & Format(birthDay, "d MMMM yyyy") & birthLeapYear & Chr(13) & "Ålder: " & years & " år och " & days & " dagar" & Chr(13) & "Personnummret tillhör en " & genderName, MsgBoxStyle.Information)


    End Sub

    Private Sub radioWoman_KeyDown(ByVal sender As Object, ByVal e As System.Windows.Forms.KeyEventArgs) Handles radioWoman.KeyDown
        If e.KeyCode = Keys.Enter Then randomizeChecksum()
    End Sub

    Private Sub radioMan_KeyDown(ByVal sender As Object, ByVal e As System.Windows.Forms.KeyEventArgs) Handles radioMan.KeyDown
        If e.KeyCode = Keys.Enter Then randomizeChecksum()
    End Sub
End Class
