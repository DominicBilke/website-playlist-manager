<?php

/*
 Löschen von Benutzerkonto
 GET id als Benutzer id
*/

session_start();
$url = $_SESSION['url'];

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";


$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

  $sql = "DELETE FROM users WHERE id=".$_GET['id'];

  // Prepare statement
  $stmt = $pdo->prepare($sql);

  // execute the query
  $stmt->execute();

$meldung='meldung=Löschen des Kontos erfolgreich!';
if(isset($_GET['forward']))
	header("Location: ".$_GET['forward'].$meldung);
else
 	header("Location: ".$url.$meldung);
?>