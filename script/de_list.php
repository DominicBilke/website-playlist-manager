<?php

	/*
		Generierung der deutschen Liste für den editaccount.php
	*/
session_start();

function number_format2($time, $c, $d1, $d2) {
return sprintf('%02d:%02d', (int) $time, fmod($time, 1) * 60);
}

// Vergleich der Zeiten, d.h. extrahieren der zeiten aus Datenbank, zusammenrechnen
function matching_time($stamp_from, $stamp_to, $time_from, $time_to, $days) {
 $stamp_from_w = date('W', $stamp_from);
 $stamp_from_d = date('w', $stamp_from);
 $stamp_from_h = date('H', $stamp_from);
 $stamp_from_m = date('i', $stamp_from);
 $stamp_to_d = date('w', $stamp_to);
 $stamp_to_w = date('W', $stamp_to);
 $stamp_to_h = date('H', $stamp_to);
 $stamp_to_m = date('i', $stamp_to);
 $arr_from = explode(':', $time_from);
 $from_h = (isset($arr_from[0]) && $arr_from[0] ? $arr_from[0] : 0);
 $from_m = (isset($arr_from[1]) && $arr_from[1] ? $arr_from[1] : 0);
 $arr_to = explode(':', $time_to);
 $to_h = (isset($arr_to[0]) && $arr_to[0] ? $arr_to[0] : 0);
 $to_m = (isset($arr_to[1]) && $arr_to[1] ? $arr_to[1] : 0);


$i = 0;
$result = [];
for($stamp_week=$stamp_from_w; $stamp_week<=$stamp_to_w; $stamp_week++) 
for($stamp_day=($stamp_week == $stamp_from_w ? $stamp_from_d : 0); $stamp_day<=($stamp_week == $stamp_to_w ? $stamp_to_d : 6); $stamp_day++) 
 {

$stamp_from_current = strtotime("+".$i." days", $stamp_from);

$stamp_day = strval($stamp_day);
// innerhalb der Spielzeit
$first=FALSE;
 if(strpos($days, $stamp_day) !== FALSE && $stamp_from_d == $stamp_to_d && ($stamp_from_h > $from_h || ($stamp_from_h >= $from_h && $stamp_from_m >= $from_m)) && ($stamp_to_h < $to_h || ($stamp_to_h <= $to_h && $stamp_to_m <= $to_m))) {
	array_push($result,[date('d.m.Y', $stamp_to), ($stamp_to_h-$stamp_from_h+(($stamp_to_m-$stamp_from_m) / 60))]);
	$first=TRUE; 
}

//Anfang in Spielzeit
 else if(!$first && strpos($days, $stamp_day) !== FALSE && $stamp_from_d == $stamp_day && ($stamp_from_h > $from_h || ($stamp_from_h >= $from_h && $stamp_from_m >= $from_m)) && ( $stamp_from_h < $to_h || ($stamp_from_h <= $to_h && $stamp_from_m <= $to_m))) {
	array_push($result,[date('d.m.Y', $stamp_from), ($to_h-$stamp_from_h+(($to_m-$stamp_from_m) / 60))]);
}

// Ende in Spielzeit
  else if(!$first && strpos($days, $stamp_day) !== FALSE && $stamp_day == $stamp_to_d && ($stamp_to_h > $from_h || ($stamp_to_h >= $from_h && $stamp_to_m >= $from_m)) && ($stamp_to_h < $to_h || ($stamp_to_h <= $to_h && $stamp_to_m <= $to_m))) {
	array_push($result,[date('d.m.Y', $stamp_to), ($stamp_to_h-$from_h+(($stamp_to_m-$from_m) / 60))]);
}

// Anfang vor der Spielzeit, Ende nach der Spielzeit
  else if(strpos($days, $stamp_day) !== FALSE && $stamp_from_d == $stamp_to_d && ($stamp_from_h < $from_h || ($stamp_from_h < $from_h && $stamp_from_m < $from_m)) && ($stamp_to_h > $to_h || ($stamp_to_h > $to_h && $stamp_to_m > $to_m))) {
	array_push($result,[date('d.m.Y', $stamp_from_current), ($to_h-$from_h+(($to_m-$from_m) / 60))]); 
}

// Vor dem Tag, bis nach dem Tag
  else if(strpos($days, $stamp_day) !== FALSE && $stamp_from < $stamp_from_current && $stamp_from_current < $stamp_to) {
	array_push($result,[date('d.m.Y', $stamp_from_current), ($to_h-$from_h+(($to_m-$from_m) / 60))]); 
}

$i++;
}
 return $result;
}

// implode mit keys ...
function array_implode_with_keys($array){
    $return = '';
    if (count($array) > 0){
    foreach ($array as $key=>$value){
    $return .= $key . '||||' . $value . '----';
    }
    $return = substr($return,0,strlen($return) - 4);
    }
    return $return;
}

// explode mit keys ...
function array_explode_with_keys($string){
    $return = array();
    $pieces = explode('----',$string);
    foreach($pieces as $piece){
        $keyval = explode('||||',$piece);
        if (count($keyval) > 1){
        $return[$keyval[0]] = $keyval[1];
        } else {
        $return[$keyval[0]] = '';
        }
    }
    return $return;
}

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";


// DATENBANK 

$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$sql = "SELECT playing_time FROM users WHERE id=".$_GET['id'];
foreach ($pdo->query($sql) as $row) {
	$playing_time = array_explode_with_keys($row['playing_time']);
}

$playing_array1 = [];
$result = array();
foreach($playing_time as $d=>$t) {
	if(strpos($d, 'day')===FALSE) {
		$stampFrom = $d;
		$stampTo = $t;
		if(!isset($playing_time['daytime_from_'.$stampFrom]) || !isset($playing_time['daytime_from_'.$stampFrom]) || !isset($playing_time['daytime_to_'.$stampFrom]) || !isset($playing_time['days_'.$stampFrom])) continue;
		$daytime_from = $playing_time['daytime_from_'.$stampFrom];
		$daytime_to = $playing_time['daytime_to_'.$stampFrom];
		$days = $playing_time['days_'.$stampFrom];
		$result = matching_time($stampFrom, $stampTo, $daytime_from, $daytime_to, $days);
		foreach($result as $res) {
			$day = $res[0];
			$tim = $res[1];
			$playing_array1[$day][] = $tim;
		}

}
}
// playing_array auflösen
$playing_array = [];
foreach($playing_array1 as $d=>$t) {
	$playing_array[$d] = array_sum(array_unique($t));
	if((int) $playing_array[$d] >= 24) $playing_array[$d] = 24;
}

// kleiner als 5 min und größer als 90 Tage, dann löschen
foreach($playing_array as $d=>$t) {
	if($playing_array[$d] < 0.084) unset($playing_array[$d]);

	if(strtotime($d) < strtotime('-90 days')) unset($playing_array[$d]); 
	if(strtotime($d) > strtotime('now')) unset($playing_array[$d]); 
}
// JETZT KOMMT LISTE IN HTML
?>
<!DOCTYPE HTML>
<!--
	Editorial by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Account - Playlist Manager</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="../assets/css/main.css" />
	</head>
<body>
<form name="filter_form" id="filter_form" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>"/>
<label for="date_from"> Filter von: </label>
<input type="date" required id="date_from" name="date_from" <?php if(!isset($_GET['Reset'])) echo 'value="'.(isset($_GET['date_from']) ? $_GET['date_from'] : '').'"'; ?>/>
<input type="submit" value="Zurücksetzen" name="Reset"/>
<label for="date_to"> bis Datum: </label>
<input type="date" required id="date_to" name="date_to" <?php if(!isset($_GET['Reset'])) echo 'value="'.(isset($_GET['date_to']) ? $_GET['date_to'] : '').'"'; ?>/>
<input type="submit" value="Filter setzen" name="Filter"/>
</form>
<img src="de_graph.php?id=<?php echo $_GET['id']; ?>&<?php echo (isset($_GET['Reset']) ? 'Reset=1&' : '').(isset($_GET['Filter']) ? 'Filter=1&' : '').'date_from='.(isset($_GET['date_from']) ? $_GET['date_from'] : '').'&'.'date_to='.(isset($_GET['date_to']) ? $_GET['date_to'] : '' ); ?>" style="width:100%;">
<table>
<tr>
	<th>Woche</th>
	<th>Monat</th>
	<th>Jahr</th>
	<th>Tag</th>
	<th>Stunden</th>
</tr>
<?php 

$week = "";
$month = "";
$year = "";
$weektime = 0;
$monthtime = 0;
$yeartime = 0;

foreach($playing_array as $day=>$time) // Daten abrufen und Filter überprüfen
	if(isset($_GET['Reset']) || !isset($_GET['Filter']) || (strtotime($_GET['date_from']) <= strtotime($day) && strtotime($day) <= strtotime($_GET['date_to'])) ){ 
$oldweek = $week;
$oldmonth = $month;
$oldyear = $year;
$week = date('W', strtotime($day));
$month = date('F', strtotime($day));
$year = date('Y', strtotime($day));

if($oldweek != "" && $oldweek != $week) {
echo '
<tr>
	<td>'.$oldweek.'</td>
	<td></td>
	<td></td>
	<td></td>
	<td>'.number_format2($weektime, 2, ",", ".").'</td>
</tr>';
$weektime = 0;
}

if($oldmonth != "" && $oldmonth != $month) {

echo '
<tr>
	<td></td>
	<td>'.$oldmonth.'</td>
	<td></td>
	<td></td>
	<td>'.number_format2($monthtime, 2, ",", ".").'</td>
</tr>';
$monthtime = 0;
}

if($oldyear != "" && $oldyear != $year) {

echo '
<tr>
	<td></td>
	<td></td>
	<td>'.$oldyear.'</td>
	<td></td>
	<td>'.number_format2($yeartime, 2, ",", ".").'</td>
</tr>';
$yeartime = 0;
}
$weektime += $time;
$monthtime += $time;
$yeartime += $time;

echo '
<tr>
	<td>'.$week.'</td>
	<td>'.$month.'</td>
	<td>'.$year.'</td>
	<td>'.$day.'</td>
	<td>'.number_format2($time, 2, ",", ".").'</td>
</tr>';
} 

echo '
<tr>
	<td>'.$week.'</td>
	<td></td>
	<td></td>
	<td></td>
	<td>'.number_format2($weektime, 2, ",", ".").'</td>
</tr>';
$weektime = 0;

echo '
<tr>
	<td></td>
	<td>'.$month.'</td>
	<td></td>
	<td></td>
	<td>'.number_format2($monthtime, 2, ",", ".").'</td>
</tr>';
$monthtime = 0;

echo '
<tr>
	<td></td>
	<td></td>
	<td>'.$year.'</td>
	<td></td>
	<td>'.number_format2($yeartime, 2, ",", ".").'</td>
</tr>';

?>
</table>
<p align="center">(Werte kleiner als 5 min werden nicht angezeigt!)<br>(Die letzten 90 Tage werden abgedeckt!)</p>
</body>
</html>