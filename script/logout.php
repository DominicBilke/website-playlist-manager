<?php

/*
 Logout aus Benutzerkonto
 Session Destroy
*/

session_start();


$url = $_SESSION['url'];

session_destroy();


$meldung='meldung=Logout erfolgreich!';

header("Location: ".$url.$meldung);
?>