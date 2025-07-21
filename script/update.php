<?php

/*
 Benutzerkonto aktualisieren
 Login in neuem Benutzer
 $_GET['password'], $_GET['days'], $_GET['daytime_from'], $_GET['daytime_to'], $_GET['id'] - siehe Formularfelder
*/

session_start();
$url = $_SESSION['url'];

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";


$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$days = "";
foreach ($_GET['days'] as $selectedOption)
	$days .= $selectedOption.", ";
  if($_GET['password'])
	$sql = "UPDATE users SET password='".$_GET['password']."', days='".$days."', daytime_from='".$_GET['daytime_from']."', daytime_to='".$_GET['daytime_to']."', days_random='".($_GET['days_random']=='1' ? '1' : '0')."', daytime_random='".($_GET['daytime_random']=='1' ? '1' : '0')."', office='".($_GET['office']=='1' ? '1' : '0')."', playlist_id='".$_GET['playlist_id']."', apple_playlist_id='".$_GET['apple_playlist_id']."', amazon_playlist_id='".$_GET['amazon_playlist_id']."', youtube_playlist_id='".$_GET['youtube_playlist_id']."', db_token='".$_GET['db_token']."', team='".$_GET['team']."' WHERE id=".$_GET['id'];
  else
	$sql = "UPDATE users SET days='".$days."', daytime_from='".$_GET['daytime_from']."', daytime_to='".$_GET['daytime_to']."', days_random='".($_GET['days_random']=='1' ? '1' : '0')."', daytime_random='".($_GET['daytime_random']=='1' ? '1' : '0')."', office='".($_GET['office']=='1' ? '1' : '0')."', playlist_id='".$_GET['playlist_id']."', apple_playlist_id='".$_GET['apple_playlist_id']."', amazon_playlist_id='".$_GET['amazon_playlist_id']."', youtube_playlist_id='".$_GET['youtube_playlist_id']."', db_token='".$_GET['db_token']."', team='".$_GET['team']."' WHERE id=".$_GET['id'];

  // Prepare statement
  $stmt = $pdo->prepare($sql);

  // execute the query
  $stmt->execute();

$sql = "SELECT * FROM users WHERE login='".$_GET['login']."'";
foreach ($pdo->query($sql) as $row) {
/*
 $_SESSION['login'] = $row['login'];
 $_SESSION['team'] = $row['team'];
 $_SESSION['password'] = $row['password'];
 $_SESSION['id'] = $row['id'];
 $_SESSION['days'] = $row['days'];
 $_SESSION['daytime_from'] = $row['daytime_from'];
 $_SESSION['daytime_to'] = $row['daytime_to'];
 $_SESSION['db_token'] = $row['db_token'];*/
 $_SESSION['editaccount']['db_token'] = $row['db_token'];
 $login=1;
 $_SESSION['user_id'] = $row['id'];
}

if(isset($login)) $meldung='&meldung=Update der Benutzerdaten erfolgreich!';
else $meldung='&meldung=Update der Benutzerdaten NICHT erfolgreich!';

header("Location: ".$url.'id='.$_GET['id'].'&login='.$_GET['login'].'&password='.$_GET['password'].'&days='.$days.'&daytime_from='.$_GET['daytime_from'].'&daytime_to='.$_GET['daytime_to'].'&days_random='.$_GET['days_random'].'&daytime_random='.$_GET['daytime_random'].'&office='.$_GET['office'].'&playlist_id='.$_GET['playlist_id'].'&apple_playlist_id='.$_GET['apple_playlist_id'].'&amazon_playlist_id='.$_GET['amazon_playlist_id'].'&youtube_playlist_id='.$_GET['youtube_playlist_id'].'&db_token='.$_GET['db_token'].'&team='.$_GET['team'].$meldung);
?>