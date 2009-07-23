<?php
/**
 * Functions that generates replies to certain types of questions
 */

require_once('/var/www/core_dev/core/functions_time.php');
require_once('/var/www/core_dev/core/locale_se.php');


/**
 * http://sv.wikipedia.org/wiki/Namnsdag (FIXME valid until 2016)
 */

//needed to correctly match some swedish names, like "Östen"
function strtolower_utf8($s)
{
	return mb_convert_case($s, MB_CASE_LOWER, "UTF-8");
}

function ucfirst_utf8($s)
{
	return mb_convert_case($s, MB_CASE_TITLE, "UTF-8");
}

/**
 * Kombinerar ihop orden som en uppräkning: "kalle, britta och sven"
 * @param $words array of words to make a sentence out of
 */
function respond_swedish_listing($words)
{
	$a = '';
	for ($i=0; $i<count($words); $i++) {
		if ($i+1 == count($words)) {
			$a .= ' och '.$words[$i];
		} else {
			if ($i) $a .= ', ';
			$a .= $words[$i];
		}
	}
	return $a;
}

/**
 * Besvarar frågan "vem har namnsdag idag?"
 */
function autoreply_svensk_namnsdag_idag()
{
	global $namnsdag_swe;

	$idx = date('md');

	$reason = '';
	switch ($idx) {
		case '0101': $reason = 'Nyårsdagen'; break;
		case '0202': $reason = 'Kyndelsmässodagen'; break;
		case '0229': $reason = 'Skottdagen'; break;
		case '0325': $reason = 'Marie bebådelsedag'; break;
		case '0624': $reason = 'Johannes döparens dag'; break;
		case '1101': $reason = 'Allhelgonadagen'; break;
		case '1225': $reason = 'Juldagen'; break;
	}
	$a = 'Idag på '.$reason.' är det ingen som har namnsdag.';

	if (!empty($namnsdag_swe[$idx])) {
		$names = explode(', ', $namnsdag_swe[$idx]);
		if (count($names) > 1) {
			$a = 'Idag har '.respond_swedish_listing($names).' namnsdag';
		} else {
			$a = 'Idag har '.$namnsdag_swe[$idx].' namnsdag';
		}
	}
	return $a;
}

/**
 * Besvarar frågan "vem har namnsdag DATUM?"
 */
function autoreply_svensk_namnsdag_datum($when)
{
	global $namnsdag_swe;

	$idx = '0404'; //XXX: hmm...

	$reason = '';
	switch ($idx) {
		case '0101': $reason = 'Nyårsdagen'; break;
		case '0202': $reason = 'Kyndelsmässodagen'; break;
		case '0229': $reason = 'Skottdagen'; break;
		case '0325': $reason = 'Marie bebådelsedag'; break;
		case '0624': $reason = 'Johannes döparens dag'; break;
		case '1101': $reason = 'Allhelgonadagen'; break;
		case '1225': $reason = 'Juldagen'; break;
	}
	$a = 'Den '.$idx.' är det '.$reason.' och ingen har namnsdag då.'; //XXX: snygga till strängen

	if (!empty($namnsdag_swe[$idx])) {
		$names = explode(', ', $namnsdag_swe[$idx]);
		if (count($names) > 1) {
			$a = respond_swedish_listing($names).' har namnsdag den '.$idx; //XXX : snygga till
		} else {
			$a = $namnsdag_swe[$idx].' har namnsdag den '.$idx; //XXX : snygga till
		}
	}
	return $a;
}

/**
 * Svara när ett svenskt namn har namnsdag
 * @return "NAMN har namnsdag den X:e MÅNAD (om Y dagar), tillsammans med NAMN2 och NAMN3"
 */
function autoreply_svensk_namnsdag($in_name)
{
	global $namnsdag_swe, $month_swe, $day_suff_swe;

	//FIXME testa: 1108: "Gustav Adolf" dubbelnamn

	//ersätt vanliga smeknamn med vanliga riktiga namn. XXX array replace
	switch (ucfirst_utf8($in_name)) {
	//smeknamn:
	case 'Bengan': $name = 'Bengt'; break;
	case 'Bosse':  $name = 'Bo'; break;
	case 'Cilla':  $name = 'Cecilia'; break;
	case 'Gabbe':  $name = 'Gabriel'; break;
	case 'Gun':    $name = 'Gunborg'; break;
	case 'Jocke':  $name = 'Joakim'; break;
	case 'Kenta':  $name = 'Kennet'; break;
	case 'Lasse':  $name = 'Lars'; break;
	case 'Tobbe':  $name = 'Tobias'; break;
	case 'Uffe':   $name = 'Ulf'; break;

	//udda stavning:
	case 'Angelica': $name = 'Angelika'; break;
	case 'Annica':   $name = 'Annika'; break;
	case 'Arthur':   $name = 'Artur'; break;
	case 'Carolina': $name = 'Karolina'; break;
	case 'Claes':    $name = 'Klas'; break;
	case 'Eric':     $name = 'Erik'; break;
	case 'Gustaf':   $name = 'Gustav'; break;
	case 'Håcan':    $name = 'Håkan'; break;
	case 'Jacob':    $name = 'Jakob'; break;
	case 'Kenneth':  $name = 'Kennet'; break;
	case 'Marcus':   $name = 'Markus'; break;
	case 'Martha':   $name = 'Marta'; break;
	case 'Michael':  $name = 'Mikael'; break;
	case 'Monica':   $name = 'Monika'; break;
	case 'Niclas':   $name = 'Niklas'; break;
	case 'Olov':     $name = 'Olof'; break;
	case 'Oscar':    $name = 'Oskar'; break;
	case 'Patrick':  $name = 'Patrik'; break;
	case 'Philip':   $name = 'Filip'; break;
	case 'Rebecca':  $name = 'Rebecka'; break;
	case 'Therese':  $name = 'Terese'; break;
	case 'Thomas':   $name = 'Tomas'; break;
	case 'Ulrica':   $name = 'Ulrika'; break;
	default:         $name = $in_name;
	}

	foreach ($namnsdag_swe as $key => $val) {

		$arr = explode(', ', $val);
		foreach ($arr as $check_name) {
			if (strtolower_utf8($check_name) == strtolower_utf8($name))
			{
				$mon = intval(substr($key, 0, 2));
				$day = intval(substr($key, 2, 2));
				$namnsdag = mktime(0, 0, 0, $mon, $day, date('Y'));

				$a = ucfirst_utf8($name);
				if (strtolower_utf8($name) != strtolower_utf8($in_name)) $a .= ' ('.ucfirst_utf8($in_name).')';
				$a .= ' har namnsdag den '.$day.':'.$day_suff_swe[$day].' '.$month_swe[$mon];

				$curr_mon = date('n');
				$curr_day = date('j');
				if ($curr_mon == $mon && $curr_day == $day) {
					$a .= ' (idag)';
				} else {
					$days_diff = date_diff(time(), $namnsdag, 1); //XXX: date_diff e buggig å cp

					if ($namnsdag > time()) {
						//namnsdagen har ännu inte varit
						$a .= ' (om '.$days_diff.')'; //XXX: översätt funktionen
					} else {
						//TODO: visa "för XXX dagar sedan"
						//den var tidigare i år
					}
				}

				if (count($arr) > 1) {
					$a .= ', tillsammas med ';
					$appended = false;
					foreach ($arr as $more_names) {
						if (strtolower_utf8($more_names) != strtolower_utf8($name)) {
							if ($appended) $a .= ' och '; //om det är fler än 2 namn på samma dag!
							$a .= $more_names;
							$appended = true;
						}
					}
				}
				return $a;
			}
		}
	}

	return false;
}
//XXX: verify list! http://sv.wikipedia.org/wiki/Lista_över_namnsdagar
//XXX: minska ner storleken på koden som fyller arrayen
$namnsdag_swe["0101"] = '';//Nyårsdagen
$namnsdag_swe["0102"] = 'Svea';
$namnsdag_swe["0103"] = 'Alfred, Alfrida';
$namnsdag_swe["0104"] = 'Rut';
$namnsdag_swe["0105"] = 'Hanna, Hannele';
$namnsdag_swe["0106"] = 'Kasper, Melker, Baltsar';
$namnsdag_swe["0107"] = 'August, Augusta';
$namnsdag_swe["0108"] = 'Erland';
$namnsdag_swe["0109"] = 'Gunnar, Gunder';
$namnsdag_swe["0110"] = 'Sigurd, Sigbritt';
$namnsdag_swe["0111"] = 'Jan, Jannike';
$namnsdag_swe["0112"] = 'Frideborg, Fridolf';
$namnsdag_swe["0113"] = 'Knut';
$namnsdag_swe["0114"] = 'Felix, Felicia';
$namnsdag_swe["0115"] = 'Laura, Lorentz';
$namnsdag_swe["0116"] = 'Hjalmar, Helmer';
$namnsdag_swe["0117"] = 'Anton, Tony';
$namnsdag_swe["0118"] = 'Hilda, Hildur';
$namnsdag_swe["0119"] = 'Henrik';
$namnsdag_swe["0120"] = 'Fabian, Sebastian';
$namnsdag_swe["0121"] = 'Agnes, Agneta';
$namnsdag_swe["0122"] = 'Vincent, Viktor';
$namnsdag_swe["0123"] = 'Frej, Freja';
$namnsdag_swe["0124"] = 'Erika';
$namnsdag_swe["0125"] = 'Paul, Pål';
$namnsdag_swe["0126"] = 'Bodil, Boel';
$namnsdag_swe["0127"] = 'Göte, Göta';
$namnsdag_swe["0128"] = 'Karl, Karla';
$namnsdag_swe["0129"] = 'Diana';
$namnsdag_swe["0130"] = 'Gunilla, Gunhild';
$namnsdag_swe["0131"] = 'Ivar, Joar';

$namnsdag_swe["0201"] = 'Max, Maximilian';
$namnsdag_swe["0202"] = '';//Kyndelsmässodagen
$namnsdag_swe["0203"] = 'Disa, Hjördis';
$namnsdag_swe["0204"] = 'Ansgar, Anselm';
$namnsdag_swe["0205"] = 'Agata, Agda';
$namnsdag_swe["0206"] = 'Dorotea, Doris';
$namnsdag_swe["0207"] = 'Rikard, Dick';
$namnsdag_swe["0208"] = 'Berta, Bert';
$namnsdag_swe["0209"] = 'Fanny, Franciska';
$namnsdag_swe["0210"] = 'Iris';
$namnsdag_swe["0211"] = 'Yngve, Inge';
$namnsdag_swe["0212"] = 'Evelina, Evy';
$namnsdag_swe["0213"] = 'Agne, Ove';
$namnsdag_swe["0214"] = 'Valentin';
$namnsdag_swe["0215"] = 'Sigfrid';
$namnsdag_swe["0216"] = 'Julia, Julius';
$namnsdag_swe["0217"] = 'Alexandra, Sandra';
$namnsdag_swe["0218"] = 'Frida, Fritiof';
$namnsdag_swe["0219"] = 'Gabriella, Ella';
$namnsdag_swe["0220"] = 'Vivianne';
$namnsdag_swe["0221"] = 'Hilding';
$namnsdag_swe["0222"] = 'Pia';
$namnsdag_swe["0223"] = 'Torsten, Torun';
$namnsdag_swe["0224"] = 'Mattias, Mats';
$namnsdag_swe["0225"] = 'Sigvard, Sivert';
$namnsdag_swe["0226"] = 'Torgny, Torkel';
$namnsdag_swe["0227"] = 'Lage';
$namnsdag_swe["0228"] = 'Maria';
$namnsdag_swe["0229"] = '';//Skottdagen

$namnsdag_swe["0301"] = 'Albin, Elvira';
$namnsdag_swe["0302"] = 'Ernst, Erna';
$namnsdag_swe["0303"] = 'Gunborg, Gunvor';
$namnsdag_swe["0304"] = 'Adrian, Adriana';
$namnsdag_swe["0305"] = 'Tora, Tove';
$namnsdag_swe["0306"] = 'Ebba, Ebbe';
$namnsdag_swe["0307"] = 'Camilla';
$namnsdag_swe["0308"] = 'Siv';
$namnsdag_swe["0309"] = 'Torbjörn, Torleif';
$namnsdag_swe["0310"] = 'Edla, Ada';
$namnsdag_swe["0311"] = 'Edvin, Egon';
$namnsdag_swe["0312"] = 'Viktoria';
$namnsdag_swe["0313"] = 'Greger';
$namnsdag_swe["0314"] = 'Matilda, Maud';
$namnsdag_swe["0315"] = 'Kristoffer, Christel';
$namnsdag_swe["0316"] = 'Herbert, Gilbert';
$namnsdag_swe["0317"] = 'Gertrud';
$namnsdag_swe["0318"] = 'Edvard, Edmund';
$namnsdag_swe["0319"] = 'Josef, Josefina';
$namnsdag_swe["0320"] = 'Joakim, Kim';
$namnsdag_swe["0321"] = 'Bengt';
$namnsdag_swe["0322"] = 'Kennet, Kent';
$namnsdag_swe["0323"] = 'Gerda, Gerd';
$namnsdag_swe["0324"] = 'Gabriel, Rafael';
$namnsdag_swe["0325"] = '';//Marie bebådelsedag
$namnsdag_swe["0326"] = 'Emanuel';
$namnsdag_swe["0327"] = 'Rudolf, Ralf';
$namnsdag_swe["0328"] = 'Malkolm, Morgan';
$namnsdag_swe["0329"] = 'Jonas, Jens';
$namnsdag_swe["0330"] = 'Holger, Holmfrid';
$namnsdag_swe["0331"] = 'Ester';

$namnsdag_swe["0401"] = 'Harald, Hervor';
$namnsdag_swe["0402"] = 'Gudmund, Ingemund';
$namnsdag_swe["0403"] = 'Ferdinand, Nanna';
$namnsdag_swe["0404"] = 'Marianne, Marlene';
$namnsdag_swe["0405"] = 'Irene, Irja';
$namnsdag_swe["0406"] = 'Vilhelm, Helmi';
$namnsdag_swe["0407"] = 'Irma, Irmelin';
$namnsdag_swe["0408"] = 'Nadja, Tanja';
$namnsdag_swe["0409"] = 'Otto, Ottilia';
$namnsdag_swe["0410"] = 'Ingvar, Ingvor';
$namnsdag_swe["0411"] = 'Ulf, Ylva';
$namnsdag_swe["0412"] = 'Liv';
$namnsdag_swe["0413"] = 'Artur, Douglas';
$namnsdag_swe["0414"] = 'Tiburtius';
$namnsdag_swe["0415"] = 'Olivia, Oliver';
$namnsdag_swe["0416"] = 'Patrik, Patricia';
$namnsdag_swe["0417"] = 'Elias, Elis';
$namnsdag_swe["0418"] = 'Valdemar, Volmar';
$namnsdag_swe["0419"] = 'Olaus, Ola';
$namnsdag_swe["0420"] = 'Amalia, Amelie, Emelie';
$namnsdag_swe["0421"] = 'Anneli, Annika';
$namnsdag_swe["0422"] = 'Allan, Glenn';
$namnsdag_swe["0423"] = 'Georg, Göran';
$namnsdag_swe["0424"] = 'Vega';
$namnsdag_swe["0425"] = 'Markus';
$namnsdag_swe["0426"] = 'Teresia, Terese';
$namnsdag_swe["0427"] = 'Engelbrekt';
$namnsdag_swe["0428"] = 'Ture, Tyra';
$namnsdag_swe["0429"] = 'Tyko';
$namnsdag_swe["0430"] = 'Mariana';//Valborgsmässoafton

$namnsdag_swe["0501"] = 'Valborg';//Första maj
$namnsdag_swe["0502"] = 'Filip, Filippa';
$namnsdag_swe["0503"] = 'John, Jane';
$namnsdag_swe["0504"] = 'Monika, Mona';
$namnsdag_swe["0505"] = 'Gotthard, Erhard';
$namnsdag_swe["0506"] = 'Marit, Rita';
$namnsdag_swe["0507"] = 'Carina, Carita';
$namnsdag_swe["0508"] = 'Åke';
$namnsdag_swe["0509"] = 'Reidar, Reidun';
$namnsdag_swe["0510"] = 'Esbjörn, Styrbjörn';
$namnsdag_swe["0511"] = 'Märta, Märit';
$namnsdag_swe["0512"] = 'Charlotta, Lotta';
$namnsdag_swe["0513"] = 'Linnea, Linn';
$namnsdag_swe["0514"] = 'Halvard, Halvar';
$namnsdag_swe["0515"] = 'Sofia, Sonja';
$namnsdag_swe["0516"] = 'Ronald, Ronny';
$namnsdag_swe["0517"] = 'Rebecka, Ruben';
$namnsdag_swe["0518"] = 'Erik';
$namnsdag_swe["0519"] = 'Maj, Majken';
$namnsdag_swe["0520"] = 'Karolina, Carola';
$namnsdag_swe["0521"] = 'Konstantin, Conny';
$namnsdag_swe["0522"] = 'Hemming, Henning';
$namnsdag_swe["0523"] = 'Desideria, Desirée';
$namnsdag_swe["0524"] = 'Ivan, Vanja';
$namnsdag_swe["0525"] = 'Urban';
$namnsdag_swe["0526"] = 'Vilhelmina, Vilma';
$namnsdag_swe["0527"] = 'Beda, Blenda';
$namnsdag_swe["0528"] = 'Ingeborg, Borghild';
$namnsdag_swe["0529"] = 'Yvonne, Jeanette';
$namnsdag_swe["0530"] = 'Vera, Veronika';
$namnsdag_swe["0531"] = 'Petronella, Pernilla';

$namnsdag_swe["0601"] = 'Gun, Gunnel';
$namnsdag_swe["0602"] = 'Rutger, Roger';
$namnsdag_swe["0603"] = 'Ingemar, Gudmar';
$namnsdag_swe["0604"] = 'Solbritt, Solveig';
$namnsdag_swe["0605"] = 'Bo';
$namnsdag_swe["0606"] = 'Gustav, Gösta';
$namnsdag_swe["0607"] = 'Robert, Robin';
$namnsdag_swe["0608"] = 'Eivor, Majvor';
$namnsdag_swe["0609"] = 'Börje, Birger';
$namnsdag_swe["0610"] = 'Svante, Boris';
$namnsdag_swe["0611"] = 'Bertil, Berthold';
$namnsdag_swe["0612"] = 'Eskil';
$namnsdag_swe["0613"] = 'Aina, Aino';
$namnsdag_swe["0614"] = 'Håkan, Hakon';
$namnsdag_swe["0615"] = 'Margit, Margot';
$namnsdag_swe["0616"] = 'Axel, Axelina';
$namnsdag_swe["0617"] = 'Torborg, Torvald';
$namnsdag_swe["0618"] = 'Björn, Bjarne';
$namnsdag_swe["0619"] = 'Germund, Görel';
$namnsdag_swe["0620"] = 'Linda';
$namnsdag_swe["0621"] = 'Alf, Alvar';
$namnsdag_swe["0622"] = 'Paulina, Paula';
$namnsdag_swe["0623"] = 'Adolf, Alice';
$namnsdag_swe["0624"] = '';//Johannes döparens dag
$namnsdag_swe["0625"] = 'David, Salomon';
$namnsdag_swe["0626"] = 'Rakel, Lea';
$namnsdag_swe["0627"] = 'Selma, Fingal';
$namnsdag_swe["0628"] = 'Leo';
$namnsdag_swe["0629"] = 'Peter, Petra';
$namnsdag_swe["0630"] = 'Elof, Leif';

$namnsdag_swe["0701"] = 'Aron, Mirjam';
$namnsdag_swe["0702"] = 'Rosa, Rosita';
$namnsdag_swe["0703"] = 'Aurora';
$namnsdag_swe["0704"] = 'Ulrika, Ulla';
$namnsdag_swe["0705"] = 'Laila, Ritva';
$namnsdag_swe["0706"] = 'Esaias, Jessika';
$namnsdag_swe["0707"] = 'Klas';
$namnsdag_swe["0708"] = 'Kjell';
$namnsdag_swe["0709"] = 'Jörgen, Örjan';
$namnsdag_swe["0710"] = 'André, Andrea';
$namnsdag_swe["0711"] = 'Eleonora, Ellinor';
$namnsdag_swe["0712"] = 'Herman, Hermine';
$namnsdag_swe["0713"] = 'Joel, Judit';
$namnsdag_swe["0714"] = 'Folke';
$namnsdag_swe["0715"] = 'Ragnhild, Ragnvald';
$namnsdag_swe["0716"] = 'Reinhold, Reine';
$namnsdag_swe["0717"] = 'Bruno';
$namnsdag_swe["0718"] = 'Fredrik, Fritz';
$namnsdag_swe["0719"] = 'Sara';
$namnsdag_swe["0720"] = 'Margareta, Greta';
$namnsdag_swe["0721"] = 'Johanna';
$namnsdag_swe["0722"] = 'Magdalena, Madeleine';
$namnsdag_swe["0723"] = 'Emma';
$namnsdag_swe["0724"] = 'Kristina, Kerstin';
$namnsdag_swe["0725"] = 'Jakob';
$namnsdag_swe["0726"] = 'Jesper';
$namnsdag_swe["0727"] = 'Marta';
$namnsdag_swe["0728"] = 'Botvid, Seved';
$namnsdag_swe["0729"] = 'Olof';
$namnsdag_swe["0730"] = 'Algot';
$namnsdag_swe["0731"] = 'Helena, Elin';

$namnsdag_swe["0801"] = 'Per';
$namnsdag_swe["0802"] = 'Karin, Kajsa';
$namnsdag_swe["0803"] = 'Tage';
$namnsdag_swe["0804"] = 'Arne, Arnold';
$namnsdag_swe["0805"] = 'Ulrik, Alrik';
$namnsdag_swe["0806"] = 'Alfons, Inez';
$namnsdag_swe["0807"] = 'Dennis, Denise';
$namnsdag_swe["0808"] = 'Silvia, Sylvia';
$namnsdag_swe["0809"] = 'Roland';
$namnsdag_swe["0810"] = 'Lars';
$namnsdag_swe["0811"] = 'Susanna';
$namnsdag_swe["0812"] = 'Klara';
$namnsdag_swe["0813"] = 'Kaj';
$namnsdag_swe["0814"] = 'Uno';
$namnsdag_swe["0815"] = 'Stella, Estelle';
$namnsdag_swe["0816"] = 'Brynolf';
$namnsdag_swe["0817"] = 'Verner, Valter';
$namnsdag_swe["0818"] = 'Ellen, Lena';
$namnsdag_swe["0819"] = 'Magnus, Måns';
$namnsdag_swe["0820"] = 'Bernhard, Bernt';
$namnsdag_swe["0821"] = 'Jon, Jonna';
$namnsdag_swe["0822"] = 'Henrietta, Henrika';
$namnsdag_swe["0823"] = 'Signe, Signhild';
$namnsdag_swe["0824"] = 'Bartolomeus';
$namnsdag_swe["0825"] = 'Lovisa, Louise';
$namnsdag_swe["0826"] = 'Östen';
$namnsdag_swe["0827"] = 'Rolf, Raoul';
$namnsdag_swe["0828"] = 'Gurli, Leila';
$namnsdag_swe["0829"] = 'Hans, Hampus';
$namnsdag_swe["0830"] = 'Albert, Albertina';
$namnsdag_swe["0831"] = 'Arvid, Vidar';

$namnsdag_swe["0901"] = 'Samuel';
$namnsdag_swe["0902"] = 'Justus, Justina';
$namnsdag_swe["0903"] = 'Alfhild, Alva';
$namnsdag_swe["0904"] = 'Gisela';
$namnsdag_swe["0905"] = 'Adela, Heidi';
$namnsdag_swe["0906"] = 'Lilian, Lilly';
$namnsdag_swe["0907"] = 'Regina, Roy';
$namnsdag_swe["0908"] = 'Alma, Hulda';
$namnsdag_swe["0909"] = 'Anita, Annette';
$namnsdag_swe["0910"] = 'Tord, Turid';
$namnsdag_swe["0911"] = 'Dagny, Helny';
$namnsdag_swe["0912"] = 'Åsa, Åslög';
$namnsdag_swe["0913"] = 'Sture';
$namnsdag_swe["0914"] = 'Ida';
$namnsdag_swe["0915"] = 'Sigrid, Siri';
$namnsdag_swe["0916"] = 'Dag, Daga';
$namnsdag_swe["0917"] = 'Hildegard, Magnhild';
$namnsdag_swe["0918"] = 'Orvar';
$namnsdag_swe["0919"] = 'Fredrika';
$namnsdag_swe["0920"] = 'Elise, Lisa';
$namnsdag_swe["0921"] = 'Matteus';
$namnsdag_swe["0922"] = 'Maurits, Moritz';
$namnsdag_swe["0923"] = 'Tekla, Tea';
$namnsdag_swe["0924"] = 'Gerhard, Gert';
$namnsdag_swe["0925"] = 'Tryggve';
$namnsdag_swe["0926"] = 'Enar, Einar';
$namnsdag_swe["0927"] = 'Dagmar, Rigmor';
$namnsdag_swe["0928"] = 'Lennart, Leonard';
$namnsdag_swe["0929"] = 'Mikael, Mikaela';
$namnsdag_swe["0930"] = 'Helge';

$namnsdag_swe["1001"] = 'Ragnar, Ragna';
$namnsdag_swe["1002"] = 'Ludvig, Love';
$namnsdag_swe["1003"] = 'Evald, Osvald';
$namnsdag_swe["1004"] = 'Frans, Frank';
$namnsdag_swe["1005"] = 'Bror';
$namnsdag_swe["1006"] = 'Jenny, Jennifer';
$namnsdag_swe["1007"] = 'Birgitta, Britta';
$namnsdag_swe["1008"] = 'Nils';
$namnsdag_swe["1009"] = 'Ingrid, Inger';
$namnsdag_swe["1010"] = 'Harry, Harriet';
$namnsdag_swe["1011"] = 'Erling, Jarl';
$namnsdag_swe["1012"] = 'Valfrid, Manfred';
$namnsdag_swe["1013"] = 'Berit, Birgit';
$namnsdag_swe["1014"] = 'Stellan';
$namnsdag_swe["1015"] = 'Hedvig, Hillevi';
$namnsdag_swe["1016"] = 'Finn';
$namnsdag_swe["1017"] = 'Antonia, Toini';
$namnsdag_swe["1018"] = 'Lukas';
$namnsdag_swe["1019"] = 'Tore, Tor';
$namnsdag_swe["1020"] = 'Sibylla';
$namnsdag_swe["1021"] = 'Ursula, Yrsa';
$namnsdag_swe["1022"] = 'Marika, Marita';
$namnsdag_swe["1023"] = 'Severin, Sören';
$namnsdag_swe["1024"] = 'Evert, Eilert';
$namnsdag_swe["1025"] = 'Inga, Ingalill';
$namnsdag_swe["1026"] = 'Amanda, Rasmus';
$namnsdag_swe["1027"] = 'Sabina';
$namnsdag_swe["1028"] = 'Simon, Simone';
$namnsdag_swe["1029"] = 'Viola';
$namnsdag_swe["1030"] = 'Elsa, Isabella';
$namnsdag_swe["1031"] = 'Edit, Edgar';

$namnsdag_swe["1101"] = '';//Allhelgonadagen
$namnsdag_swe["1102"] = 'Tobias';
$namnsdag_swe["1103"] = 'Hubert, Hugo';
$namnsdag_swe["1104"] = 'Sverker';
$namnsdag_swe["1105"] = 'Eugen, Eugenia';
$namnsdag_swe["1106"] = 'Gustav Adolf';
$namnsdag_swe["1107"] = 'Ingegerd, Ingela';
$namnsdag_swe["1108"] = 'Vendela';
$namnsdag_swe["1109"] = 'Teodor, Teodora';
$namnsdag_swe["1110"] = 'Martin, Martina';
$namnsdag_swe["1111"] = 'Mårten';
$namnsdag_swe["1112"] = 'Konrad, Kurt';
$namnsdag_swe["1113"] = 'Kristian, Krister';
$namnsdag_swe["1114"] = 'Emil, Emilia';
$namnsdag_swe["1115"] = 'Leopold';
$namnsdag_swe["1116"] = 'Vibeke, Viveka';
$namnsdag_swe["1117"] = 'Naemi, Naima';
$namnsdag_swe["1118"] = 'Lillemor, Moa';
$namnsdag_swe["1119"] = 'Elisabet, Lisbet';
$namnsdag_swe["1120"] = 'Pontus, Marina';
$namnsdag_swe["1121"] = 'Helga, Olga';
$namnsdag_swe["1122"] = 'Cecilia, Sissela';
$namnsdag_swe["1123"] = 'Klemens';
$namnsdag_swe["1124"] = 'Gudrun, Rune';
$namnsdag_swe["1125"] = 'Katarina, Katja';
$namnsdag_swe["1126"] = 'Linus';
$namnsdag_swe["1127"] = 'Astrid, Asta';
$namnsdag_swe["1128"] = 'Malte';
$namnsdag_swe["1129"] = 'Sune';
$namnsdag_swe["1130"] = 'Andreas, Anders';

$namnsdag_swe["1201"] = 'Oskar, Ossian';
$namnsdag_swe["1202"] = 'Beata, Beatrice';
$namnsdag_swe["1203"] = 'Lydia';
$namnsdag_swe["1204"] = 'Barbara, Barbro';
$namnsdag_swe["1205"] = 'Sven';
$namnsdag_swe["1206"] = 'Nikolaus, Niklas';
$namnsdag_swe["1207"] = 'Angela, Angelika';
$namnsdag_swe["1208"] = 'Virginia';
$namnsdag_swe["1209"] = 'Anna';
$namnsdag_swe["1210"] = 'Malin, Malena';
$namnsdag_swe["1211"] = 'Daniel, Daniela';
$namnsdag_swe["1212"] = 'Alexander, Alexis';
$namnsdag_swe["1213"] = 'Lucia';
$namnsdag_swe["1214"] = 'Sten, Sixten';
$namnsdag_swe["1215"] = 'Gottfrid';
$namnsdag_swe["1216"] = 'Assar';
$namnsdag_swe["1217"] = 'Stig';
$namnsdag_swe["1218"] = 'Abraham';
$namnsdag_swe["1219"] = 'Isak';
$namnsdag_swe["1220"] = 'Israel, Moses';
$namnsdag_swe["1221"] = 'Tomas';
$namnsdag_swe["1222"] = 'Natanael, Jonatan';
$namnsdag_swe["1223"] = 'Adam';
$namnsdag_swe["1224"] = 'Eva';//Julafton
$namnsdag_swe["1225"] = '';//Juldagen
$namnsdag_swe["1226"] = 'Stefan, Staffan';//Annandag jul
$namnsdag_swe["1227"] = 'Johannes, Johan';
$namnsdag_swe["1228"] = 'Benjamin';//Värnlösa barns dag
$namnsdag_swe["1229"] = 'Natalia, Natalie';
$namnsdag_swe["1230"] = 'Abel, Set';
$namnsdag_swe["1231"] = 'Sylvester';

/*
$a = autoreply_svensk_namnsdag('niclas');

*/

//$a = autoreply_svensk_namnsdag_idag();
$a = autoreply_svensk_namnsdag_datum('0202');

echo $a."\n";

?>
