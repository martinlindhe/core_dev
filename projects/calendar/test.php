<?

/**
 * Genererates Swedish "work free days" for given year
 * Only lists "red days", which is free from work by swedish law
 * Sweden uses the Gregorian calendar
 *
 * Calculations verified 2008.05.13
 *
 * Ignores "Allmänt lediga dagar i Sverige" (påskafton, midsommarafton, julafton, nyårsafton): 
 * http://sv.wikipedia.org/wiki/Helgdag#Allm.C3.A4nna_helgdagar_i_Sverige
 */
function generateHelgdagar($year)
{
	if (!is_numeric($year)) return false;

	//Nyårsdagen (fast): 1:a januari
	$ts = mktime(0, 0, 0, 1, 1, $year);	//1:a januari
	$res['nyårsdagen'] = date('Y-m-d', $ts);

	//Trettondag jul (fast): 6:e januari
	$ts = mktime(0, 0, 0, 1, 6, $year);	//6:e januari
	$res['trettondagen'] = date('Y-m-d', $ts);

	//Första maj (fast): 1:a maj
	$ts = mktime(0, 0, 0, 5, 1, $year);	//1:a maj
	$res['första_maj'] = date('Y-m-d', $ts);

	//Sveriges nationaldag (fast): 6:e juni
	$ts = mktime(0, 0, 0, 6, 6, $year);	//6:e juni
	$res['sveriges_nationaldag'] = date('Y-m-d', $ts);

	//Juldagen (fast): 25:e december
	$ts = mktime(0, 0, 0, 12, 25, $year);	//25:e dec
	$res['juldagen'] = date('Y-m-d', $ts);

	//Annandag jul (fast): 26:e december
	$ts = mktime(0, 0, 0, 12, 26, $year);	//26:e dec
	$res['annandag_jul'] = date('Y-m-d', $ts);

	//Påskdagen (rörlig): söndagen närmast efter den fullmåne som infaller på eller närmast efter den 21 mars
	$easter_ofs = easter_days($year, CAL_GREGORIAN);	//number of days after March 21 on which Easter falls
	$ts = mktime(0, 0, 0, 3, 21 + $easter_ofs, $year);
	$res['påskdagen'] = date('Y-m-d', $ts);

	//Långfredagen (rörlig): fredagen närmast före påskdagen
	$ts = mktime(0, 0, 0, 3, 21 + $easter_ofs - 2, $year);
	$res['långfredagen'] = date('Y-m-d', $ts);

	//Annandag påsk (rörlig): dagen efter påskdagen. alltid måndag
	$ts = mktime(0, 0, 0, 3, 21 + $easter_ofs + 1, $year);
	$res['annandag_påsk'] = date('Y-m-d', $ts);

	//Kristi himmelfärdsdag (rörlig): sjätte torsdagen efter påskdagen (39 dagar efter)
	$ts = mktime(0, 0, 0, 3, 21 + $easter_ofs + 39, $year);
	$res['kristi_himmelfärd'] = date('Y-m-d', $ts);

	//Pingsdagen (rörlig): sjunde söndagen efter påskdagen (49 dagar efter)
	$ts = mktime(0, 0, 0, 3, 21 + $easter_ofs + 49, $year);
	$res['pingstdagen'] = date('Y-m-d', $ts);

	//Midsommardagen (rörlig): den lördag som infaller under tiden den 20-26 jun
	$ts = mktime(0, 0, 0, 6, 20, $year);	//20:e juni
	$dow = date('N', $ts);	//day of week. 1=monday,7=sunday
	$ts = mktime(0, 0, 0, 6, 20-$dow+6, $year);
	$res['midsommardagen'] = date('Y-m-d', $ts);

	//Alla helgons dag (rörlig): den lördag som infaller under tiden den 31 oktober-6 november
	$ts = mktime(0, 0, 0, 10, 31, $year);	//31:a okt
	$dow = date('N', $ts);	//day of week. 1=monday,7=sunday
	$ts = mktime(0, 0, 0, 10, 31-$dow+6, $year);
	$res['alla_helgons_dag'] = date('Y-m-d', $ts);

	return $res;
}

for ($i= 2007; $i <= 2015; $i++) {
	$x = generateHelgdagar($i);
	echo $x['pingstdagen']."\n";
}

?>
