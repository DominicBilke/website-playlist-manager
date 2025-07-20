<?php

/*
 Benutezr einloggen, aus Datenbank abrufen.
 $_GET['login'], $_GET['password']
*/

session_start();
date_default_timezone_set('Europe/Berlin');

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";

$url = $_SESSION['url'];
if($_GET['forward']) $url = str_replace(basename($url, ".php"), $_GET['forward']."?", $url);

$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$sql = "SELECT * FROM users WHERE login='".$_GET['login']."' AND password='".$_GET['password']."'";
foreach ($pdo->query($sql) as $row) {

 $_SESSION['days_random'] = $row['days_random'];
 $_SESSION['daytime_random'] = $row['daytime_random'];
 $_SESSION['login'] = $row['login'];
 $_SESSION['password'] = $row['password'];
 $_SESSION['id'] = $row['id'];
 $_SESSION['days'] = $row['days'];
 if($_SESSION['days_random']=='1') { 
	$r = rand(0,7);
      $days = [];
	for($i=0; $i<$r; $i++) {
		$days[] = rand(0,6);
	}
      $_SESSION['days'] = implode(', ', $days);
 }
 $_SESSION['daytime_from'] = $row['daytime_from'];
 $_SESSION['daytime_to'] = $row['daytime_to'];
 if($_SESSION['daytime_random']=='1') 
	do { 
 	$_SESSION['daytime_from'] = rand(0, 23).':'.rand(0, 60);
 	$_SESSION['daytime_to'] = rand(0, 23).':'.rand(0, 60); 
      if(strlen($_SESSION['daytime_from']) < 5) {
		$day_from = explode(':', $_SESSION['daytime_from']);
		$_SESSION['daytime_from'] = (strlen($day_from[0]) == 1 ? "0".$day_from[0] : $day_from[0]).":".(strlen($day_from[1]) == 1 ? "0".$day_from[1] : $day_from[1]);
	}
      if(strlen($_SESSION['daytime_to']) < 5) { 
		$day_to = explode(':', $_SESSION['daytime_to']);
		$_SESSION['daytime_to'] = (strlen($day_to[0]) == 1 ? "0".$day_to[0] : $day_to[0]).":".(strlen($day_to[1]) == 1 ? "0".$day_to[1] : $day_to[1]);
	}
	}
	while(date_parse($_SESSION["daytime_from"])["hour"] >= date_parse($_SESSION["daytime_to"])["hour"]);
 
 $_SESSION['office'] = $row['office'];
 $_SESSION['playlist_id'] = $row['playlist_id'];
 $_SESSION['apple_playlist_id'] = $row['apple_playlist_id'];
 $_SESSION['amazon_playlist_id'] = $row['amazon_playlist_id'];
 $_SESSION['youtube_playlist_id'] = $row['youtube_playlist_id'];
 $_SESSION['db_token'] = $row['db_token'];
 $_SESSION['editaccount']['db_token'] = $row['db_token'];
 $login=1;
}

if(!isset($login))
{
	session_destroy(); 
	$meldung='meldung=Login NICHT erfolgreich!';
	header("Location: ".$url.$meldung);
        exit;
}



$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";


$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$sql = "UPDATE users SET login_counter=login_counter+1 WHERE id=".$_SESSION['id'];

// Prepare statement
$stmt = $pdo->prepare($sql);

// execute the query
$stmt->execute();


$meldung='meldung=Login erfolgreich!';

header("Location: ".$url.$meldung);
?>