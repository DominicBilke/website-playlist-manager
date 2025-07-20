<?php
date_default_timezone_set("Europe/Berlin");

//Set the maxlifetime of the session
ini_set( "session.gc_maxlifetime",31536000);

//Set the cookie lifetime of the session
ini_set( "session.cookie_lifetime", 0);
session_start();


function matching_time($stamp_from, $stamp_to, $time_from, $time_to, $days) {
 $stamp_from_d = date('w', $stamp_from);
 $stamp_from_h = date('H', $stamp_from);
 $stamp_from_m = date('i', $stamp_from);
 $stamp_to_d = date('w', $stamp_to);
 $stamp_to_h = date('H', $stamp_to);
 $stamp_to_m = date('i', $stamp_to);
 $arr_from = explode(':', $time_from);
 $from_h = $arr_from[0];
 $from_m = $arr_from[1];
 $arr_to = explode(':', $time_to);
 $to_h = $arr_to[0];
 $to_m = $arr_to[1];

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


$url = (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
$url .= $_SERVER['HTTP_HOST'];
$url .= $_SERVER['REQUEST_URI'];
if(strpos($url, "?")!==FALSE) {
    $url = strstr($url,"?",true);
    $url .= "?";
}
else {
    $url .= "?";
}

if(basename($url, ".php") == "?") $url = str_replace("?","index.php?",$url);
$filename = str_replace("de_", "", basename($url, ".php"));
$filename_de = "de_".$filename;
$_SESSION['url'] = $url;
$_SESSION['de_url'] = str_replace($filename_de,$filename,$url);
$_SESSION['de_url'] = str_replace($filename,$filename_de,$_SESSION['de_url']);
$_SESSION['en_url'] = str_replace($filename_de,$filename,$url);


if(strpos($url, "spotify")===FALSE && strpos($url, "applemusic")===FALSE && strpos($url, "editaccount")===FALSE) {
$_SESSION['de_url'].=$_SERVER['QUERY_STRING'];
$_SESSION['en_url'].=$_SERVER['QUERY_STRING'];
}


if(strpos($url, "spotify_play")===FALSE) {
	$playing=0;
	if(isset($_SESSION['playing_time']) && $_SESSION['playing_time'][1] == 0) $_SESSION['playing_time'][1] = time();
}
else {
	$playing=1;
	$_SESSION['playing_time'] = [time(), 0, $_SESSION['daytime_from'], $_SESSION['daytime_to'], $_SESSION['days']];
}

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";



if(isset($_SESSION['id'])) {
if(isset($_SESSION['playing_time'])) {
$pdo = new PDO('mysql:host=localhost;dbname=d03c87b2', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$sql = "SELECT playing_time FROM users WHERE id=".$_SESSION['id'];
foreach ($pdo->query($sql) as $row) {
	$playing_time = array_explode_with_keys($row['playing_time']);
}

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
	unset($_SESSION['playing_time']);
}
$playing_time = array_implode_with_keys($playing_time);

if($playing_time) {
$sql = "UPDATE users SET currently_playing=".$playing.", playing_time='".$playing_time."' WHERE id=".$_SESSION['id'];

// Prepare statement
$stmt = $pdo->prepare($sql);

// execute the query
$stmt->execute();
}
}
else 
{
$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');
$sql = "UPDATE users SET currently_playing=".$playing." WHERE id=".$_SESSION['id'];

// Prepare statement
$stmt = $pdo->prepare($sql);

// execute the query
$stmt->execute();
}
}
?>