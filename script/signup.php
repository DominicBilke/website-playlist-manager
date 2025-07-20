<?php

/*
 Benutzer registrieren, in Datenbank anlegen.
 $_GET['login'], $_GET['password']
*/
session_start();

$url = $_SESSION['url'];

$servername = "localhost";
$username = "d03c87b1";
$password = "WaBtpcMKcgf49wqp";


$pdo = new PDO('mysql:host=localhost;dbname=d03c87b1', 'd03c87b1', 'WaBtpcMKcgf49wqp');

$sql = "SELECT * FROM users WHERE login='".$_GET['login']."'";
foreach ($pdo->query($sql) as $row) {
 $login=1;
 $meldung = "meldung=Login bereits vergeben!";
}

if(!isset($login))
{
$statement = $pdo->prepare("INSERT INTO users (login, password)  VALUES (?, ?)");

$statement->execute(array($_GET['login'], $_GET['password']));

$sql = "SELECT * FROM users WHERE login='".$_GET['login']."' AND password='".$_GET['password']."'";
foreach ($pdo->query($sql) as $row) {

 $_SESSION['login'] = $row['login'];
 $_SESSION['password'] = $row['password'];
 $_SESSION['id'] = $row['id'];
 $_SESSION['days'] = $row['days'];
 $_SESSION['daytime_from'] = $row['daytime_from'];
 $_SESSION['daytime_to'] = $row['daytime_to'];
 $login=1;
 $meldung = "meldung=SignUp erfolgreich!";
}
}

header("Location: ".$url.$meldung);
?>