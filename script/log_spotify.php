<?php
/*
	LOGGING FÜR DATENBANK, ZUSAMMENRECHNEN DER ZEITEN
*/
session_start();

if(isset($_COOKIE['session_data1']) && (!isset($_SESSION['user_id']) || !$_SESSION['user_id'])) {
  $data = explode(' -|- ', $_COOKIE['session_data1']); 
  $_SESSION['user_id'] = $data[0];
  $_SESSION['login'] = $data[1];
  $_SESSION['daytime_from'] = $data[2];
  $_SESSION['daytime_to'] = $data[3];
  $_SESSION['days'] = $data[4];
}

if(isset($_SESSION['user_id']))
  setcookie('session_data1', implode(' -|- ', [$_SESSION['user_id'], $_SESSION['login'], $_SESSION['daytime_from'], $_SESSION['daytime_to'], $_SESSION['days']]), time() + (86400 * 30), "/");

function matching_time($stamp_from, $stamp_to, $time_from, $time_to, $days) {
 $stamp_from_d = date('w', $stamp_from);
 $stamp_from_h = date('H', $stamp_from);
 $stamp_from_m = date('i', $stamp_from);
 $stamp_to_d = date('w', $stamp_to);
 $stamp_to_h = date('H', $stamp_to);
 $stamp_to_m = date('i', $stamp_to);
 $arr_from = explode(':', $time_from);
 $from_h = (isset($arr_from[0]) && $arr_from[0] ? $arr_from[0] : 0);
 $from_m = (isset($arr_from[1]) && $arr_from[1] ? $arr_from[1] : 0);
 $arr_to = explode(':', $time_to);
 $to_h = (isset($arr_to[0]) && $arr_to[0] ? $arr_to[0] : 0);
 $to_m = (isset($arr_to[1]) && $arr_to[1] ? $arr_to[1] : 0);

// innerhalb der Spielzeit
 if(strpos($days, $stamp_to_d) !== FALSE && strpos($days, $stamp_from_d) !== FALSE && ($stamp_from_h >= $from_h || ($stamp_from_h >= $from_h && $stamp_from_m >= $from_m)) && ($stamp_to_h <= $to_h || ($stamp_to_h <= $to_h && $stamp_to_m <= $to_m))) 
	return TRUE; 

//Anfang in Spielzeit
 if(strpos($days, $stamp_from_d) !== FALSE && ($stamp_from_h >= $from_h || ($stamp_from_h >= $from_h && $stamp_from_m >= $from_m)) && ( $stamp_from_h <= $to_h || ($stamp_from_h <= $to_h && $stamp_from_m <= $to_m)))
	return TRUE;

// Ende in Spielzeit
 if(strpos($days, $stamp_to_d) !== FALSE && ($stamp_to_h >= $from_h || ($stamp_to_h >= $from_h && $stamp_to_m >= $from_m)) && ($stamp_to_h <= $to_h || ($stamp_to_h <= $to_h && $stamp_to_m <= $to_m)))
	return TRUE;

// Anfang vor der Spielzeit, Ende nach der Spielzeit
 if(($stamp_from_h < $from_h || ($stamp_from_h < $from_h && $stamp_from_m < $from_m)) && ($stamp_to_h > $to_h || ($stamp_to_h > $to_h && $stamp_to_m > $to_m))) 
	return TRUE; 

 return FALSE;
}

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
	
$playing=1;
if(isset($_SESSION['playing_time'])) $_SESSION['playing_time'][1] = time();
else $_SESSION['playing_time'] = [time(), 0, (isset($_SESSION['daytime_from']) ? $_SESSION['daytime_from'] : '00:00'), (isset($_SESSION['daytime_to']) ? $_SESSION['daytime_to'] : '23:59'), (isset($_SESSION['days']) ? $_SESSION['days'] : implode(', ', range(0, 6)))];

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";


if(isset($_SESSION['user_id']) && $_SESSION['user_id']) {
if(isset($_SESSION['playing_time'])) {
$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$sql = "SELECT playing_time FROM users WHERE id=".$_SESSION['user_id'];
foreach ($pdo->query($sql) as $row) {
	$playing_time = array_explode_with_keys($row['playing_time']);
}
if($playing_time)
foreach($playing_time as $d=>$t) {
	if(strpos($d, 'day')===FALSE && $t < strtotime('-90 days')) {
		unset($playing_time[$d]);
		unset($playing_time['daytime_from_'.$d]);
		unset($playing_time['daytime_to_'.$d]);
		unset($playing_time['days_'.$d]);
	}
}

if(isset($_SESSION['playing_time']) && $_SESSION['playing_time'][1] != 0) {
	$playing_time[$_SESSION['playing_time'][0]] = $_SESSION['playing_time'][1];
	$playing_time['daytime_from_'.$_SESSION['playing_time'][0]] = $_SESSION['playing_time'][2];
	$playing_time['daytime_to_'.$_SESSION['playing_time'][0]] = $_SESSION['playing_time'][3];
	$playing_time['days_'.$_SESSION['playing_time'][0]] = $_SESSION['playing_time'][4];
}
$playing_time = array_implode_with_keys($playing_time);

if($playing_time) {
$sql = "UPDATE users SET currently_playing=".$playing.", playing_time='".$playing_time."' WHERE id=".$_SESSION['user_id'];

// Prepare statement
$stmt = $pdo->prepare($sql);

// execute the query
$stmt->execute();
}
}
else 
{
$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');
$sql = "UPDATE users SET currently_playing=".$playing." WHERE id=".$_SESSION['user_id'];

// Prepare statement
$stmt = $pdo->prepare($sql);

// execute the query
$stmt->execute();
}
}
?>